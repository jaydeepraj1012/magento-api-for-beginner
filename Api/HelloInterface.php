<?php
namespace Letscms\TestApi\Api;

interface HelloInterface
{
    /**
     * Return a simple message
     * @return string
     */
    public function getMessage();

    /**
     * Set a message
     * @param string $message
     * @return string
     */
    public function setMessage($message);
}
