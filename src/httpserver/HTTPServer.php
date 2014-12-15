<?php
namespace httpserver;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class HTTPServer extends PluginBase{
    public $server, $bound, $task;
    /** @var  Config */
    public static $serverConfig;
    public function onEnable(){
        $this->saveDefaultConfig();
        self::$serverConfig = $this->getConfig();
        $this->getLogger()->warning("HTTPServer 3.0 preview is mystical, magical and buggy.");
        if(!is_dir($this->getServer()->getDataPath() . "HTTPServer")) mkdir($this->getServer()->getDataPath() . "HTTPServer");
        $this->bound = [];
        $this->server = new ServerTask($this->getServer()->getDataPath() . "HTTPServer", $this->getServer()->getLoader(), $this->getServer()->getLogger());
        if ($this->server->stop === false){
            $this->task = new PostCollectionTask($this);
            $this->getServer()->getScheduler()->scheduleRepeatingTask($this->task, 5);
        }
    }
    public function addValue($n, $v){
        /*if($this->server instanceof ServerTask){
            $this->server->synchronized(function($thread, $n, $v){
                $c = unserialize($thread->vars);
                $c[$n] = $v;
                $thread->vars = serialize($c);
            }, $this->server, $n, $v);
            return true;
        }
        return false;*/
        $this->getLogger()->warning(TextFormat::DARK_AQUA . 'addValue($n, $v)' . TextFormat::RESET . TextFormat::YELLOW . " is no longer supported.");
    }
    public function bindTo($n, Callable $func){
        /*if($this->server instanceof ServerTask){
            $this->bound[$n][] = $func;
            return true;
        }
        return false;*/
        $this->getLogger()->warning(TextFormat::DARK_AQUA . 'bindTo($n, Callable $func)' . TextFormat::RESET . TextFormat::YELLOW . " is no longer supported.");
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