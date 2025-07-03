<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use App\Enums\OrganiztionStatus;

class Organiztions extends Model
{
    use HasFactory, SoftDeletes, HasUlids;

    protected $table = 'organiztions';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'type',
        'parent_id',
        'manager_user_id',
        'address',
        'phone',
        'email',
        'monthly_budget',
        'approval_settings',
        'settings',
        'cost_center_code',
        'status',
    ];

    protected $casts = [
        'approval_settings' => 'array',
        'settings' => 'array',
        'monthly_budget' => 'decimal:2',
        'deleted_at' => 'datetime',
        'status' => OrganiztionStatus::class,
    ];

    // Define relationship methods here
    // Examples:
    // public function posts(): HasMany
    // {
    //     return $this->hasMany(Post::class);
    // }

    /** @return BelongsTo<Organiztions,Organiztions> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /** @return HasMany<Organiztions> */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /** @return BelongsTo<User,Organiztions> */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }
}
