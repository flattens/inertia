<?php

namespace Flattens\Inertia\Taxonomies;

use Illuminate\Support\Str;
use Flattens\Inertia\Contracts\Resourceable;
use Statamic\Taxonomies\Taxonomy as Statamic;
use Flattens\Inertia\Http\Resourceable as HasResource;
use Statamic\Contracts\Taxonomies\Taxonomy as Contract;

class Taxonomy extends Statamic implements Contract, Resourceable
{
    use HasResource;

    public function resourceName()
    {
        $class = (string) Str::of($this->handle())
            ->camel()
            ->ucfirst();

        return "Taxonomies\\$class";
    }
}
