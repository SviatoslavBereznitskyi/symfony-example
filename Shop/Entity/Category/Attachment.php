<?php

declare(strict_types=1);

namespace App\Shop\Entity\Category;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="shop_categories_attachments")
 */
class Attachment
{

    /**
     * @ORM\Column(type="shop_category_id")
     * @ORM\Id
     */
    private Id $id;

    /**
     * @ORM\Column(length=255)
     */
    private string $path;

    /**
     * @ORM\Column(length=255)
     */
    private string $basePath;

    /**
     * @ORM\Column(type="shop_category_attachment")
     */
    private AttachmentType $type;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="attachments")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private Category $category;


    /**
     * Attachment constructor.
     *
     * @param Id             $id
     * @param string         $basePath
     * @param string         $path
     * @param AttachmentType $type
     * @param Category       $category
     */
    public function __construct(Id $id, string $basePath, string $path, AttachmentType $type, Category $category)
    {
        $this->id       = $id;
        $this->path     = $path;
        $this->type     = $type;
        $this->category = $category;
        $this->basePath = $basePath;
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return AttachmentType
     */
    public function getType(): AttachmentType
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getFullPath(): string
    {
        return $this->basePath . '/' . $this->path;
    }
}
