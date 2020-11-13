<?php

namespace app\models;

use core\model;

class CountryStatistic extends model
{
    public $table = 'country_statistic';
    
    public function attributes() {
        return [
            'country_id',
            'chamionats_count',
            'games_played',
            'win_count',
            'drow_count',
            'lose_count',
            'goals_scorred',
            'goals_missed',
            'points_total',
            'percentage'
        ];
    }
}
