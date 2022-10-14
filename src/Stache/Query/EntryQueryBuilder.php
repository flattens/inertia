<?php

namespace Flattens\Inertia\Stache\Query;

use Statamic\Contracts\Entries\QueryBuilder as Contract;
use Statamic\Stache\Query\EntryQueryBuilder as Statamic;

class EntryQueryBuilder extends Statamic implements Contract
{
    public function updateWheres($wheres)
    {
        $this->wheres = array_merge($this->wheres, $wheres);

        return $this;
    }
}
