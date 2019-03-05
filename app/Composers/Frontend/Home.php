<?php namespace App\Composers\Frontend;

use App\Composers\Composer;

class Home extends Composer
{
    private $data;

    /**
     * @param \Illuminate\View\View $view
     */
    public function compose($view)
    {
        $this->setVar('meta_description');
        $this->setVar('meta_jsonld');
        $this->setVar('meta_robots');
        if (!is_null($this->data)) {
            $this->addVarsToView($this->data, $view);
        }
    }

    public function setVar($var)
    {
        $cVar = \Cache::get($var);
        if (!is_null($cVar) && !empty($cVar)) {
            $this->data[$var]= $cVar;
        }
    }
}