<?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Accounts extends Authenticatable
{
    protected $table = 'accounts';

    protected $fillable = [
        'firstname',  
        'lastname',   
        'email',
        'email_verified_at', 
        'password',   
        'otp',         
        'user_status',
        'created_at',
        'updated_at',
      
    ];

    public function incomes()
    {
        return $this->hasMany(Income::class, 'input_by');
    }

    public function expenses()
{
    return $this->hasMany(Expense::class, 'released_by');
}


}
