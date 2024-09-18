<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelBookingGuest extends Model
{
    protected $table = 'hotel_booking_guests';

    protected $fillable = [
        'hotel_booking_id', 'guest_name', 'guest_email', 'guest_contact', 'guest_country', 'guest_city', 'guest_state', 'guest_pincode'
    ];

    public function hotelBooking()
    {
        return $this->belongsTo(HotelBooking::class, 'hotel_booking_id');
    }
}
