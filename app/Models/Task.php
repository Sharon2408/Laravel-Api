<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['complaint_id', 'lineman_id'];
    public function task()
    {
        return $this->belongsTo(Lineman::class);
    }
    public function getStatusAttribute($attribute)
    {
        return [
            5 => 'Not Assigned',
            2 => 'In Progress',
            3 => 'Completed',
            4 => 'Assigned'
        ][$attribute];
    }


}