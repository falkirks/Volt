<?php
namespace volt\exception;
/**
 * Class PluginNotEnabledException
 * This error is thrown when Volt has been disabled and the
 * APi is no longer accessible.
 *
 * It is NOT your job to re-enable Volt. This may be deemed dangerous behaviour.
 * @package volt\exception
 */
class PluginNotEnabledException extends \Exception{

}