![Volt Icon](/resources/smallicon.png) ***Volt***
====
###### Formerly HTTPServer

Volt is an hyper-powerful integrated website solution for PocketMine. Driven by Handlebars and pthreads, volt is an extensive webserver.

### What's changing in v3.0.0?
* **Name.** HTTPServer is now volt. Why? I thought carefully about this one and I figured that "HTTPServer" did not represent the project correctly.
* **Threading.** Volt is now better at threading. Every request is run by a worker in a pool. This allows multiple requests to be processed in parallel. 
* **Templating.** Now Volt is driven by Handlebars for high speeds and extreme customization.
* **API.** The API has been entirely rewritten to be more fun to use. It is much more logical and powerful.

### Volt API
The API is still tentative and might undergo some large structural changes before its release, so don't get too attached to it. The API centralizes on `WebsiteData` objects which are magical objects that allow interaction with the server. These objects mimic arrays by implementing `\ArrayAccess`, `\Countable`, and `\IteratorAggregate`.

#### Getting API access
##### Anonymous
This mode of access is **not** recommended . It allows direct access to the API without any logging or monitoring.
```php
$data = new \volt\api\WebsiteData();
```
##### Identified 
In an optimal setting you should identify yourself to Volt. This will allow Volt to create logs of your API usage. There are three options for identification
* PluginBase (Recommended)
* Plugin name
* Auto-detect (Not recommended)

```php
/* Option #1 (Recommended) */
$data = new \volt\api\MonitoredWebsiteData($this); //Called from within a PluginBase
/* Option #2 */
$data = new \volt\api\MonitoredWebsiteData("PluginName"); //Called from anywhere
/* Option #3 (Not recommended) */
$data = new \volt\api\MonitoredWebsiteData(); //Called from within a PluginBase, and requires class name to equal plugin name
```
If an identification request fails a `volt\exception\PluginIdentificationException` will be thrown.

#### Setting and getting values
Once you have a `WebsiteData` object, you get a link to the global scope of handlebars variables. It is highly recommended to put all your variables in a namespace for your plugin, this helps avoid collisions.
```php
$data = new \volt\WebsiteData(); //We are using anon
$data["foo"] = ["1", "2", "3"];
var_dump($data["foo"]); //["1", "2", "3"]
```

#### Dynamic Page Registration
To ease plugin installation, pages can be dynamically registered into Volt. This feature is still experimental and may be expanded upon in a future release.
```php
$page = new \volt\api\DynamicPage("/hello");
$page("This is the content"); //Page is now available at /hello and will display "This is the content"
```