<?php
namespace volt;

use Handlebars\Loader;

class TemplateLoader implements Loader{
    private $dir;
    private $templates;
    private $client;
    public function __construct(ClientTask $client, $dir, $templates = []){
        $this->client = $client;
        $this->dir = $dir;
        $this->templates = is_array($templates) ? serialize($templates) : $templates;
    }
    public function addTemplate($path, $template){
        if(is_file($this->dir . $path)) return false;
        $templates = unserialize($this->templates);
        $templates[$path] = $template;
        $this->templates = serialize($templates);
        return true;
    }
    public function getTemplate($name){
        return isset($this->templates[$name]) ? $this->templates[$name] : null;
    }
    public function load($name){
        $templates = unserialize($this->templates);
        if (isset($templates[$name])) {
            return $templates[$name];
        }
        else return $this->client->getFile($name);
    }
}