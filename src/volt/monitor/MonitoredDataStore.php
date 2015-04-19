<?php
namespace volt\monitor;



class MonitoredDataStore{
    private $plugins;
    public function __construct(){
        $this->plugins = [];
    }
    public function addWrite($plugin, $name, $value){
        $this->createPlugin($plugin);
        $this->plugins[$plugin]["writes"][] = ["name" => $name, "value" => $value, "time" => time()];
    }
    public function addRead($plugin, $name, $value){
        $this->createPlugin($plugin);
        $this->plugins[$plugin]["reads"][] = ["name" => $name, "value" => $value, "time" => time()];
    }
    public function createPlugin($name){
        if(!isset($this->plugins[$name])){
            $this->plugins[$name] = ["reads" => [], "writes" => []];
        }
    }
    public function getIterator(){
        return $this->plugins;
    }
}