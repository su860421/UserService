<?php

namespace Database\Factories;

use App\Models\Organizations;
use App\Models\User;
use App\Enums\OrganizationStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organizations>
 */
class OrganizationsFactory extends Factory
{
    protected $model = Organizations::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'type' => fake()->randomElement(['department', 'team', 'division', 'branch']),
            'parent_id' => null,
            'manager_user_id' => null,
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->companyEmail(),
            'monthly_budget' => fake()->randomFloat(2, 10000, 1000000),
            'approval_settings' => [
                'require_manager_approval' => true,
                'max_approval_amount' => 50000,
                'auto_approve_below' => 1000,
            ],
            'settings' => [
                'allow_overtime' => true,
                'flexible_hours' => false,
                'remote_work_allowed' => true,
            ],
            'cost_center_code' => 'CC' . str_pad(fake()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT),
            'status' => OrganizationStatus::ACTIVE,
        ];
    }

    /**
     * Indicate that the organization is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => OrganizationStatus::INACTIVE,
        ]);
    }

    /**
     * Indicate that the organization is active.
     */
    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => OrganizationStatus::ACTIVE,
        ]);
    }

    /**
     * Create an organization with a specific type.
     */
    public function withType(string $type): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => $type,
        ]);
    }

    /**
     * Create an organization with a manager.
     */
    public function withManager(): static
    {
        return $this->state(fn(array $attributes) => [
            'manager_user_id' => User::factory(),
        ]);
    }

    /**
     * Create a child organization.
     */
    public function asChild(Organizations $parent): static
    {
        return $this->state(fn(array $attributes) => [
            'parent_id' => $parent->id,
        ]);
    }
}
