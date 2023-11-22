<?php

namespace IwslibLaravel\Email;

use Illuminate\Database\Eloquent\Collection;

class TextEmail extends BaseEmailer
{

    private string $__subject;
    private string $__contents;

    public function __construct(string $subject, string $contents, ?Collection $attachments = null)
    {
        $this->__subject = $subject;
        $this->__contents = $contents;
        $this->attachments = $attachments;
    }

    public function getTemplateName(): string
    {
        return 'iwsliblaravel-emails::free_text';
    }

    public function getSubject(): string
    {
        return $this->__subject;
    }

    public function getParams(): array
    {
        return [
            'contents' => $this->__contents,
        ];
    }
}
