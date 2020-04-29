<?php

declare(strict_types=1);

namespace App\Common\File\Service;

use App\Common\File\AbstractFile;
use DomainException;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\FileNotFoundException;

/**
 * Class AttachmentService
 */
class Uploader
{

    /**
     * @var FilesystemInterface
     */
    private FilesystemInterface $storage;


    /**
     * AttachmentService constructor.
     *
     * @param FilesystemInterface $defaultStorage
     */
    public function __construct(FilesystemInterface $defaultStorage)
    {
        $this->storage = $defaultStorage;
    }

    /**
     * @param string $date
     *
     * @return false|string
     */
    private function decodeBase64(string $date)
    {
        return base64_decode($date);
    }

    /**
     * @param AbstractFile $baseFile
     *
     * @return void
     */
    public function save(AbstractFile $baseFile)
    {
        $directory = $baseFile->getDirectory();
        if (false === $this->storage->has($directory)) {
            if (false === $this->storage->createDir($directory)) {
                throw new DomainException("Directory $directory is not created.");
            }
        }

        $file = $baseFile->decode();

        $path = $baseFile->getPath();

        if (false ===  $this->storage->put($path, $file)) {
            throw new DomainException('File is not saved.');
        }
    }

    /**
     * @param string $path
     *
     * @return void
     *
     * @throws FileNotFoundException
     */
    public function remove(string $path)
    {
        if (true === $this->storage->has($path)) {
            if (false === $this->storage->delete($path)) {
                throw new DomainException('File is not deleted.');
            }
        }
    }
}
