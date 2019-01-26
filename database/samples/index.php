<?php
require_once '../helpers/helpers.php';
//ini_set('max_execution_time', '60');
require_once 'ProductRepo.php';

$startExecTime = microtime(true);
$startMemory = memory_get_usage();

organizeData(organizeEmbCodes());


function organizeData($codes)
{
    $filteredData = [];
    $i = 1;

    $sep = chr(9);
    $handle = @fopen('../csv/products.tsv', "r");
    $header = fgetcsv($handle, 1024, $sep);
    $productRepo = new ProductRepo();
    $productRepo->setEmbCodeList($codes);
    while (($data = fgetcsv($handle, 8192, $sep)) != false) {
        $product = new Product();
        while (list($key, $item) = each($data)) {
            switch ($header[$key]) {
                case 'product_name':
                    $product->setName($item);
                    break;
                case 'brands':
                    $product->setBrands($productRepo->addBrands($item));
                    break;
                case 'categories':
                    $product->setCategories($productRepo->addCategories($item));
                    break;
                case 'categories_tags':
                    $product->setCategories($productRepo->addCategories($item, ';'));
                    break;
                case 'emb_codes_tags':
                    if (!is_null($item) && !empty($item)) {
                        $product->setEmbCode($productRepo->addEmbCodes($item));
                    }
                    break;
                case 'ingredients_text':
                    $product->setIngredientsText($item);
                    break;
                case 'ingredients':
                    $product->setIngredients($productRepo->addIngredients($item));
                    break;
                case 'nova_group':
                    $product->setNovaGroup(intval($item));
                    break;
                case 'nutrient_levels_tags':
                    $product->setNutrientLevels($productRepo->addNutrientLevels($item));
                    break;
                case 'nutriments':
                    $product->setNutriments($productRepo->addNutriments($item));
                    break;
                case 'nutrition_grades':
                    $product->setNutritionGrade($item);
                    break;
                case 'packaging':
                    $product->setPackaging($productRepo->addPackaging($item));
                    break;
            }
//            $filteredData[$header[$key]][] = $item;
        }
        $product->unsetCategoryIndex();
        $productRepo->add($product);
        $i++;
        if ($i == 10) {
            break;
        }
    }
    fclose($handle);

    dd($productRepo->getProducts());
}

function organizeEmbCodes()
{
    $codes = [];
    $sep = ',';
    $handle = @fopen('codes.csv', "r");
    fgetcsv($handle, 1024, $sep);
    $header = array_flip(['dept', 'emb', 'siret', 'address', 'postcode', 'town', 'category', 'activity', 'species']);
    while (($data = fgetcsv($handle, 8192)) != false) {
        $codes[str_replace('_', '-', slugify(sprintf('FR %s EC', $data[$header['emb']])))] = new Packager(
            $data[$header['emb']],
            $data[$header['siret']],
            $data[$header['address']],
            $data[$header['postcode']],
            $data[$header['town']],
            $data[$header['category']],
            $data[$header['activity']],
            $data[$header['species']]
        );
    }
    fclose($handle);
//    dd(array_shift(array_chunk($codes,100)));

    return $codes;
}

function getEmbCodesFromFiles()
{
    $origDir = "codes";
    $headerIsWritten = false;

    $dir = opendir($origDir);
    if (!$dir) {
        die(sprintf("%s could not be read.", $origDir));
    }
    $handle2 = @fopen('codes.csv', "w");

    while (($file = readdir($dir)) !== false) {
        if (is_file($origDir . '/' . $file)) {
            $sep = ',';
            $handle = @fopen($origDir . '/' . $file, "r");
            $header = fgets($handle, 1024);
            if (!$headerIsWritten) {
                $headerIsWritten = true;
                fwrite($handle2, $header);
            }
            while (($data = fgets($handle, 8192)) != false) {
                fwrite($handle2, $data);
            }
            fclose($handle);
        }
    }
    fclose($handle2);
}