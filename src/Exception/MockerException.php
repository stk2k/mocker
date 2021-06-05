<?php
declare(strict_types=1);

namespace stk2k\Mocker\Exception;

use Throwable;
use Exception;

class MockerException extends Exception
{
    /**
     * construct
     *
     * @param string $message
     * @param Throwable|null $prev
     */
    public function __construct(string $message, Throwable $prev = null){
        parent::__construct($message, 0, $prev);
    }
}