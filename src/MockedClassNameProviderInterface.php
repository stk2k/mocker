<?php
declare(strict_types=1);

namespace stk2k\mocker;

interface MockedClassNameProviderInterface
{
    /**
     * Get concrete class name from abstrct class name
     *
     * @param string $class_name
     *
     * @return array
     */
    public function getMockedClassName(string $class_name) : array;
}