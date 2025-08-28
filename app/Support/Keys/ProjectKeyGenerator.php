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

        // Try acronym: first letter of first N words
        foreach ($words as $w) {
            $letters[] = mb_substr($w, 0, 1);
        }

        $acronym = implode('', $letters);
        $acronym = $acronym !== '' ? $acronym : Str::ascii($name); // fallback
        $acronym = preg_replace('/[^A-Za-z0-9]/', '', Str::upper($acronym));

        // If too short (1–2 words), pad with more letters from first word(s)
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
                } // avoid runaway
            }
        }

        // Trim to max 5 (configurable)
        return mb_substr($acronym, 0, max(2, $targetLen));
    }

    /**
     * Make a unique project key, appending a numeric suffix if needed: OMP, OMP2, OMP3…
     */
    public function uniqueForName(string $name, int $targetLen = 3): string
    {
        $base = $this->suggest($name, $targetLen);

        $key = $base;
        $i = 2;

        // Try base, then base2, base3, …
        while ($this->exists($key)) {
            $key = $base . $i;
            $i++;
            if ($i > 9999) {
                // Extremely defensive fallback
                $key = Str::upper(Str::random(4));
                if (! $this->exists($key)) {
                    break;
                }
            }
        }

        return $key;
    }

    private function exists(string $key): bool
    {
        return Project::query()->where('key', $key)->exists();
    }
}
