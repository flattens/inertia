<?php

namespace Flattens\Inertia\Concerns;

use InvalidArgumentException;
use Statamic\Contracts\Entries\Entry;

trait ResolvesStructuredData
{
    public function getParent(Entry $entry = null)
    {
        return $this->resolveStructure($entry)->parent();
    }

    public function getChildren(Entry $entry = null)
    {
        $children = $this->resolveStructure($entry)->pages();

        if ($children->all()->count()) {
            return $children;
        }
    }

    protected function resolveStructure(Entry $entry = null)
    {
        return $this->resolveOwnedEntry($entry)->page();
    }

    protected function resolveOwnedEntry(Entry $entry = null)
    {
        if ($entry) {
            return $entry;
        }

        if ($this instanceof Entry) {
            return $this;
        }

        throw new InvalidArgumentException(sprintf('The resolvable class "%s" must be type of %s.', get_class($this), Entry::class));
    }
}
