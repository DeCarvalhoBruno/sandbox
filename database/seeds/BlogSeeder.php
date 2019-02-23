<?php

use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    private $origDir = __DIR__ . '/../files/data';

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Exception
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

        $faker = Faker\Factory::create();
        $blogPostEntityIds = \DB::select('
select entity_type_id from blog_posts
inner join entity_types on entity_types.entity_type_target_id = blog_posts.blog_post_id 
and entity_types.entity_id = 300'
        );

        $viewsRecords = [];
        foreach ($blogPostEntityIds as $bpe) {
            $viewsRecords = [];
            $num = rand(359, 67000);

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


}
