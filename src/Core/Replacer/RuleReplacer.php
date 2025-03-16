<?php

namespace Gzhegow\Validator\Core\Replacer;

use Gzhegow\Validator\Package\Illuminate\Validation\ValidatorInterface;


class RuleReplacer implements ReplacerInterface
{
    public function replace(
        $message, $attribute, $rule, $parameters,
        ValidatorInterface $validator
    ) : string
    {
        $ruleInstanceKey = md5(serialize([ $attribute, $rule, $parameters ]));

        $ruleInstance = $validator->getRuleInstance($ruleInstanceKey);

        return $ruleInstance->replace(
            $message, $attribute, $rule, $parameters,
            $validator
        );
    }
}
