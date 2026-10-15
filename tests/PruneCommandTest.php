<?php

namespace Vibefilter\Filament\Tests;

use Illuminate\Support\Facades\Date;
use Vibefilter\Filament\Drivers\FakeDriver;
use Vibefilter\Filament\Models\Decision;
use Vibefilter\Filament\Models\Question;
use Vibefilter\Filament\Scorer;

class PruneCommandTest extends TestCase
{
    protected function score(string $statement): void
    {
        (new Scorer(new FakeDriver(fn () => 0.9)))->score($statement, ['One.', 'Two.']);
    }

    public function test_running_a_statement_marks_it_used(): void
    {
        Date::setTestNow('2026-10-01 10:00:00');
        $this->score('The customer is angry.');

        Date::setTestNow('2026-10-12 08:00:00');
        $this->score('The customer is angry.');

        $this->assertSame('2026-10-12 08:00:00', Question::first()->last_used_at->toDateTimeString());
    }

    public function test_it_deletes_statements_not_run_lately_with_their_scores(): void
    {
        Date::setTestNow('2026-06-01');
        $this->score('The customer is happy.');
        Date::setTestNow('2026-10-01');
        $this->score('The customer is angry.');

        Date::setTestNow('2026-10-12');
        $this->artisan('vibefilter:prune', ['--days' => 90])
            ->expectsOutput('Deleted 1 statement not run for 90 days, with 2 cached scores.')
            ->assertSuccessful();

        $this->assertSame(['The customer is angry.'], Question::pluck('text')->all());
        $this->assertSame(2, Decision::count());
    }

    public function test_a_dry_run_deletes_nothing(): void
    {
        Date::setTestNow('2026-06-01');
        $this->score('The customer is happy.');

        Date::setTestNow('2026-10-12');
        $this->artisan('vibefilter:prune', ['--dry-run' => true])
            ->expectsOutput('Would delete 1 statement not run for 90 days, with 2 cached scores.')
            ->assertSuccessful();

        $this->assertSame(1, Question::count());
        $this->assertSame(2, Decision::count());
    }

    public function test_days_must_be_positive(): void
    {
        $this->artisan('vibefilter:prune', ['--days' => 0])->assertFailed();
    }

    public function test_scoring_still_works_before_the_new_migration_has_run(): void
    {
        (include __DIR__ . '/../database/migrations/add_last_used_at_to_vibefilter_questions.php.stub')->down();

        $scores = (new Scorer(new FakeDriver(fn () => 0.9)))->score('The customer is angry.', ['One.']);

        $this->assertSame([0.9], $scores);
        $this->artisan('vibefilter:prune')->assertFailed();
    }
}
