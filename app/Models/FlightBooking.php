<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',        
        'trip_type',      
        'from',            
        'to',             
        'departure_date', 
        'return_date',     
        'traveler_number',  
        'bill_to',  
        'preference_arrival_time',  
        'preference_departure_date'    
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
