<?php 

namespace UdaraWeerasinghe\QueryFilter;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class QueryFilter
{
    protected Builder $builder;

    public function apply(Builder $builder, array $filters): Builder
    {
        $this->builder = $builder;

        foreach ($filters as $filter) {
            $this->applyCondition($filter);
        }

        return $this->builder;
    }

    protected function applyCondition(array $filter): void
    {
        $field = $filter['field'];
        $operator = strtolower($filter['operator']);
        $value = $filter['value'];
        $type = $filter['type'] ?? 'where';

        $method = $type === 'or' ? 'orWhere' : 'where';

        // whereDate
        if ($operator === 'date') {
            $this->builder->{$method . 'Date'}($field, $value);
            return;
        }

        // whereHas
        if ($operator === 'has') {
            $this->builder->{$method . 'Has'}($field, function ($q) use ($value) {
                foreach ($value as $sub) {
                    $q->where($sub['field'], $sub['operator'], $sub['value']);
                }
            });
            return;
        }

        // whereRelation or whereHas for dot notation
        if (str_contains($field, '.')) {
            $segments = explode('.', $field);
            $column = array_pop($segments);
            $relation = implode('.', $segments);

            $this->builder->{$method . 'Has'}($relation, function ($q) use ($column, $operator, $value) {
                $q->where($column, $operator, $operator === 'like' ? "%{$value}%" : $value);
            });

            return;
        }

        // Simple _id exact match or LIKE fallback
        if (Str::endsWith($field, '_id')) {
            $this->builder->{$method}($field, $value);
        } else {
            $this->builder->{$method}($field, $operator, $operator === 'like' ? "%{$value}%" : $value);
        }
    }
}
