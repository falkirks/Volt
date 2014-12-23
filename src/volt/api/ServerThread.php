<?php
namespace volt\api;

use pocketmine\Server;
use volt\exception\PluginNotEnabledException;
use volt\Volt;

class ServerThread{
    private $name;
    private $plugin;

    public function __construct($name, $plugin = false){
        $this->plugin = IdentificationController::identify($plugin);
        Subscription::assertGreater(Server::getInstance()->getPluginManager()->getPlugin($this->plugin), Subscription::MEGA);
        $this->getVolt()->getMonitoredDataStore()->createPlugin($this->plugin);

        $this->name = $name;
    }

    public function __call($name, $arguments){
        $volt = $this->getVolt();
        if($volt !== null){
            return $volt->getVoltServer()->$name(...$arguments);
        }
        else{
            throw new PluginNotEnabledException;
        }
    }

    /**
     * @return mixed
     */
    public function getName(){
        return $this->name;
    }

    protected function getVolt(){
        $plugin = Server::getInstance()->getPluginManager()->getPlugin("Volt");
        return (($plugin instanceof Volt && $plugin->isEnabled()) ? $plugin : null);
    }
}
