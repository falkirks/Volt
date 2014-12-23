<?php
namespace volt\exception;
/**
 * Class InternalMethodException
 * This exception is thrown when a plugin attempts to use a semi-protected method
 * these methods are not intended to be used through the API and issues may occur
 * if they are.
 * @package volt\exception
 */
class InternalMethodException extends \Exception{

}