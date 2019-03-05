<?php namespace App\Support\Frontend\Jsonld\Schemas\Thing\CreativeWork;

use App\Support\Frontend\Jsonld\Schemas\Thing\Organization\Organization;
use App\Support\Frontend\Jsonld\Schemas\Thing\Thing;

class CreativeWork extends Thing
{
    static $websiteList = ['WebSite' => '', 'Blog' => ''];
    static $publisherThingList = ['Person' => ''];
    protected $about;
    protected $aggregateRating;
    protected $alternativeHeadline;
    protected $author;
    protected $comment;
    protected $commentCount;
    protected $creator;
    protected $dateCreated;
    protected $dateModified;
    protected $datePublished;
    protected $headline;
    protected $inLanguage;
    protected $keywords;
    protected $mainEntity;
    protected $publisher;
    protected $review;
    protected $text;
    protected $thumbnailUrl;
    protected $video;

    public function setPublisher($values, $class)
    {
        if (isset(static::$publisherThingList[$class])) {
            return $this->setValuesDefault(
                sprintf('\App\Support\Frontend\Jsonld\Schemas\Thing\%s', $class), $values);
        } else {
            return $this->setValuesDefault(
                Organization::getClassName($class), $values
            );
        }
    }

    public static function getClassName($value)
    {
        if (!isset(static::$websiteList[$value])) {
            throw new \InvalidArgumentException('Invalid website type for structured data.');
        }
        return sprintf('\App\Support\Frontend\Jsonld\Schemas\Thing\CreativeWork\%s', $value);
    }

}