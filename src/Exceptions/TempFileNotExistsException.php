<?php

namespace IwslibLaravel\Exceptions;

use Exception;

class TempFileNotExistsException extends Exception
{
    private $filepath = "";

    public function setFilepath(string $filepath): static
    {
        $this->filepath = $filepath;
        return $this;
    }

    public function getFilepath(): string
    {
        return $this->filepath;
    }
}
