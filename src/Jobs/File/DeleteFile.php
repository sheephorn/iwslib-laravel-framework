<?php

namespace App\Jobs\File;

use App\Codes\QueueName;
use App\Files\TmpFile;
use App\Jobs\BaseJob;
use Illuminate\Support\Facades\Storage;

class DeleteFile extends BaseJob
{

    private string $fileId;
    private string $storagePath;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        private TmpFile $file,
    ) {
        $this->onQueue(QueueName::JOB->value);
        $this->fileId = $file->getId();
        $this->storagePath = $file->getPath();
        logger("FILE削除JOB登録:" . $this->storagePath);
    }

    protected function handleJob()
    {
        if (Storage::exists($this->storagePath)) {
            Storage::delete($this->storagePath);
            info(sprintf("ファイル削除:%s ", $this->storagePath));
        }
    }
}
