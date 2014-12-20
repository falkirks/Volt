<?php
namespace volt;

use pocketmine\Server;
use volt\exception\PluginNotEnabledException;

class WebsiteData implements \ArrayAccess{
    public function __construct(){

    }

    public function offsetExists($offset){
        $volt = $this->getVolt();
        if($volt !== null){
            return $volt->getVoltServer()->synchronized(function(ServerTask $thread, $var){
                $values = $thread->getValueStore()->getValues();
                return isset($values[$var]);
            }, $volt->getVoltServer(), $offset);
        }
        else{
            throw new PluginNotEnabledException;
        }
    }

    public function offsetGet($offset){
        $volt = $this->getVolt();
        if($volt !== null){
            return $volt->getVoltServer()->synchronized(function(ServerTask $thread, $var){
                $values = $thread->getValueStore()->getValue($var);
                return isset($values[$var]) ? $values[$var] : null;
            }, $volt->getVoltServer(),$offset);
        }
        else{
            throw new PluginNotEnabledException;
        }
    }

    public function offsetSet($offset, $value){
        $volt = $this->getVolt();
        if($volt !== null){
            $volt->getVoltServer()->synchronized(function(ServerTask $thread, $var, $value){
                $thread->getValueStore()->setValue($var, $value);
            }, $volt->getVoltServer(), $offset, $value);
        }
        else{
            throw new PluginNotEnabledException;
        }
    }
    
    public function offsetUnset($offset){
        $this->offsetSet($offset, null);
    }

    protected function getVolt(){
        $plugin = Server::getInstance()->getPluginManager()->getPlugin("Volt");
        return (($plugin instanceof Volt && $plugin->isEnabled()) ? $plugin : null);
    }
}