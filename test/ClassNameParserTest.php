<?php
declare(strict_types=1);

namespace stk2k\Mocker\Test;

use PHPUnit\Framework\TestCase;
use stk2k\Mocker\ClassNameParser;
use stk2k\Mocker\Exception\MockerException;

final class ClassNameParserTest extends TestCase
{
    public function testParse()
    {
        try{
            $ret = ClassNameParser::parse('Foo\\Bar\\AbstractBazz');
        }
        catch(MockerException $ex)
        {
            $this->fail($ex->getMessage());
        }

        $namespace = $ret['namespace'] ?? [];
        $class_name = $ret['class_name'] ?? '';

        $this->assertIsArray($namespace);
        $this->assertEquals(['Foo', 'Bar'], $namespace);

        $this->assertEquals('AbstractBazz', $class_name);
    }

}