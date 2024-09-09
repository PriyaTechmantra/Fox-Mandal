<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class BookReturnedNotification extends Notification
{
    public $issueBook;

    public function __construct($issueBook)
    {
        $this->issueBook = $issueBook;
    }
    /** Specify the channels 
     * for notification*/
    public function via($notifiable)
    {
        return ['broadcast', 'database'];
    }
    /**Prepare the data 
     * for broadcasting */ 
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->notificationData());
    }
    /**  Prepare the data 
     * for storing 
     * in the database*/
    public function toDatabase($notifiable)
    {
        return new DatabaseMessage($this->notificationData());
    }
    /**Common data structure
     *  for both broadcast 
     * and database notifications */
 
    protected function notificationData()
    {
        return [
            'book_id' => $this->issueBook->book_id,
            'user_id' => $this->issueBook->user_id,
            'return_date' => $this->issueBook->return_date,
            'message' => 'A book has been returned.',
        ];
    }
    
}
