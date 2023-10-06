<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'consumer_id', 'issue_details', 'landmark'];

    public function complaint()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusAttribute($attribute){
        return [
            5 => 'Not Assigned',
            2 => 'In Progress',
            3 => 'Completed',
            4 => 'Assigned'
        ][$attribute];
    }
}