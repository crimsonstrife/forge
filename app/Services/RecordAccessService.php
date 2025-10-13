<?php

namespace App\Services;

use App\Enums\AccessLevel;
use App\Models\Organization;
use App\Models\RecordShare;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Cache\TaggableStore;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Resolves effective per-record share level only.
 * Returns null when sharing is disabled or no shares exist.
 */
class RecordAccessService
{
    public function levelFor(User $user, Model $record): ?AccessLevel
    {
        if (! feature('PerRecordSharing')->active($user)) {
            return null;
        }

        if (! $this->hasAnyShare($record)) {
            return null;
        }

        $cache = $this->cache();
        $cacheKey = $this->cacheKeyFor($user, $record);

        return $cache->remember($cacheKey, now()->addMinutes(10), function () use ($user, $record) {
            $candidates = $this->principalPairsFor($user);

            $levels = new Collection();

            $levels->push(
                $this->highestLevelFromShares($record, $candidates)
            );

            if (method_exists($record, 'parentShareable')) {
                $parent = $record->parentShareable();
                if ($parent && $this->hasAnyShare($parent)) {
                    $levels->push(
                        $this->highestLevelFromShares($parent, $candidates, true)
                    );
                }
            }

            $levels = $levels->filter();

            if ($levels->isEmpty()) {
                return null;
            }

            return $levels->reduce(
                fn (?AccessLevel $carry, AccessLevel $lvl) => AccessLevel::max($carry, $lvl),
                null
            );
        });
    }

    /**
     * Light existence check, cached, so we can skip heavy work when there are no shares.
     */
    public function hasAnyShare(Model $record): bool
    {
        $key = sprintf('rs:any:%s:%s', $record::class, $record->id);

        return $this->cache()->remember($key, now()->addMinutes(10), function () use ($record) {
            return RecordShare::query()
                ->where('shareable_type', $record::class)
                ->where('shareable_id', $record->id)
                ->notExpired()
                ->exists();
        });
    }

    public function bustCacheForShareable(Model $record): void
    {
        // Remove only the cache entry for this record's share existence
        $key = sprintf('rs:any:%s:%s', $record::class, $record->id);
        $this->cache()->forget($key);
    }

    private function highestLevelFromShares(object $shareable, Collection $pairs, bool $requirePropagation = false): ?AccessLevel
    {
        $rows = RecordShare::query()
            ->select(['access_level', 'propagate_to_children'])
            ->where('shareable_type', $shareable::class)
            ->where('shareable_id', $shareable->id)
            ->whereIn('principal_type', $pairs->pluck('type'))
            ->whereIn('principal_id', $pairs->pluck('id'))
            ->notExpired()
            ->when($requirePropagation, fn ($q) => $q->where('propagate_to_children', true))
            ->get();

        if ($rows->isEmpty()) {
            return null;
        }

        return $rows->reduce(
            fn (?AccessLevel $carry, RecordShare $row) => AccessLevel::max($carry, $row->access_level),
            null
        );
    }

    /** @return Collection<int, array{type: class-string, id: string}> */
    private function principalPairsFor(User $user): Collection
    {
        return collect()
            ->push(['type' => \App\Models\User::class, 'id' => $user->id])
            ->when(method_exists($user, 'roles'), fn ($c) => $c->merge(
                $user->roles->map(fn ($r) => ['type' => Role::class, 'id' => $r->id])
            ))
            ->when(method_exists($user, 'teams'), fn ($c) => $c->merge(
                $user->teams->map(fn ($t) => ['type' => Team::class, 'id' => $t->id])
            ))
            ->when(method_exists($user, 'organizations'), fn ($c) => $c->merge(
                $user->organizations->map(fn ($o) => ['type' => Organization::class, 'id' => $o->id])
            ));
    }

    private function cache(): CacheRepository
    {
        $store = Cache::getStore();

        if ($store instanceof TaggableStore) {
            return Cache::tags(['record-shares']);
        }

        return Cache::store();
    }

    private function cacheKeyFor(User $user, Model $record): string
    {
        return sprintf('rs:%s:%s:%s', $user->id, $record::class, $record->id);
    }
}
