<?php
namespace volt\api;

use pocketmine\plugin\Plugin;
use volt\exception\APIFeatureNotAvailableException;

class Subscription{
    private static $levels = [
        "micro", // Registration of pages and variables
        "deci", // Unused
        "kilo", // Registration of helpers
        "mega", // Direct access to server thread (not yet enforced)
        "peta" // Unused
    ];
    const MICRO = 0;
    const DECI = 1;
    const KILO = 2;
    const MEGA = 3;
    const PETA = 4;

    public static function assertEquals(Plugin $plugin, $level){
        if(Subscription::getLevel($plugin) === $level) throw new APIFeatureNotAvailableException;
    }
    public static function assertGreater(Plugin $plugin, $level){
        if(Subscription::getLevel($plugin) < $level) throw new APIFeatureNotAvailableException("API feature requires level " . Subscription::$levels[$level] . " in plugin " . $plugin->getName());
    }
    public static function getLevel(Plugin $plugin){
        $reflection = new \ReflectionClass($plugin);
        preg_match_all("`@volt-api (.*)`", $reflection->getDocComment(), $matches);
        if(isset($matches[1][0])){
            return Subscription::switchLevelFormat($matches[1][0]);
        }
        return Subscription::MICRO;
    }
    public static function switchLevelFormat($level){
        if(is_int($level)){
            return Subscription::$levels[$level];
        }
        else{
            $level = array_search(strtolower($level), Subscription::$levels);
            if($level !== false){
                return $level;
            }
            else{
                return Subscription::MICRO;
            }
        }
    }
}