<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Expense extends Model
{
    protected $table = 'expenses';

    protected $fillable = [
        'amount',  
        'released_by',
        'date',   
        'attachment',
        'project_id',
        'remarks',
        'status',
        'removed_by',
        'date_removed',
    ];

    // Encrypt amount before saving
    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = Crypt::encryptString($value);
    }

    // Decrypt amount when retrieving
    public function getAmountAttribute($value)
    {
        return Crypt::decryptString($value);
    }

    public function releasedBy()
    {
        return $this->belongsTo(Accounts::class, 'released_by');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}

