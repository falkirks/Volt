<?php
namespace volt\api;

use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use volt\exception\PluginIdentificationException;
use volt\exception\PluginNotEnabledException;
use volt\ServerTask;
use volt\Volt;

class HandlebarsHelper{
    private $name;
    private $plugin;
    public function __construct($name, $plugin = false){
        $this->plugin = IdentificationController::identify($plugin);
        Subscription::assertGreater(Server::getInstance()->getPluginManager()->getPlugin($this->plugin), Subscription::KILO);
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