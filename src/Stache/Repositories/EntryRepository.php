<?php

namespace Flattens\Inertia\Stache\Repositories;

use Statamic\Contracts\Entries\Entry;
use Statamic\Contracts\Entries\QueryBuilder;
use Statamic\Contracts\Entries\EntryRepository as Contract;
use Statamic\Stache\Repositories\EntryRepository as Statamic;

class EntryRepository extends Statamic implements Contract
{
    public function findByUri(string $uri, string $site = null): ?Entry
    {
        $site = $site ?? $this->stache->sites()->first();

        return $this->query()
                ->where('uri', $uri)
                ->where('site', $site)
                ->first();
    }

    public static function bindings(): array
    {
        return [
            QueryBuilder::class => \Flattens\Inertia\Stache\Query\EntryQueryBuilder::class,
            Entry::class => \Flattens\Inertia\Entries\Entry::class,
        ];
    }
}
