<?php

namespace core;

use core\app;

class view
{
    private $viewPath;    

    private $data = array();

    private $renderFile = FALSE;

    public function __construct($template=false, array $params=[])
    {
        $this->viewPath = getcwd().app::parameters()->get('app')['viewPath'];
        
        if($template)
        {
            $this->render($template, $params);
        }
    }

    public function assign($variable, $value)
    {
       
    }
    
    public function render($template,array $variables=[]) 
    {
        $data = [];
        
        if (count($variables)) {
            foreach ($variables as $name => $value) {
                 $data[$name] = $value;
            }
        }
        
        try {
            $file = $this->viewPath . '/' . strtolower($template) . '.php';

            if (file_exists($file)) {
        
                extract($data);
                include($file);

            } else {
                throw new  \Exception('Template ' . $template . ' not found!');
            }
        }
        catch (Exception $e) {
            echo $e->errorMessage();
        } 
    }
    
    public function renderPartial($template, $params)
    {
        ob_start();
        $this->render($template, $params);
        return ob_get_clean();
    }

    
}
?>
