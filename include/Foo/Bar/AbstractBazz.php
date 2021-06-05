<?php
declare(strict_types=1);

namespace Foo\Bar;

use DateTime;

abstract class AbstractBazz
{
    public abstract function methodA() : void;
    public function methodB(int $param1) : int
    {
        return 1;
    }
    public function methodC(int $x, string $y = 'foo', array $z = null) : DateTime
    {
        return new DateTime;
    }
    public function methodD() : Qux
    {
        return new Qux;
    }
}