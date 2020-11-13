<?php

namespace app\models;

use core\model;

class Matches extends model
{
    public $table = 'matches';
    
    public function attributes() {
        return [
            'home_team_id',
            'visitor_team_id',
            'status',
            'home_score',
            'visitors_score',
            'stage'
        ];
    }
}
