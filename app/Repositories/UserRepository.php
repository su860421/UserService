<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\UserRepositoryInterface;
use App\Models\User;
use JoeSu\LaravelScaffold\BaseRepository;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * 應用過濾器到查詢
     */
    protected function applyFilters($query, array $filters)
    {
        foreach ($filters as $filter) {
            if (count($filter) >= 3) {
                $field = $filter[0];
                $operator = $filter[1];
                $value = $filter[2];

                switch ($operator) {
                    case '=':
                        $query->where($field, $value);
                        break;
                    case '!=':
                        $query->where($field, '!=', $value);
                        break;
                    case '>':
                        $query->where($field, '>', $value);
                        break;
                    case '<':
                        $query->where($field, '<', $value);
                        break;
                    case '>=':
                        $query->where($field, '>=', $value);
                        break;
                    case '<=':
                        $query->where($field, '<=', $value);
                        break;
                    case 'like':
                        $query->where($field, 'LIKE', '%' . $value . '%');
                        break;
                    case 'not like':
                        $query->where($field, 'NOT LIKE', '%' . $value . '%');
                        break;
                    case 'in':
                        $values = is_array($value) ? $value : explode(',', $value);
                        $query->whereIn($field, $values);
                        break;
                    case 'not in':
                        $values = is_array($value) ? $value : explode(',', $value);
                        $query->whereNotIn($field, $values);
                        break;
                }
            }
        }

        return $query;
    }

    // Add custom methods here
}
