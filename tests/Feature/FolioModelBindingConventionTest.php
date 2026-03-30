<?php

namespace Tests\Feature;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Tests\TestCase;

class FolioModelBindingConventionTest extends TestCase
{
    public function test_model_backed_folio_segments_use_the_model_class_case(): void
    {
        $modelBasenames = collect(glob(app_path('Models/*.php')) ?: [])
            ->mapWithKeys(fn (string $path) => [
                Str::lower(pathinfo($path, PATHINFO_FILENAME)) => pathinfo($path, PATHINFO_FILENAME),
            ]);

        $mismatches = collect();

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(resource_path('views/pages'), RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST,
        );

        foreach ($iterator as $item) {
            $relativePath = Str::after($item->getPathname(), resource_path('views/pages').DIRECTORY_SEPARATOR);

            foreach (explode(DIRECTORY_SEPARATOR, $relativePath) as $segment) {
                $this->recordCaseMismatchForSegment($mismatches, $modelBasenames, $relativePath, $segment);
            }
        }

        $this->assertSame(
            [],
            $mismatches->unique()->values()->all(),
            'Model-backed Folio segments must match the model class case on case-sensitive filesystems.'
        );
    }

    private function recordCaseMismatchForSegment(
        Collection $mismatches,
        Collection $modelBasenames,
        string $relativePath,
        string $segment,
    ): void {
        if (! str_contains($segment, '[') || ! str_contains($segment, ']')) {
            return;
        }

        $segmentName = Str::of($segment)
            ->after('[')
            ->before(']')
            ->after('...')
            ->before('-')
            ->before('|')
            ->before(':')
            ->trim('$')
            ->value();

        if ($segmentName === '' || str_contains($segmentName, '\\')) {
            return;
        }

        $modelClass = $modelBasenames->get(Str::lower($segmentName));

        if ($modelClass !== null && $segmentName !== $modelClass) {
            $mismatches->push(sprintf('%s uses [%s] but should use [%s].', $relativePath, $segmentName, $modelClass));
        }
    }
}
