<?php
require_once '../helpers/helpers.php';
ini_set('max_execution_time', '8000');
require_once 'ProductRepo.php';

$startExecTime = microtime(true);
$startMemory = memory_get_usage();

$data = organizeData(organizeEmbCodes(), true, 10);
test($data);

//readData();

function readData()
{
    $size = filesize('data1.txt');
    $handle = @fopen('data1.txt', "r");
    $phpData = unserialize(fread($handle, ($size + 128)));
    fclose($handle);
//    dd(count(unserialize($s)));
    $i = 1;

    foreach ($phpData as $data) {

        dd($data);
        if ($i == 20) {
            break;
        }
        $i++;

    }
}

function test($data)
{
    $f = $data->getProducts();
    dd($f);
    foreach ($f as $d) {
        if ($d->getImage()) {
            dd($d);
        }
    }
}

function organizeData($codes, $test = true, $amountOfRecordsProcessed = 10)
{
    $filteredData = [];
    $i = 1;

    $sep = chr(9);
    $handle = @fopen('../csv/products.tsv', "r");
    $header = fgetcsv($handle, 1024, $sep);
    $productRepo = new ProductRepo();
    $productRepo->setEmbCodeList($codes);
    while (($data = fgetcsv($handle, 16382, $sep)) != false) {
        $product = new Product();
        $tag = $id = '';

        while (list($key, $item) = each($data)) {
            switch ($header[$key]) {
                case '_id':
                    $id = $item;
                    $product->setIdentifier($id);
                    break;
                case 'product_name':
                    $product->setName(ucfirst(trim($item)));
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
                case 'codes_tags':
                    $tag = $item;
                    break;
                case 'images':
                    $imageData = explode(';', $item);
                    if (count($imageData) === 4) {
                        $product->setImage($productRepo->processImage($id, $tag, $imageData));
                    } else {
                        $product->setImage(null);
                    }
                    break;

            }
//            $filteredData[$header[$key]][] = $item;
        }
        $product->unsetCategoryIndex();
        $productRepo->add($product);
        $i++;

        if ($test) {
            if ($i == $amountOfRecordsProcessed) {
                break;
            }
        } else {
            if ($i % 10000 === 0) {
                writeProducts($i, $productRepo);
            }
        }
    }
    fclose($handle);

    if (!$test) {
        writeProducts($i, $productRepo);
    }

    return $productRepo;
}

function writeProducts($i, ProductRepo $productRepo)
{
    $handle2 = @fopen(sprintf('data%s.txt', round($i / 10000)), "w");
    fwrite($handle2, serialize($productRepo->flushProducts()));
    fclose($handle2);
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

execution_time($startExecTime);
execution_memory($startMemory);