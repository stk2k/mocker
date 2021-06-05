<?php
declare(strict_types=1);

namespace stk2k\mocker;

use ReflectionClass;
use ReflectionException;
use stk2k\mocker\exception\MockerException;

final class ClassNameParser
{
    private const NAMESPACE_SEPARATOR = '\\';

    /**
     * @param string $class_name
     *
     * @return array
     * @throws MockerException
     */
    public static function parse(string $class_name) : array
    {
        try{
            $clazz = new ReflectionClass($class_name);
            $namespace = $clazz->getNamespaceName();
        }
        catch(ReflectionException $ex)
        {
            throw new MockerException('Failed to get reflection class of: ' . $class_name);
        }

        return [
            'namespace' => explode(self::NAMESPACE_SEPARATOR, $namespace),
            'class_name' => $clazz->getShortName(),
            'reflection_class' => $clazz,
        ];
    }
}