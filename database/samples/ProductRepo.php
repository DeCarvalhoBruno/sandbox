<?php
require_once 'Product.php';
require_once 'Brand.php';
require_once 'ProductCategory.php';
require_once 'Packager.php';
require_once 'Ingredient.php';
require_once 'Nutriment.php';

class ProductRepo
{
    /**
     * @var array
     */
    private $productIndex = [];
    /**
     * @var array
     */
    private $brandIndex = [];
    /**
     * @var array
     */
    private $categoryIndex = [];
    /**
     * @var array
     */
    private $embCodeIndex = [];
    /**
     * @var array
     */
    private $ingredientsIndex = [];
    /**
     * @var array
     */
    private $nutrientLevelsIndex = [];
    /**
     * @var array
     */
    private $nutrimentsIndex = [];
    /**
     * @var array
     */
    private $packagesIndex = [];

    public function add(Product $product)
    {
        if (!isset($this->productIndex[$product->getName()])) {
            $this->productIndex[$product->getName()] = $product;
        }
    }

    public function getProducts()
    {
        return array_values($this->productIndex);
    }

    public function addBrands(string $brands): array
    {
        $tmp = explode(',', $brands);
        $result = [];
        foreach ($tmp as $brand) {
            $index = slugify($brand);
            if (!isset($this->brandIndex[$index])) {
                $this->brandIndex[$index] = $brand;
                $result[] = $brand;
            } else {
                $result[] = $this->brandIndex[$index];
            }
        }
        return $result;
    }

    public function addEmbCodes(string $embCode): Packager
    {
        if (!isset($this->embCodeIndex[$embCode])) {
            return new Packager(strtoupper($embCode));
        } else {
            return $this->embCodeIndex[$embCode];
        }
    }

    public function addCategories($categories, $delimiter = ',')
    {
        $tmp = explode($delimiter, $categories);
        $result = [];

        foreach ($tmp as $category) {
            $lang = explode(':', $category);
            if (isset($lang[1])) {
                $lg = $lang[0];
                if ($lg == 'en') {
                    $cat = ucwords(str_replace('-', ' ', $lang[1]));
                } else {
                    $cat = $lang[1];
                }
            } else {
                $cat = $lang[0];
                $lg = 'fr';
            }
            if (!isset($this->categoryIndex[$lg])) {
                $this->categoryIndex[$lg] = array();
            }
            $index = slugify($cat);
            if (!isset($this->categoryIndex[$lg][$index])) {
                $this->categoryIndex[$lg][$index] = $cat;
                $result[] = [$lg, $cat];
            } else {
                $result[] = [$lg, $this->categoryIndex[$lg][$index]];
            }
        }
        return $result;
    }

    public function setEmbCodeList($codes)
    {
        $this->embCodeIndex = $codes;
    }

    public function addIngredients($ingredients)
    {
        $productIngredients = [];
        $ingredientList = explode(';¦;', $ingredients);
        foreach ($ingredientList as $ingredient) {
            $rankings = explode(';', $ingredient);
            $ingredientObj = new Ingredient();
            $ingredientObj2 = new Ingredient();
            foreach ($rankings as $rank) {
                $tmp = explode('|', $rank);
                switch ($tmp[0]) {
                    case "id":
                        $idTag = explode(':', $tmp[1]);
                        if ($idTag[0] == 'en') {
//                            $ingredientObj2 = new Ingredient(0, ucwords(str_replace('-',' ',$idTag[1])), 'en');
                            $ingredientObj2->setName(ucwords(str_replace('-', ' ', $idTag[1])));
                            $ingredientObj2->setLanguage('en');
                        }
                        break;
                    case "text":
                        $ingredientObj->setName(ucfirst($tmp[1]));
                        $ingredientObj->setLanguage('fr');
                        break;
                    case "rank":
                        $ingredientObj->setRank($tmp[1]);
                        $ingredientObj2->setRank($tmp[1]);
                        break;
                }
            }
            $this->ingredientsIndex[$ingredientObj->getTag()] = $ingredientObj;
            $productIngredients[] = $ingredientObj;
            if ($ingredientObj2->hasName()) {
                $this->ingredientsIndex[$ingredientObj2->getTag()] = $ingredientObj2;
                $productIngredients[] = $ingredientObj2;
            }
        }
        return $productIngredients;

    }

    public function addNutrientLevels($nutrientLevels)
    {
        if (empty($nutrientLevels)) {
            return null;
        }
        $productNutrients = [];
        $nutrients = explode(';', $nutrientLevels);
        foreach ($nutrients as $nutrient) {
            $label = explode(':', $nutrient);
            if ($label[0] == 'en') {
                $productNutrients[] = $label[1];
                $this->nutrientLevelsIndex[$label[1]] = true;
            }
        }
        return $productNutrients;
    }

    public function addNutriments($nutriments)
    {
        //nova-group_100g|1;alcohol_serving|14.5;alcohol|14.5;nova-group|1;nova-group_serving|1;alcohol_unit|% vol;alcohol_value|14.5;alcohol_100g|14.5
        $nutrimentList = [];
        if (empty($nutriments)) {
            return null;
        }
        $productNutriments = explode(';', $nutriments);
        foreach ($productNutriments as $nutriment) {
            $label = explode('|', $nutriment);
            $this->nutrimentsIndex[$label[0]] = true;
            $nutrimentList[]=new Nutriment($label[0],$label[1]);
        }
        return $nutrimentList;
    }

    public function addPackaging($packaging)
    {
        $packageList=[];
        $packages = explode(',',$packaging);
        foreach($packages as $package){
            $this->packagesIndex[$package]=true;
            $packageList[]=$package;
        }
        return $packageList;
    }
}