<?php

use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    private $origDir = __DIR__ . '/../files/data';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
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

    }

    private function seedChunk($data, $model, $nbChunks = 25)
    {
        $chunks = array_chunk($data, $nbChunks);
        foreach ($chunks as $chunk) {
            forward_static_call(sprintf('%s::insert', $model), $chunk);
        }
    }


}
