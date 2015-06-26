<?php
/**
 * This is a shim which will inject a replacement
 * for the deprecated mine_content_type function
 * if the said function doesn't exist.
 */
namespace{
    if(!function_exists('mime_content_type')) {

        function mime_content_type($filename) {

            $mime_types = array(

                'txt' => 'text/plain',
                'htm' => 'text/html',
                'html' => 'text/html',
                'php' => 'text/html',
                'css' => 'text/css',
                'js' => 'application/javascript',
                'json' => 'application/json',
                'xml' => 'application/xml',
                'swf' => 'application/x-shockwave-flash',
                'flv' => 'video/x-flv',

                // images
                'png' => 'image/png',
                'jpe' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'jpg' => 'image/jpeg',
                'gif' => 'image/gif',
                'bmp' => 'image/bmp',
                'ico' => 'image/vnd.microsoft.icon',
                'tiff' => 'image/tiff',
                'tif' => 'image/tiff',
                'svg' => 'image/svg+xml',
                'svgz' => 'image/svg+xml',

                // archives
                'zip' => 'application/zip',
                'rar' => 'application/x-rar-compressed',
                'exe' => 'application/x-msdownload',
                'msi' => 'application/x-msdownload',
                'cab' => 'application/vnd.ms-cab-compressed',

                // audio/video
                'mp3' => 'audio/mpeg',
                'qt' => 'video/quicktime',
                'mov' => 'video/quicktime',

                // adobe
                'pdf' => 'application/pdf',
                'psd' => 'image/vnd.adobe.photoshop',
                'ai' => 'application/postscript',
                'eps' => 'application/postscript',
                'ps' => 'application/postscript',

                // ms office
                'doc' => 'application/msword',
                'rtf' => 'application/rtf',
                'xls' => 'application/vnd.ms-excel',
                'ppt' => 'application/vnd.ms-powerpoint',

                // open office
                'odt' => 'application/vnd.oasis.opendocument.text',
                'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
            );

            $ext = strtolower(array_pop(explode('.',$filename)));
            if (array_key_exists($ext, $mime_types)) {
                return $mime_types[$ext];
            }
            elseif (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME);
                $mimetype = finfo_file($finfo, $filename);
                finfo_close($finfo);
                return $mimetype;
            }
            else {
                return 'application/octet-stream';
            }
        }
    }
}

namespace volt {

    use pocketmine\plugin\PluginBase;
    use pocketmine\utils\Config;
    use pocketmine\utils\TextFormat;
    use volt\api\MonitoredWebsiteData;
    use volt\api\Subscription;
    use volt\command\VoltCommand;
    use volt\exception\InternalMethodException;
    use volt\monitor\MonitoredDataStore;


    /**
     * Class Volt
     * @package volt
     * @volt-api peta
     */
    class Volt extends PluginBase{
        /** @var  Config */
        public static $serverConfig;
        /** @var  ServerTask */
        private $voltServer;
        /** @var  VoltCommand */
        private $voltCommand;
        /** @var  MonitoredDataStore */
        private $monitoredDataStore;

        public function onEnable(){
            $this->saveDefaultConfig();
            self::$serverConfig = $this->getConfig();

            if (!is_dir($this->getServer()->getDataPath() . "volt")) mkdir($this->getServer()->getDataPath() . "volt");
            $names = [];
            foreach ($this->getServer()->getIPBans()->getEntries() as $ban) {
                $names[] = $ban->getName();
            }
            $this->voltServer = new ServerTask($this->getServer()->getDataPath() . "volt", $this->getLogger(), $this->getConfig(), $names);
            $this->monitoredDataStore = new MonitoredDataStore();

            $this->voltCommand = new VoltCommand($this);
            $this->getServer()->getCommandMap()->register("volt", $this->voltCommand);
        }

        public function addValue($n, $v){
            $this->getLogger()->warning(TextFormat::DARK_AQUA . 'addValue($n, $v)' . TextFormat::RESET . TextFormat::YELLOW . " is no longer supported.");
        }

        public function bindTo($n, Callable $func){
            $this->getLogger()->warning(TextFormat::DARK_AQUA . 'bindTo($n, Callable $func)' . TextFormat::RESET . TextFormat::YELLOW . " is no longer supported.");
        }

        /**
         * @return ServerTask
         * @throws InternalMethodException
         */
        public function getVoltServer(){
            $trace = debug_backtrace();
            if (isset($trace[1])) {
                $fullClass = explode("\\", $trace[1]['class']);
                if ($fullClass[0] === __NAMESPACE__) {
                    return $this->voltServer;
                }
            }
            throw new InternalMethodException;
        }

        /**
         * @return VoltCommand
         */
        public function getVoltCommand(){
            return $this->voltCommand;
        }

        /**
         * @return MonitoredDataStore
         */
        public function getMonitoredDataStore(){
            return $this->monitoredDataStore;
        }

        public function unbindServer(){
            $this->voltServer->synchronized(function (ServerTask $thread) {
                $thread->stop();
            }, $this->voltServer);
            $this->voltServer->join();
        }

        public function onDisable(){
            if ($this->voltServer instanceof ServerTask) {
                $this->getLogger()->info("Killing server...");
                $this->unbindServer();
            }
        }
    }
}