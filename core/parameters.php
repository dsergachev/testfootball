<?php

namespace core;

class parameters
{
    public $values;
    
    /*
     * Загрузка параметров из конфигурационного файла parameters.json
     */
    public function get($value)
    {
        return $this->values[$value];
    }
    /*
     * Инициализация
     */
    public function __construct() {
        $parametersJson = file_get_contents('parameters.json');
        $this->values = json_decode($parametersJson, 1);
    }
}

