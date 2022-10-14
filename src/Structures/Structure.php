<?php

namespace Flattens\Inertia\Structures;

use JsonSerializable;
use Statamic\Structures\Page;
use Statamic\Structures\Pages;
use Illuminate\Contracts\Support\Arrayable;

class Structure implements Arrayable, JsonSerializable
{
    protected $page;

    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    public static function collection(Pages $pages)
    {
        return $pages->all()->map(fn ($page) => new static($page));
    }

    public function entry()
    {
        return $this->page->entry()->toResource();
    }

    public function children()
    {
        return self::collection($this->page->pages());
    }

    public function toArray()
    {
        return array_filter([
            'title' => $this->page->title(),
            'url' => $this->page->url(),
            'children' => $this->children()->toArray(),
        ]);
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
