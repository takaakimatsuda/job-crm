<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'interaction_date',
        'type',
        'memo',
        'summary',
    ];

    protected $casts = [
        'interaction_date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
