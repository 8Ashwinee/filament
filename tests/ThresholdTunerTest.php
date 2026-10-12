<?php

namespace Vibefilter\Filament\Tests;

use Livewire\Livewire;
use Vibefilter\Filament\Contracts\DecisionDriver;
use Vibefilter\Filament\Drivers\FakeDriver;
use Vibefilter\Filament\Tests\Fixtures\ListReviews;
use Vibefilter\Filament\Tests\Fixtures\Review;

class ThresholdTunerTest extends TestCase
{
    protected FakeDriver $driver;

    /** @var array<string, Review> */
    protected array $reviews = [];

    protected function setUp(): void
    {
        parent::setUp();

        // Each review says its own score, e.g. "Scores 0.75."
        $this->driver = new FakeDriver(fn (string $statement, string $text) => (float) str_replace(['Scores ', '.'], ['', '.'], rtrim($text, '.')));
        $this->app->instance(DecisionDriver::class, $this->driver);

        foreach (['0.65', '0.75', '0.85', '0.95'] as $score) {
            $this->reviews[$score] = Review::create(['body' => "Scores {$score}."]);
        }
    }

    public function test_it_is_off_by_default(): void
    {
        Livewire::test(ListReviews::class)
            ->filterTable('vibe', ['statement' => 'The customer is angry.'])
            ->assertDontSee('Rows kept at each threshold');
    }

    public function test_it_shows_how_many_rows_each_threshold_keeps(): void
    {
        Livewire::test(ListReviews::class, ['tuner' => true])
            ->filterTable('vibe', ['statement' => 'The customer is angry.'])
            ->assertSee('Rows kept at each threshold')
            ->assertSee('0.60 · 4')
            ->assertSee('0.70 · 3')
            ->assertSee('0.80 · 2')
            ->assertSee('0.90 · 1');
    }

    public function test_picking_a_threshold_refilters_without_asking_the_model_again(): void
    {
        Livewire::test(ListReviews::class, ['tuner' => true])
            ->filterTable('vibe', ['statement' => 'The customer is angry.'])
            ->assertCountTableRecords(2)
            ->set('tableDeferredFilters.vibe.threshold', '0.60')
            ->assertCountTableRecords(4)
            ->assertSee('Vibe: The customer is angry. (≥ 0.60)');

        $this->assertCount(1, $this->driver->calls);
    }

    public function test_a_threshold_outside_the_steps_is_ignored(): void
    {
        Livewire::test(ListReviews::class, ['tuner' => true])
            ->filterTable('vibe', ['statement' => 'The customer is angry.', 'threshold' => '0.10'])
            ->assertCountTableRecords(2);
    }
}
