<?php

class Nutriment
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string;
     */
    private $quantity;

    /**
     *
     * @param string $name
     * @param string $quantity
     */
    public function __construct(string $name, string $quantity)
    {
        $this->name = $name;
        $this->quantity = $quantity;
    }


}