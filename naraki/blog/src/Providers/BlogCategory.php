<?php namespace Naraki\Blog\Providers;

use Naraki\Blog\Contracts\BlogCategory as BlogCategoryInterface;
use App\Models\Blog\BlogLabel;
use App\Models\Blog\BlogLabelRecord;
use App\Models\Blog\BlogLabelType;

class BlogCategory extends Model implements BlogCategoryInterface
{
    protected $model = \App\Models\Blog\BlogCategory::class;

    public function createOne($label, $parentSlug)
    {
        $newLabelType = BlogLabelType::create(
            ['blog_label_id' => BlogLabel::BLOG_CATEGORY]
        );
        $newCat = $this->createModel(
            [
                'blog_category_name' => $label,
                'blog_label_type_id' => $newLabelType->getKey()
            ]);
        if (!is_null($parentSlug) && !empty($parentSlug)) {
            $parentCategory = $this->getCat($parentSlug);
            if (!is_null($parentCategory)) {
                $newCat->appendToNode($parentCategory);
            } else {
                return null;
            }
        }
        $newCat->save();
        return $newCat;
    }

    /**
     * @param string|array $codename
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getByCodename($codename)
    {
        $builder = $this->createModel()->newQuery();
        if (is_array($codename)) {
            $builder->whereIn('blog_category_slug', $codename);
        } else {
            $builder->where('blog_category_slug', '=', $codename);
        }
        return $builder;
    }

    /**
     * @param array $categories
     * @param \App\Models\Blog\BlogPost $post
     * @return void
     */
    public function attachToPost($categories, $post)
    {
        if (empty($categories)) {
            return;
        }
        $labelTypes = $this->getByCodename($categories)->labelType()
            ->select(['blog_label_types.blog_label_type_id as id'])
            ->get();
        if (!is_null($labelTypes)) {
            $records = [];
            foreach ($labelTypes as $label) {
                $records[] = [
                    $post->getKeyName() => $post->getKey(),
                    'blog_label_type_id' => $label->getAttribute('id')
                ];
            }
            BlogLabelRecord::insert($records);
        }
    }

    /**
     * @param array $updated
     * @param \Illuminate\Database\Eloquent\Model $post
     * @return void
     */
    public function updatePost(?array $updated, $post)
    {
        if(is_null($updated)){
            return;
        }
        $inStore = $this->getSelected($post->getKey());
        $toBeRemoved = array_diff($inStore, $updated);
        if (!empty($toBeRemoved)) {
            $entries = $this->getByCodename($toBeRemoved)->labelType()
                ->select(['blog_label_types.blog_label_type_id'])
                ->get()->pluck('blog_label_type_id')->toArray();
            BlogLabelRecord::query()
                ->whereIn('blog_label_type_id', $entries)
                ->where('blog_post_id', $post->getKey())
                ->delete();
        }

        $toBeAdded = array_diff($updated, $inStore);
        if (!empty($toBeAdded)) {
            $this->attachToPost($toBeAdded, $post);
        }
    }

    /**
     * @param int $postId
     * @return array
     */
    public function getSelected($postId)
    {
        return $this->createModel()->newQuery()
            ->select(['blog_category_slug'])
            ->labelType()
            ->labelRecord($postId)
            ->get()->pluck('blog_category_slug')->toArray();
    }

    /**
     * @param string $id
     * @return \App\Models\Blog\BlogCategory|null
     */
    public function getCat($id)
    {
        return $this->createModel()
            ->where('blog_category_slug', $id)->first();
    }

}