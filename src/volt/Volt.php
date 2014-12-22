<?php
namespace volt;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use volt\command\VoltCommand;
use volt\monitor\MonitoredDataStore;

class Volt extends PluginBase{
    /** @var  Config */
    public static $serverConfig;
    /** @var  ServerTask */
    private $server;
    /** @var  VoltCommand */
    private $voltCommand;
    /** @var  MonitoredDataStore */
    private $monitoredDataStore;
    public function onEnable(){
        $this->saveDefaultConfig();
        self::$serverConfig = $this->getConfig();
        $this->getLogger()->warning("Volt 3.0 preview is mystical, magical and " . TextFormat::RED . "buggy" . TextFormat::YELLOW . ".");
        if(!is_dir($this->getServer()->getDataPath() . "volt")) mkdir($this->getServer()->getDataPath() . "volt");
        $this->server = new ServerTask($this->getServer()->getDataPath() . "volt", $this->getServer()->getLoader(), $this->getServer()->getLogger());

        $this->monitoredDataStore = new MonitoredDataStore();

        $this->voltCommand = new VoltCommand($this);
        $this->getServer()->getCommandMap()->register("volt", $this->voltCommand);
    }
    public function addValue($n, $v){
        $this->getLogger()->warning(TextFormat::DARK_AQUA . 'addValue($n, $v)' . TextFormat::RESET . TextFormat::YELLOW . " is no longer supported.");
    }
    public function bindTo($n, Callable $func){
        $this->getLogger()->warning(TextFormat::DARK_AQUA . 'bindTo($n, Callable $func)' . TextFormat::RESET . TextFormat::YELLOW . " is no longer supported.");
    }
    /**
     * @return ServerTask
     */
    public function getVoltServer(){
        return $this->server;
    }
    /**
     * @return VoltCommand
     */
    public function getVoltCommand(){
        return $this->voltCommand;
    }

    /**
     * @return MonitoredDataStore
     */
    public function getMonitoredDataStore(){
        return $this->monitoredDataStore;
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