<?php

namespace Gzhegow\Validator\Rule\Kit\Type\File;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\GenericRule;
use Gzhegow\Validator\Validation\ValidationInterface;
use Gzhegow\Validator\Rule\Kit\Type\AbstractRuleType;


class FileRule extends AbstractRuleType
{
    public static function parse(string $ruleName, array $ruleArguments = []) : GenericRule
    {
        $ruleParameters[ 0 ] = $ruleArguments[ 0 ] ?? null;
        $ruleParameters[ 1 ] = $ruleArguments[ 1 ] ?? null;
        $ruleParameters[ 2 ] = $ruleArguments[ 2 ] ?? null;

        $ruleParameters[ 0 ] = is_string($ruleParameters[ 0 ])
            ? explode(',', $ruleParameters[ 0 ])
            : [];

        $ruleParameters[ 1 ] = is_string($ruleParameters[ 1 ])
            ? explode(',', $ruleParameters[ 1 ])
            : [];

        $ruleParameters[ 2 ] = is_string($ruleParameters[ 2 ])
            ? explode(',', $ruleParameters[ 2 ])
            : [];

        return GenericRule::fromRuleClass(
            static::class,
            [ 'parameters' => $ruleParameters ]
        );
    }


    const NAME = 'file';

    public static function message(array $conditions = []) : string
    {
        return 'validation.file';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $parameter0 = $this->parameters[ 0 ] ?? null;
        $parameter1 = $this->parameters[ 1 ] ?? null;
        $parameter2 = $this->parameters[ 2 ] ?? null;

        $extensions = null
            ?? (is_array($parameter0) ? $parameter0 : null)
            ?? (is_string($parameter0) ? [ $parameter0 ] : null)
            ?? null;

        $mimeTypes = null
            ?? (is_array($parameter1) ? $parameter1 : null)
            ?? (is_string($parameter1) ? [ $parameter1 ] : null)
            ?? null;

        $filterStrings = null
            ?? (is_array($parameter2) ? $parameter2 : null)
            ?? (is_string($parameter2) ? [ $parameter2 ] : null)
            ?? [];

        $filters = null;
        if ([] !== $filterStrings) {
            foreach ( $filterStrings as $filterName => $filterValue ) {
                if (is_int($filterName)) {
                    [ $filterName, $filterValue ] = explode('=', $filterValue);
                }

                $filters[ $filterName ] = $filterValue;
            }
        }

        $status = Lib::type()->file(
            $value[ 0 ],
            $extensions, $mimeTypes,
            $filters
        )->isOk();

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
