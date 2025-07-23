<?php

namespace Gzhegow\Validator\Rule;

use Gzhegow\Lib\Lib;
use Gzhegow\Lib\Modules\Type\Ret;
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
     * @return static|Ret<static>
     */
    public static function from($from, array $context = [], ?array $fallback = null)
    {
        $ret = Ret::new();

        $instance = null
            ?? static::fromStatic($from)->orNull($ret)
            ?? static::fromRuleInstance($from)->orNull($ret)
            ?? static::fromRuleClass($from, $context)->orNull($ret)
            ?? static::fromRuleString($from, $context)->orNull($ret);

        if ($ret->isFail()) {
            return Ret::throw($fallback, $ret);
        }

        return Ret::ok($fallback, $instance);
    }

    /**
     * @return static|Ret<static>
     */
    public static function fromObject($from, ?array $fallback = null)
    {
        $ret = Ret::new();

        $instance = null
            ?? static::fromStatic($from)->orNull($ret)
            ?? static::fromRuleInstance($from)->orNull($ret);

        if ($ret->isFail()) {
            return Ret::throw($fallback, $ret);
        }

        return Ret::ok($fallback, $instance);
    }


    /**
     * @return static|Ret<static>
     */
    public static function fromStatic($from, ?array $fallback = null)
    {
        if ($from instanceof static) {
            return Ret::ok($fallback, $from);
        }

        return Ret::throw(
            $fallback,
            [ 'The `from` should be instance of: ' . static::class, $from ],
            [ __FILE__, __LINE__ ]
        );
    }

    /**
     * @return static|Ret<static>
     */
    public static function fromRuleInstance($from, ?array $fallback = null)
    {
        if (! ($from instanceof RuleInterface)) {
            return Ret::throw(
                $fallback,
                [ 'The `from` should be instance of: ' . RuleInterface::class, $from ],
                [ __FILE__, __LINE__ ]
            );
        }

        $instance = new static();
        $instance->ruleInstance = $from;
        $instance->ruleClass = get_class($from);

        return Ret::ok($fallback, $instance);
    }

    /**
     * @return static|Ret<static>
     */
    public static function fromRuleClass($from, array $context = [], ?array $fallback = null)
    {
        $theType = Lib::type();

        if (! $theType->string_not_empty($from)->isOk([ &$fromStringNotEmpty, &$ret ])) {
            return Ret::throw($fallback, $ret);
        }

        if (! is_subclass_of($from, RuleInterface::class)) {
            return Ret::throw(
                $fallback,
                [ 'The `from` should be class-string of: ' . RuleInterface::class, $from ],
                [ __FILE__, __LINE__ ]
            );
        }

        $ruleParameters = $context[ 'parameters' ] ?? [];

        if (! is_array($ruleParameters)) {
            $ruleParameters = [];
        }

        $instance = new static();
        $instance->ruleClass = $fromStringNotEmpty;
        $instance->ruleClassParameters = $ruleParameters;

        return Ret::ok($fallback, $instance);
    }

    /**
     * @return static|Ret<static>
     */
    public static function fromRuleString($from, array $context = [], ?array $fallback = null)
    {
        $theType = Lib::type();

        if (! $theType->string_not_empty($from)->isOk([ &$fromStringNotEmpty, &$ret ])) {
            return Ret::throw($fallback, $ret);
        }

        if (! isset($context[ 'registry' ])) {
            return Ret::throw(
                $fallback,
                [ 'The `context[registry]` is required', $context ],
                [ __FILE__, __LINE__ ]
            );
        }

        $ruleRegistry = $context[ 'registry' ];

        if (! ($ruleRegistry instanceof RuleRegistryInterface)) {
            return Ret::throw(
                $fallback,
                [
                    'The `context[registry]` should be instance of: ' . RuleRegistryInterface::class,
                    $ruleRegistry,
                ],
                [ __FILE__, __LINE__ ]
            );
        }

        if (false
            || ! isset($context[ 'separator' ])
            || ! $theType->letter($context[ 'separator' ])->isOk([ &$ruleArgsSeparator ])
        ) {
            return Ret::throw(
                $fallback,
                [ 'The `context[ruleArgsSeparator]` should be one letter', $context[ 'separator' ] ],
                [ __FILE__, __LINE__ ]
            );
        }

        if (false
            || ! isset($context[ 'delimiter' ])
            || ! $theType->letter($context[ 'delimiter' ])->isOk([ &$ruleArgsDelimiter ])
        ) {
            return Ret::throw(
                $fallback,
                [ 'The `context[ruleArgsDelimiter]` should be one letter', $context[ 'delimiter' ] ],
                [ __FILE__, __LINE__ ]
            );
        }

        $explode = explode($ruleArgsSeparator, $from, 2);

        [ $ruleName, $ruleArguments ] = $explode + [ '', null ];

        if (! $ruleRegistry->hasRuleName($ruleName, $ruleClass)) {
            return Ret::throw(
                $fallback,
                [ 'Missing rule with name: ' . $ruleName ],
                [ __FILE__, __LINE__ ]
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

        $instance->ruleString = $fromStringNotEmpty;

        return Ret::ok($fallback, $instance);
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
     * @param string|null $refRuleString
     */
    public function hasRuleString(&$refRuleString = null) : bool
    {
        $refRuleString = null;

        if (null !== $this->ruleString) {
            $refRuleString = $this->ruleString;

            return true;
        }

        return false;
    }

    public function getRuleString() : string
    {
        return $this->ruleString;
    }
}
