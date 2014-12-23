<?php
namespace volt\api;

use pocketmine\plugin\Plugin;
use pocketmine\Server;
use volt\exception\PluginIdentificationException;
use volt\Volt;

class IdentificationController{
    public static function identify($payload = false){
        if($payload == false){
            $trace = debug_backtrace();
            if (isset($trace[1])) {
                $fullClass = explode("\\", $trace[2]['class']);
                $payload = array_pop($fullClass);

            }
        }
        if($payload instanceof Plugin) {
            $plugin = $payload->getName();
        }
        else{
            if(Server::getInstance()->getPluginManager()->getPlugin($payload) instanceof Plugin){
                $plugin = $payload;
            }
        }
        if($plugin == null) throw new PluginIdentificationException;
        IdentificationController::getVolt()->getMonitoredDataStore()->createPlugin($plugin);
        return $plugin;
    }
    protected static function getVolt(){
        $plugin = Server::getInstance()->getPluginManager()->getPlugin("Volt");
        return (($plugin instanceof Volt && $plugin->isEnabled()) ? $plugin : null);
    }
}