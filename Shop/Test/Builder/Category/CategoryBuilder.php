<?php

declare(strict_types=1);

namespace App\Shop\Test\Builder\Category;

use App\Shop\Entity\Category\Category;
use App\Shop\Entity\Category\CategoryTranslation;
use App\Shop\Entity\Category\Code;
use App\Shop\Entity\Category\Id;
use App\Shop\Entity\Category\Locale;
use App\Shop\Entity\Category\Preview;

class CategoryBuilder
{
    private const BASE = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAIAAAACCAIAAAD91JpzAAAAA3NCSVQICAjb4U/gAAAAEHRFWHRTb2Z0d2FyZQBTaHV0dGVyY4LQCQAAABZJREFUCNdj/P//PwMDAxMDAwMDAwMAJAYDAb0e47oAAAAASUVORK5CYII=';
    private Id $id;
    private string $name;
    private string $description;
    private Code $code;
    private Locale $locale;
    private string $slug;
    private int $position;
    private ?Category $parent = null;
    private ?Preview $preview = null;
    private ?CategoryTranslation $translation = null;

    public function __construct()
    {
        $this->id = Id::generate();
        $this->name = 'name';
        $this->description = 'description';
        $this->code = new Code(123);
        $this->locale = Locale::enUs();
        $this->slug = 'name';
        $this->position = 1;
    }

    public function withPreview()
    {
        $clone = clone $this;

        $clone->preview = new Preview(self::BASE, 'localhost', $this->id);

        return $clone;
    }

    public function withParent()
    {
        $clone = clone $this;

        $parent = new Category(
            $this->id,
            $this->code,
            $this->name,
            $this->position,
            $this->description,
            $this->slug
        );

        $clone->parent = $parent;

        return $clone;
    }

    public function build(): Category
    {
        $category = new Category(
            $this->id,
            $this->code,
            $this->name,
            $this->position,
            $this->description,
            $this->slug
        );

        if($this->parent !== null) {
            $category->becomeSubordinate($this->parent);
        }

        if($this->preview !== null) {

            $category->attachPreview($this->preview);
        }

        if($this->translation !==null) {
            $category->addTranslation($this->translation);
        }

        return $category;
    }

    public function withTranslation()
    {
        $clone = clone $this;

        $clone->translation = new CategoryTranslation(
            Id::generate(),
            Locale::enUs(),
            Category::DESCRIPTION_FIELD,
            'value'
        );

        return $clone;
    }
}
