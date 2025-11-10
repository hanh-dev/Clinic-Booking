<?php 
class App { 

    protected $controller = "Home"; 
    protected $action = "show";
    protected $param = [];     
    function __construct() {
        $arr = $this->UrlProcess(); 

        // Xử lý controllers
        if (file_exists("./app/controllers/".$arr[0].".php")) { 
            $this->controller = $arr[0];
            unset($arr[0]); 
        }
        require_once "./app/controllers/".$this->controller.".php"; 
        $this->controller = new $this->controller;

        
        //Xử lý Action
        if (isset($arr[1])) {
            if (method_exists($this->controller,$arr[1])) { 
                $this->action = $arr[1]; 
            }
            unset($arr[1]); 
        }

        // Xử lý param 
        $this->param = $arr?array_values($arr):[];
        call_user_func_array([$this->controller, $this->action], $this->param);

    }

    function UrlProcess() {
        if (isset($_GET["url"])) { 
            return explode("/", filter_var(trim($_GET["url"], "/"))); 
        }
    }
}
?>