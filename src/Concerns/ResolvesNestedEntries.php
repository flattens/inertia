<?php

namespace Flattens\Inertia\Concerns;

use JsonSerializable;
use Illuminate\Support\Collection;
use Statamic\Facades\Entry as Query;
use Illuminate\Contracts\Support\Arrayable;
use Flattens\Inertia\Contracts\Resourceable;

trait ResolvesNestedEntries
{
    protected $nestedEntries = [];

    public function resolveNestedEntries($data)
    {
        if (is_string($data)) {
            if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $data)) {
                $entry = Query::find($data);

                if ($entry instanceof Resourceable) {
                    $entry = $entry->toResource()->as('excerpt');
                }

                if ($entry instanceof JsonSerializable) {
                    $entry = $entry->jsonSerialize();
                }

                if ($entry instanceof Arrayable) {
                    $entry = $entry->toArray();
                }

                $this->nestedEntries[$data] = $entry;
            }
        }

        if (is_array($data)) {
            $collection = new Collection($data);
            $this->resolveNestedEntries($collection);
        }

        if ($data instanceof Collection) {
            $data->each(fn ($value) => $this->resolveNestedEntries($value));
        }
    }
}
