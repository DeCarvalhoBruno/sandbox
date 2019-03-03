<?php namespace App\Support\Frontend\Jsonld\Schemas\Thing\CreativeWork;

use App\Support\Frontend\Jsonld\Schemas\Thing\Thing;

class CreativeWork extends Thing
{
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
        return $this->setValuesDefault(
            sprintf('\App\Support\Frontend\Jsonld\Schemas\Thing\%s', $class), $values);
    }

}