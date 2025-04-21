<?php

namespace Gzhegow\Validator\Filter;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Exception\LogicException;


class GenericFilter
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var array
     */
    protected $args = [];

    /**
     * @var bool
     */
    protected $isClosure = false;
    /**
     * @var \Closure
     */
    protected $closureObject;

    /**
     * @var bool
     */
    protected $isMethod = false;
    /**
     * @var class-string
     */
    protected $methodClass;
    /**
     * @var object
     */
    protected $methodObject;
    /**
     * @var string
     */
    protected $methodName;

    /**
     * @var bool
     */
    protected $isInvokable = false;
    /**
     * @var callable|object
     */
    protected $invokableObject;
    /**
     * @var class-string
     */
    protected $invokableClass;

    /**
     * @var bool
     */
    protected $isFunction = false;
    /**
     * @var callable|string
     */
    protected $functionStringInternal;
    /**
     * @var callable|string
     */
    protected $functionStringNonInternal;


    private function __construct()
    {
    }


    /**
     * @return static
     */
    public static function from($from, array $args = []) // : static
    {
        $instance = static::tryFrom($from, $args, $error);

        if (null === $instance) {
            throw $error;
        }

        return $instance;
    }

    /**
     * @return static|null
     */
    public static function tryFrom($from, array $args = [], ?\Throwable &$last = null) // : ?static
    {
        $last = null;

        Lib::php()->errors_start($b);

        $instance = null
            ?? static::tryFromInstance($from)
            ?? static::tryFromClosure($from, $args)
            ?? static::tryFromMethod($from, $args)
            ?? static::tryFromInvokable($from, $args)
            ?? static::tryFromFunction($from, $args);

        $errors = Lib::php()->errors_end($b);

        if (null === $instance) {
            foreach ( $errors as $error ) {
                $last = new LogicException($error, $last);
            }
        }

        return $instance;
    }


    /**
     * @return static|null
     */
    protected static function tryFromInstance($instance) // : ?static
    {
        if (! is_a($instance, static::class)) {
            return Lib::php()->error(
                [ 'The `from` should be instance of: ' . static::class, $instance ]
            );
        }

        return $instance;
    }

    /**
     * @return static|null
     */
    protected static function tryFromClosure($closure, array $args = []) // : ?static
    {
        if (! is_a($closure, \Closure::class)) {
            return Lib::php()->error(
                [ 'The `from` should be instance of: ' . \Closure::class, $closure ]
            );
        }

        $instance = new static();
        $instance->args = $args;

        $instance->isClosure = true;
        $instance->closureObject = $closure;

        $phpId = spl_object_id($closure);

        $instance->key = "{ object # \Closure # {$phpId} }";

        return $instance;
    }

    /**
     * @return static|null
     */
    protected static function tryFromMethod($method, array $args = []) // : ?static
    {
        $thePhp = Lib::php();

        if (! $thePhp->type_method_string($methodString, $method, [ &$methodArray ])) {
            return $thePhp->error(
                [ 'The `from` should be existing method', $method ]
            );
        }

        [ $objectOrClass, $methodName ] = $methodArray;

        $instance = new static();
        $instance->args = $args;

        $instance->isMethod = true;

        if (is_object($objectOrClass)) {
            $object = $objectOrClass;

            $phpClass = get_class($object);
            $phpId = spl_object_id($object);

            $key0 = "\"{ object # {$phpClass} # {$phpId} }\"";

            $instance->methodObject = $object;

        } else {
            $objectClass = $objectOrClass;

            $key0 = '"' . $objectClass . '"';

            $instance->methodClass = $objectClass;
        }

        $key1 = "\"{$methodName}\"";

        $instance->methodName = $methodName;

        $instance->key = "[ {$key0}, {$key1} ]";

        return $instance;
    }

    /**
     * @return static|null
     */
    protected static function tryFromInvokable($invokable, array $args = []) // : ?static
    {
        $instance = null;

        if (is_object($invokable)) {
            if (! method_exists($invokable, '__invoke')) {
                return null;
            }

            $instance = new static();
            $instance->args = $args;

            $instance->isInvokable = true;
            $instance->invokableObject = $invokable;

            $phpClass = get_class($invokable);
            $phpId = spl_object_id($invokable);

            $instance->key = "\"{ object # {$phpClass} # {$phpId} }\"";

        } else {
            if (Lib::type()->string_not_empty($_invokableClass, $invokable)) {
                if (! class_exists($_invokableClass)) {
                    return null;
                }

                if (! method_exists($_invokableClass, '__invoke')) {
                    return null;
                }

                $instance = new static();
                $instance->args = $args;

                $instance->isInvokable = true;
                $instance->invokableClass = $_invokableClass;

                $instance->key = "\"{$_invokableClass}\"";
            }
        }

        if (null === $instance) {
            return Lib::php()->error(
                [ 'The `from` should be existing invokable class or object', $invokable ]
            );
        }

        return $instance;
    }

    /**
     * @return static|null
     */
    protected static function tryFromFunction($function, array $args = []) // : ?static
    {
        $thePhp = Lib::php();

        if (! Lib::type()->string_not_empty($_function, $function)) {
            return $thePhp->error(
                [ 'The `from` should be existing function name', $function ]
            );
        }

        if (! function_exists($_function)) {
            return $thePhp->error(
                [ 'The `from` should be existing function name', $_function ]
            );
        }

        $instance = new static();
        $instance->args = $args;

        $instance->isFunction = true;

        $isInternal = $thePhp->type_callable_string_function_internal($_functionInternal, $_function);

        if ($isInternal) {
            $instance->functionStringInternal = $_function;

        } else {
            $instance->functionStringNonInternal = $_function;
        }

        $instance->key = "\"{$_function}\"";

        return $instance;
    }


    public function __serialize() : array
    {
        $vars = get_object_vars($this);

        return array_filter($vars);
    }

    public function __unserialize(array $data) : void
    {
        foreach ( $data as $key => $val ) {
            $this->{$key} = $val;
        }
    }

    public function serialize()
    {
        $array = $this->__serialize();

        return serialize($array);
    }

    public function unserialize($data)
    {
        $array = unserialize($data);

        $this->__unserialize($array);
    }


    public function getKey() : string
    {
        return $this->key;
    }


    public function getArgs() : array
    {
        return $this->args;
    }


    public function isClosure() : bool
    {
        return $this->isClosure;
    }

    public function hasClosureObject() : ?\Closure
    {
        return $this->closureObject;
    }

    public function getClosureObject() : \Closure
    {
        return $this->closureObject;
    }



    public function isMethod() : bool
    {
        return $this->isMethod;
    }

    /**
     * @return \class-string|null
     */
    public function hasMethodClass() : ?string
    {
        return $this->methodClass;
    }

    /**
     * @return \class-string
     */
    public function getMethodClass() : string
    {
        return $this->methodClass;
    }


    public function hasMethodObject() : ?object
    {
        return $this->methodObject;
    }

    public function getMethodObject() : object
    {
        return $this->methodObject;
    }


    public function hasMethodName() : ?string
    {
        return $this->methodName;
    }

    public function getMethodName() : string
    {
        return $this->methodName;
    }


    public function isInvokable() : bool
    {
        return $this->isInvokable;
    }

    /**
     * @return callable|object|null
     */
    public function hasInvokableObject() : ?object
    {
        return $this->invokableObject;
    }

    /**
     * @return callable|object
     */
    public function getInvokableObject() : object
    {
        return $this->invokableObject;
    }

    /**
     * @return callable|object|null
     */
    public function hasInvokableClass() : ?string
    {
        return $this->invokableObject;
    }

    /**
     * @return \class-string
     */
    public function getInvokableClass() : string
    {
        return $this->invokableClass;
    }


    public function isFunction() : bool
    {
        return $this->isFunction;
    }


    /**
     * @return callable|string|null
     */
    public function hasFunctionStringInternal() : ?string
    {
        return $this->functionStringInternal;
    }

    /**
     * @return callable|string
     */
    public function getFunctionStringInternal() : string
    {
        return $this->functionStringInternal;
    }


    /**
     * @return callable|string|null
     */
    public function hasFunctionStringNonInternal() : ?string
    {
        return $this->functionStringNonInternal;
    }

    /**
     * @return callable|string
     */
    public function getFunctionStringNonInternal() : string
    {
        return $this->functionStringNonInternal;
    }
}
