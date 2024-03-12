<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Author extends Model
{
    use HasFactory, SoftDeletes;

    /** @var array<string> */
    protected $fillable = [
        'first_name',
        'last_name',
        'place_of_birth',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function fullName(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
