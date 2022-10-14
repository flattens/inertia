<?php

namespace Flattens\Inertia\Factories;

use Flattens\Inertia\Entries\Entry;
use Flattens\Inertia\Http\Resources;
use Flattens\Inertia\Taxonomies\Term;
use Flattens\Inertia\Entries\Collection;
use Flattens\Inertia\Contracts\Resourceable;
use Flattens\Inertia\Globals\GlobalSet;
use Flattens\Inertia\Taxonomies\LocalizedTerm;
use Flattens\Inertia\Taxonomies\Taxonomy;

class Resource
{
    public static $namespace = 'App\Http\Resources';

    public function namespace($namespace)
    {
        self::$namespace = $namespace;
    }

    public function make(Resourceable $value)
    {
        $class = $this->resolveNamespace($value);

        return new $class($value);
    }

    protected function resolveNamespace($value)
    {
        if (class_exists($class = self::$namespace . '\\' . $value->resourceName())) {
            return $class;
        }

        if ($value instanceof Entry) {
            return Resources\EntryResource::class;
        }

        if ($value instanceof Collection) {
            return Resources\CollectionResource::class;
        }

        if ($value instanceof GlobalSet) {
            return Resources\GlobalSetResource::class;
        }

        if ($value instanceof Taxonomy) {
            return Resources\TaxonomyResource::class;
        }

        if ($value instanceof Term || $value instanceof LocalizedTerm) {
            return Resources\TermResource::class;
        }

        return Resources\Resource::class;
    }
}
