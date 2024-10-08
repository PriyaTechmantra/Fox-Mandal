<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueBook extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'user_id',
        'request_date',
        'status',
        'approve_date',
        'is_transfer',
        'user_id_to_transfer',
        'transfer_date',
        'transfer_approve_status',
        'transfer_approve_date',
        'book_holder_user_id',
        'user_id2',
        'name_of_issue_person',
        'is_return',
        'return_date'
    ];
    
    
    public function user()
    {
         return $this->belongsTo(User::class);
    }
    
   
    public function book()
    {
         return $this->belongsTo(Book::class);
    }
}
