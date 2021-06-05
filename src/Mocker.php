<?php
declare(strict_types=1);

namespace stk2k\Mocker;

final class Mocker
{
    /**
     * Generate mock object
     *
     * @param string $target_class
     * @param MockedClassNameProviderInterface|null $class_name_provider
     *
     * @return Mock
     * @throws Exception\MockerException
     */
    public static function mock(string $target_class, MockedClassNameProviderInterface $class_name_provider = null) : Mock
    {
        if (!$class_name_provider){
            $class_name_provider = new DefaultMockedClassNameProvider();
        }

        $ret = $class_name_provider->getMockedClassName($target_class);

        $fqcn = $ret['fqcn'] ?? null;

        return new Mock($fqcn, $class_name_provider);
    }
}