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
        $this->seedNonProductForeignKeyedItems();
        $this->seedProducts();
        $this->seedProductDependencies();
    }

    private function seedNonProductForeignKeyedItems()
    {
        $data = $this->initSeeding();
        $this->seedPackagers($data->packagers);
        unset($data->packagers);
        $this->seedChunk($data->productBrands, \App\Models\Shop\ProductBrand::class);
        $this->seedChunk($data->productCategories, \App\Models\Shop\ProductCategory::class);
        $this->seedChunk($data->productIngredients, \App\Models\Shop\ProductIngredient::class, 100);
        $this->seedChunk($data->productPackages, \App\Models\Shop\ProductPackage::class, 25);
        $this->seedChunk($data->productNutriments, \App\Models\Shop\ProductNutriment::class, 15);
        $this->seedChunk($data->productNutrientLevels, \App\Models\Shop\ProductNutrientLevel::class, 1);
    }

    private function insertProducts(DBEntries $DBEntries)
    {
        $this->seedChunk($DBEntries->productNutritionInfo,\App\Models\Shop\ProductNutritionInfo::class,15);
        $this->seedChunk($DBEntries->productBrandRecords,\App\Models\Shop\ProductBrandRecord::class,15);
        $this->seedChunk($DBEntries->productCategoryRecords,\App\Models\Shop\ProductCategoryRecord::class,25);
        $this->seedChunk($DBEntries->productIngredientRecords,\App\Models\Shop\ProductIngredientRecord::class,50);
        $this->seedChunk($DBEntries->productPackageRecords,\App\Models\Shop\ProductPackageRecord::class,25);
        $this->seedChunk($DBEntries->productNutrimentRecords,\App\Models\Shop\ProductNutrimentRecord::class,60);
        $this->seedChunk($DBEntries->productNutrientLevelRecords,\App\Models\Shop\ProductNutrientLevelRecord::class,25);
    }

    private function seedProducts()
    {
        $fullPath = $this->origDir . '/products_000.txt' ;
        $size = filesize($fullPath);
        $handle = fopen($fullPath, "r");
        $products = unserialize(fread($handle, ($size + 128)));
        $this->seedChunk($products,\App\Models\Shop\Product::class,15);
        fclose($handle);
    }

    private function seedProductDependencies()
    {
        $dir = opendir($this->origDir);
        if (!$dir) {
            die(sprintf("%s could not be read.", $this->origDir));
        }

        while (($file = readdir($dir)) !== false) {
            $fullPath = $this->origDir . '/' . $file;
            if (is_file($fullPath) && strpos($file, 'dependents') !== false) {
                $size = filesize($fullPath);
                $handle = fopen($fullPath, "r");
                /**
                 * @var $DBEntries \DBEntries
                 */
                $DBEntries = unserialize(fread($handle, ($size + 128)), [DBEntries::class]);
                $this->insertProducts($DBEntries);
                fclose($handle);
            }
        }
        closedir($dir);

    }

    private function seedChunk($data, $model, $nbChunks = 50)
    {
        $chunks = array_chunk($data, $nbChunks);
        foreach ($chunks as $chunk) {
            forward_static_call(sprintf('%s::insert', $model), $chunk);
        }
    }

    private function seedPackagers($packagers)
    {
        $this->seedChunk($packagers, \App\Models\Shop\ProductPackager::class);

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
        $repoFile = $this->origDir . '/query_init_000.txt';
        $size = filesize($repoFile);
        $handle = fopen($repoFile, "r");
        $DBEntriesRepo = unserialize(fread($handle, ($size + 128)));
        fclose($handle);
        return $DBEntriesRepo;
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
