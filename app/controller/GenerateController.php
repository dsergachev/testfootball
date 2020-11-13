<?php

namespace app\controller;

use core\app;
use core\controller;
use core\view;
use app\models\Country;
use app\models\Groups;
use app\models\Matches;
use app\helpers\TeamHelper;

class GenerateController extends controller
{
    public function index()
    {
        TeamHelper::restartChampionat();
        
        $allCountryID = $GLOBALS['app']->getModel(Country::class)->findAll([],'id');
        shuffle($allCountryID);
        
        for($i=0 ; $i<8 ; $i++) {
            $group = [];
            for($j=0 ; $j<4 ; $j++) {
                $teamId = $allCountryID[$i*4+$j];
                $newGroup = $GLOBALS['app']->getModel(Groups::class);
                $newGroup->country_id = $teamId;
                $newGroup->group_id = $i;
                $newGroup->save();
                $group[]=$teamId;
            }
            for($k=0 ; $k<4 ; $k++){
                for($l=$k+1 ; $l<4 ; $l++) {
                    $newMatch = $GLOBALS['app']->getModel(Matches::class);
                    $newMatch->home_team_id=$group[$k];
                    $newMatch->visitor_team_id=$group[$l];
                    $newMatch->home_score=0;
                    $newMatch->visitors_score=0;
                    $newMatch->status='planned';
                    $newMatch->stage='group';
                    
                    $newMatch->save();
                    
                }
            }
        }
        
        $allGroups = $GLOBALS['app']->getModel(Groups::class)->findAll();
        
        $this->content = $this->renderPartial('tournament',['groups'=>$allGroups]);
    }
    
}

?>