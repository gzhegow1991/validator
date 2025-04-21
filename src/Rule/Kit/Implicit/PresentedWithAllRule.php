<?php

namespace Gzhegow\Validator\Rule\Kit\Implicit;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\GenericRule;
use Gzhegow\Validator\Validation\ValidationInterface;


class PresentedWithAllRule extends AbstractRuleImplicit
{
    public static function parse(string $ruleName, array $ruleArguments = []) : GenericRule
    {
        $ruleParameters[ 0 ] = $ruleArguments[ 0 ] ?? null;

        $ruleParameters[ 0 ] = is_string($ruleParameters[ 0 ])
            ? explode(',', $ruleParameters[ 0 ])
            : [];

        return GenericRule::fromClassAndParameters(
            static::class,
            [ 'parameters' => $ruleParameters ]
        );
    }


    const NAME = 'presented_with_all';

    public static function message(array $conditions = []) : string
    {
        return 'validation.presented_with_all';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if (! isset($this->parameters[ 0 ])) {
            return 'validation.fatal';
        }

        $parameter0 = $this->parameters[ 0 ];

        if (! is_array($parameter0)) {
            return 'validation.fatal';
        }

        $fieldsArray = $parameter0;

        $allExists = true;
        foreach ( $fieldsArray as $field ) {
            $fieldPath = $validation->fieldPathOrAbsolute($field, $path);

            if (! $validation->has($fieldPath)) {
                $allExists = false;

                break;
            }
        }

        if ($allExists) {
            if ([] === $value) {
                return static::message();
            }

            if (null === $value[ 0 ]) {
                return static::message();
            }
        }

        return null;
    }
}
