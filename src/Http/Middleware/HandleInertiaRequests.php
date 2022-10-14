<?php

namespace Flattens\Inertia\Http\Middleware;

use Inertia\Middleware;
use Illuminate\Http\Request;
use Statamic\Contracts\Globals\GlobalRepository;
use Statamic\Contracts\Structures\NavigationRepository;
use Statamic\Facades\Site;

class HandleInertiaRequests extends Middleware
{
    protected $globals;

    protected $navigations;

    protected $rootView = 'app';

    public function __construct(GlobalRepository $globals, NavigationRepository $navigations)
    {
        $this->globals = $globals;
        $this->navigations = $navigations;
    }

    public function share(Request $request)
    {
        $sites = [
            'current' => Site::current()->toArray(),
            'all' => Site::all()->map->toArray()->values(),
        ];

        $navigations = $this->navigations->all()->keyBy->handle();

        $globals = $this->globals->all()->keyBy->handle()->all();

        return array_merge(
            parent::share($request),
            compact('globals', 'navigations', 'sites')
        );
    }
}
