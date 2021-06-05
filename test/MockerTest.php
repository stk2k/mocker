<?php
declare(strict_types=1);

namespace stk2k\Mocker\Test;

use PHPUnit\Framework\TestCase;
use stk2k\Mocker\Exception\MockerException;
use stk2k\Mocker\Mock;
use stk2k\Mocker\Mocker;

final class MockerTest extends TestCase
{
    public function testMock()
    {
        try{
            $mock = Mocker::mock(Foo::class);

            if (!($mock instanceof Mock)){
                $this->fail('Returned Not Mock instance');
            }

            $this->assertEquals('stk2k\Mocker\Test\MockedFoo', $mock->getClassName());
        }
        catch(MockerException $ex)
        {
            $this->fail($ex->getMessage());
        }
    }
}