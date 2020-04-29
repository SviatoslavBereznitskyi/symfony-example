<?php

namespace App\Shop\Entity\Category;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="shop_categories_translations",
 *     uniqueConstraints={@ORM\UniqueConstraint(columns={
 *         "locale", "object_id", "field"
 *     })}
 * )
 */
class CategoryTranslation
{

    /**
     * @ORM\Column(type="shop_category_id")
     * @ORM\Id
     */
    private Id $id;

    /**
     * @var Locale
     *
     * @ORM\Column(type="shop_category_locale", length=8)
     */
    private Locale $locale;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32)
     */
    private string $field;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private string $content;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private Category $object;


    /**
     * CategoryTranslation constructor.
     *
     * @param Id       $id
     * @param Locale   $locale
     * @param string   $field
     * @param string   $value
     */
    public function __construct(Id $id, Locale $locale, string $field, string $value)
    {
        $this->id      = $id;
        $this->locale  = $locale;
        $this->field   = $field;
        $this->content = $value;
    }

    /**
     * @param Category $object
     *
     * @return void
     */
    public function assign(Category $object)
    {
        $this->object = $object;
    }

    /**
     * @param string $value
     *
     * @return void
     */
    public function edit(string $value)
    {
        $this->content = $value;
    }

    /**
     * @param string $field
     *
     * @return boolean
     */
    public function isEqualField(string $field): bool
    {
        return $this->field === $field;
    }

    /**
     * @param string $locale
     *
     * @return boolean
     */
    public function isEqualLocale(string $locale): bool
    {
        return $this->getLocale()->getName() === $locale;
    }

    /**
     * Get id
     *
     * @return Id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get locale
     *
     * @return Locale
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Get field
     *
     * @return string $field
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Get related object
     *
     * @return Category
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
}
