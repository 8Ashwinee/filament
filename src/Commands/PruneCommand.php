<?php

namespace Vibefilter\Filament\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Schema;
use Vibefilter\Filament\Models\Decision;
use Vibefilter\Filament\Models\Question;
use Vibefilter\Filament\Support\Numbers;

/**
 * Deletes the statements nobody has run for a while, with their cached scores.
 * Running one of them again simply scores it again.
 */
class PruneCommand extends Command
{
    protected $signature = 'vibefilter:prune
        {--days=90 : Delete statements not run for this many days}
        {--dry-run : Only show what would be deleted}';

    protected $description = 'Delete cached Vibefilter scores for statements nobody has run lately';

    public function handle(): int
    {
        if (! Schema::hasColumn((new Question)->getTable(), 'last_used_at')) {
            $this->error('The vibefilter_questions table has no last_used_at column yet. Publish and run the new migration: php artisan vendor:publish --tag="vibefilter-migrations" && php artisan migrate');

            return self::FAILURE;
        }

        $days = (int) $this->option('days');

        if ($days < 1) {
            $this->error('--days must be at least 1.');

            return self::FAILURE;
        }

        $stale = Question::query()->where('last_used_at', '<', Date::now()->subDays($days));
        $statements = (clone $stale)->count();
        $scores = Decision::query()->whereIn('question_id', (clone $stale)->select('id'))->count();

        if ($this->option('dry-run')) {
            $this->info("Would delete {$this->count($statements, 'statement')} not run for {$days} days, with {$this->count($scores, 'cached score')}.");

            return self::SUCCESS;
        }

        // Scores first, in chunks, so this works the same with or without foreign key cascades.
        (clone $stale)->select('id')->chunkById(500, function ($questions) {
            Decision::query()->whereIn('question_id', $questions->modelKeys())->delete();
            Question::query()->whereKey($questions->modelKeys())->delete();
        });

        $this->info("Deleted {$this->count($statements, 'statement')} not run for {$days} days, with {$this->count($scores, 'cached score')}.");

        return self::SUCCESS;
    }

    protected function count(int $count, string $noun): string
    {
        return Numbers::format($count) . ' ' . $noun . ($count === 1 ? '' : 's');
    }
}
