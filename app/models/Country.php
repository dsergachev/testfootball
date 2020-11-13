<?php

namespace app\models;

use core\model;

class Country extends model
{
    public $table = 'country';
    
    public function attributes() {
        return [
            'name',
        ];
    }
}
