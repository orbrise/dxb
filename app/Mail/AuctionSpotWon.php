<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AuctionSpotWon extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $profileName;
    public $spotNumber;
    public $cityName;
    public $gender;
    public $winningBid;
    public $durationDays;
    public $spotExpiryDate;
    public $profileUrl;

    public function __construct(array $data)
    {
        $this->userName       = $data['userName'];
        $this->profileName    = $data['profileName'];
        $this->spotNumber     = $data['spotNumber'];
        $this->cityName       = $data['cityName'];
        $this->gender         = $data['gender'];
        $this->winningBid     = $data['winningBid'];
        $this->durationDays   = $data['durationDays'];
        $this->spotExpiryDate = $data['spotExpiryDate'];
        $this->profileUrl     = $data['profileUrl'];
    }

    public function build()
    {
        return $this->subject('Congratulations — you won spot #' . $this->spotNumber . ' in ' . $this->cityName)
                    ->view('emails.auction-spot-won');
    }
}
