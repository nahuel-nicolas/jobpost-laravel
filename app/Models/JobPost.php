<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class JobPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'company',
        'location',
        'website',
        'email',
        'description',
        'tags',
        'logo',
        'user_id'
    ];

    // Relationship with User
    public function user() {
        return $this->belongsTo(User::class);
    }

}
