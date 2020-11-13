<?php

include "app.inc";

use core\app;
use app\models\Country;
use app\models\CountryStatistic;

class console
{
    public static function main($argv)
    {
        $methodName = $argv[1];
        call_user_func('self::'.$methodName);
    }
    
    public static function testinsert()
    {
        $app = app::init();
        $CS = $app->getModel(Country::class);
        $CS->name = 'Россия';
        $CS->save();
        
        $CS = $app->getModel(Country::class);
        $CS->name = 'Нигерия';
        $CS->save();
        
        $CS = $app->getModel(Country::class);
        $CS->name = 'Шотландия';
        $CS->save();
        
        echo $CS->id." saved! \n";
        
    }
    public static function testfetchone()
    {
        $app = app::init();
        $CS = $app->getModel(Country::class,[['=','id','7']]);
        
        if($CS->id) { 
            echo $CS->name;
        }
        else {
            echo "Record with id=7 is not found \n";
        }
        
    }
     public static function testfetchall()
    {
        $app = app::init();
        $CS = $app->getModel(Country::class);
        
        $allCS = $CS->findAll([["=","id","8"]]);
        foreach ($allCS as $csElem) {
            echo $csElem->id." \n";
        }
        
    }
    
    public static function migrate()
    {
        $app = app::init();
        
        /*Страны сборные*/
        $sql = "
        CREATE TABLE `country` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(50) NOT NULL,
	PRIMARY KEY (`id`),
        UNIQUE INDEX `name` (`name`)
        )
        COLLATE='utf8_general_ci'
        ENGINE=InnoDB
        ;";
        if(!$app->database->execute($sql)){
            echo mysqli_error($app->database->db);
        }
        /*Статистика*/
        $sql= "
        CREATE TABLE `country_statistic` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`country_id` INT NOT NULL,
	`chamionats_count` INT NULL,
	`games_played` INT NULL,
	`win_count` INT NULL,
	`drow_count` INT NULL,
	`lose_count` INT NULL,
	`goals_scorred` INT NULL,
	`goals_missed` INT NULL,
	`points_total` INT NULL,
	`percentage` FLOAT NULL,
	PRIMARY KEY (`id`),
        INDEX `FK_country_statistic_country` (`country_id`)
        )
        COLLATE='utf8_general_ci'
        ENGINE=InnoDB    
        ;";
         if(!$app->database->execute($sql)){
            echo mysqli_error($app->database->db);
        }
        /*Группы*/
        $sql="CREATE TABLE `groups` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`country_id` INT NOT NULL,
	`group_id` INT NOT NULL,
	PRIMARY KEY (`id`)
        )
        COLLATE='utf8_general_ci'
        ENGINE=InnoDB
        ;
        ";
         if(!$app->database->execute($sql)){
            echo mysqli_error($app->database->db);
        }
        /*Матчи*/
        $sql="CREATE TABLE `matches` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`home_team_id` INT(11) NOT NULL,
	`visitor_team_id` INT(11) NOT NULL,
	`status` SET('planned','running','played') NOT NULL,
	`home_score` INT(11) NOT NULL,
	`visitors_score` INT(11) NOT NULL,
	`stage` SET('group','quarterfinal','semifinal','final','winner') NOT NULL,
	PRIMARY KEY (`id`)
        )
        ENGINE=InnoDB
        ;
        ";
         if(!$app->database->execute($sql)){
            echo mysqli_error($app->database->db);
        }
        
        
        
        echo "APP DATA INITIALIZED! \n";
    }
    public static function loaddata()
    {
        $app = app::init();
        
        $file_content = fopen("data.csv","r");
        while(!feof($file_content)) {
            
            $line = fgets($file_content);
            $lineData = explode(',', $line);
            
            $country = $app->getModel(Country::class);
            $country->name = $lineData[1];
            $country->save();
            if($country->id>0) {
                echo "Country " . $country->id . " saved \n";
            }
            else die("Something go wrong \n");
            
            $countryStatistic = $app->getModel(CountryStatistic::class);
            $countryStatistic->country_id = $country->id;
            $countryStatistic->chamionats_count = $lineData[2];
            $countryStatistic->games_played = $lineData[3];
            $countryStatistic->win_count = $lineData[4];
            $countryStatistic->drow_count = $lineData[5];
            $countryStatistic->lose_count = $lineData[6];
            $countryStatistic->goals_scorred = $lineData[7];
            $countryStatistic->goals_missed = $lineData[8];
            $countryStatistic->points_total = $lineData[10];
            $countryStatistic->percentage = $lineData[11];
            
            $countryStatistic->save();
            
            if($countryStatistic->id>0) {
                echo "Country statistic for " . $country->id . " saved \n";
            }
            
        }
        
        
    }
    
}
console::main($argv);
