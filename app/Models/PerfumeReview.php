<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerfumeReview extends Model
{
    protected $fillable = ['user_id', 'perfume_id', 'rating', 'body', 'image_path'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function perfume(): BelongsTo { return $this->belongsTo(Perfume::class); }
}
