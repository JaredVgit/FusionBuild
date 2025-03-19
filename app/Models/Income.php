<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Income extends Model
{
    protected $table = 'income';

    protected $fillable = [
        'amount',  
        'input_by',
        'date',   
        'mode_of_payment',
        'project_id',
        'remarks',
        'status',
        'removed_by',
        'date_removed',
    ];

    // Automatically encrypt the amount when setting it
    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = Crypt::encryptString($value);
    }

    // Automatically decrypt the amount when getting it
    public function getAmountAttribute($value)
    {
        return Crypt::decryptString($value);
    }

    public function inputBy()
    {
        return $this->belongsTo(Accounts::class, 'input_by');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}

