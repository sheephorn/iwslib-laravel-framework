<?php

namespace App\Files\PDF;


class Receipt extends PDFFile
{

    protected const DIR = [
        ...parent::DIR,
        'receipt'
    ];

    /**
     * @override
     */
    protected function getFileTypeName(): string
    {
        return "ReceiptPDF";
    }
}
