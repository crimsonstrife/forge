<?php

namespace App\Support\Keys;

use App\Models\Project;
use Illuminate\Support\Str;

final class ProjectKeyGenerator
{
    /**
     * Suggest a 3–5 char code from a name (no DB check).
     * "One Man's Poison" => "OMP"
     */
    public function suggest(string $name, int $targetLen = 3): string
    {
        $words = preg_split('/[^A-Za-z0-9]+/u', $name, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $letters = [];

        foreach ($words as $w) {
            $letters[] = mb_substr($w, 0, 1);
        }

        $acronym = implode('', $letters);
        $acronym = $acronym !== '' ? $acronym : Str::ascii($name);
        $acronym = preg_replace('/[^A-Za-z0-9]/', '', Str::upper($acronym));

        if (mb_strlen($acronym) < $targetLen && count($words) > 0) {
            $i = 1;
            while (mb_strlen($acronym) < $targetLen) {
                foreach ($words as $w) {
                    $acronym .= Str::upper(mb_substr($w, $i, 1) ?: '');
                    if (mb_strlen($acronym) >= $targetLen) {
                        break 2;
                    }
                }
                $i++;
                if ($i > 6) {
                    break;
                }
            }
        }

        return mb_substr($acronym, 0, max(2, $targetLen));
    }

    /**
     * Make a unique key from a *name*, keeping it within $maxLen.
     * Produces: OMP, OMP2, OMP3…
     */
    public function uniqueForName(string $name, int $targetLen = 3, int $maxLen = 8): string
    {
        $base = $this->sanitize($this->suggest($name, $targetLen));
        return $this->withCounter($base, $maxLen);
    }

    /**
     * Make a unique key from an existing *seed* (incoming key/slug/name).
     * Useful for imports where a key is provided.
     */
    public function uniqueFromSeed(string $seed, int $maxLen = 8): string
    {
        $base = $this->sanitize($seed);
        if ($base === '') {
            $base = 'PRJ';
        }
        return $this->withCounter($base, $maxLen);
    }

    private function exists(string $key): bool
    {
        return Project::query()->where('key', $key)->exists();
    }

    private function sanitize(string $s): string
    {
        return Str::upper(preg_replace('/[^A-Z0-9]/', '', Str::ascii($s)));
    }

    /**
     * Try the base, then append an integer suffix, always fitting into $maxLen.
     */
    private function withCounter(string $base, int $maxLen): string
    {
        $base = mb_substr($base, 0, max(1, $maxLen)); // trim base to maxLen
        $i = 0;

        while (true) {
            $suffix = $i === 0 ? '' : (string) $i; // '', '2', '3', ...
            $headLen = $maxLen - strlen($suffix);
            $headLen = max(1, $headLen);           // keep at least 1 char
            $key = mb_substr($base, 0, $headLen) . $suffix;

            if (! $this->exists($key)) {
                return $key;
            }

            $i++;
            if ($i > 9999) {
                // ultra defensive fallback: short random
                $rand = Str::upper(Str::substr(Str::random(6), 0, $maxLen));
                if (! $this->exists($rand)) {
                    return $rand;
                }
            }
        }
    }
}
