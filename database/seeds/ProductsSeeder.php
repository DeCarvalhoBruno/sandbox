<?php

use Illuminate\Database\Seeder;


class ProductsSeeder extends Seeder
{
    private $origDir = __DIR__ . '/../files/data';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        require_once(__DIR__ . '/../files/products/ProductRepo.php');

        $data = $this->initSeeding();
        /*
        productBrands :15075            DONE AND SAVED
        packagers :18819                DONE AND SAVED
        languages :183                  DONE AND SAVED
        productCategories :13184
        productIngredients :87897
        productPackages :4246
        productNutriments :2685
        productNutrientLevels :12
         */
//        dd($this->getClassArrayCount($data));

//        $this->seedPackagers($data->packagers);
//        unset($data->packagers);
//        \App\Models\Shop\Language::insert($data->languages);
//        $this->seedBrands($data->productBrands);

//        dd($data->productCategories);
        $this->seedCategories($data->productCategories);


    }

    private function seedCategories($cats)
    {
        $chunks = array_chunk($cats, 50);
        foreach ($chunks as $chunk) {
            \App\Models\Shop\ProductCategory::insert($chunk);
        }
    }

    private function seedBrands($brands)
    {
        $chunks = array_chunk($brands, 50);
        foreach ($chunks as $chunk) {
            \App\Models\Shop\ProductBrand::insert($chunk);
        }
    }

    private function seedPackagers($packagers)
    {
        $chunks = array_chunk($packagers, 50);
        foreach ($chunks as $chunk) {
            \App\Models\Shop\ProductPackager::insert($chunk);
        }
        $entity = \App\Models\Shop\ProductPackager::create([
            'product_packager_name' => 'FOR SYSTEM USE'
        ]);
        $entity->save();
        $pk = $entity->getKeyName();
        $entity->setAttribute($pk, 0);
        $entity->save();
    }

    private function initSeeding()
    {
        $repoFile = $this->origDir . '/query_init0.txt';
        $size = filesize($repoFile);
        $handle = fopen($repoFile, "r");
        $DBEntriesRepo = unserialize(fread($handle, ($size + 128)));
        fclose($handle);
        return $DBEntriesRepo;
    }

    private function seedProducts()
    {
        $dir = opendir($this->origDir);
        if (!$dir) {
            die(sprintf("%s could not be read.", $this->origDir));
        }
        while (($file = readdir($dir)) !== false) {
            $fullPath = $this->origDir . '/' . $file;
            if (is_file($fullPath) && strpos($file, 'query_arrays') !== false) {
                $size = filesize($fullPath);
                $handle = fopen($fullPath, "r");
                $DBEntries = unserialize(fread($handle, ($size + 128)));

                fclose($handle);
            }
        }
        closedir($dir);
    }

    private function getClassArrayCount($class)
    {
        $s = new \ReflectionClass($class);
        $arrays = $s->getProperties();
        /**
         * @var $item \ReflectionProperty
         */
        foreach ($arrays as $item) {
            if ($class->{$item->getName()}) {
                echo $item->getName() . " :" . count($class->{$item->getName()}) . "\n";
            }
        }

    }
}
