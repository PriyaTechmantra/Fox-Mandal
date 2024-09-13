<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CabBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'booking_type', 
        'from_location', 
        'to_location', 
        'departure_date', 
        'pickup_date', 
        'pickup_time', 
        'hours'
    ];
}
