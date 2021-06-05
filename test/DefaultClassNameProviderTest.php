<?php
declare(strict_types=1);

namespace stk2k\Mocker\Test;

use Foo\Bar\AbstractBazz;
use PHPUnit\Framework\TestCase;
use stk2k\Mocker\DefaultMockedClassNameProvider;
use stk2k\Mocker\Exception\MockerException;

final class DefaultClassNameProviderTest extends TestCase
{
    public function testGetConcreteClassName()
    {
        $provider = new DefaultMockedClassNameProvider();

        try{
            $ret = $provider->getMockedClassName(AbstractBazz::class);
        }
        catch(MockerException $ex)
        {
            $this->fail($ex->getMessage());
        }

        $this->assertEquals('Foo\Bar\MockedAbstractBazz', $ret['fqcn']);
        $this->assertEquals('AbstractBazz', $ret['base_class_name']);
        $this->assertEquals('MockedAbstractBazz', $ret['extended_class_name']);
        $this->assertEquals(['Foo', 'Bar'], $ret['namespace']);
    }
}