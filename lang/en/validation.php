<?php

return [
    'auth' => [
        'name' => [
            'required' => 'Name is required',
            'max' => 'Name cannot exceed 255 characters',
        ],
        'email' => [
            'required' => 'Email is required',
            'email' => 'Please enter a valid email address',
            'unique' => 'This email address is already in use',
            'exists' => 'User not found with this email address',
        ],
        'password' => [
            'required' => 'Password is required',
            'min' => 'Password must be at least 8 characters',
            'confirmed' => 'Password confirmation does not match',
        ],
        'current_password' => [
            'required' => 'Current password is required',
        ],
        'phone' => [
            'max' => 'Phone number cannot exceed 20 characters',
        ],
        'employee_id' => [
            'unique' => 'This employee ID is already in use',
        ],
        'token' => [
            'required' => 'Password reset token is required',
        ],
    ],
    'user' => [
        'name' => [
            'required' => 'Name is required',
            'max' => 'Name cannot exceed 255 characters',
        ],
        'email' => [
            'required' => 'Email is required',
            'email' => 'Please enter a valid email format',
            'unique' => 'This email is already in use',
        ],
        'password' => [
            'required' => 'Password is required',
            'confirmed' => 'Password confirmation does not match',
        ],
        'phone' => [
            'max' => 'Phone number cannot exceed 20 characters',
        ],
        'employee_id' => [
            'max' => 'Employee ID cannot exceed 50 characters',
            'unique' => 'This employee ID is already in use',
        ],
        'is_active' => [
            'boolean' => 'Active status must be a boolean value',
        ],
    ],
    'pagination' => [
        'per_page' => [
            'min' => 'Items per page must be at least 1',
            'max' => 'Items per page cannot exceed 100',
        ],
        'page' => [
            'min' => 'Page number must be at least 1',
        ],
    ],
    'sorting' => [
        'order_by' => [
            'in' => 'Sort field is incorrect',
        ],
        'order_direction' => [
            'in' => 'Sort direction must be asc or desc',
        ],
    ],
    'filtering' => [
        'filters' => [
            'array' => 'Filter conditions must be in array format',
        ],
        'filters_field' => [
            'in' => 'Specified filter field does not exist',
        ],
        'filters_operator' => [
            'in' => 'Specified filter operator is incorrect',
        ],
        'filters_value' => [
            'required' => 'Filter value is required',
        ],
        'search' => [
            'string' => 'Search keyword must be a string',
        ],
    ],
    'selection' => [
        'columns' => [
            'array' => 'Column selection must be in array format',
        ],
        'columns_each' => [
            'in' => 'Specified column does not exist',
        ],
        'with' => [
            'array' => 'Relationship loading must be in array format',
        ],
        'with_each' => [
            'in' => 'Specified relationship does not exist',
        ],
    ],
    'auth.password_reset' => [
        'invalid_token' => 'Password reset link is invalid or expired',
    ],
];
