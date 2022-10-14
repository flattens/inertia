<?php

namespace Flattens\Inertia\Http\Resources;

use Flattens\Inertia\Globals\GlobalSet;

class GlobalSetResource extends Resource
{
    protected $variables;

    public function __construct(GlobalSet $value)
    {
        $this->variables = $value->inCurrentSite();

        parent::__construct($value);
    }

    public function attributes($attrs = [])
    {
        return parent::attributes($attrs);
    }

    public function properties($props = [])
    {
        $props = $this->toArray();

        return parent::properties($props);
    }

    public function get($key, $default = null)
    {
        if (! $this->variables) {
            return $default;
        }

        return $this->variables->get($key, $default);
    }

    public function toArray()
    {
        return $this->variables ? $this->variables->fileData() : [];
    }
}
