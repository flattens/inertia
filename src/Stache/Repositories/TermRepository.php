<?php

namespace Flattens\Inertia\Stache\Repositories;

use Statamic\Contracts\Taxonomies\Term;
use Statamic\Contracts\Taxonomies\TermRepository as Contract;
use Statamic\Stache\Repositories\TermRepository as Statamic;

class TermRepository extends Statamic implements Contract
{
    public static function bindings(): array
    {
        return [
            Term::class => \Flattens\Inertia\Taxonomies\Term::class,
        ];
    }
}
