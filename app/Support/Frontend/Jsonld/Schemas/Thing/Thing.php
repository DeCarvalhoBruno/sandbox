<?php namespace App\Support\Frontend\Jsonld\Schemas\Thing;

use App\Support\Frontend\Jsonld\Schema;

class Thing extends Schema
{
    protected $alternateName;
    protected $description;
    protected $image;
    protected $name;
    protected $sameAs;
    protected $url;
    protected $potentialAction;

    public function setPotentialAction($values, $class)
    {
        return $this->setValuesDefault(
            sprintf('\App\Support\Frontend\Jsonld\Schemas\Thing\Action\%s', $class), $values);
    }

}