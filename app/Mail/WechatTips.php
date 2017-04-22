<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class WechatTips extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
	    return $this
		        ->from('18676756298@139.com')
		        ->subject('微信掉线')
		        ->view('email.wechat')
		        ->with([
		        	'time' => Carbon::now('Asia/Shanghai')->format('Y-m-d H:i:s')
		        ]);
		    ;
    }
}
