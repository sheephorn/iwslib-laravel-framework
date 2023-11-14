<?php

namespace App\Events\Email;

use IwslibLaravel\Email\BaseEmailer;
use IwslibLaravel\Models\Email;
use IwslibLaravel\Models\EmailAttachment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConfirmEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Email $email;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Email|BaseEmailer $email, ?Collection $attachments = null)
    {
        if ($email instanceof Email) {
            $this->email = $email;
        } else {
            $this->email = $email->makeModel();
            $this->email->save();
        }
        if ($attachments !== null) {
            foreach ($attachments as $attachment) {
                if (!($attachment instanceof EmailAttachment)) continue;

                $emailId = $this->email->id;
                $attachment->email_id = $emailId;
                $attachment->save();
            }
        }
    }
}
