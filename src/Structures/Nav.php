<?php

namespace Flattens\Inertia\Structures;

use JsonSerializable;
use Statamic\Facades\Entry;
use Statamic\Structures\Nav as Statamic;
use Illuminate\Contracts\Support\Arrayable;
use Statamic\Contracts\Structures\Nav as Contract;
use Statamic\Facades\Site;

class Nav extends Statamic implements Contract, Arrayable, JsonSerializable
{
    protected $site;

    public function forSite($site)
    {
        $this->site = $site;

        return $this;
    }

    public function toArray()
    {
        return $this->transformTree(
            $this->in($this->site ?? Site::current())->tree()
        );
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    protected function transformTree($tree)
    {
        return array_map(function ($item) {
            if (array_key_exists('entry', $item)) {
                $entry = Entry::find($item['entry']);

                $item['title'] = $item['title'] ?? $entry->title;
                $item['url'] = $entry->url;
            }

            if (array_key_exists('children', $item)) {
                $item['children'] = $this->transformTree($item['children']);
            }

            return $item;
        }, $tree);
    }
}
