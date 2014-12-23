<?php
namespace volt\api;

use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use volt\exception\APIFeatureNotAvailableException;
use volt\exception\PluginIdentificationException;
use volt\exception\PluginNotEnabledException;
use volt\ServerTask;
use volt\Volt;

class HandlebarsHelper{
    private $name;
    private $plugin;
    public function __construct($name, $plugin = false){
        if($plugin == false){
            $trace = debug_backtrace();
            if (isset($trace[1])) {
                $fullClass = explode("\\", $trace[1]['class']);
                $plugin = array_pop($fullClass);

            }
        }
        if($name instanceof PluginBase) {
            $this->plugin = $name->getName();
        }
        else{
            if(Server::getInstance()->getPluginManager()->getPlugin($plugin) instanceof PluginBase){
                $this->plugin = $name;
            }
        }
        if($this->plugin == null) throw new PluginIdentificationException;
        $reflection = new \ReflectionClass(Server::getInstance()->getPluginManager()->getPlugin($this->plugin));
        if(stripos($reflection->getDocComment(), "@volt-api dev") === false) throw new APIFeatureNotAvailableException;

        $this->getVolt()->getMonitoredDataStore()->createPlugin($this->plugin);

        $this->name = $name;
    }
    public function __invoke(callable $helper){
        $this->setHelper($helper);
    }
    /**
     * @return mixed
     */
    public function getName(){
        return $this->name;
    }
    public function getHelper(){
        $volt = $this->getVolt();
        if($volt !== null){
            return $volt->getVoltServer()->synchronized(function(ServerTask $thread, $name){
                return $thread->getHelper($name);
            }, $volt->getVoltServer(), $this->name);
        }
        else{
            throw new PluginNotEnabledException;
        }
    }
    public function setHelper(callable $helper){
        $volt = $this->getVolt();
        if($volt !== null){
            $out = $volt->getVoltServer()->synchronized(function(ServerTask $thread, $name, $helper){
                return $thread->addHelper($name, $helper);
            }, $volt->getVoltServer(), $this->name, $helper);
        }
        else{
            throw new PluginNotEnabledException;
        }
    }
    protected function getVolt(){
        $plugin = Server::getInstance()->getPluginManager()->getPlugin("Volt");
        return (($plugin instanceof Volt && $plugin->isEnabled()) ? $plugin : null);
    }
}