<?php

namespace Gzhegow\Validator\Rule;

use Gzhegow\Lib\Lib;
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
    public static function from($from, array $context = [], array $refs = [])
    {
        $withErrors = array_key_exists(0, $refs);

        $refs[ 0 ] = $refs[ 0 ] ?? null;

        $instance = null
            ?? static::fromStatic($from, $refs)
            ?? static::fromRuleInstance($from, $refs)
            ?? static::fromRuleClass($from, $context, $refs)
            ?? static::fromRuleString($from, $context, $refs);

        if (! $withErrors) {
            if (null === $instance) {
                throw $refs[ 0 ];
            }
        }

        return $instance;
    }

    /**
     * @return static
     */
    public static function fromObject($from, array $refs = [])
    {
        $withErrors = array_key_exists(0, $refs);

        $refs[ 0 ] = $refs[ 0 ] ?? null;

        $instance = null
            ?? static::fromStatic($from, $refs)
            ?? static::fromRuleInstance($from, $refs);

        if (! $withErrors) {
            if (null === $instance) {
                throw $refs[ 0 ];
            }
        }

        return $instance;
    }


    /**
     * @return static|bool|null
     */
    public static function fromStatic($from, array $refs = [])
    {
        if ($from instanceof static) {
            return Lib::refsResult($refs, $from);
        }

        return Lib::refsError(
            $refs,
            new LogicException(
                [ 'The `from` should be instance of: ' . static::class, $from ]
            )
        );
    }

    /**
     * @return static|bool|null
     */
    public static function fromRuleInstance($from, array $refs = [])
    {
        if (! ($from instanceof RuleInterface)) {
            return Lib::refsError(
                $refs,
                new LogicException(
                    [ 'The `from` should be instance of: ' . RuleInterface::class, $from ]
                )
            );
        }

        $instance = new static();
        $instance->ruleInstance = $from;
        $instance->ruleClass = get_class($from);

        return Lib::refsResult($refs, $instance);
    }

    /**
     * @return static|bool|null
     */
    public static function fromRuleClass($from, array $context = [], array $refs = [])
    {
        if (! (is_string($from) && ('' !== $from))) {
            return Lib::refsError(
                $refs,
                new LogicException(
                    [ 'The `from` should be non-empty string', $from ]
                )
            );
        }

        if (! is_subclass_of($from, RuleInterface::class)) {
            return Lib::refsError(
                $refs,
                new LogicException(
                    [ 'The `from` should be class-string of: ' . RuleInterface::class, $from ]
                )
            );
        }

        $ruleParameters = $context[ 'parameters' ] ?? [];

        if (! is_array($ruleParameters)) {
            $ruleParameters = [];
        }

        $instance = new static();
        $instance->ruleClass = $from;
        $instance->ruleClassParameters = $ruleParameters;

        return Lib::refsResult($refs, $instance);
    }

    /**
     * @return static|bool|null
     */
    public static function fromRuleString($from, array $context = [], array $refs = [])
    {
        if (! (is_string($from) && ('' !== $from))) {
            return Lib::refsError(
                $refs,
                new LogicException(
                    [ 'The `from` should be non-empty string', $from ]
                )
            );
        }

        if (! isset($context[ 'registry' ])) {
            return Lib::refsError(
                $refs,
                new LogicException(
                    [ 'The `context[registry]` is required', $context ]
                )
            );
        }

        $ruleRegistry = $context[ 'registry' ];

        if (! ($ruleRegistry instanceof RuleRegistryInterface)) {
            return Lib::refsError(
                $refs,
                new LogicException(
                    [
                        'The `context[registry]` should be instance of: ' . RuleRegistryInterface::class,
                        $ruleRegistry,
                    ]
                )
            );
        }

        if (false
            || ! isset($context[ 'separator' ])
            || ! Lib::type()->letter($ruleArgsSeparator, $context[ 'separator' ])
        ) {
            return Lib::refsError(
                $refs,
                new LogicException(
                    [
                        'The `context[ruleArgsSeparator]` should be one letter',
                        $context[ 'separator' ],
                    ]
                )
            );
        }

        if (false
            || ! isset($context[ 'delimiter' ])
            || ! Lib::type()->letter($ruleArgsDelimiter, $context[ 'delimiter' ])
        ) {
            return Lib::refsError(
                $refs,
                new LogicException(
                    [
                        'The `context[ruleArgsDelimiter]` should be one letter',
                        $context[ 'delimiter' ],
                    ]
                )
            );
        }

        $explode = explode($ruleArgsSeparator, $from, 2);

        [ $ruleName, $ruleArguments ] = $explode + [ '', null ];

        if (! $ruleRegistry->hasRuleName($ruleName, $ruleClass)) {
            return Lib::refsError(
                $refs,
                new RuntimeException(
                    [ 'Missing rule with name: ' . $ruleName ]
                )
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

        $instance->ruleString = $from;

        return Lib::refsResult($refs, $instance);
    }


    public function hasRuleInstance() : ?RuleInterface
    {
        return $this->ruleInstance;
    }

    /**
     * @return RuleInterface
     */
    public function getRuleInstance() : RuleInterface
    {
        return $this->ruleInstance;
    }


    /**
     * @return class-string<RuleInterface>|null
     */
    public function hasRuleClass() : ?string
    {
        return $this->ruleClass;
    }

    /**
     * @return class-string<RuleInterface>
     */
    public function getRuleClass() : string
    {
        return $this->ruleClass;
    }

    /**
     * @return array
     */
    public function getRuleClassParameters() : array
    {
        return $this->ruleClassParameters;
    }


    /**
     * @return string|null
     */
    public function hasRuleString() : ?string
    {
        return $this->ruleString;
    }

    public function getRuleString() : string
    {
        return $this->ruleString;
    }
}
