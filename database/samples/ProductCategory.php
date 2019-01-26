<?php

class ProductCategory
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $language;

    /**
     *
     * @param string $name
     * @param string $language
     * @throws \Exception
     */
    public function __construct(string $name, string $language = 'fr')
    {
        $this->name = $name;
        $this->setLanguage($language);
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage(string $language)
    {
        if(preg_match('/(en|fr)/',$language)){
            $this->language = $language;
        }else{
            throw new \Exception('language not recognized');
        }
    }


}