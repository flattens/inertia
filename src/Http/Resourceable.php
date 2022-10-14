<?php

namespace Flattens\Inertia\Http;

use Flattens\Inertia\Factories\Resource;

trait Resourceable
{
    public function toResource()
    {
        $resource = new Resource();

        return $resource->make($this);
    }
}
