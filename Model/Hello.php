<?php
namespace Letscms\TestApi\Model;

use Letscms\TestApi\Api\HelloInterface;

class Hello implements HelloInterface
{
    /**
     * Return a simple message
     * @return string
     */
    public function getMessage()
    {
        return "Hello from Magento 2 API!";
    }

    /**
     * Set and return a message
   
     * @return string
     */
    public function setMessage($message)
    {
        return $message;
    }
}
