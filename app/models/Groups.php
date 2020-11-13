<?php

namespace app\models;

use core\model;

class Groups extends model
{
    public $table = 'groups';
    
    public function attributes() {
        return [
            'country_id',
            'group_id'
        ];
    }
}
