<?php

namespace App\Http\Requests\Traits;

trait WithFieldValidation
{
    // 預設可用欄位
    protected function getAvailableFields(): array
    {
        // 預設抓 model fillable
        if (method_exists($this, 'model')) {
            $model = $this->model();
            if (property_exists($model, 'fillable')) {
                return $model->getFillable();
            }
        }
        // fallback 空陣列，避免假設所有表都有特定欄位
        return [];
    }

    // 取得最終要 select 的欄位
    public function getSelectColumns(): array
    {
        $columns = $this->input('columns');
        if (is_array($columns) && count($columns) > 0) {
            // 過濾掉不合法的欄位
            return array_values(array_intersect($columns, $this->getAvailableFields()));
        }
        // 沒帶 columns 就回傳全部
        return $this->getAvailableFields();
    }

    // order_by 驗證
    protected function getOrderByRule(): array
    {
        return ['string', 'in:' . implode(',', $this->getAvailableFields())];
    }

    // columns 驗證
    protected function getColumnsRule(): array
    {
        return ['array'];
    }

    // columns.* 驗證
    protected function getColumnsEachRule(): array
    {
        return ['string', 'in:' . implode(',', $this->getAvailableFields())];
    }

    // filters.*.0 驗證（欄位名稱）
    protected function getFiltersFieldRule(): array
    {
        return ['string', 'in:' . implode(',', $this->getAvailableFields())];
    }

    // filters.*.1 驗證（操作符）
    protected function getFiltersOperatorRule(): array
    {
        return ['string', 'in:=,!=,>,<,>=,<=,like,not like,in,not in'];
    }

    // filters.*.2 驗證（值）
    protected function getFiltersValueRule(): array
    {
        return ['required', 'string'];
    }

    // 取得過濾器驗證規則
    protected function getFiltersRules(): array
    {
        return [
            'filters' => ['array'],
            'filters.*.0' => $this->getFiltersFieldRule(),
            'filters.*.1' => $this->getFiltersOperatorRule(),
            'filters.*.2' => $this->getFiltersValueRule(),
        ];
    }

    // 取得 INDEX 基本驗證規則
    protected function getIndexRules(): array
    {
        return [
            'per_page' => ['integer', 'min:1', 'max:100'],
            'page' => ['integer', 'min:1'],
            'order_by' => $this->getOrderByRule(),
            'order_direction' => ['string', 'in:asc,desc'],
            'columns' => $this->getColumnsRule(),
            'columns.*' => $this->getColumnsEachRule(),
            'with' => ['array'],
            'filters' => ['array'],
            'filters.*.0' => $this->getFiltersFieldRule(),
            'filters.*.1' => $this->getFiltersOperatorRule(),
            'filters.*.2' => $this->getFiltersValueRule(),
            'search' => ['string'],
        ];
    }

    // 取得 INDEX 基本錯誤訊息
    protected function getIndexMessages(): array
    {
        return [
            'per_page.min' => __('validation.pagination.per_page.min'),
            'per_page.max' => __('validation.pagination.per_page.max'),
            'page.min' => __('validation.pagination.page.min'),
            'order_direction.in' => __('validation.sorting.order_direction.in'),
            'columns.array' => __('validation.selection.columns.array'),
            'columns.*.in' => __('validation.selection.columns_each.in'),
            'with.array' => __('validation.selection.with.array'),
            'with.*.in' => __('validation.selection.with_each.in'),
            'filters.array' => __('validation.filtering.filters.array'),
            'filters.*.0.in' => __('validation.filtering.filters_field.in'),
            'filters.*.1.in' => __('validation.filtering.filters_operator.in'),
            'filters.*.2.required' => __('validation.filtering.filters_value.required'),
            'search.string' => __('validation.filtering.search.string'),
        ];
    }

    // 取得 INDEX 基本屬性
    protected function getIndexAttributes(): array
    {
        return [
            'per_page' => __('attributes.pagination.per_page'),
            'page' => __('attributes.pagination.page'),
            'order_by' => __('attributes.sorting.order_by'),
            'order_direction' => __('attributes.sorting.order_direction'),
            'columns' => __('attributes.selection.columns'),
            'with' => __('attributes.selection.with'),
            'filters' => __('attributes.filtering.filters'),
            'search' => __('attributes.filtering.search'),
        ];
    }

    // 根據模型動態取得欄位的輔助方法
    protected function getModelFields(string $modelClass): array
    {
        if (!class_exists($modelClass)) {
            return [];
        }

        $model = new $modelClass();

        // 優先使用 fillable 欄位
        if (property_exists($model, 'fillable') && !empty($model->getFillable())) {
            return $model->getFillable();
        }

        // 如果沒有 fillable，嘗試從資料庫結構取得
        if (method_exists($model, 'getConnection') && method_exists($model, 'getTable')) {
            try {
                $connection = $model->getConnection();
                $table = $model->getTable();
                $columns = $connection->getSchemaBuilder()->getColumnListing($table);
                return $columns ?: [];
            } catch (\Exception $e) {
                // 如果無法取得資料庫結構，回傳空陣列
                return [];
            }
        }

        return [];
    }
}
