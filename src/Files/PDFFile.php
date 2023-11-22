<?php

namespace IwslibLaravel\Files;

abstract class PDFFile extends TmpFile
{
    protected const DIR = ['pdf'];

    private string $appFileName = "";

    public function __construct(?string $id = null)
    {
        parent::__construct($id);
    }

    /**
     * @override
     */
    public function getFileExtension(): string
    {
        return "pdf";
    }

    /**
     * @override
     */
    public function getMimeType(): string
    {
        return "application/pdf";
    }

    /**
     * @override
     */
    public function getAppFileName()
    {
        return $this->appFileName;
    }

    public function setAppFileName(string $fileName)
    {
        $this->appFileName = $fileName;
        return $this;
    }
}
