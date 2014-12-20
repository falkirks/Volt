<?php
namespace volt;

use pocketmine\scheduler\PluginTask;

class PostCollectionTask extends PluginTask{
    public function onRun($tick){
        $post = $this->getOwner()->server->synchronized(function ($thread){
            $p = unserialize($thread->post);
            $thread->post = serialize([]);
            return $p;
        }, $this->getOwner()->server);
        if(count($post) > 0){
            foreach($post as $name => $data){
                $this->getOwner()->getLogger()->info($name);
                if(isset($this->getOwner()->bound[$name])){
                    foreach($data as $d){
                        foreach($this->getOwner()->bound[$name] as $f){
                            $f($d, $name);
                        }
                    }
                }
            }
        }
    }
}