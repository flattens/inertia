<?php

namespace Flattens\Inertia\Concerns;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Statamic\Facades\Asset;
use Statamic\Facades\Entry as Query;

trait ResolvesInternalUrls
{
    protected $urlPrefix = 'statamic://';

    public function resolveInternalUrls($data)
    {
        if (is_string($data)) {
            if (!Str::contains($data, '::')) {
                return $data;
            }

            if (Str::startsWith($data, $this->urlPrefix)) {
                $data = Str::after($data, $this->urlPrefix);
            }

            [$type, $id] = explode('::', $data, 2);


            if ('entry' === $type) {
                return Query::find($id)->url();
            }

            if ('asset' === $type) {
                return Asset::find($id)->url();
            }

            return $data;
        }

        if (is_array($data)) {
            $collection = new Collection($data);
            return $this->resolveInternalUrls($collection);
        }

        if ($data instanceof Collection) {
            return $data->map(fn ($value) => $this->resolveInternalUrls($value))->toArray();
        }

        return $data;
    }
}
