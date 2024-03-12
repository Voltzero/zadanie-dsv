<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    /** @var array<string> */
    protected $fillable = [
        'title',
        'price',
        'author_id',
        'type',
        'details',
    ];

    /** @var array */
    protected $casts = [
        'details' => 'array',
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' => 'date:Y-m-d H:i:s',
        'deleted_at' => 'date:Y-m-d H:i:s',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function description(): string
    {
        $key = key($this->details);
        $details = $key . ': ' . $this->details[$key];

        return "title: {$this->title}, type: {$this->type}, price: {$this->price}, author: {$this->author->fullName()}, {$this->author->place_of_birth}, {$details}";
    }
}
