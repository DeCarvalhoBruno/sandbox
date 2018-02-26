<?php namespace App\Support\GroupHierarchy;

use App\Traits\Enumerable;

class GroupHierarchy
{
    use Enumerable;

    const root = 1;
    const superadmins = 2;
    const admins = 2000;
    const users = 5000;

    public static function getTree($groups)
    {
        $tree = new GroupHierarchy();
        return $tree->makeTree($groups);
    }

    private function makeTree($groups)
    {
//        $groups = $this->getConstants();
//        asort($groups, SORT_NUMERIC);
        $tree = new Group();
        $tmp = $tree;
        $currentIndex = 0;
        foreach ($groups as $group) {
            if ($group->group_mask > $currentIndex) {
                $child = $tmp->addChild(new Group($group->group_name, $group->group_mask));
                $tmp = $child;
            } else {
                $tmp->addSibling(new Group($group->group_name, $group->group_mask));
            }
            $currentIndex = $group->group_mask;
        }
        return $tree;
    }

//    public static function findGroupAtIndex($tree, $index){
//        return
//    }


}

class Group
{
    /**
     * @var self[]
     */
    private $children = [];
    /**
     * @var self[]
     */
    private $siblings = [];
    /**
     * @var self
     */
    private $parent = null;
    /**
     * @var string
     */
    private $name = null;
    /**
     * @var integer
     */
    private $index = null;


    /**
     *
     * @param string $name
     * @param integer $index
     */
    public function __construct($name = null, $index = null)
    {
        $this->name = $name;
        $this->index = $index;
    }

    public function findAtIndex($index)
    {
        if (isset($this->siblings[$index])) {
            return $this->siblings[$index];
        } elseif (isset($this->children[$index])) {
            return $this->children[$index];
        } elseif (!empty($this->children)) {
            reset($this->children);
            return $this->children[key($this->children)]->findAtIndex($index);
        }
        return false;
    }

    /**
     * @param \App\Support\GroupHierarchy\Group $parent
     */
    public function addParent(self $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @param \App\Support\GroupHierarchy\Group $sibling
     */
    public function addSibling(self $sibling)
    {
        $sibling->addParent($this->parent);
        $this->siblings[$sibling->getIndex()] = $sibling;
    }

    /**
     * @param \App\Support\GroupHierarchy\Group $child
     * @return \App\Support\GroupHierarchy\Group
     */
    public function addChild(self $child)
    {
        $this->children[$child->getIndex()] = $child;
        $child->addParent($this);
        if (!empty($this->siblings)) {
            foreach ($this->siblings as $sibling) {
                $sibling[$child->getIndex()] = $child;
            }
        }
        return $child;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return integer
     */
    public function getIndex()
    {
        return $this->index;
    }
}