<?php

namespace Flattens\Inertia\Http\Resources;

class TermResource extends Resource
{
    public function properties($props = [])
    {
        $props = $this->toArray();

        return parent::properties($props);
    }

    public function toArray()
    {
        return [
            'title' => $this->title(),
            'slug' => $this->slug(),
            'taxonomy' => $this->taxonomyHandle(),
        ];
    }
}
