<?php

return [
    'auth' => [
        'name' => 'Name',
        'email' => 'Email',
        'password' => 'Password',
        'current_password' => 'Current Password',
        'phone' => 'Phone',
        'employee_id' => 'Employee ID',
        'token' => 'Password Reset Token',
    ],
    'user' => [
        'name' => 'Name',
        'email' => 'Email',
        'password' => 'Password',
        'password_confirmation' => 'Password Confirmation',
        'phone' => 'Phone',
        'employee_id' => 'Employee ID',
        'is_active' => 'Active Status',
        'last_login_at' => 'Last Login Time',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
        'deleted_at' => 'Deleted At',
    ],
    'pagination' => [
        'per_page' => 'Items per page',
        'page' => 'Page number',
    ],
    'sorting' => [
        'order_by' => 'Sort field',
        'order_direction' => 'Sort direction',
    ],
    'filtering' => [
        'filters' => 'Filter conditions',
        'search' => 'Search keyword',
    ],
    'selection' => [
        'columns' => 'Column selection',
        'with' => 'Relationship loading',
    ],
];
