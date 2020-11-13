<?php

namespace core;

include "app.inc";

use core\database;
use core\parameters;

class app
{
    /**
     *
     * @var type 
     */
    public $database;
    
    /**
     *
     * @var type 
     */
    public $parameters;
    
    /**
     *
     * @var type 
     */
    public $view;
    
    /**
     *  RUN APP
     */
    public function run()
    {
       $this->parameters = new parameters();
       $this->database = new database($this->parameters);
       $this->view = new view('layout');
    }
    
    /**
     * INIT APP
     * 
     * @return \core\app
     */
    public static function init()
    {
        $app = new app();
        $app->run();
        
        return $app;
    }
    
    public static function parameters()
    {
        return new parameters();
    }
    
    public function getModel($modelName, $fetchParams=[])
    {
        $model = new $modelName($this->parameters, $fetchParams);
        
        return $model;
    }
    
    public function __destruct() {
        
    }
}

