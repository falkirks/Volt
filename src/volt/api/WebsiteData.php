<?php
namespace volt\api;

use pocketmine\Server;
use volt\exception\PluginNotEnabledException;
use volt\ServerTask;
use volt\Volt;

class WebsiteData implements \ArrayAccess, \Countable, \Iterator{
    public function __construct(){
        $this->k = 0;
    }

    public function offsetExists($offset){
        $volt = $this->getVolt();
        if($volt !== null){
            return $volt->getVoltServer()->synchronized(function(ServerTask $thread, $var){
                $values = $thread->getValues();
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
                $values = $thread->getValue($var);
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
                $thread->setValue($var, $value);
            }, $volt->getVoltServer(), $offset, $value);
        }
        else{
            throw new PluginNotEnabledException;
        }
    }

    public function getIterator(){
        $volt = $this->getVolt();
        if($volt !== null) {
            return $volt->getVoltServer()->synchronized(function (ServerTask $thread) {
                return $thread->getValues();
            }, $volt->getVoltServer());
        }
        else{
            throw new PluginNotEnabledException;
        }
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next(){
        $this->k++;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current(){
        return $this->getIterator()[$this->key()];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key(){
        return array_keys($this->getIterator())[$this->k];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid(){
        if($this->k < $this->count() && $this->k >= 0){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind(){
        $this->k = 0;
    }

    public function count(){
        $volt = $this->getVolt();
        if($volt !== null) {
            return $volt->getVoltServer()->synchronized(function (ServerTask $thread) {
                return count($thread->getValues());
            }, $volt->getVoltServer());
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