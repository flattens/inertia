<?php

namespace Flattens\Inertia\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Statamic\Facades\Asset;
use Illuminate\Support\Collection;
use Flattens\Inertia\Assets\Asset as Resource;

trait ResolvesAssets
{
    protected $assets = [];

    protected $manipulations = [];

    protected $urlPrefix = 'statamic://';

    public function storeAssetResource($asset, $manipulations = null)
    {
        $manipulations = $manipulations ?? $this->manipulations;

        return tap(
            crc32($asset->path()),
            fn ($key) => $this->assets[$key] = new Resource($asset, $manipulations)
        );
    }

    public function assetFromReference($reference)
    {
        [$type, $id] = explode('::', $reference, 2);

        return Asset::find($id);
    }

    public function resolveAssets($data)
    {
        if (is_string($data)) {
            $data = preg_replace_callback('/(?<markdown>!\[[^\]]*\]\((?<path>.*?)(?=\"|\))\))/im', function ($matches) {
                $asset = $this->assetFromReference(Str::after($matches['path'], $this->urlPrefix));
                return str_replace($matches['path'], $asset->url(), $matches['markdown']);
            }, $data);

            if (preg_match('/asset::[^::]+::.+/', $data)) {
                return $this->assetFromReference($data)->url();
            }

            return $data;
        }

        if (is_array($data)) {
            if (array_key_exists('type', $data) && 'image' === $data['type']) {
                $asset = preg_match('/asset::[^::]+::.+/', Arr::get($data, 'attrs.src'))
                    ? $this->assetFromReference(Arr::get($data, 'attrs.src'))
                    : Asset::find(Arr::get($data, 'attrs.src'));
                Arr::set($data, 'attrs.id', $this->storeAssetResource($asset));
                Arr::set($data, 'attrs.src', $asset->url());
            }

            $collection = new Collection($data);
            return $this->resolveAssets($collection);
        }

        if ($data instanceof Collection) {
            return $data->map(fn ($value) => $this->resolveAssets($value))->toArray();
        }

        return $data;
    }

    public function resolveAssetsFromPaths(array $paths, $manipulations = [])
    {
        $paths = Collection::make($paths);

        return Asset::all()
            ->filter(fn ($asset) => $paths->flatten()->contains($asset->path()))
            ->map(fn ($asset) => [
                'id' => $this->storeAssetResource($asset, $manipulations),
                'src' => $asset->url(),
            ]);
    }
}
