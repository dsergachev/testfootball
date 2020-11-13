<?php

namespace app\controller;

use core\app;
use core\controller;
use core\view;
use app\models\Country;
use app\models\Groups;
use app\models\Matches;
use app\helpers\TeamHelper;

class RunController extends controller
{
    public function groupstage()
    {
        $allMatches = $GLOBALS['app']->getModel(Matches::class)->findAll();
        
        foreach ($allMatches as $match)
        {
        
            $matchresult = TeamHelper::runMatch($match->home_team_id, $match->visitor_team_id);
       
            $match->status = 'played';
            $match->home_score = $matchresult[$match->home_team_id];
            $match->visitors_score = $matchresult[$match->visitor_team_id];
            $match->save(true);
            
        }
        
        $groupStageReport = TeamHelper::groupStageRepport();
        TeamHelper::plannigQuarterfinals($groupStageReport);
        
        
        $this->content = $this->renderPartial('groupstageresult',['result' => $groupStageReport]);
    }
    public function playoff($stage=0)
    {
        
        $stages=['quarterfinal','semifinal','final','winner'];
        
        $allMatches = $GLOBALS['app']->getModel(Matches::class)->findAll([['=','stage',$stages[$stage]]]);
        
        $winners = [];
        
        foreach ($allMatches as $match)
        {
        
            do{
            $matchresult = TeamHelper::runMatch($match->home_team_id, $match->visitor_team_id);
            } while($matchresult[$match->home_team_id]==$matchresult[$match->visitor_team_id]);
            
            $match->status = 'played';
            $match->home_score = $matchresult[$match->home_team_id];
            $match->visitors_score = $matchresult[$match->visitor_team_id];
            $match->save(true);
            
            if($match->home_score>$match->visitors_score) {
                $winners[]=$match->home_team_id;
            }
            else {
                $winners[]=$match->visitor_team_id;
            }
            if(count($winners)%2==0) {
                //Два победителя идут в слудующий раунд
                $newMatch = $GLOBALS['app']->getModel(Matches::class);
                $newMatch->home_team_id = $winners[0];
                $newMatch->visitor_team_id = $winners[1];
                $newMatch->status='planned';
                $newMatch->stage=$stages[$stage+1];
                $newMatch->home_score = 0;
                $newMatch->visitors_score = 0;
                $newMatch->save();
                
                $winners=[];
                
            }
            
            $homeTeam = $GLOBALS['app']->getModel(Country::class)->findOne([['=','id',$match->home_team_id]]);
            $visitorTeam = $GLOBALS['app']->getModel(Country::class)->findOne([['=','id',$match->visitor_team_id]]);
            
            $results[]=[
                'id'=>$match->id,
                'hometeam'=>$homeTeam->name,
                'visitorteam'=>$visitorTeam->name,
                'homescore'=>$match->home_score,
                'visitorsscore'=>$match->visitors_score
            ];
            
        }
        
        $this->content = $this->renderPartial('playoff',['results' => $results,'stage'=>$stage]);
    }
    
}

?>