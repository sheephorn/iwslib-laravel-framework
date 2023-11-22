<?php

namespace IwslibLaravel\Files;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use IwslibLaravel\Util\DateUtil;

abstract class BaseFile
{

    protected UploadedFile|null $file = null;

    private bool $commit = false;

    protected Carbon|null $updatedAt = null;

    /**
     * ディレクトリのパスを取得
     *
     * @return string
     */
    abstract public function getDir(): string;

    /**
     * ファイル名の取得
     *
     * @return string
     */
    abstract public function getFilename(): string;

    /**
     * MIMETYPEの取得
     *
     * @return string
     */
    abstract public function getMimetype(): string;

    /**
     * DBの登録などを定義
     *
     * @return boolean
     */
    abstract protected function onUpload(Carbon $timestamp): bool;


    /**
     * コミットする
     *
     * @param array<BaseFile>|Collection<BaseFile>|BaseFile $files
     * @return void
     */
    public static function commitAll(array|Collection|BaseFile $files)
    {
        if (is_array($files) || $files instanceof Collection) {
            foreach ($files as $file) {
                $file->commit();
            }
        } else {
            $files->commit();
        }
    }


    public function __construct(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * 変更後、コミットしていない場合は削除する
     */
    public function __destruct()
    {
        if (!$this->commit && $this->updatedAt !== null) {
            $this->delete();
        }
    }

    /**
     * コミット
     *
     * @param boolean $commit
     * @return void
     */
    public function commit($commit = true)
    {
        $this->commit = $commit;
    }

    /**
     * ファイルパスを取得する disk.rootからの相対パス
     *
     * @return string
     */
    public function getFilepath(): string
    {
        return $this->getDir() . "/" . $this->getFilename();
    }

    /**
     * ファイル取得
     *
     * @return string|bool
     */
    public function get(): string|bool
    {
        if ($this->exists()) {
            return Crypt::decryptString(Storage::get($this->getFilepath()));
        }
        return false;
    }

    /**
     * ファイルの存在確認
     *
     * @return boolean
     */
    public function exists(): bool
    {
        return Storage::exists($this->getFilepath());
    }

    /**
     * ファイル削除
     *
     * @return boolean 成功可否
     */
    public function delete(): bool
    {
        if ($this->exists()) {
            return Storage::delete($this->getFilepath());
        }
        return true;
    }

    /**
     * アップロードファイルの保存
     *
     * @param UploadedFile $file
     * @param Carbon|null|null $updatedAt
     * @return boolean
     */
    public function store(Carbon|null $timestamp = null): bool
    {

        if ($this->file === null) return false;

        $this->updatedAt = $timestamp ?? DateUtil::now();
        $contents = Crypt::encryptString($this->file->get());

        $ret =  Storage::put($this->getDir() . DIRECTORY_SEPARATOR . $this->getFilename(), $contents);

        if ($ret === false) {
            return false;
        }


        //DBへの登録
        try {
            $ret = $this->onUpload($timestamp ?? DateUtil::now());
            if (!$ret) {
                $this->delete();
            }
        } catch (Exception $e) {
            $this->delete();
            throw $e;
        }


        return $ret;
    }

    public function toImageStr()
    {
        return (new Image($this))->__toString();
    }
}
