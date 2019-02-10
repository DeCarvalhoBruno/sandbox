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
        // render (blog,
        $this->breadcrumbs = <<<EOD
<ul class="breadcrumbs">
EOD;
        $this->addNode(route_i18n('home'), chr(10) . str_repeat(' ',8).'<fa icon="home"></fa>');
        foreach ($chain as $label=>$url) {
            $this->addNode($url, $label);
        }
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

}