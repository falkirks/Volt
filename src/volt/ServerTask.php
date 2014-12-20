<?php
namespace volt;
use pocketmine\Thread;
use pocketmine\utils\TextFormat;

class ServerTask extends Thread{
    private $sock;
    private $pool;
    private $logger;
    private $config;
    private $valueStore;
    public $stop, $path;
    public function __construct($path, \ClassLoader $loader, \Logger $logger) {
        $this->stop = false;
        $this->pool = new \Pool(4, \Worker::CLASS);
        $this->valueStore = serialize([]);
        $this->templates = serialize([]);
        $this->logger = $logger;
        $this->path = $path;
        $this->config = Volt::$serverConfig;
        $this->loader = clone $loader;
        if (($this->sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
            print "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
            $this->stop();
        }
        if(socket_set_option ($this->sock, SOL_SOCKET, SO_REUSEADDR, 1) === false){
            $this->stop();
        }
        if (socket_bind($this->sock, "0.0.0.0", $this->config->get("server-port")) === false) {
            print "socket_bind() failed: reason: " . socket_strerror(socket_last_error($this->sock)) . "\n";
            $this->stop();
        }
        if (socket_listen($this->sock, 5) === false) {
            print "socket_listen() failed: reason: " . socket_strerror(socket_last_error($this->sock)) . "\n";
            $this->stop();
        }
        $this->getLogger()->info("[SUCCESS] HTTP Server Status: " . TextFormat::GREEN . "Active" . TextFormat::WHITE . " on port " . volt::$serverConfig->get("server-port") . "\n");
        $this->start();
    }
    public function stop() {
        $this->getLogger()->warning("HTTP Server Status: " . TextFormat::RED . "Stopped\n");
        $this->stop = true;
    }
    public function run() {
        $this->loader->register(true);

        /*while ($this->stop === false) {
           if(($con = socket_accept($this->sock)) !== false){
               $page = trim(socket_read($con, 2048, PHP_NORMAL_READ));
               if(substr($page,0,4) == "POST") $this->processDataPost($page, $con);
               else{
                   $page = substr($page,strpos($page, " ")+1);
                   $page = substr($page, 0,strpos($page, " "));
                   $page = parse_url("http://e.co" . $page, PHP_URL_PATH); //Parse url won't work on relative URLs
                   $page = str_replace("/.", "", str_replace("/..", "", $page));
                   if($page == "/") $page = "/index.html";
                   if(!is_file($this->path . $page)) socket_write($con, $this->h . "File not found.");
                   elseif (substr($page, -4) == "html") socket_write($con, $this->h . $this->replace(file_get_contents($this->path . $page)));
                   else socket_write($con, $this->h . file_get_contents($this->path . $page));
                   socket_close($con);
               }
           }
        }*/
        while($this->stop === false) {
            if (($msgsock = socket_accept($this->sock)) === false) {
                break;
            }
            $client = new ClientTask($msgsock, $this->loader, $this->getLogger(), $this->path, $this->config, $this->templates, $this);
            $this->pool->submit($client);
        }
        $this->pool->collect(function(ClientTask $client){
            $client->close();
            return true;
        });
        socket_shutdown($this->sock);
        $arrOpt = array('l_onoff' => 1, 'l_linger' => 1);
        socket_set_block($this->sock);
        socket_set_option($this->sock, SOL_SOCKET, SO_LINGER, $arrOpt);
        socket_close($this->sock);

        $this->pool->shutdown();
        exit(0);
    }
    public function addTemplate($path, $template){
        if(is_file($this->path . $path)) return false;
        $templates = unserialize($this->templates);
        $templates[$path] = $template;
        $this->templates = serialize($templates);
        return true;
    }
    public function getTemplate($name){
        $templates = unserialize($this->templates);
        return isset($templates[$name]) ? $templates[$name] : null;
    }

    public function getValues(){
        return unserialize($this->valueStore);
    }
    public function getValue($name){
        $valueStore = unserialize($this->valueStore);
        return isset($valueStore[$name]) ? $valueStore[$name] : null;
    }
    public function setValue($name, $value){
        $valueStore = unserialize($this->valueStore);
        $valueStore[$name] = $value;
        $this->valueStore = serialize($valueStore);
    }
    /**
     * @return \Logger
     */
    public function getLogger(){
        return $this->logger;
    }
}

