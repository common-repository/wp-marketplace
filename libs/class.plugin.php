<?php
if(!class_exists('ahm_plugin')){
class ahm_plugin{
    protected $plugin_dir;
    protected $plugin_url;
    function ahm_plugin($plugin){               
             $this->plugin_dir = str_replace("libs","",dirname(__FILE__));
             if(function_exists('plugins_url'))
             $this->plugin_url = plugins_url().'/'.$plugin;
    }

    function load_styles(){
        
        $dir = is_admin()?'admin':'site';
        $cssdir = $this->plugin_dir.'css/'.$dir.'/';
        $cssurl = $this->plugin_url.'/css/'.$dir.'/';
        $files = scandir($cssdir);
        foreach($files as $file){
            if(!is_dir($file)&&end(explode(".",$file))=='css')
            wp_enqueue_style(uniqid(),$cssurl.$file);
        }
    }
    
    function load_scripts(){
        
        wp_enqueue_script('jquery');
        $dir = is_admin()?'admin':'site';
        $jsdir = $this->plugin_dir.'js/'.$dir.'/';
        $jsurl = $this->plugin_url.'/js/'.$dir.'/';
        $files = scandir($jsdir);
        foreach($files as $file){
            if(!is_dir($file)&&end(explode(".",$file))=='js')
            wp_enqueue_script(uniqid(),$jsurl.$file);
        }
    }
    
    function load_modules(){       
        
        $mdir = $this->plugin_dir.'modules/';
        
        $files = scandir($mdir);
        foreach($files as $file){
            if(!is_dir($file)&&end(explode(".",$file))=='php')
            include($mdir.$file);
        }
    }
    
    
    
}

}

?>