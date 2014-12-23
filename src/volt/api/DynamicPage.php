<?php
namespace volt\api;

use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use volt\exception\PluginIdentificationException;
use volt\exception\PluginNotEnabledException;
use volt\ServerTask;
use volt\Volt;

class DynamicPage{
    private $name;
    private $plugin;
    public function __construct($name, $plugin = false){
        $this->plugin = IdentificationController::identify($plugin);
        $this->name = $name;
    }
    public function __invoke($content){
        $this->setContent($content);
    }
    /**
     * @return mixed
     */
    public function getName(){
        return $this->name;
    }
    public function getContent(){
        $volt = $this->getVolt();
        if($volt !== null){
            return $volt->getVoltServer()->synchronized(function(ServerTask $thread, $name){
                return $thread->getTemplate($name);
            }, $volt->getVoltServer(), $this->name);
        }
        else{
            throw new PluginNotEnabledException;
        }
    }
    public function setContent($content){
        $volt = $this->getVolt();
        if($volt !== null){
            $out = $volt->getVoltServer()->synchronized(function(ServerTask $thread, $name, $content){
                return $thread->addTemplate($name, $content);
            }, $volt->getVoltServer(), $this->name, $content);
            if(!$out) throw new PageAlreadyExistsException;
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