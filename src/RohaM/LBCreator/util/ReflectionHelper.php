<?php

declare(strict_types=1);

namespace RohaM\LBCreator\util;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

class ReflectionHelper {

    public static function getGetterMethods(ReflectionClass $reflection): array {
        $methods = [];
        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $name = $method->getName();
            if (str_starts_with($name, "get") && $method->getNumberOfParameters() <= 1) {
                $methods[] = $method;
            }
        }
        return $methods;
    }

    public static function getObjectProperties(ReflectionClass $reflection): array {
        $properties = [];
        foreach ($reflection->getProperties() as $prop) {
            $type = $prop->getType();
            if ($type && !$type->isBuiltin()) {
                $properties[] = $prop;
            }
        }
        return $properties;
    }

    public static function getPropertyValue(object $object, string $propertyName) {
        $reflection = new ReflectionClass($object);
        if ($reflection->hasProperty($propertyName)) {
            $prop = $reflection->getProperty($propertyName);
            $prop->setAccessible(true);
            return $prop->getValue($object);
        }
        return null;
    }

    public static function callMethod(object $object, string $methodName, array $args = []) {
        $reflection = new ReflectionClass($object);
        if ($reflection->hasMethod($methodName)) {
            $method = $reflection->getMethod($methodName);
            $method->setAccessible(true);
            return $method->invokeArgs($object, $args);
        }
        return null;
    }

    public static function hasMethod(object $object, string $methodName): bool {
        $reflection = new ReflectionClass($object);
        return $reflection->hasMethod($methodName);
    }

    public static function hasProperty(object $object, string $propertyName): bool {
        $reflection = new ReflectionClass($object);
        return $reflection->hasProperty($propertyName);
    }

    public static function getSubclasses(string $className): array {
        $classes = [];
        foreach (get_declared_classes() as $class) {
            if (is_subclass_of($class, $className)) {
                $classes[] = $class;
            }
        }
        return $classes;
    }

    public static function getInterfaces(ReflectionClass $reflection): array {
        return $reflection->getInterfaces();
    }
}