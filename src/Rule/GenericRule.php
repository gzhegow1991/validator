<?php

namespace Gzhegow\Validator\Rule;

use Gzhegow\Lib\Lib;
use Gzhegow\Lib\Modules\Php\Result\Ret;
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
     * @param Ret $ret
     *
     * @return static|bool|null
     */
    public static function from($from, array $context = [], $ret = null)
    {
        $retCur = Result::asValue();

        $instance = null
            ?? static::fromStatic($from, $retCur)
            ?? static::fromRuleInstance($from, $retCur)
            ?? static::fromRuleClass($from, $context, $retCur)
            ?? static::fromRuleString($from, $context, $retCur);

        if ($retCur->isErr()) {
            return Result::err($ret, $retCur);
        }

        return Result::ok($ret, $instance);
    }

    /**
     * @param Ret $ret
     *
     * @return static|bool|null
     */
    public static function fromObject($from, $ret = null)
    {
        $retCur = Result::asValue();

        $instance = null
            ?? static::fromStatic($from, $retCur)
            ?? static::fromRuleInstance($from, $retCur);

        if ($retCur->isErr()) {
            return Result::err($ret, $retCur);
        }

        return Result::ok($ret, $instance);
    }


    /**
     * @param Ret $ret
     *
     * @return static|bool|null
     */
    public static function fromStatic($from, $ret = null)
    {
        if ($from instanceof static) {
            return Result::ok($ret, $from);
        }

        return Result::err(
            $ret,
            [ 'The `from` should be instance of: ' . static::class, $from ],
            [ __FILE__, __LINE__ ]
        );
    }

    /**
     * @param Ret $ret
     *
     * @return static|bool|null
     */
    public static function fromRuleInstance($from, $ret = null)
    {
        if (! ($from instanceof RuleInterface)) {
            return Result::err(
                $ret,
                [ 'The `from` should be instance of: ' . RuleInterface::class, $from ],
                [ __FILE__, __LINE__ ]
            );
        }

        $instance = new static();
        $instance->ruleInstance = $from;
        $instance->ruleClass = get_class($from);

        return Result::ok($ret, $instance);
    }

    /**
     * @param Ret $ret
     *
     * @return static|bool|null
     */
    public static function fromRuleClass($from, array $context = [], $ret = null)
    {
        if (! (is_string($from) && ('' !== $from))) {
            return Result::err(
                $ret,
                [ 'The `from` should be non-empty string', $from ],
                [ __FILE__, __LINE__ ]
            );
        }

        if (! is_subclass_of($from, RuleInterface::class)) {
            return Result::err(
                $ret,
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

        return Result::ok($ret, $instance);
    }

    /**
     * @param Ret $ret
     *
     * @return static|bool|null
     */
    public static function fromRuleString($from, array $context = [], $ret = null)
    {
        if (! (is_string($from) && ('' !== $from))) {
            return Result::err(
                $ret,
                [ 'The `from` should be non-empty string', $from ],
                [ __FILE__, __LINE__ ]
            );
        }

        if (! isset($context[ 'registry' ])) {
            return Result::err(
                $ret,
                [ 'The `context[registry]` is required', $context ],
                [ __FILE__, __LINE__ ]
            );
        }

        $ruleRegistry = $context[ 'registry' ];

        if (! ($ruleRegistry instanceof RuleRegistryInterface)) {
            return Result::err(
                $ret,
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
                $ret,
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
                $ret,
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
                $ret,
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

        return Result::ok($ret, $instance);
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
