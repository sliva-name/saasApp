<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $fillable = ['name', 'slug', 'preview_image', 'description'];

    public function stores()
    {
        return $this->hasMany(Store::class);
    }
}
