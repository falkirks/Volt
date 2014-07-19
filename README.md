HTTPServer
==========

HTTPServer is a simple mutithreaded web server for integration with PocketMine. It listens on port 8080 and can be accessed externally (provided port is forwarded). HTTPServer looks for files in the "HTTPServer" folder inside your PocketMine directory (created on first run). HTTPServer will serve any file type and works with folder hierarchy.

##API

###Variables

Inside HTML pages variables can be used. A variable is any text enclosed within double braces (eg: {{name}}). A variable will be replaced with it's contents. If you are planning on using this you should add it as a dependancy in your plugin.yml (that way HTTPServer loads before you).

```php
$this->getServer()->getPluginManager()->getPlugin("HTTPServer")->addValue("name", "value");
```

###POST Handling

Any page on your site can be subjected to a POST request. Any post request will be shown "Data Posted" or post.html (if available). You can bind your plugin to receive post request data from a specific page (post connection).

```php
$this->getServer()->getPluginManager()->getPlugin("HTTPServer")->bindTo("/contact.txt", array($this, 'postCallback'));
```

```php
public function postCallback($data, $name){
//$data is raw request, you will need to parse it
}
```

