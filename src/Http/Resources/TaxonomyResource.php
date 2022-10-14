<?php

namespace Flattens\Inertia\Http\Resources;

class TaxonomyResource extends Resource
{
    public function terms($args = [], $wheres = [])
    {
        return $this->queryTerms()->get()->map->toResource();
    }

    public function properties($props = [])
    {
        $props = $this->toArray();

        return parent::properties($props);
    }

    public function toArray()
    {
        return [
            'title' => $this->title(),
            'handle' => $this->handle(),
        ];
    }
}
