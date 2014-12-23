<?php
namespace volt\exception;

/**
 * Class APIFeatureNotAvailableException
 * This exception is thrown when an API feature is not available to your plugin
 * this could be caused by one of two things.
 *  - Your plugin was banned from accessing the API using the /volt command
 *  - The feature is unstable and requires an opt-in.
 * @package volt\exception
 */
class APIFeatureNotAvailableException extends \Exception{
}