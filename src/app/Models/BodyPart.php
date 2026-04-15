<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BodyPart extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'order'];

    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class);
    }
}
