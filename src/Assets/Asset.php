<?php

namespace Flattens\Inertia\Assets;

use JsonSerializable;
use Statamic\Assets\Asset as Entity;
use Illuminate\Contracts\Support\Arrayable;

class Asset implements Arrayable, JsonSerializable
{
    protected $asset;

    protected $manipulations;

    public function __construct(Entity $asset, $manipulations = [])
    {
        $this->asset = $asset;
        $this->manipulations = $manipulations;
    }

    public function sizes()
    {
        return array_map(
            fn ($params) => $this->asset->manipulate($params),
            $this->manipulations
        );
    }

    public function toArray()
    {
        $attributes = [
            'url' => $this->asset->url(),
            'meta' => $this->asset->meta(),
        ];

        if ($this->asset->isImage()) {
            $attributes['sizes'] = $this->sizes();
            $attributes['isImage'] = true;
        }

        if ($this->asset->isVideo()) {
            $attributes['isVideo'] = true;
        }

        return $attributes;
    }

    public function jsonSerialize()
    {
        return (array) $this->toArray();
    }
}
