<?php
namespace volt;

use Handlebars\Handlebars;
use Handlebars\Helpers;
use pocketmine\utils\Config;

class ClientTask extends \Threaded{
    private $clientSocket;
    /** @var  \ClassLoader */
    private $loader;
    private $logger;
    private $basePath;
    /** @var  Config */
    private $config;
    /** @var ServerTask  */
    private $serverTask;
    /** @var TemplateLoader  */
    private $templateLoader;
    /** @var  callable[] */
    private $helpers;
    public function __construct($clientSocket, \ClassLoader $loader, \Logger $logger, $path, Config $config, $templates, $helpers, ServerTask $serverTask){
        $this->clientSocket = $clientSocket;
        $this->loader = unserialize(serialize($loader));
        $this->logger = $logger;
        $this->basePath = $path;
        $this->config = $config;
        $this->serverTask = $serverTask;
        $this->helpers = new Helpers($helpers);
        $this->templateLoader = new TemplateLoader($this, $this->basePath, $templates);
    }
    public function run(){
        $this->loader->register(true);
        $engine = new Handlebars([
            "loader" => $this->templateLoader,
            "helpers" => $this->helpers
        ]);
        $buf = '';
        $headers = [];
        while ($message = socket_read($this->clientSocket, 2048, PHP_NORMAL_READ)) {
            $buf .= $message;
            if ($message == "\r") break;
            if ($message == "\n") continue;
            list($key, $val) = explode(' ', $message, 2);
            $headers[$key] = $val;
        }
        //$html .= socket_read($soc, 1+$length);
        if($buf == ""){
            return;
        }
        if (!$buf = trim($buf)) {
            return;
        }
        socket_getpeername($this->clientSocket, $ip);
        $url = explode(" ", reset($headers))[0];
        $query = parse_url("http://e.co" . $url , PHP_URL_QUERY);
        $path = $this->sanitizePath($url);
        $msg = "";
        if(substr($path, strlen($path)-1, 1) === "/"){
            $path = $this->getConfig()->get("special-pages")["index"];
        }
        if(is_file($this->basePath . $path)){
            $mime = (substr($this->basePath . $path, -4) === ".hbs" ? "text/html" : mime_content_type($this->basePath . $path));
            $msg = "HTTP/1.1 200 OK\r\nContent-Type: $mime \r\n\r\n";

            $verb = key($headers);

            switch(strtoupper($verb)){
                case 'GET':
                    //$msg .= $this->getFile($path);
                    if($mime === "text/html") {
                        $msg .= $engine->render($path, array_merge($this->serverTask->getValues(), ["_request" => ["query" => $query, "path" => $path, "ip" => $ip, "isError" => false]]));
                    }
                    else{
                        $msg .= file_get_contents($this->basePath . $path);
                    }
                    break;
                case 'POST':
                    break;
            }
        }
        else{
            if(is_file($this->basePath . $this->getConfig()->get("special-pages")["404"])){
                $path = $this->getConfig()->get("special-pages")["404"];
                $mime = (substr($path, -4) === ".hbs" ? "text/html" : mime_content_type($this->basePath . $path));
                $msg = "HTTP/1.1 404 Not Found\r\nContent-Type: $mime \r\n\r\n";
                if($mime === "text/html") {
                    $msg .= $engine->render($path, array_merge($this->serverTask->getValues(), ["_request" => ["query" => $query, "path" => $path, "ip" => $ip, "isError" => true]]));
                }
                else{
                    $msg .= file_get_contents($this->basePath . $path);
                }
            }
            else{
                $msg = "HTTP/1.1 404 Not Found\r\nContent-Type: text/html \r\n\r\n";
                $msg .= "404! Resource Not Found!";
            }
        }
        //elseif (substr($page, -4) == "html") socket_write($con, $this->h . $this->replace(file_get_contents($this->path . $page)));

        socket_write($this->clientSocket, $msg);
        $this->close();
    }
    public function sanitizePath($path){
        $path = parse_url("http://e.co" . $path, PHP_URL_PATH); //Parse url won't work on relative URLs
        return str_replace("/.", "", str_replace("/..", "", $path));
    }
    /**
     * @return \Logger
     */
    public function getLogger(){
        return $this->logger;
    }

    /**
     * @return mixed
     */
    public function getConfig(){
        return $this->config;
    }

    /**
     * @return TemplateLoader
     */
    public function getTemplateLoader(){
        return $this->templateLoader;
    }

    /**
     * @return mixed
     */
    public function getBasePath(){
        return $this->basePath;
    }


    public function close(){
        @socket_shutdown($this->clientSocket);
        socket_close($this->clientSocket);

        $this->clientSocket = false;
    }
}
