<?php
namespace volt;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use volt\api\Subscription;
use volt\command\VoltCommand;
use volt\exception\InternalMethodException;
use volt\monitor\MonitoredDataStore;

/**
 * Class Volt
 * @package volt
 * @volt-api peta
 */
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

        if(!is_dir($this->getServer()->getDataPath() . "volt")) mkdir($this->getServer()->getDataPath() . "volt");
        $names = [];
        foreach($this->getServer()->getIPBans()->getEntries() as $ban){
            $names[] = $ban->getName();
        }
        $this->voltServer = new ServerTask($this->getServer()->getDataPath() . "volt", $this->getServer()->getLoader(), $this->getLogger(), $this->getConfig(), $names);
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
     * @throws InternalMethodException
     */
    public function getVoltServer(){
        $trace = debug_backtrace();
        if (isset($trace[1])) {
            $fullClass = explode("\\", $trace[1]['class']);
            if($fullClass[0] === __NAMESPACE__){
                return $this->voltServer;
            }
        }
        throw new InternalMethodException;
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
        $this->voltServer->join();
    }
    public function onDisable(){
        if($this->voltServer instanceof ServerTask){
            $this->getLogger()->info("Killing server...");
            $this->unbindServer();
        }
    }
}