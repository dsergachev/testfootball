<?php 
include "app.inc";

use core\app;

$app = app::init();

$GLOBALS['app'] = $app;

$action = (isset($_GET['a'])?$_GET['a']:'site/index');
unset($_GET['a']);
$parameter = $_GET['p'];

$actionArray = explode('/', $action);

$controllerName = $actionArray[0];
$actionName = $actionArray[1];

$controllerName = "\app\controller".'\\'.ucfirst(strtolower($controllerName))."Controller";
$controller = new $controllerName;

$controller->$actionName($parameter);

$content = $controller->content;
$layout  = $controller->layout;

if(!$layout) {
    $layout='empty';
}

$app->view->render($layout,['header'=>'ТЗ Мирафокс','content'=> $content]);

?>