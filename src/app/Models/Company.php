<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'hope_level',
        'tags',
        'contact_person',
        'email',
        'phone',
        'website_url',
        'memo',
    ];

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function interactions()
    {
        return $this->hasMany(Interaction::class);
    }

}
