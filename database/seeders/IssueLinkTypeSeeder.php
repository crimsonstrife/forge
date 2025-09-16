<?php

namespace Database\Seeders;

use App\Models\IssueLinkType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IssueLinkTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var array<int, array{key:string,name:string,inverse?:string,symmetric?:bool}> $defs */
        $defs = [
            ['key' => 'blocks',        'name' => 'blocks',         'inverse' => 'is blocked by'],
            ['key' => 'clones',        'name' => 'clones',         'inverse' => 'is cloned by'],
            ['key' => 'duplicates',    'name' => 'duplicates',     'inverse' => 'is duplicated by'],
            ['key' => 'idea-for',      'name' => 'is idea for',    'inverse' => 'added to idea'],
            ['key' => 'implements',    'name' => 'implements',     'inverse' => 'is implemented by'],
            ['key' => 'merged-into',   'name' => 'merged into',    'inverse' => 'merged from'],
            ['key' => 'reviews',       'name' => 'reviews',        'inverse' => 'is reviewed by'],
            ['key' => 'causes',        'name' => 'causes',         'inverse' => 'is caused by'],
            ['key' => 'relates-to',    'name' => 'relates to',     'symmetric' => true],
        ];

        foreach ($defs as $d) {
            IssueLinkType::query()->updateOrCreate(
                ['key' => $d['key']],
                [
                    'name'         => $d['name'],
                    'inverse_name' => $d['symmetric'] ?? false ? $d['name'] : ($d['inverse'] ?? null),
                    'is_symmetric' => (bool)($d['symmetric'] ?? false),
                    'is_active'    => true,
                    'is_system'    => true,
                ],
            );
        }
    }
}
