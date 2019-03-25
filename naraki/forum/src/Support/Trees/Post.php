<?php namespace Naraki\Forum\Support\Trees;

class Post
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var array
     */
    private $data;
    /**
     * @var array
     */
    private $children = [];

    /**
     *
     * @param string $id
     * @param array $data
     */
    public function __construct(string $id, array $data)
    {
        $this->id = $id;
        $this->data = $data;
    }

    /**
     * @param int $level
     * @return \stdClass
     */
    public function toArray($level = 1): \stdClass
    {
        $ar = $this->getNewRoot($this);

        $level++;
        foreach ($this->children as $child) {
            $ar->children[] = $child->toArray($level);
        }
        return $ar;
    }

    /**
     * @param self $node
     * @return object
     */
    private function getNewRoot($node): \stdClass
    {
        $data = $node->getData();
        unset($data['id']);
        $data['children'] = [];
        return (object)$data;
    }

    /**
     * @param string $id
     * @param $data
     * @return self
     */
    public function addChild($id, $data): self
    {
        $new = new static($id, $data);
        array_push($this->children, $new);
        return $new;
    }

    /**
     * @param array $posts
     * @return array
     */
    public static function getTree($posts): array
    {
        if (empty($posts)) {
            return [];
        }
        return static::makeTree($posts);
    }

    /**
     * @param $posts
     * @return array
     */
    private static function makeTree($posts)
    {
        $level = 0;
        $root = '';
        $l = [];
        foreach ($posts as $node) {
            $lvl = $node['lvl'];
            $id = $node['slug'];
            switch (true) {
                case ($lvl === 0):
                    $level = $lvl;
                    $root = $id;
                    $l[$root][$level] = new static($id, $node);
                    break;
                case ($lvl === $level):
                    $l[$root][$lvl] = $l[$root][$lvl - 1]->addChild($id, $node);
                    break;
                case ($lvl > $level):
                    $l[$root][$lvl] = $l[$root][$level]->addChild($id, $node);
                    $level = $lvl;
                    break;
                case ($lvl < $level):
                    $l[$root][$lvl] = $l[$root][$lvl - 1]->addChild($id, $node);
                    $level = $lvl;
                    break;
            }
        }
        $tree = [];
        foreach ($l as $node) {
            $tree[] = $node[0]->toArray();
        }
        return $tree;
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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}