<?php namespace App\Support\Providers;

use App\Contracts\Models\BlogTag as BlogTagInterface;
use App\Models\Blog\BlogPostLabelRecord;
use App\Models\Blog\BlogPostLabelType;

class BlogTag extends Model implements BlogTagInterface
{
    protected $model = \App\Models\Blog\BlogPostTag::class;

    public function getByPost($postId)
    {
        return $this->createModel()->newQuery()
            ->select(['blog_post_tag_name'])
            ->labelType()
            ->labelRecord($postId)
            ->get()->pluck('blog_post_tag_name')->toArray();
    }

    public function createMany($names)
    {
        if (empty($names)) {
            return;
        }
        //We create a first label type to get its id
        $newLabelType = BlogPostLabelType::create();
        $firstLabelId = $newLabelType->getKey();
        $nbTags = count($names);

        if ($nbTags > 1) {
            //When we create the rest of the label types we account for the one we created (n-1)
            BlogPostLabelType::insert(array_fill(0, $nbTags - 1, []));
        }
        $newTags = [];
        foreach ($names as $name) {
            $newTags[] = [
                'blog_post_tag_name' => $name,
                'blog_post_tag_slug' => str_slug($name, '-', app()->getLocale()),
                'blog_post_label_type_id' => $firstLabelId++
            ];
        }
        \App\Models\Blog\BlogPostTag::insert($newTags);
    }

    /**
     * @param string|array $name
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getByName($name)
    {
        $builder = $this->createModel()->newQuery();
        if (is_array($name)) {
            $builder->whereIn('blog_post_tag_name', $name);
        } else {
            $builder->where('blog_post_tag_name', '=', $name);
        }
        return $builder;
    }

    /**
     * @param array $tags
     * @param \App\Models\Blog\BlogPost $post
     * @return void
     */
    public function attachToPost($tags, $post)
    {
        if (empty($tags)) {
            return;
        }
        $this->createMany($this->getUnknownTags($tags));

        $labelTypes = $this->getByName($tags)->labelType()
            ->select(['blog_post_label_types.blog_post_label_type_id as id'])
            ->get();
        if (!is_null($labelTypes)) {
            $records = [];
            foreach ($labelTypes as $label) {
                $records[] = [
                    $post->getKeyName() => $post->getKey(),
                    'blog_post_label_type_id' => $label->getAttribute('id')
                ];
            }
            BlogPostLabelRecord::insert($records);
        }
    }

    /**
     * @param array $updated
     * @param \App\Models\Blog\BlogPost $post
     */
    public function updatePost(array $updated, $post)
    {
        $inStore = $this->getByPost($post->getKey());
        $toBeRemoved = array_diff($inStore, $updated);
        if (!empty($toBeRemoved)) {
            $entries = $this->getByName($toBeRemoved)->labelType()
                ->select(['blog_post_label_types.blog_post_label_type_id'])
                ->get()->pluck('blog_post_label_type_id')->toArray();
            BlogPostLabelRecord::query()
                ->whereIn('blog_post_label_type_id', $entries)
                ->where('blog_post_id', $post->getKey())
                ->delete();
        }

        $toBeAdded = array_diff($updated, $inStore);
        if (!empty($toBeAdded)) {
            $this->attachToPost($toBeAdded, $post);
        }
    }

    public function getUnknownTags($names)
    {
        $tags = $this->getByName($names)->labelType()
            ->select(['blog_post_tag_name'])
            ->get()
            ->pluck('blog_post_tag_name')
            ->toArray();
        return array_diff($names, $tags);
    }

}