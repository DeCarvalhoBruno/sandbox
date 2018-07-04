<?php namespace App\Support\Providers;

use App\Contracts\Models\BlogCategory as BlogCategoryInterface;
use App\Models\Blog\BlogPostLabelRecord;
use App\Models\Blog\BlogPostLabelType;

class BlogCategory extends Model implements BlogCategoryInterface
{
    protected $model = \App\Models\Blog\BlogPostCategory::class;

    public function createOne($label, $codename)
    {
        $newLabelType = BlogPostLabelType::create();
        $newCat = $this->createModel(
            [
                'blog_post_category_name' => $label,
                'blog_post_category_codename' => makeHexUuid(),
                'blog_post_label_type_id' => $newLabelType->getKey()
            ]);
        if (!is_null($codename)) {
            $parentCategory = $this->getCat($codename);
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
            $builder->whereIn('blog_post_category_codename', $codename);
        } else {
            $builder->where('blog_post_category_codename', '=', $codename);
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
        if (!empty($categories)) {
            $labelTypes = $this->getByCodename($categories)->labelType()
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
    }

}