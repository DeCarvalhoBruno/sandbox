<?php namespace App\Support\Frontend;

class Breadcrumbs
{
    private $breadcrumbs = '';

    public static function render($chain)
    {
        return (new static())->make($chain);
    }

    private function make($chain)
    {
        $this->breadcrumbs = <<<EOD
<ul class="breadcrumbs">
EOD;
        $this->addNode(route_i18n('home'), chr(10) . str_repeat(' ',8).'<i class="fa fa-home"></i>');
        $lastItem = array_pop($chain);
        foreach ($chain as $item) {
            $this->addNode($item['url'], $item['label']);
        }
        $this->lastNode($lastItem['label']);
        $this->breadcrumbs .= chr(10) . "</ul>";

        return $this->breadcrumbs;
    }

    private function addNode($url, $nodeLabel)
    {
        $this->breadcrumbs .= <<<EOD
  
  <li>
    <span class="breadcrumb-item">
      <a class="breadcrumb-link" href="$url">$nodeLabel</a>
    </span>
  </li>
EOD;
    }

    private function lastNode($nodeLabel)
    {
        $this->breadcrumbs .= <<<EOD
  
  <li>
    <span class="breadcrumb-item">$nodeLabel</span>
  </li>
EOD;
    }

}