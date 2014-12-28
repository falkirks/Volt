<?php
namespace volt\api;

use pocketmine\Server;
use volt\exception\PluginNotEnabledException;
use volt\ServerTask;
use volt\Volt;

class ServerThread{
    private $plugin;

    public function __construct($plugin = false){
        $this->plugin = IdentificationController::identify($plugin);
        Subscription::assertGreater(Server::getInstance()->getPluginManager()->getPlugin($this->plugin), Subscription::MEGA);
        $this->getVolt()->getMonitoredDataStore()->createPlugin($this->plugin);
    }

    public function __call($name, $arguments){
        $volt = $this->getVolt();
        if($volt !== null){
            $server = $volt->getServer();
            if($server instanceof ServerTask) {
                return $volt->getVoltServer()->$name(...$arguments);
            }
            else{
                throw new PluginNotEnabledException; //TODO change to ServerNotEnabled
            }
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
