<?php namespace App\Emails;

use Illuminate\Queue\SerializesModels;

class Email
{
    use SerializesModels;
    protected $viewName;
    protected $view;
    protected $data;
    protected $domain;
    protected $files;
    private $from;
    private $fromName;
    private $testing;

    /**
     *
     * @param array $data
     * @param array $files
     * @param bool $testing
     */
    public function __construct($data, $files = null, $testing = false)
    {
        $this->parseFiles($files);
        $this->data     = $data;
        $this->view     = new \stdClass();
        $this->from     = \Config::get('mail.from.address');
        $this->fromName = \Config::get('mail.from.name');
        $this->testing  = $testing;
    }

    /**
     * @return mixed
     */
    public function send()
    {
        $this->prepareViewData();
        $this->setDomain();
        $this->sendmail();
    }

    /**
     * @return void
     */
    protected function sendmail()
    {
        $currentInstance = $this;

        \Mail::send($this->viewName, (array)$this->view, function ($message) use ($currentInstance) {
            return call_user_func([$currentInstance, 'message'], $message);
        });
    }

    /**
     * @return array
     */
    public function getData()
    {
        return ['data' => $this->data, 'view' => $this->view];
    }

    /**
     * @return void
     */
    public function setDomain()
    {
        $this->domain = 'lti.local';
    }

    /**
     * @param array $files
     */
    private function parseFiles($files)
    {
        if ( ! is_null($files)) {
            foreach ($files as $file) {
                $path          = storage_path() . '/uploads/' . str_random(5) . $file->getClientOriginalName();
                $this->files[] = (object)[
                    'path' => $path,
                    'as'   => $file->getClientOriginalName(),
                    'mime' => $file->getMimeType()
                ];
                \File::move($file->getRealPath(), $path);
            }
        }
    }

}