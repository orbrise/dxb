<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AuctionSpotLost extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $spotNumber;
    public $cityName;
    public $gender;
    public $yourBid;
    public $winningBid;
    public $refundAmount;
    public $auctionsUrl;

    public function __construct(array $data)
    {
        $this->userName     = $data['userName'];
        $this->spotNumber   = $data['spotNumber'];
        $this->cityName     = $data['cityName'];
        $this->gender       = $data['gender'];
        $this->yourBid      = $data['yourBid'];
        $this->winningBid   = $data['winningBid'];
        $this->refundAmount = $data['refundAmount'];
        $this->auctionsUrl  = $data['auctionsUrl'];
    }

    public function build()
    {
        return $this->subject('Auction result — spot #' . $this->spotNumber . ' in ' . $this->cityName)
                    ->view('emails.auction-spot-lost');
    }
}
