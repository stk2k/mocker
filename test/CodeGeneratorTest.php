<?php
declare(strict_types=1);

namespace stk2k\Mocker\Test;

use Exception;

use Foo\Bar\Qux;
use PHPUnit\Framework\TestCase;

use Foo\Bar\AbstractBazz;
use stk2k\Mocker\CodeGenerator;
use stk2k\Mocker\DefaultMockedClassNameProvider;

final class CodeGeneratorTest extends TestCase
{
    public function testGenerateClassCode()
    {
        $save_dir = __DIR__ . '/cache';

        try{
            CodeGenerator::generateClassCode(AbstractBazz::class, $save_dir, new DefaultMockedClassNameProvider());
        }
        catch(Exception $ex){
            $this->fail($ex->getMessage());
        }

        try{
            CodeGenerator::generateClassCode(Qux::class, $save_dir, new DefaultMockedClassNameProvider());
            $this->fail();
        }
        catch(Exception $ex){
            $this->assertTrue(true);
        }
    }
}