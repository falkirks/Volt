<?php
namespace httpserver;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class HTTPServer extends PluginBase{
    public $server, $bound, $task;
    public function onEnable(){
        if(!is_dir($this->getServer()->getDataPath() . "HTTPServer")) mkdir($this->getServer()->getDataPath() . "HTTPServer");
        $this->bound = [];
        $this->server = new ServerTask($this->getServer()->getDataPath() . "HTTPServer");
        if ($this->server->stop === false){
            $this->getLogger()->info("[SUCCESS] HTTP Server Status: " . TextFormat::GREEN . "Active\n");
            $this->task = new PostCollectionTask($this);
            $this->getServer()->getScheduler()->scheduleRepeatingTask($this->task, 10);
        }
        else $this->getLogger()->warning("HTTP Server Status: " . TextFormat::RED . "Failed\n");

    }
    public function addValue($n, $v){
        if($this->server instanceof ServerTask){
            $this->server->synchronized(function($thread, $n, $v){
                $c = unserialize($thread->vars);
                $c[$n] = $v;
                $thread->vars = serialize($c);
            }, $this->server, $n, $v);
            return true;
        }
        return false;
    }
    public function bindTo($n, Callable $func){
        if($this->server instanceof ServerTask){
            $this->bound[$n][] = $func;
            return true;
        }
        return false;
    }
    public function unbindServer(){
        $this->server->synchronized(function($thread){
            $thread->stop();
        }, $this->server);
    }
    public function onDisable(){
        if($this->server instanceof ServerTask){
            $this->getLogger()->info("Killing server...");
            $this->unbindServer();
        }
    }
}