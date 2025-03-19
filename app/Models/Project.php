<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'projects';

    protected $fillable = [
        'name',  
        'coordinator',   
        'status',
        'previous_status',
        'remarks', 
        'start_date',   
        'end_date',
        'removed_by',
        'date_removed',         
      
    ];

    public function incomes()
    {
        return $this->hasMany(Income::class, 'project_id');
    }

    public function expenses()
{
    return $this->hasMany(Expense::class, 'project_id');
}

}
