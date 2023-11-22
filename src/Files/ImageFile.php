<?php

namespace IwslibLaravel\Files;

class ImageFile
{

    protected string $binary;

    protected string $mimetype;

    public function __construct(BaseFile $file)
    {
        $this->binary = $file->get();
        $this->mimetype = $file->getMimetype();
    }

    public function __toString()
    {
        return sprintf("data:%s;base64,%s", $this->mimetype, base64_encode($this->binary));
    }
}
