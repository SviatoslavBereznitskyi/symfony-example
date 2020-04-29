<?php

declare(strict_types=1);

namespace App\Shop\Entity\Category;

use App\AggregateRoot;
use App\EventsTrait;
use App\Shop\Entity\Product\Product;
use App\Shop\Event\ShopFileAdded;
use App\Shop\Event\ShopFileRemoved;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Webmozart\Assert\Assert;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="shop_categories", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns="code"),
 *     @ORM\UniqueConstraint(columns="slug"),
 * })
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 * @Gedmo\Tree(type="nested")
 * @ORM\Entity(repositoryClass="Gedmo\Tree\Entity\Repository\NestedTreeRepository")
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class Category implements AggregateRoot
{
    use SoftDeleteableEntity;
    use EventsTrait;

    public const NAME_FIELD        = 'name';
    public const DESCRIPTION_FIELD = 'description';

    /**
     * @ORM\Column(type="shop_category_id")
     * @ORM\Id
     */
    private Id $id;

    /**
     * @ORM\SequenceGenerator(sequenceName="shop_category_code_seq", initialValue=1, allocationSize=1)
     * @ORM\Column(type="shop_category_code", length=11)
     */
    private Code $code;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(length=64)
     */
    private string $name;

    /**
     * @Gedmo\Translatable
     * @Gedmo\Slug(fields={"name"}, updatable=false)
     * @ORM\Column(type="string", length=255)
     */
    private ?string $slug;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description = null;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    private int $lft;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    private int $lvl;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    private int $rgt;

    /**
     * @Gedmo\TreeRoot
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="tree_root", referencedColumnName="id", onDelete="CASCADE")
     */
    private $root;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $children;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer")
     */
    private int $position;

    /**
     * @ORM\OneToMany(
     *   targetEntity="Attachment",
     *   mappedBy="category",
     *   orphanRemoval=true,
     *   cascade={"persist", "remove"}
     * )
     */
    private $attachments;

    /**
     * @ORM\OneToMany(
     *   targetEntity="App\Shop\Entity\Product\Product",
     *   mappedBy="category",
     *   orphanRemoval=true,
     *   cascade={"persist", "remove"}
     * )
     */
    private $products;

    /**
     * @ORM\OneToMany(targetEntity="CategoryTranslation",
     *   mappedBy="object",
     *   cascade={"persist", "remove"}
     * )
     */
    private $translations;


    /**
     * Category constructor.
     * @param Id           $id
     * @param Code         $code
     * @param string       $name
     * @param integer|null $position
     * @param string|null  $description
     * @param string|null  $slug
     */
    public function __construct(
        Id $id,
        Code $code,
        string $name,
        int $position,
        ?string $description = null,
        ?string $slug = null
    ) {
        Assert::notEmpty($name);

        $this->translations = new ArrayCollection();
        $this->id           = $id;
        $this->code         = $code;
        $this->name         = $name;
        $this->description  = $description;
        $this->slug         = $slug;
        $this->position     = $position;
        $this->deletedAt    = null;

        $this->attachments  = new ArrayCollection();
        $this->translations = new ArrayCollection();
        $this->products     = new ArrayCollection();
    }

    /**
     * @param string      $name
     * @param integer     $position
     * @param string      $slug
     * @param string|null $description
     *
     * @return void
     */
    public function edit(string $name, int $position, string $slug, ?string $description = null)
    {
        Assert::notEmpty($name);
        Assert::greaterThanEq($position, 0);

        $this->name        = $name;
        $this->position    = $position;
        $this->slug        = $slug;
        $this->description = $description;
    }

    /**
     * @param CategoryTranslation $translation
     *
     * @return void
     */
    public function addTranslation(CategoryTranslation $translation)
    {
        /**
         * @var ArrayCollection<CategoryTranslation> $sameTranslation
         */
        $sameTranslation = $this->translations->filter(
            function (CategoryTranslation $item) use ($translation) {
                return  $item->isEqualField($translation->getField())
                    && $item->isEqualLocale($translation->getLocale()->getName());
            }
        );

        if (false === $sameTranslation->isEmpty()) {
            throw new \DomainException("Translation for \"{$translation->getField()}\" already exists");
        }

        $translation->assign($this);
        $this->translations->add($translation);
    }

    /**
     * @param string $locale
     * @param string $field
     * @param string $value
     *
     * @return void
     */
    public function editTranslation(string $locale, string $field, string $value)
    {
        /**
         * @var ArrayCollection<CategoryTranslation> $sameTranslation
         */
        $sameTranslations = $this->translations->filter(
            fn (CategoryTranslation $item) => $item->isEqualField($field) && $item->isEqualLocale($locale)
        );

        if (true === $sameTranslations->isEmpty()) {
            throw new \DomainException('Translation not exists');
        }

        /**
         * @var CategoryTranslation $translation
         */
        $translation = $sameTranslations->first();

        $translation->edit($value);
    }

    /**
     * @return Id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Code
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     *
     * @psalm-suppress InvalidNullableReturnType
     * @psalm-suppress NullableReturnStatement
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return ArrayCollection<CategoryTranslation>
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * @return Category
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @param Category $parent
     *
     * @return void
     */
    public function becomeSubordinate(Category $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return void
     */
    public function cancelSubordinate()
    {
        if ($this->parent === null) {
            throw new \DomainException('no parent to cancel');
        }

        $this->parent = null;
    }

    /**
     * @return Category
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return boolean
     */
    public function hasChildren()
    {
        return $this->children->count() !== 0;
    }

    /**
     * @param Preview $preview
     *
     * @return void
     */
    public function attachPreview(Preview $preview)
    {
        $attachment = new Attachment(
            new Id($preview->getId()),
            $preview->getBasePath(),
            $preview->getPath(),
            AttachmentType::preview(),
            $this
        );

        $this->attachments->add($attachment);

        $this->recordEvent(new ShopFileAdded($preview));
    }

    /**
     * @return void
     */
    public function removePreview()
    {
        /**
         * @var Attachment $preview
         */
        $preview = $this->attachments->filter(
            function (Attachment $attachment) {
                return $attachment->getType()->isEqualTo(AttachmentType::preview());
            }
        )->first();

        if (!$preview) {
            throw new \DomainException('preview not fount');
        }

        $this->attachments->removeElement($preview);

        $this->recordEvent(new ShopFileRemoved($preview->getPath()));
    }

    /**
     * @return ArrayCollection<Category>
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return integer
     */
    public function getLevel()
    {
        return $this->lvl;
    }

    /**
     * @return integer
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param integer $position
     *
     * @return void
     */
    public function changePosition(int $position): void
    {
        Assert::greaterThanEq($position, 0);

        $this->position = $position;
    }

    /**
     * @return mixed|Attachment
     */
    public function getPreview()
    {
        return $this->attachments->filter(
            fn (Attachment $attachment) => $attachment->getType()->isEqualTo(AttachmentType::preview())
        )->first();
    }
}
