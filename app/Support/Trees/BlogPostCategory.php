<?php namespace App\Support\Trees;

use App\Models\Views\BlogPostCategoryTree;

class BlogPostCategory
{
    /**
     * @var string
     */
    private $label;
    /**
     * @var array
     */
    private $children = [];
    /**
     * @var string
     */
    private $id;
    /**
     * @var bool
     */
    private $selected;

    /**
     *
     * @param string $label
     * @param string $id
     * @param bool $selected
     */
    public function __construct($label, $id, $selected = false)
    {
        $this->label = $label;
        $this->id = $id;
        $this->selected = $selected;
    }

    /**
     * @param int $level
     * @return \stdClass
     */
    public function toArray($level = 1)
    {
        $ar = $this->getNewRoot($this, $level);

        $level++;
        foreach ($this->children as $child) {
            $ar->children[] = $child->toArray($level);
        }
        return $ar;
    }

    /**
     * @param \App\Support\Trees\BlogPostCategory $node
     * @param int $level
     * @return object
     */
    private function getNewRoot($node, $level)
    {
        return (object)[
            'label' => $node->getLabel(),
            'open' => ($level > 2 ? false : true),
            'selected' => $node->isSelected(),
            'mode' => 1,
            'id' => $node->getId(),
            'children' => []
        ];
    }

    /**
     * @param string $label
     * @param string $id
     * @param bool $selected
     * @return \App\Support\Trees\BlogPostCategory
     */
    public function addChild($label, $id, $selected)
    {
        $new = new static($label, $id, $selected);
        array_push($this->children, $new);
        return $new;
    }

    /**
     * @return array
     */
    public static function getTree($postId = null)
    {
        $selectedCategories = [];
        $f = BlogPostCategoryTree::query()
            ->select(['label', 'lvl', 'id'])->get()->toArray();
        if (!is_null($postId)) {
            $selectedCategories = array_flip(\App\Models\Blog\BlogPostCategory::query()
                ->select(['blog_post_category_codename'])
                ->labelType()
                ->labelRecord(1)
                ->get()->pluck('blog_post_category_codename')->toArray());
        }
        $level = 0;
        $root = '';
        $l = [];
        foreach ($f as $node) {
            $lvl = $node['lvl'];
            $label = $node['label'];
            $id = $node['id'];
            $selected = (!is_null($postId) && isset($selectedCategories[$node['id']])) ? true : false;
            switch (true) {
                case ($lvl === 0):
                    $level = $lvl;
                    $root = $label;
                    $l[$root][$level] = new static($root, $id, $selected);
                    break;
                case ($lvl === $level):
                    $l[$root][$lvl] = $l[$root][$lvl - 1]->addChild($label, $id, $selected);
                    break;
                case ($lvl > $level):
                    $l[$root][$lvl] = $l[$root][$level]->addChild($label, $id, $selected);
                    $level = $lvl;
                    break;
                case ($lvl < $level):
                    $l[$root][$lvl] = $l[$root][$lvl - 1]->addChild($label, $id, $selected);
                    $level = $lvl;
                    break;
            }
        }
        $root = [];
        foreach ($l as $node) {
            $root[] = $node[0]->toArray();
        }
        return $root;
    }

    /**
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isSelected(): bool
    {
        return $this->selected;
    }


}