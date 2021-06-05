<?php
declare(strict_types=1);

namespace stk2k\mocker;

use Exception;

use stk2k\mocker\exception\MockerException;

final class DefaultMockedClassNameProvider implements MockedClassNameProviderInterface
{
    private const NAMESPACE_SEPARATOR = '\\';

    private const KEYWORD_MOCKED_PREFIX = 'Mocked';

    /**
     * Provide default concrete class name
     *
     * @param string $class_name
     *
     * @return array
     * @throws MockerException
     */
    public function getMockedClassName(string $class_name): array
    {
        try{
            $ret = ClassNameParser::parse($class_name);

            $namespace = $ret['namespace'] ?? [];
            $base_class_name = $ret['class_name'] ?? '';

            if (empty($base_class_name)){
                throw new MockerException('Class name empty: ' . $class_name);
            }

            $extended_class_name = self::KEYWORD_MOCKED_PREFIX . $base_class_name;
            $fqcn = implode(self::NAMESPACE_SEPARATOR, $namespace) . self::NAMESPACE_SEPARATOR . $extended_class_name;

            return [
                'namespace' => $namespace,
                'base_class_name' => $base_class_name,
                'extended_class_name' => $extended_class_name,
                'fqcn' => $fqcn,
            ];
        }
        catch(Exception $ex)
        {
            throw new MockerException('Failed to get concrete class: ' . $ex->getMessage());
        }
    }

}