<?php

namespace app\helpers;

use app\models\CountryStatistic;
use app\models\Matches;

class TeamHelper
{
    public static function getTeamPowers($team_id)
    {
        $coutryStatistic = $GLOBALS['app']->getModel(CountryStatistic::class)->findOne([['=','country_id',$team_id]]);
        
        $deffencePower = 90 / ($coutryStatistic->goals_missed / $coutryStatistic->games_played);
        $offencePower = 90 / ($coutryStatistic->goals_scorred / $coutryStatistic->games_played);
        
        return ['deffence'=>$deffencePower,'offence'=>$offencePower];
        
    }
    public static function runMatch($homeTeamId, $visitorTeamId)
    {
        $team1power = TeamHelper::getTeamPowers($homeTeamId);
        $team2power = TeamHelper::getTeamPowers($visitorTeamId);
        
        $score1 = 0;
        $score2 = 0;
        
        for($i=0 ; $i<90 ;$i++) {
            if($i%2==0) {
                // Атака второй команды
                $deffence1 = rand(0, 100);
                $offence1 = rand(0, 100);
                $deffence2 = rand(0, 100);
                $offence2 = rand(0, 100);
                if($team1power['deffence']<$deffence1 
                        && $team1power['offence']>$offence1 
                        && $team2power['deffence']>$deffence2
                        && $team2power['offence']<$offence2) {
                    //ГОЛ!
                    $score2++;
                }
            }
            else {
                //Атака первой команды
                $deffence2 = rand(0, 100);
                $offence2 = rand(0, 100);
                $deffence1 = rand(0, 100);
                $offence1 = rand(0, 100);
                
                if($team2power['deffence']<$deffence2 
                        && $team2power['offence']>$offence2 
                        && $team1power['deffence']>$deffence1
                        && $team1power['offence']<$offence1) {
                    //ГОЛ!
                    $score1++;
                }
            }
            }
            // Закончился матч
            return[$homeTeamId=>$score1,$visitorTeamId=>$score2];
    }
    public static function groupStageRepport()
    {
        $sql = "select teamid, country.name as teamname, gpid,sum(wincount) as totalwin,sum(drawcount) as totaldraw,sum(losecount) as totallose,  sum(resulttable.pointtotal) as resultpoint,sum(resulttable.goalsscorred) as totalscorred,sum(resulttable.goalsmissed) as totalmissed, sum(resulttable.goalsscorred)-sum(resulttable.goalsmissed) as resultgoals from (
                    select  gpid, home_team_id as teamid,sum(win) as wincount,sum(draw) as drawcount,sum(lose) as losecount, sum(homepoint) as pointtotal, sum(home_score) as goalsscorred, sum(visitors_score) as goalsmissed from 
                    (
                            select home_team_id,visitor_team_id,home_score,visitors_score,gp.group_id as gpid, 
                            if(home_score > visitors_score,3,if(home_score=visitors_score,1,0)) as homepoint,
                            if(home_score < visitors_score,3,if(home_score=visitors_score,1,0)) as visitorspoint,
                            if(home_score > visitors_score,1,0) as win,
                            if(home_score = visitors_score,1,0) as draw,
                            if(home_score < visitors_score,1,0) as lose


                            from matches
                            left join groups gp on (matches.home_team_id = gp.country_id)
                    ) as results group by home_team_id

                    union 
                    select  gpid, visitor_team_id as teamid,sum(win) as wincount,sum(draw) as drawcount,sum(lose) as losecount, sum(visitorspoint) as pointtotal, sum(visitors_score) as goalsscorred, sum(home_score) as goalsmisseg  from 
                    (
                            select home_team_id,visitor_team_id,home_score,visitors_score,gp.group_id as gpid, 
                            if(home_score > visitors_score,3,if(home_score=visitors_score,1,0)) as homepoint,
                            if(home_score < visitors_score,3,if(home_score=visitors_score,1,0)) as visitorspoint,
                            if(home_score < visitors_score,1,0) as win,
                            if(home_score = visitors_score,1,0) as draw,
                            if(home_score > visitors_score,1,0) as lose

                            from matches
                            left join groups gp on (matches.home_team_id = gp.country_id)
                    ) as results group by visitor_team_id
                    ) as resulttable
                    left join country on (resulttable.teamid = country.id)
                     group by teamid order by gpid, resultpoint desc, resultgoals desc
        ";
        $result = $GLOBALS['app']->database->fetchAll($sql);
        return $result;
    }
    public static function plannigQuarterfinals($result)
    {
        $qaurterfinalTeams = [];
        
        for($i=0 ; $i<8 ; $i++){
            for($j=0 ; $j<4; $j++) {
                if($j==0) {
                    $qaurterfinalFirst[]=$result[$i*4+$j]['teamid'];
                }
                if($j==1) {
                    $qaurterfinalSecond[]=$result[$i*4+$j]['teamid'];
                }
            }
        }
        
        for($i=0;$i<8;$i++) {
            $newMatch = $GLOBALS['app']->getModel(Matches::class);
            $newMatch->home_team_id = $qaurterfinalFirst[$i];
            if($i==7) {
                $k=0;
            }
            else {
                $k=$i+1;
            }
            $newMatch->visitor_team_id = $qaurterfinalSecond[$k];
            $newMatch->status='planned';
            $newMatch->stage='quarterfinal';
            $newMatch->home_score = 0;
            $newMatch->visitors_score = 0;
            
            $newMatch->save();        
        }
    }
    public static function restartChampionat()
    {
        $q='truncate table matches;';
        $GLOBALS['app']->database->execute($q);
        
        $q='truncate table groups;';
        $GLOBALS['app']->database->execute($q);
    }
}
