<?php

namespace Flattens\Inertia\Globals;

use JsonSerializable;
use Illuminate\Support\Str;
use Statamic\Globals\GlobalSet as Statamic;
use Flattens\Inertia\Contracts\Resourceable;
use Statamic\Contracts\Globals\GlobalSet as Contract;
use Flattens\Inertia\Http\Resourceable as HasResource;

class GlobalSet extends Statamic implements Contract, Resourceable, JsonSerializable
{
    use HasResource;

    public function resourceName()
    {
        $class = (string) Str::of($this->handle())
            ->camel()
            ->ucfirst();

        return "Globals\\$class";
    }

    public function jsonSerialize()
    {
        return $this->toResource()->jsonSerialize();
    }
}
