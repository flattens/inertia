<?php

namespace Flattens\Inertia\Http\Resources;

use Statamic\Facades\Term;
use Flattens\Inertia\Structures\Structure;
use Statamic\Facades\Collection as CollectionRepository;

class EntryResource extends Resource
{
    protected $type = 'entry';

    public function as($type)
    {
        $this->type = $type;

        return $this;
    }

    public function hasStructure()
    {
        return ('entry' === $this->type)
            ? $this->value->hasStructure()
            : false;
    }

    public function get($key, $default = null)
    {
        if ($this->value->has($key)) {
            return $this->value->getSupplement($key) ?? $this->value->get($key, $default);
        }

        if ($this->value->hasOrigin() && $this->value->origin()->has($key)) {
            return $this->value->origin()->get($key, $default);
        }

        if (! $this->value->isRoot()) {
            return $this->value->root()->get($key, $default);
        }

        return $default;
    }

    public function terms($key, $taxonomy, $default = [])
    {
        if (!$terms = $this->get($key, null)) {
            return $default;
        }

        return $this->queryTerms($taxonomy, $terms, 'or')->jsonSerialize();
    }

    public function term($key, $taxonomy, $default = null)
    {
        if (!$term = $this->get($key, null)) {
            return $default;
        }

        return $this->queryTerms($taxonomy, [$term])->first()->jsonSerialize();
    }

    protected function queryTerms($taxonomy, array $terms, $boolean = 'and')
    {
        $query = Term::query();

        foreach ($terms as $name) {
            $query->where('id', "=", "{$taxonomy}::{$name}", $boolean);
        }

        return $query->get();
    }

    public function structure()
    {
        $structure = [];

        if ($parent = $this->value->getParent()) {
            $structure['parent'] = new Structure($parent);
        }

        if ($children = $this->value->getChildren()) {
            $structure['children'] = Structure::collection($children);
        }

        return array_filter($structure);
    }

    public function toArray()
    {
        return $this->augmented()->toArray();
    }

    public function toExcerpt()
    {
        return $this->augmented()->toArray();
    }

    protected function attributes($attrs = [])
    {
        $attrs['mounted'] = $this->mounted();

        if ($this->hasStructure()) {
            $attrs['structure'] = $this->structure();
        }

        return parent::attributes($attrs);
    }

    public function mounted($attributes = [])
    {
        // Get the origin entry for multisite support.
        $entry = $this->value->hasOrigin() ? $this->value->origin() : $this->value;

        $collection = $entry->value('mount') ?? CollectionRepository::findByMount($entry);

        if ($collection) {
            return $collection->toResource()->jsonSerialize();
        }
    }

    protected function properties($props = [])
    {
        $props = ('excerpt' === $this->type)
            ? $this->toExcerpt()
            : $this->toArray();

        return parent::properties($props);
    }
}
