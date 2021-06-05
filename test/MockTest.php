<?php
declare(strict_types=1);

namespace stk2k\Mocker\Test;

use PHPUnit\Framework\TestCase;
use Stk2k\FileSystem\FileSystem;
use stk2k\Mocker\DefaultMockedClassNameProvider;
use stk2k\Mocker\Exception\MockerException;
use stk2k\Mocker\Mock;

final class MockTest extends TestCase
{
    public function setUp() : void
    {
        $save_dir = __DIR__ . '/cache/save_dir';
        FileSystem::delete($save_dir, true);
    }
    public function testConstruct()
    {
        $mock = new Mock('Foo', new DefaultMockedClassNameProvider());

        $this->assertEquals('Foo', $mock->getClassName());
    }
    public function testSave()
    {
        $mock = new Mock(Foo::class, new DefaultMockedClassNameProvider());
        $save_dir = __DIR__ . '/cache/save_dir';

        $this->assertDirectoryNotExists($save_dir);

        try{
            $mock->save($save_dir);

            $this->assertDirectoryExists($save_dir);
        }
        catch(MockerException $ex)
        {
            $this->fail($ex->getMessage());
        }


        $this->assertEquals(Foo::class, $mock->getClassName());
    }
}