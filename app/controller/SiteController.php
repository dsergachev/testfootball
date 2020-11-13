<?php

namespace app\controller;

use core\app;
use core\controller;
use core\view;

class SiteController extends controller
{
    public function index()
    {
        $this->content = $this->renderPartial('index',['message'=>'Чемпионат мира по футболу']);
    }
    
}

?>