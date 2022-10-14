<?php

namespace Flattens\Inertia\Http\Resources;

use Carbon\Carbon;
use Inertia\Inertia;
use JsonSerializable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Support\Arrayable;
use Flattens\Inertia\Concerns\ResolvesAssets;
use Illuminate\Contracts\Support\Responsable;
use Flattens\Inertia\Concerns\ResolvesInternalUrls;
use Flattens\Inertia\Concerns\ResolvesNestedEntries;

class Resource implements Arrayable, JsonSerializable, Responsable
{
    use ResolvesAssets;
    use ResolvesInternalUrls;
    use ResolvesNestedEntries;

    protected $value;

    protected $relations = [];

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function assets($key, $manipulations = [])
    {
        $paths = $this->collect([$key])->toArray();

        return $this->resolveAssetsFromPaths($paths, $manipulations);
    }

    public function asset($key, $manipulations = [])
    {
        return $this->assets($key, $manipulations)->first();
    }

    public function hasRelations()
    {
        return !! count($this->relations);
    }

    public function withRelations(array $keys)
    {
        $this->relations = array_merge($this->relations, $keys);

        return $this->collect($keys);
    }

    public function relations()
    {
        $this->resolveNestedEntries(
            Arr::only($this->properties(), $this->relations)
        );

        return $this->nestedEntries;
    }

    public function get($key, $default = null)
    {
        return $this->value->get($key, $default);
    }

    public function datetime($key, $default = 'now')
    {
        return Carbon::parse($this->get($key, $default));
    }

    public function load($key, $default = null, $name = null)
    {
        $this->relations[] = $name ?? $key;
        return $this->get($key, $default);
    }

    public function augmented(array $keys = null)
    {
        return $this->value->toAugmentedCollection($keys)
            ->withShallowNesting();
    }

    public function collect(array $keys)
    {
        return Collection::make($keys)
            ->keyBy(fn ($key) => $key)
            ->map(fn ($key) => $this->get($key));
    }

    public function __get($key)
    {
        if (property_exists($this->value, $key)) {
            return $this->value->$key;
        }

        return $this->get($key);
    }

    public function __call($method, $params)
    {
        return $this->value->$method(...$params);
    }

    public function toArray()
    {
        return $this->augmented()->toArray();
    }

    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    public function toResponse($request)
    {
        return Inertia::render(
            $this->template(),
            $this->jsonSerialize(),
        )->toResponse($request);
    }

    protected function template()
    {
        return $this->value->template();
    }

    protected function attributes($attrs = [])
    {
        if ($this->hasRelations()) {
            $attrs['relations'] = $this->relations();
        }

        if (count($this->assets)) {
            $attrs['assets'] = $this->assets;
        }

        return $attrs;
    }

    protected function properties($props = [])
    {
        if ($props instanceof Arrayable) {
            $props = $props->toArray();
        }

        if ($props instanceof JsonSerializable) {
            $props = $props->jsonSerialize();
        }

        $props = $this->resolveInternalUrls($props);

        $props = $this->resolveAssets($props);

        return $props;
    }

    public function jsonSerialize()
    {
        $props = $this->properties();

        $attrs = $this->attributes();

        return array_filter(
            compact('attrs', 'props')
        );
    }
}
