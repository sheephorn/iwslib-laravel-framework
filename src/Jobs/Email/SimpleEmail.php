<?php

namespace IwslibLaravel\Jobs\Email;

use IwslibLaravel\Codes\QueueName;
use IwslibLaravel\Exceptions\TempFileNotExistsException;
use IwslibLaravel\Jobs\BaseJob;
use IwslibLaravel\Models\Email;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use IwslibLaravel\Email\TextEmail;
use IwslibLaravel\Util\DateUtil;

class SimpleEmail extends BaseJob
{
    private string $emailId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Email $email)
    {
        $this->emailId = $email->id;
        $this->onQueue(QueueName::EMAIL->value);
    }

    protected function handleJob()
    {
        try {
            $this->send();
        } catch (TempFileNotExistsException $e) {
            // 一時ファイルが存在しないため、処理終了
            Log::warning(sprintf("ファイル存在しないため、メール送信処理スキップ :%s", $e->getFilepath()));
        }
    }

    public function send()
    {
        $email =  Email::findOrFail($this->emailId);

        info("メール送信", [
            'id' => $email->id,
            'email' => $email->email,
            'mailer' => $email->type,
        ]);

        try {
            Mail::to($email->email)
                ->send(new TextEmail($email->subject, $email->content, $email->emailAttachments));
        } catch (Exception $e) {
            Log::error("メール送信失敗", [
                'id' => $email->id,
                'email' => $email->email,
                'mailer' => $email->type,
            ]);
            $email->is_failed = true;
            $email->save();
            throw $e;
        }


        $email->send_datetime = DateUtil::now();
        $email->save();
    }
}
