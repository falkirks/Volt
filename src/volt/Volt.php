<?php
namespace volt;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class Volt extends PluginBase{
    /** @var  Config */
    public static $serverConfig;
    /** @var  ServerTask */
    private $server;
    public function onEnable(){
        $this->saveDefaultConfig();
        self::$serverConfig = $this->getConfig();
        $this->getLogger()->warning("Volt 3.0 preview is mystical, magical and " . TextFormat::RED . "buggy" . TextFormat::YELLOW . ".");
        if(!is_dir($this->getServer()->getDataPath() . "volt")) mkdir($this->getServer()->getDataPath() . "volt");
        $this->server = new ServerTask($this->getServer()->getDataPath() . "volt", $this->getServer()->getLoader(), $this->getServer()->getLogger());
        /*if ($this->server->stop === false){
            $this->task = new PostCollectionTask($this);
            $this->getServer()->getScheduler()->scheduleRepeatingTask($this->task, 5);
        }*/
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
    public function makeMeASandwich(PluginBase $plugin){
        $this->getLogger()->info("* gives " . TextFormat::DARK_AQUA . $plugin->getName() . TextFormat::WHITE . " a sandwich.");
        return new MonitoredWebsiteData($plugin->getName());
    }
    public function getVoltServer(){
        return $this->server;
    }
    public function unbindServer(){
        $this->server->synchronized(function(ServerTask $thread){
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