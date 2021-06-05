<?php
declare(strict_types=1);

namespace stk2k\Mocker\Exception;

use Throwable;

class FinalClassCanNotBeExtendedException extends MockerException
{
    /**
     * construct
     *
     * @param string $class
     * @param int $code
     * @param Throwable|null $prev
     */
    public function __construct(string $class, int $code = 0, Throwable $prev = null){
        $message = 'Final class can not be extended: ' . $class;
        parent::__construct($message, $prev);
    }
}