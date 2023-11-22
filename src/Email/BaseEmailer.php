<?php

namespace IwslibLaravel\Email;

use IwslibLaravel\Exceptions\AppCommonException;
use IwslibLaravel\Exceptions\TempFileNotExistsException;
use IwslibLaravel\Models\Email;
use IwslibLaravel\Models\EmailAttachment;
use IwslibLaravel\Models\User;
use IwslibLaravel\Util\UrlUtil;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use IwslibLaravel\Jobs\Email\SimpleEmail;

abstract class BaseEmailer extends Mailable
{
    use Queueable, SerializesModels;

    protected string $emailAddress = "";
    protected string $targetKeyType = "";
    protected string $targetKey = "";

    /**
     * 添付ファイル
     * @var Collection<int, EmailAttachment>|null
     */
    protected ?Collection $tmpAttachments = null;

    public function __construct()
    {
        $this->tmpAttachments = collect();
    }

    public function setEmail(string $target)
    {
        $this->emailAddress = $target;
        return $this;
    }

    public function setTargetKey(string $type, string $key)
    {
        $this->targetKeyType = $type;
        $this->targetKey = $key;
        return $this;
    }

    public function setUser(User $user)
    {
        return $this->setTargetKey($user::class, $user->id)->setEmail($user->email);
    }

    public function attachFile(string $filepath, string $filename, string $mimeType)
    {

        if ($this->tmpAttachments === null) {
            $this->tmpAttachments = collect();
        }

        $attachment = new EmailAttachment();
        $attachment->filepath = $filepath;
        $attachment->send_filename = $filename;
        $attachment->mime = $mimeType;

        $this->tmpAttachments->push($attachment);

        return $this;
    }

    public function save()
    {
        $model = $this->makeModel();
        $model->save();
        foreach ($this->tmpAttachments ?? [] as $attachment) {
            $attachment->email_id = $model->id;
            $attachment->save();
        }
        return $model;
    }

    public function confirm()
    {
        $model = $this->save();
        SimpleEmail::dispatch($model);
    }

    public function build()
    {
        $this->text($this->getTemplateName())
            ->subject($this->getSubject())
            ->with($this->getParams());

        // 添付ファイル処理
        foreach ($this->tmpAttachments ?? [] as $attachment) {
            $filepath = $attachment->filepath;

            if (!file_exists($filepath)) {
                $e = new TempFileNotExistsException();
                throw $e->setFilepath($filepath);
            }

            $as = $attachment->send_filename;
            $mime = $attachment->mime;
            $this->attach($filepath, [
                'as' => $as,
                'mime' => $mime,
            ]);
        }

        return $this;
    }

    public function makeModel(): Email
    {
        if (!$this->emailAddress) {
            throw new AppCommonException("Email宛先不明");
        }

        $model = new Email();
        $model->setId();
        $model->subject = $this->getSubject();
        $model->content = $this->render();
        $model->type = get_class($this);
        $model->email = $this->emailAddress;
        $model->target_key_type = $this->targetKeyType;
        $model->target_key = $this->targetKey;
        return $model;
    }

    abstract public function getTemplateName(): string;

    abstract public function getSubject(): string;

    abstract public function getParams(): array;


    /**
     * 画面のＵＲＬを生成する
     *
     * @param array|string $path
     * @return string
     */
    protected function getAppUrl(array|string $path, array $query = []): string
    {
        return UrlUtil::getAppUrl($path, $query);
    }
}
