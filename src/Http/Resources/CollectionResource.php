<?php

namespace Flattens\Inertia\Http\Resources;

use Statamic\Facades\Site;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Flattens\Inertia\Structures\Structure;

class CollectionResource extends Resource
{
    protected $paginator;

    protected $structure;

    public function attributes($attrs = [])
    {
        $attrs['pagination'] = $this->pagination();

        if ($this->value->hasStructure()) {
            $attrs['structure'] = $this->structure();
        }


        return parent::attributes($attrs);
    }

    public function properties($props = [])
    {
        $props = $this->toArray();

        return parent::properties($props);
    }

    public function structure()
    {
        if (!$this->structure) {
            $tree = $this->value->structure()->trees()->get(
                Site::current()->handle()
            );

            $this->structure = Structure::collection($tree->pages());
        }

        return $this->structure;
    }

    public function taxonomies(array $handles = null)
    {
        $taxonomies = $this->value->taxonomies();

        if ($handles) {
            $taxonomies = $taxonomies->filter(
                fn ($taxonomy) => in_array($taxonomy->handle(), $handles)
            )->values();
        }

        return $taxonomies->map->toResource();
    }

    public function taxonomy($handle)
    {
        return $this->taxonomies([$handle])->first();
    }

    public function entries($args = [], $wheres = null)
    {
        $query = $this->queryEntries($wheres ?? [
            [
                "type" => "Basic",
                "column" => "locale",
                "value" => Site::current()->handle(),
                "operator" => "=",
                "boolean" => "and",
            ]
        ], $args);

        $entries = ($perPage = Arr::get($args, 'perPage'))
            ? tap($query->paginate($perPage), fn ($paginator) => $this->paginator = $paginator->withQueryString())->items()
            : $query->get()->all();

        return Collection::make($entries)->map->toResource()->values();
    }

    public function toArray()
    {
        return $this->value->toArray();
    }

    protected function pagination()
    {
        if ($this->paginator) {
            $queryParams = Arr::except(
                $this->paginator->resolveQueryString(),
                $this->paginator->getPageName()
            );

            return [
                'query' => $queryParams,
                'name' => $this->paginator->getPageName(),
                'total' => $this->paginator->total(),
                'lastPage' => $this->paginator->lastPage(),
                'currentPage' => $this->paginator->currentPage(),
            ];
        }
    }

    protected function queryEntries($wheres = [], $args = [])
    {
        $query = $this->value->queryEntries()
            ->updateWheres($wheres);

        if (Arr::has($args, 'order')) {
            [$column, $direction] = explode('::', Arr::get($args, 'order'));
            $query = $query->orderBy($column, $direction ?? 'asc');
        }

        if ($this->value->dated() && !Arr::has($args, 'order')) {
            $query = $query->orderBy('date', $this->value->sortDirection());
        }

        if (Arr::has($args, 'limit')) {
            $query = $query->limit($args['limit']);
        }

        return $query;
    }
}
