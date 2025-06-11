<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeleteAccountCode extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isValid()
    {
        return $this->expires_at->isFuture();
    }
} 