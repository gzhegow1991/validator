<?php

namespace Gzhegow\Validator\Rule\Kit\Type;

use Gzhegow\Validator\Rule\RuleInterface;


/**
 * > Этот тип правил означает, что после применения правила значение нужно сохранить в исходный массив
 * > Разумно привести к дате только один раз, если далее предполагается её сравнить с чем-либо
 */
interface RuleTypeInterface extends RuleInterface
{
}
