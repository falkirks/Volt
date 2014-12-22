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
    private $voltServer;
    /** @var  VoltCommand */
    private $voltCommand;
    /** @var  MonitoredDataStore */
    private $monitoredDataStore;
    public function onEnable(){
        $this->saveDefaultConfig();
        self::$serverConfig = $this->getConfig();
        $this->getLogger()->warning("Volt 3.0 preview is mystical, magical and " . TextFormat::RED . "buggy" . TextFormat::YELLOW . ".");
        if(!is_dir($this->getServer()->getDataPath() . "volt")) mkdir($this->getServer()->getDataPath() . "volt");
        $names = [];
        foreach($this->getServer()->getIPBans()->getEntries() as $ban){
            $names[] = $ban->getName();
        }
        $this->voltServer = new ServerTask($this->getServer()->getDataPath() . "volt", $this->getServer()->getLoader(), $this->getServer()->getLogger(), $this->getConfig(), $names);
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
        return $this->voltServer;
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
        $this->voltServer->synchronized(function(ServerTask $thread){
            $thread->stop();
        }, $this->voltServer);
    }
    public function onDisable(){
        if($this->voltServer instanceof ServerTask){
            $this->getLogger()->info("Killing server...");
            $this->unbindServer();
        }
    }
}