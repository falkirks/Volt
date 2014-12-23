<?php
namespace volt\exception;
/**
 * Class PluginIdentificationException
 * This exception is thrown when an API identification fails to complete. This
 * failure only occurs when using a plugin name or auto-detect to authenticate.
 *
 * - If you are using the "plugin name" authentication mode you should double
 *   check your spelling and case.
 * - If you are using auto-detect, make sure that you are making the authenticate
 *   call inside your PluginBase. Volt will NOT climb the call stack. If you are
 *   calling correctly, make sure your plugin name and case exactly match that of
 *   your PluginBase class.
 * @package volt\exception
 */
class PluginIdentificationException extends \Exception{

}