<?php

namespace Flattens\Inertia\Contracts;

interface Resourceable
{
    public function resourceName();

    public function toResource();
}
