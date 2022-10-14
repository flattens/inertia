<?php

namespace Flattens\Inertia\Stache\Repositories;

use Statamic\Contracts\Entries\Collection;
use Statamic\Contracts\Entries\CollectionRepository as Contract;
use Statamic\Stache\Repositories\CollectionRepository as Statamic;

class CollectionRepository extends Statamic implements Contract
{
    public static function bindings(): array
    {
        return [
            Collection::class => \Flattens\Inertia\Entries\Collection::class,
        ];
    }
}
