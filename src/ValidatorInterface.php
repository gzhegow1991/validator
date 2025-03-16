<?php

namespace Gzhegow\Validator;

namespace Gzhegow\Validator;

use Gzhegow\Validator\Core\Rule\RuleInterface;
use Gzhegow\Validator\Core\Validation\Validation;


interface ValidatorInterface
{
    public function builder() : Validation;

    /**
     * @param array<string, callable|callable[]> $filters
     */
    public function make(
        array $data,
        //
        array $rules = [],
        array $filters = [],
        array $messages = [],
        array $attributes = []
    ) : Validation;


    /**
     * @param class-string<RuleInterface> $ruleClass
     *
     * @return static
     */
    public function extend(
        string $name, string $ruleClass, string $replacerClass = null,
        bool $isImplicit = null, bool $isDependent = null
    );

    /**
     * @param class-string<RuleInterface> $ruleClass
     *
     * @return static
     */
    public function extendImplicit(
        string $name, string $ruleClass, string $replacerClass = null,
        bool $isDependent = null
    );

    /**
     * @param class-string<RuleInterface> $ruleClass
     *
     * @return static
     */
    public function extendDependent(
        string $name, string $ruleClass, string $replacerClass = null,
        bool $isImplicit = null
    );
}
