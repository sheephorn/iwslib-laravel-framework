<?php

namespace IwslibLaravel\Files;

use IwslibLaravel\Jobs\File\DeleteFile;
use IwslibLaravel\Util\DateUtil;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TmpFile
{
    final protected const BASE_DIR = "/tmp";

    protected const DIR = [];


    /**
     * @param string $id
     * @return static
     * @throws  FileNotFoundException
     */
    static public function loadFile(string $id, ...$other): static
    {
        $file = new static($id, $other);

        if (!$file->exists()) {
            throw new FileNotFoundException("ファイルが存在しません:" . $file->getFullPath());
        }
        return $file;
    }



    protected string $uuid;

    protected string $content;


    public function __construct(?string $id = null)
    {
        if ($id === null) {
            $this->uuid = Str::uuid();
        } else {
            $this->uuid = $id;
        }
    }

    public function __destruct()
    {
        // 消し忘れ防止のため、削除を予約しておく
        if ($this->exists()) {
            $lifeTimeMin = config("filesystems.tmpFile.lifetime", 60);
            $this->delete(DateUtil::now()->addMinutes($lifeTimeMin));
        }
    }

    protected function getFileTypeName()
    {
        return "tmp";
    }

    public function getFileExtension(): string
    {
        return "tmp";
    }

    public function getMimeType(): string
    {
        return "txt/plain";
    }

    final public function getFileName(): string
    {
        return sprintf("%s_%s.%s", $this->getFileTypeName(), $this->uuid, $this->getFileExtension());
    }

    public function getAppFileName()
    {
        return $this->getFileName();
    }

    public function getId(): string
    {
        return $this->uuid;
    }

    public function getPath()
    {
        return implode(
            "/",
            [
                self::BASE_DIR,
                ...static::DIR
            ]
        ) . "/" . $this->getFileName();
    }

    public function getFullPath()
    {
        return Storage::path($this->getPath());
    }

    public function put(string $content)
    {
        Storage::put($this->getPath(), $content);
        $this->content = $content;
        return $this;
    }

    public function load()
    {
        $this->content =  Storage::get($this->getPath());
        return $this;
    }

    public function append(string $content)
    {
        Storage::append($this->getPath(), $content);
        $this->content .= $content;
        return $this;
    }
    public function get(): string
    {
        return $this->content;
    }

    public function download(string $name = "download")
    {
        return response()->download($this->getFullPath(), $name)->deleteFileAfterSend();
    }

    public function exists()
    {
        return  Storage::exists($this->getPath());
    }

    public function delete(?Carbon $delay = null): void
    {
        if ($delay === null) {
            $ret = Storage::delete($this->getPath());
            if ($ret) info(sprintf("ファイル削除:%s ", $this->getFullPath()));
            return;
        } else {
            $this->content = "";
            DeleteFile::dispatch($this)
                ->delay($delay);
            return;
        }
    }
}
