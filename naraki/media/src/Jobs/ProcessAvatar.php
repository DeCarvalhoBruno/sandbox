<?php namespace Naraki\Media\Jobs;

use App\Jobs\Job;
use App\Models\Entity;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Naraki\Media\Facades\Media as MediaProvider;
use Naraki\Media\Models\Media;
use Naraki\Media\Support\SimpleImage;

class ProcessAvatar extends Job
{
    public $queue = 'db';

    /**
     * @var string
     */
    private $url;
    /**
     * @var string
     */
    private $username;

    /**
     *
     * @param string $url
     * @param string $username
     */
    public function __construct(string $url, string $username)
    {
        $this->url = $url;
        $this->username = $username;
    }


    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        parent::handle();
        try {
            $this->processAvatar();
        } catch (\Exception $e) {
            \Log::critical($e->getMessage(), ['trace' => $e->getTraceAsString()]);
//            app('bugsnag')->notifyException($e, ['mailData'=>$this->email->getData()], "error");
            $this->release(60);
        }
    }

    public function processAvatar()
    {
        $contentTypes = [
            'image/gif' => 'gif',
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
        ];
        $url = vsprintf('%s://%s%s', parse_url($this->url));
        $fileStream = null;
        try {
            $client = new Client();
            $response = $client->get($url);
            $status = $response->getStatusCode();
            $headers = $response->getHeaders();
            if ($status === 200) {
                if (isset($headers['Content-Type'])) {
                    if (isset($contentTypes[$headers['Content-Type'][0]])) {
                        $extension = $contentTypes[$headers['Content-Type'][0]];
                        $avatarInfo = new SimpleImage(
                            $this->username,
                            $this->username,
                            Entity::USERS,
                            Media::IMAGE_AVATAR,
                            $extension,
                            sprintf('%s_%s', $this->username, makeHexUuid())
                        );
                        $avatarInfo->cropAvatarFromStream($response->getBody());
                        MediaProvider::image()->saveAvatar($avatarInfo);
                    }
                }
            }
            unset($response);
        } catch (\Exception $e) {
dd($e);
        }
    }

    public function __get($value)
    {
        return $this->{$value};
    }

}