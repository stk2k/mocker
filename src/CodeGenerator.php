<?php
declare(strict_types=1);

namespace stk2k\mocker;

use Exception;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionException;
use ReflectionNamedType;
use ReflectionType;

use stk2k\FileSystem\File;
use stk2k\filesystem\FileSystem;
use stk2k\mocker\Exception\FinalClassCanNotBeExtendedException;
use stk2k\mocker\Exception\MockerException;

final class CodeGenerator
{
    private const NAMESPACE_SEPARATOR = '\\';

    /**
     * @param string $class
     * @param string $save_dir
     * @param MockedClassNameProviderInterface $class_name_provider
     *
     * @return void
     *
     * @throws MockerException
     */
    public static function generateClassCode(string $class, string $save_dir, MockedClassNameProviderInterface $class_name_provider) : void
    {
        try{
            // concrete class name
            $ret = $class_name_provider->getMockedClassName($class);

            $namespace = $ret['namespace'] ?? [];
            $base_class_name = $ret['base_class_name'] ?? '';
            $extended_class_name = $ret['extended_class_name'] ?? '';
            //$fqcn = $ret['fqcn'] ?? null;

            // get methods
            $ref_class = new ReflectionClass($class);
            $method_list = $ref_class->getMethods();

            // final class can not be extended
            if ($ref_class->isFinal()){
                throw new FinalClassCanNotBeExtendedException($class);
            }

            // recursively create class dir under save dir
            $class_dir = $save_dir . DIRECTORY_SEPARATOR . 'Mocker';
            $class_dir = FileSystem::mkdir($class_dir);

            // destination file path
            $class_file = new File($extended_class_name . '.php', $class_dir);

            // create class file
            $lines = [];

            $lines[] = '<?php';
            $lines[] = 'declare(strict_types=1);';
            $lines[] = '';
            $lines[] = 'namespace Mocker;';
            $lines[] = '';
            $lines[] = 'use ' . implode(self::NAMESPACE_SEPARATOR, $namespace) . self::NAMESPACE_SEPARATOR . $base_class_name . ';';
            $lines[] = '';
            $lines[] = 'final class ' . $extended_class_name . ' extends ' . $base_class_name;
            $lines[] = '{';
            foreach($method_list as $method){
                self::generateMethodCode($lines, $method);
            }
            $lines[] = '}';

            $class_file->put($lines);
        }
        catch(Exception $ex)
        {
            throw new MockerException('Failed to generate class code: ' . $ex->getMessage(), $ex);
        }
    }

    private static function getMethodModifiers(ReflectionMethod $method) : array
    {
        $modifiers = [];
        if ($method->isPublic())      $modifiers[] = 'public';
        if ($method->isProtected())   $modifiers[] = 'protected';
        if ($method->isPrivate())     $modifiers[] = 'private';
        if ($method->isStatic())      $modifiers[] = 'static';
        // if ($method->isAbstract())   $modifiers[] = 'abstract';
        if ($method->isFinal())       $modifiers[] = 'final';
        return $modifiers;
    }

    /**
     * @param array $lines
     * @param ReflectionMethod $method
     *
     * @throws ReflectionException
     */
    private static function generateMethodCode(array& $lines, ReflectionMethod $method) : void
    {
        $modifiers = self::getMethodModifiers($method);
        $modifiers = implode(' ', $modifiers);

        $parameters = self::generateParameters($method);
        $return_type = self::generateReturnType($method);

        $lines[] = '    ' . $modifiers . ' function ' . $method->getName() . '(' . $parameters . ')' . $return_type;
        $lines[] = '    {';
        self::generateMethodBody($lines, $method);
        $lines[] = '    }';
    }

    private static function generateReturnType(ReflectionMethod $method) : string
    {
        $ret_type_str = self::getMethodReturnTypeString($method);

        /*
        foreach(get_declared_classes() as $clazz){
            if (strpos($clazz, $ret_type_str) !== false){
                return ' : ' . $clazz;
            }
        }
        */
        if (class_exists($ret_type_str)){
            $ret_type_str = self::NAMESPACE_SEPARATOR . $ret_type_str;
        }

        if ($method->hasReturnType()){
            return ' : ' . $ret_type_str;
        }
        return '';
    }

    private static function getMethodReturnTypeString(ReflectionMethod $method) : string
    {
        return self::getTypeString($method->getReturnType());
    }

    /**
     * @param array $lines
     * @param ReflectionMethod $method
     */
    private static function generateMethodBody(array& $lines, ReflectionMethod $method) : void
    {
        $calling_params = self::getParentMethodCallingParams($method);

        if ($method->isAbstract()){
            $lines[] = '        // Abstract Method';
        }
        else{
            $ret_type_str = self::getMethodReturnTypeString($method);
            if ($method->hasReturnType() && 'void' !== $ret_type_str){
                $lines[] = '        return parent::' . $method->getName() . '(' . $calling_params . ');';
            }
            else{
                $lines[] = '        parent::' . $method->getName() . '(' . $calling_params . ');';
            }
        }
    }

    /**
     * @param ReflectionMethod $method
     *
     * @return string
     */
    private static function getParentMethodCallingParams(ReflectionMethod $method) : string
    {
        $ret = [];
        foreach($method->getParameters() as $param){
            $ret[] = '$' . $param->getName();
        }
        return implode(', ', $ret);
    }

    /**
     * @param ReflectionType|null $type
     *
     * @return string
     */
    private static function getTypeString(?ReflectionType $type) : string
    {
        if ($type === null){
            return '';
        }
        return ($type instanceof ReflectionNamedType) ? $type->getName() : "$type";
    }

    /**
     * @param ReflectionParameter $param
     *
     * @return string
     * @throws ReflectionException
     */
    private static function getParamString(ReflectionParameter $param) : string
    {
        $ret = [];
        if ($param->hasType()){
            $ret[] = self::getTypeString($param->getType());
        }
        $ret[] = '$' . $param->getName();
        if ($param->allowsNull()){
            $ret[] = '= null';
        }
        else if ($param->isOptional()){
            $ret[] = '= ' . self::getDefaultValueString($param);
        }
        return implode(' ', $ret);
    }

    /**
     * @param ReflectionParameter $param
     *
     * @return string
     * @throws ReflectionException
     */
    private static function getDefaultValueString(ReflectionParameter $param) : string
    {
        $default_value = $param->getDefaultValue();
        if (is_string($default_value)){
            return '\'' . $default_value . '\'';
        }
        else{
            return $default_value;
        }
    }

    /**
     * @param ReflectionMethod $method
     *
     * @return string
     * @throws ReflectionException
     */
    private static function generateParameters(ReflectionMethod $method) : string
    {
        $params = $method->getParameters();

        $ret = [];
        foreach($params as $param){
            $ret[] = self::getParamString($param);
        }

        return implode(', ', $ret);
    }
}