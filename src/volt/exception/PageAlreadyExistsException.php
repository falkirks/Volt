<?php
namespace volt\exception;
/**
 * Class PageAlreadyExistsException
 * This exception is thrown when a DynamicPage name collides with
 * a page in the /volt folder. The page in the filesystem will win
 * as it is user created.
 *
 * This will NOT be thrown when overwriting in memory pages as
 * it is hard to assess different calling contexts' right
 * to the page name.
 * @package volt\exception
 */
class PageAlreadyExistsException extends \Exception{

}