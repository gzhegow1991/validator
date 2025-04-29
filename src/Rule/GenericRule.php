<?php

namespace Gzhegow\Validator\Rule;

use Gzhegow\Lib\Lib;
use Gzhegow\Lib\Modules\Php\Result\Result;
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
     * @return static|bool|null
     */
    public static function from($from, array $context = [], $ctx = null)
    {
        Result::parse($cur);

        $instance = null
            ?? static::fromStatic($from, $cur)
            ?? static::fromRuleInstance($from, $cur)
            ?? static::fromRuleClass($from, $context, $cur)
            ?? static::fromRuleString($from, $context, $cur);

        if ($cur->isErr()) {
            return Result::err($ctx, $cur);
        }

        return Result::ok($ctx, $instance);
    }

    /**
     * @return static|bool|null
     */
    public static function fromObject($from, $ctx = null)
    {
        Result::parse($cur);

        $instance = null
            ?? static::fromStatic($from, $cur)
            ?? static::fromRuleInstance($from, $cur);

        if ($cur->isErr()) {
            return Result::err($ctx, $cur);
        }

        return Result::ok($ctx, $instance);
    }


    /**
     * @return static|bool|null
     */
    public static function fromStatic($from, $ctx = null)
    {
        if ($from instanceof static) {
            return Result::ok($ctx, $from);
        }

        return Result::err(
            $ctx,
            [ 'The `from` should be instance of: ' . static::class, $from ],
            [ __FILE__, __LINE__ ]
        );
    }

    /**
     * @return static|bool|null
     */
    public static function fromRuleInstance($from, $ctx = null)
    {
        if (! ($from instanceof RuleInterface)) {
            return Result::err(
                $ctx,
                [ 'The `from` should be instance of: ' . RuleInterface::class, $from ],
                [ __FILE__, __LINE__ ]
            );
        }

        $instance = new static();
        $instance->ruleInstance = $from;
        $instance->ruleClass = get_class($from);

        return Result::ok($ctx, $instance);
    }

    /**
     * @return static|bool|null
     */
    public static function fromRuleClass($from, array $context = [], $ctx = null)
    {
        if (! (is_string($from) && ('' !== $from))) {
            return Result::err(
                $ctx,
                [ 'The `from` should be non-empty string', $from ],
                [ __FILE__, __LINE__ ]
            );
        }

        if (! is_subclass_of($from, RuleInterface::class)) {
            return Result::err(
                $ctx,
                [ 'The `from` should be class-string of: ' . RuleInterface::class, $from ],
                [ __FILE__, __LINE__ ]
            );
        }

        $ruleParameters = $context[ 'parameters' ] ?? [];

        if (! is_array($ruleParameters)) {
            $ruleParameters = [];
        }

        $instance = new static();
        $instance->ruleClass = $from;
        $instance->ruleClassParameters = $ruleParameters;

        return Result::ok($ctx, $instance);
    }

    /**
     * @return static|bool|null
     */
    public static function fromRuleString($from, array $context = [], $ctx = null)
    {
        if (! (is_string($from) && ('' !== $from))) {
            return Result::err(
                $ctx,
                [ 'The `from` should be non-empty string', $from ],
                [ __FILE__, __LINE__ ]
            );
        }

        if (! isset($context[ 'registry' ])) {
            return Result::err(
                $ctx,
                [ 'The `context[registry]` is required', $context ],
                [ __FILE__, __LINE__ ]
            );
        }

        $ruleRegistry = $context[ 'registry' ];

        if (! ($ruleRegistry instanceof RuleRegistryInterface)) {
            return Result::err(
                $ctx,
                [
                    'The `context[registry]` should be instance of: ' . RuleRegistryInterface::class,
                    $ruleRegistry,
                ],
                [ __FILE__, __LINE__ ]
            );
        }

        if (false
            || ! isset($context[ 'separator' ])
            || ! Lib::type()->letter($ruleArgsSeparator, $context[ 'separator' ])
        ) {
            return Result::err(
                $ctx,
                [
                    'The `context[ruleArgsSeparator]` should be one letter',
                    $context[ 'separator' ],
                ],
                [ __FILE__, __LINE__ ]
            );
        }

        if (false
            || ! isset($context[ 'delimiter' ])
            || ! Lib::type()->letter($ruleArgsDelimiter, $context[ 'delimiter' ])
        ) {
            return Result::err(
                $ctx,
                [
                    'The `context[ruleArgsDelimiter]` should be one letter',
                    $context[ 'delimiter' ],
                ],
                [ __FILE__, __LINE__ ]
            );
        }

        $explode = explode($ruleArgsSeparator, $from, 2);

        [ $ruleName, $ruleArguments ] = $explode + [ '', null ];

        if (! $ruleRegistry->hasRuleName($ruleName, $ruleClass)) {
            return Result::err(
                $ctx,
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

        $instance->ruleString = $from;

        return Result::ok($ctx, $instance);
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
