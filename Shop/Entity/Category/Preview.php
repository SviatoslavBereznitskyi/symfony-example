<?php

declare(strict_types=1);

namespace App\Shop\Entity\Category;

use App\Common\File\AbstractFile;

/**
 * Class Preview
 */
class Preview extends AbstractFile
{

    protected string $directoryPath = 'shop/category';
    protected string $attachmentPath = 'attachments/preview';
    protected string $basePath;
    protected string $id;
    protected string $categoryId;

    protected array $fileExtensions = [
        'jpeg',
        'png',
    ];

    public function __construct(string $value, string $basePath, Id $categoryId)
    {
        parent::__construct($value);

        if (false === in_array($this->getExtension(), $this->fileExtensions, false)) {
            throw new \DomainException('Not found need extend.');
        }

        $this->categoryId = $categoryId->getValue();
        $this->id = Id::generate()->getValue();
        $this->basePath = $basePath;
    }

    public function getBaseName(): string
    {
        return $this->id . '.' . $this->extension;
    }

    public function getDirectory(): string
    {
        return $this->directoryPath . '/' . $this->categoryId . '/' . $this->attachmentPath;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function getPath(): string
    {
        return $this->getDirectory() . '/' . $this->getBaseName();
    }

    public function getId(): string
    {
        return $this->id;
    }
}
