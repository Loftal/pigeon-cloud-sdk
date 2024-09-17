<?php

namespace PigeonCloudSdk;

class PigeonCondition
{
    private array $condition;
    public function __construct()
    {
        $this->condition = [];
    }

    public function add($and_or,  $field, $condition, $value=null)
    {
        $this->condition[] = [
            'and_or' => $and_or,
            'field' => $field,
            'condition' => $condition,
            'value' => $value
        ];
    }

    public function toArray()
    {
        return $this->condition;
    }

}