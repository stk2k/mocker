<?php
declare(strict_types=1);

namespace stk2k\mocker;

use Stk2k\FileSystem\Exception\MakeDirectoryException;
use Stk2k\FileSystem\FileSystem;
use stk2k\mocker\Exception\MockerException;

final class Mock
{
    /** @var string */
    private $class_name;

    /** @var MockedClassNameProviderInterface */
    private $class_name_provider;

    /**
     * Mock constructor.
     *
     * @param string $class_name
     * @param MockedClassNameProviderInterface $class_name_provider
     */
    public function __construct(string $class_name, MockedClassNameProviderInterface $class_name_provider)
    {
        $this->class_name = $class_name;
        $this->class_name_provider = $class_name_provider;
    }

    /**
     * @return string
     */
    public function getClassName() : string
    {
        return $this->class_name;
    }

    /**
     * @param string|null $save_dir
     *
     * @return $this
     * @throws MockerException
     */
    public function save(string $save_dir = null) : self
    {
        try{
            if (!$save_dir){
                $save_dir = sys_get_temp_dir();
            }

            FileSystem::mkdir($save_dir);

            if (!is_dir($save_dir) || !is_writable($save_dir)){
                throw new MockerException('Save dir not found or unwritable: ' . $save_dir);
            }

            CodeGenerator::generateClassCode($this->class_name, $save_dir, $this->class_name_provider);

        }
        catch (MockerException|MakeDirectoryException $ex)
        {
            throw new MockerException('Failed to save mock code to: ' . $save_dir, $ex);
        }
        return $this;
    }
}