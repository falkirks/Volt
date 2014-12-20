Volt
====
###### Formerly HTTPServer

Volt is an hyper-powerful integrated website solution for PocketMine. Driven by Handlebars and pthreads, volt is an extensive webserver.

### What's changing in v3.0.0?
* **The name** HTTPServer is now volt. Why? I thought carefully about this one and I figured that "HTTPServer" did not represent the project correctly.
* **Threading.** Volt is now better at threading. Every request is run by a worker in a pool. This allows multiple requests to be processed in parallel. 
* **Templating** Now Volt is driven by Handlebars for high speeds and extreme customization.
* **New API** The API has been entirely rewritten to be more fun to use. It is much more logical and powerful.

### Volt API
The API is still tentative and might undergo some large structural changes before its release, so don't get too attached to it. The API centralizes on WebsiteData objects which are magical objects which allow interaction with the server.

#### Getting API access
##### Anonymous
This mode of access is **not** recommended . It allows direct access to the API without any logging or monitoring.
```php
$data = new \volt\WebsiteData();
```
##### Identified 
In an optimal setting you should identify yourself to Volt. This will allow Volt to create logs of your API usage. In order to identify yourself, you will need to pass a PluginBase object to Volt. You can construct a MonitoredWebsiteData directly, but this might not be supported in future versions.
```php
$data = Volt::makeMeASandwich($this); //returns MonitoredWebsiteData
```
#### Setting and getting values
Once you have a WebsiteData object, you get a link to the global scope of handlebars variables.
```php
$data = new \volt\WebsiteData(); //We are using anon
$data["foo"] = ["1", "2", "3"];
var_dump($data["foo"]); //["1", "2", "3"]
```

#### Changing the scope
Obviously some variables should only be available to /foo/* and some other should only be accessible to /foo/bar/*

**Note** Variables in higher scopes won't be available to lower scope. So if I am in $data->1->2, I won't be able to see $data->1 variables.

```php
$data = new \volt\WebsiteData(); //We are using anon
$foo = $data->foo; //Switch scope to /foo/*
$data["thestuff"] = "You are a /foo/* but not a /foo/bar/*";
$foobar = $foo->bar; //Switch scope to /foo/bar/*
var_dump($foobar["thestuff"]); //null
$data["thestuff"] = "You are a /foo/bar/*";
```

#### Dynamic Page Registration
To ease plugin installation, pages can be dynamically registered into Volt. This feature is implemented in the backend but still needs to be added to the API.