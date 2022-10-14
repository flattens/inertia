<?php

namespace Flattens\Inertia\Entries;

use JsonSerializable;
use Illuminate\Support\Str;
use Flattens\Inertia\Contracts\Resourceable;
use Statamic\Entries\Collection as Statamic;
use Flattens\Inertia\Http\Resourceable as HasResource;
use Statamic\Contracts\Entries\Collection as Contract;

class Collection extends Statamic implements Contract, Resourceable, JsonSerializable
{
    use HasResource;

    public function resourceName()
    {
        $class = (string) Str::of($this->handle())
            ->camel()
            ->ucfirst();

        return "Collections\\$class";
    }

    public function jsonSerialize()
    {
        return $this->toResource()->jsonSerialize();
    }
}
