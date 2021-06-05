<?php
declare(strict_types=1);

namespace stk2k\mocker\test;

use PHPUnit\Framework\TestCase;
use stk2k\mocker\Exception\MockerException;
use stk2k\mocker\Mock;
use stk2k\mocker\Mocker;

final class MockerTest extends TestCase
{
    public function testMock()
    {
        try{
            $mock = Mocker::mock(Foo::class);

            if (!($mock instanceof Mock)){
                $this->fail('Returned Not Mock instance');
            }

            $this->assertEquals('stk2k\mocker\test\MockedFoo', $mock->getClassName());
        }
        catch(MockerException $ex)
        {
            $this->fail($ex->getMessage());
        }
    }
}