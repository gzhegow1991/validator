<?php

namespace Gzhegow\Validator\Rule;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Validation\Validation;
use Gzhegow\Validator\Exception\LogicException;
use Gzhegow\Validator\Exception\RuntimeException;
use Gzhegow\Validator\RuleRegistry\RuleRegistryInterface;


class GenericRule
{
    /**
     * @var string
     */
    protected $ruleString;

    /**
     * @var class-string<RuleInterface>
     */
    protected $ruleClass;
    /**
     * @var array
     */
    protected $ruleClassParameters;

    /**
     * @var RuleInterface
     */
    protected $ruleInstance;


    private function __construct()
    {
    }

    public function __toString() : string
    {
        if (null === $this->ruleString) {
            throw new RuntimeException(
                [ 'Rule can be casted to string only if it was created from string' ]
            );
        }

        return $this->ruleString;
    }


    /**
     * @return static
     */
    public static function from($from, array $context = []) // : static
    {
        $instance = static::tryFrom($from, $context, $error);

        if (null === $instance) {
            throw $error;
        }

        return $instance;
    }

    /**
     * @return static
     */
    public static function fromObject($from, array $context = []) // : static
    {
        $instance = static::tryFromObject($from, $context, $error);

        if (null === $instance) {
            throw $error;
        }

        return $instance;
    }

    /**
     * @return static
     */
    public static function fromClassAndParameters($ruleClass, array $context = []) // : static
    {
        $instance = static::tryFromClassAndParameters($ruleClass, $context, $error);

        if (null === $instance) {
            throw $error;
        }

        return $instance;
    }

    /**
     * @return static
     */
    public static function fromString($ruleString, array $context = []) // : static
    {
        $instance = static::tryFromString($ruleString, $context, $error);

        if (null === $instance) {
            throw $error;
        }

        return $instance;
    }


    /**
     * @return static|null
     */
    public static function tryFrom($from, array $context = [], ?\Throwable &$last = null) // : ?static
    {
        $last = null;

        Lib::php()->errors_start($b);

        $instance = null
            ?? static::tryingFromObjectStatic($from, $context)
            ?? static::tryingFromObjectInstance($from, $context)
            ?? static::tryingFromClassAndParameters($from, $context)
            ?? static::tryingFromString($from, $context);

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
    public static function tryFromObject($from, array $context = [], ?\Throwable &$last = null) // : ?static
    {
        $last = null;

        Lib::php()->errors_start($b);

        $instance = null
            ?? static::tryingFromObjectStatic($from, $context)
            ?? static::tryingFromObjectInstance($from, $context);

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
    public static function tryFromClassAndParameters($ruleClass, array $context = [], ?\Throwable &$last = null) // : ?static
    {
        $last = null;

        Lib::php()->errors_start($b);

        $instance = null
            ?? static::tryingFromObjectStatic($ruleClass, $context)
            //    ?? static::tryingFromClassAndParameters($ruleClass, $ruleParameters)
            ?? static::tryingFromClassAndParameters($ruleClass, $context);

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
    public static function tryFromString($ruleString, array $context = [], ?\Throwable &$last = null) // : ?static
    {
        $last = null;

        Lib::php()->errors_start($b);

        $instance = null
            ?? static::tryingFromClassAndParameters($ruleString, $context)
            ?? static::tryingFromString($ruleString, $context);

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
    protected static function tryingFromObjectStatic($static, array $context = []) // : ?static
    {
        if (! is_a($static, static::class)) {
            return Lib::php()->error(
                [ 'The `static` should be instance of: ' . static::class, $static ]
            );
        }

        return $static;
    }

    /**
     * @return static|null
     */
    protected static function tryingFromObjectInstance($instance, array $context = []) // : ?static
    {
        if (! is_a($instance, RuleInterface::class)) {
            return Lib::php()->error(
                [ 'The `instance` should be instance of: ' . RuleInterface::class, $instance ]
            );
        }

        $object = new static();
        $object->ruleInstance = $instance;
        $object->ruleClass = get_class($instance);

        return $object;
    }

    /**
     * @return static|null
     */
    protected static function tryingFromClassAndParameters($ruleClass, array $context = []) // : ?static
    {
        if (! (is_string($ruleClass) && ('' !== $ruleClass))) {
            return Lib::php()->error(
                [ 'The `ruleClass` should be non-empty string', $ruleClass ]
            );
        }

        if (! is_subclass_of($ruleClass, RuleInterface::class)) {
            return Lib::php()->error(
                [ 'The `ruleClass` should be class-string of: ' . RuleInterface::class, $ruleClass ]
            );
        }

        $ruleParameters = $context[ 'parameters' ] ?? [];

        if (! is_array($ruleParameters)) {
            $ruleParameters = [];
        }

        $instance = new static();
        $instance->ruleClass = $ruleClass;
        $instance->ruleClassParameters = $ruleParameters;

        return $instance;
    }

    /**
     * @return static|null
     */
    protected static function tryingFromString($ruleString, array $context = []) // : ?static
    {
        if (! (is_string($ruleString) && ('' !== $ruleString))) {
            return Lib::php()->error(
                [ 'The `ruleString` should be non-empty string', $ruleString ]
            );
        }

        if (! isset($context[ 'registry' ])) {
            return Lib::php()->error(
                [ 'The `context[registry]` is required' ]
            );
        }

        $ruleRegistry = $context[ 'registry' ];

        if (! ($ruleRegistry instanceof RuleRegistryInterface)) {
            return Lib::php()->error(
                [
                    'The `context[registry]` should be instance of: ' . RuleRegistryInterface::class,
                    $ruleRegistry,
                ]
            );
        }

        if (false
            || ! isset($context[ 'separator' ])
            || ! Lib::type()->letter($ruleArgsSeparator, $context[ 'separator' ])
        ) {
            return Lib::php()->error(
                [
                    'The `context[ruleArgsSeparator]` should be one letter',
                    $context[ 'separator' ],
                ]
            );
        }

        if (false
            || ! isset($context[ 'delimiter' ])
            || ! Lib::type()->letter($ruleArgsDelimiter, $context[ 'delimiter' ])
        ) {
            return Lib::php()->error(
                [
                    'The `context[ruleArgsDelimiter]` should be one letter',
                    $context[ 'delimiter' ],
                ]
            );
        }

        $explode = explode($ruleArgsSeparator, $ruleString, 2);

        [ $ruleName, $ruleArguments ] = $explode + [ '', null ];

        if (! $ruleRegistry->hasRuleName($ruleName, $ruleClass)) {
            throw new RuntimeException(
                [ 'Missing rule with name: ' . $ruleName ]
            );
        }

        $ruleArgumentsArray = [];

        if (null !== $ruleArguments) {
            $ruleArgumentsArray = explode($ruleArgsDelimiter, $ruleArguments);

            foreach ( $ruleArgumentsArray as $ii => $ruleArgument ) {
                if ($ruleArgument === '') {
                    $ruleArgumentsArray[ $ii ] = null;
                }
            }
        }

        $instance = $ruleClass::parse($ruleName, $ruleArgumentsArray);

        $instance->ruleString = $ruleString;

        return $instance;
    }


    /**
     * @return string|null
     */
    public function hasString() : ?string
    {
        return $this->ruleString;
    }

    public function getString() : string
    {
        return $this->ruleString;
    }


    /**
     * @return class-string<RuleInterface>|null
     */
    public function hasClass() : ?string
    {
        return $this->ruleClass;
    }

    /**
     * @return class-string<RuleInterface>
     */
    public function getClass() : string
    {
        return $this->ruleClass;
    }

    /**
     * @return array
     */
    public function getClassParameters() : array
    {
        return $this->ruleClassParameters;
    }


    public function hasInstance() : ?RuleInterface
    {
        return $this->ruleInstance;
    }

    /**
     * @return RuleInterface
     */
    public function getInstance() : RuleInterface
    {
        return $this->ruleInstance;
    }
}
