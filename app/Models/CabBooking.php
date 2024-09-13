<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CabBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'bill_to',
        'booking_type',
        'cab_type',
        'from_location', 
        'to_location', 
        'departure_date', 
        'pickup_date', 
        'pickup_time', 
        'hours'
    ];
}
