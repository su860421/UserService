<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use App\Enums\OrganizationStatus;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Organizations extends Model
{
    use HasFactory, SoftDeletes, HasUlids;

    protected $table = 'organizations';

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
        'status' => OrganizationStatus::class,
    ];

    // Define relationship methods here
    // Examples:
    // public function posts(): HasMany
    // {
    //     return $this->hasMany(Post::class);
    // }

    /** @return BelongsTo<Organizations,Organizations> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /** @return HasMany<Organizations> */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * 遞迴載入全部子組織
     * @return HasMany<Organizations>
     */
    public function childrenRecursive(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->with(['childrenRecursive:id,name,type,parent_id,status'])
            ->select(['id', 'name', 'type', 'parent_id', 'status']);
    }

    /** @return BelongsTo<User,Organizations> */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }

    /** @return BelongsToMany<User> */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'organization_user', 'organization_id', 'user_id');
    }
}
