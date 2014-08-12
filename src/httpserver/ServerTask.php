<?php
namespace httpserver;
use pocketmine\Thread;
class ServerTask extends Thread {
    private $sock;
    public $vars, $stop, $path, $post;
    public function __construct($path) {
        $this->stop = false;
        $this->vars = serialize([]);
        $this->post = serialize([]);
        $this->path = $path;
        $this->h = "HTTP/1.1 200 OK\r\nContent-Type: text/html\r\n\r\n";
        $this->sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if(socket_bind($this->sock, "0.0.0.0", 8080) === false) $this->stop();
        if(socket_listen($this->sock, 5) === false) $this->stop();
        if(socket_set_nonblock($this->sock) === false) $this->stop();
        $this->start();
    }
    public function stop() {
        $this->stop = true;
    }
    public function run() {
        while ($this->stop === false) {
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
        }
        socket_close($this->sock);
        exit(0);
    }
    public function replace($data){
        preg_match_all('/{{(.*?)}}/', $data, $items);
        $items = $items[1];
        $v = unserialize($this->vars);
        foreach ($items as $i) if(isset($v[$i])) $data = str_replace("{{" . $i . "}}", $v[$i], $data);
        return $data;
    }
    public function processDataPost($page, $con){
        $p = "";
        while(($r = @socket_read($con, 2048, PHP_BINARY_READ)) !== "" && $r !== false) $p .= $r;
        if(!is_file($this->path . "/post.html")) socket_write($con, "Data posted.");
        else socket_write($con, $this->replace(file_get_contents($this->path . "/post.html")));
        $c = unserialize($this->post);
        $page = substr($page,strpos($page, " ")+1);
        $page = substr($page, 0,strpos($page, " "));
        $page = parse_url("http://e.co" . $page, PHP_URL_PATH); //Parse url won't work on relative URLs
        $page = str_replace("/.", "", str_replace("/..", "", $page));
        $c[$page][] = $p;
        $this->post = serialize($c);
        socket_close($con);
    }
}

