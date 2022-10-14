<?php

namespace Flattens\Inertia\Taxonomies;

use JsonSerializable;
use Illuminate\Support\Str;
use Statamic\Taxonomies\LocalizedTerm as Statamic;
use Statamic\Contracts\Taxonomies\Term as Contract;
use Flattens\Inertia\Contracts\Resourceable;
use Flattens\Inertia\Http\Resourceable as HasResource;

class LocalizedTerm extends Statamic implements Contract, Resourceable, JsonSerializable
{
    use HasResource;

    public function resourceName()
    {
        return $this->term->resourceName();
    }

    public function jsonSerialize()
    {
        return $this->toResource()->jsonSerialize();
    }
}
