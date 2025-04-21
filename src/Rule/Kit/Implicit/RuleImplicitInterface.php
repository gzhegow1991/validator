<?php

namespace Gzhegow\Validator\Rule\Kit\Implicit;

use Gzhegow\Validator\Rule\RuleInterface;


/**
 * > Этот тип правил означает, что правила должны применены даже если поле отсутствует
 * > Это не применимо к ключам, содержащим * (`wildcard`)
 */
interface RuleImplicitInterface extends RuleInterface
{
}
