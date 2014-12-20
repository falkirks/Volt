<?php
namespace volt;

use Handlebars\Loader;

class TemplateLoader implements  Loader{
    private $dir;
    private $templates;
    private $client;
    public function __construct(ClientTask $clientTask, $dir){
        $this->client = $clientTask;
        $this->dir = $dir;
        $this->templates = [];
    }

    public function addTemplate($path, $template){
        if(is_file($this->dir . $path)) return false;
        $this->templates[$path] = $template;
        return true;
    }

    public function load($name){
        if (isset($this->templates[$name])) {
            return $this->templates[$name];
        }
        else return $this->client->getFile($name);
    }
}