<?php

namespace App\Models;

use App\Policies\TaskPolicy;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $category_id
 * @property string $name
 * @property string $description
 * @property int $status
 * @property Carbon $finished_at
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @see Task::category()
 * @property-read Category $category
 * @see Task::user()
 * @property-read User $user
 */
class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'description',
        'status',
        'finished_at',
    ];

    protected $dates = ['finished_at'];

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status ? 'DONE' : 'IN_PROGRESS';
    }

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
