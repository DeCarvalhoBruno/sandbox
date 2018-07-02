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
     *
     * @param string $label
     * @param string $id
     */
    public function __construct($label, $id)
    {
        $this->label = $label;
        $this->id = $id;
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
            'mode' => 1,
            'id' => $node->getId(),
            'children' => []
        ];
    }

    /**
     * @param string $label
     * @param string $id
     * @return \App\Support\Trees\BlogPostCategory
     */
    public function addChild($label, $id)
    {
        $new = new static($label, $id);
        array_push($this->children, $new);
        return $new;

    }

    /**
     * @return array
     */
    public static function getTree()
    {
        $f = BlogPostCategoryTree::query()
            ->select(['label', 'lvl', 'id'])->get()->toArray();
        $level = 0;
        $root = '';
        $l = [];
        foreach ($f as $node) {
            $lvl = $node['lvl'];
            $label = $node['label'];
            $id = $node['id'];
            switch (true) {
                case ($lvl === 0):
                    $level = $lvl;
                    $root = $label;
                    $l[$root][$level] = new static($root, $id);
                    break;
                case ($lvl === $level):
                    $l[$root][$lvl] = $l[$root][$lvl - 1]->addChild($label, $id);
                    break;
                case ($lvl > $level):
                    $l[$root][$lvl] = $l[$root][$level]->addChild($label, $id);
                    $level = $lvl;
                    break;
                case ($lvl < $level):
                    $l[$root][$lvl] = $l[$root][$lvl - 1]->addChild($label, $id);
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

}