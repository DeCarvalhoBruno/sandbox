<?php namespace App\Traits;

trait Presentable
{
    /**
     * @param $columns
     * @return array
     */
    public function getColumnInfo($columns)
    {
        $sortable = array_flip($this->sortable);
        $result = [];
        foreach ($columns as $name => $label) {
            $result[$name] = ['name' => $name, 'label' => $label, 'sortable' => isset($sortable[$name])];
        }
        return $result;
    }

}