<?php

use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    private $origDir = __DIR__ . '/../files/data';
    private $origImagesDir = __DIR__ . '/../files/data/img';

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        $images = $this->parseImages();

        $file = sprintf('%s/%s', $this->origDir, 'blog_data.txt');
        $fhandle = fopen($file, 'r');
        $data = unserialize(fread($fhandle, filesize($file)));
        fclose($fhandle);
        foreach ($data as $key => $modelData) {
            dump(count($modelData->data), $modelData->model);
            if ($key == 'categories') {
                foreach ($modelData->data as $cats) {
                    (new \App\Models\Blog\BlogCategory($cats))->save();
                }
                continue;
            }
            $this->seedChunk($modelData->data, $modelData->model);
        }

        $faker = Faker\Factory::create();
        $blogPostEntityIds = \DB::select('
select entity_type_id,blog_post_slug as slug, blog_post_id from blog_posts
inner join entity_types on entity_types.entity_type_target_id = blog_posts.blog_post_id 
and entity_types.entity_id = 300'
        );
        $bpes = [8689 => 1, 1220 => 1, 3477 => 1, 12339 => 1, 4086 => 1, 1799 => 1];
        $viewsRecords = [];
        foreach ($blogPostEntityIds as $bpe) {
            $viewsRecords = [];
            $num = rand(118, 670);
            if (isset($images[$bpe->slug])) {
                if (isset($bpes[intval($bpe->blog_post_id)])) {
                    $num = rand(901, 1100);
                } else {
                    $num = rand(700, 900);
                }
            }

            $viewsRecords[] = [
                'entity_type_id' => $bpe->entity_type_id,
                'cnt' => $num,
                'unq' => round($num * (rand(90, 97) / 100))
            ];
            $this->seedChunk($viewsRecords, \App\Models\Stats\StatPageView::class, 10);
        }
    }

    private function seedChunk($data, $model, $nbChunks = 25)
    {
        $chunks = array_chunk($data, $nbChunks);
        foreach ($chunks as $chunk) {
            forward_static_call(sprintf('%s::insert', $model), $chunk);
        }
    }

    private function parseImages()
    {
        $dir = opendir($this->origImagesDir);
        if (!$dir) {
            die(sprintf("%s could not be read.", $this->origImagesDir));
        }
        //We wanna insert products in the database before the rest because of the integrity checks.
        $images = [];
        while (($file = readdir($dir)) !== false) {
            $fullPath = $this->origImagesDir . '/' . $file;
            if (is_file($fullPath)) {
                $pos = strrpos($file, '.');
                $images[substr($file, 0, $pos)] = substr($file, $pos + 1);
            }
        }
        closedir($dir);
        return $images;
    }


}
