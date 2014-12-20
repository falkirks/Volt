<?php
namespace volt;

use pocketmine\Server;
use volt\exception\PluginNotEnabledException;

class WebsiteData implements \ArrayAccess{
    public function __construct(array $page = []){
        $this->page = $page;
    }

    public function offsetExists($offset){
        $volt = $this->getVolt();
        if($volt !== null){
            return $volt->getVoltServer()->synchronized(function(ServerTask $thread, array $scope, $var){
                $values = $thread->getValueStore()->getScopeValues($scope);
                return isset($values[$var]);
            }, $volt->getVoltServer(), $this->page, $offset);
        }
        else{
            throw new PluginNotEnabledException;
        }
    }

    public function offsetGet($offset){
        $volt = $this->getVolt();
        if($volt !== null){
            return $volt->getVoltServer()->synchronized(function(ServerTask $thread, array $scope, $var){ //TODO consider limiting to current scope
                $values = $thread->getValueStore()->getScopeValues($scope);
                return isset($values[$var]) ? $values[$var] : null;
            }, $volt->getVoltServer(), $this->page, $offset);
        }
        else{
            throw new PluginNotEnabledException;
        }
    }

    public function offsetSet($offset, $value){
        $volt = $this->getVolt();
        if($volt !== null){
            $volt->getVoltServer()->synchronized(function(ServerTask $thread, array $scope, $var, $value){ //TODO consider limiting to current scope
                $thread->getValueStore()->setValue($scope, $var, $value);
            }, $volt->getVoltServer(), $this->page, $offset, $value);
        }
        else{
            throw new PluginNotEnabledException;
        }
    }
    
    public function offsetUnset($offset){
        $this->offsetSet($offset, null);
    }

    function __get($name){
        $page = $this->page;
        $page[] = $name;
        return new WebsiteData($page);
    }
    protected function getVolt(){
        $plugin = Server::getInstance()->getPluginManager()->getPlugin("Volt");
        return (($plugin instanceof Volt && $plugin->isEnabled()) ? $plugin : null);
    }
}