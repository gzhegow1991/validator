<?php

namespace Gzhegow\Validator\Processor;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Filter\GenericFilter;
use Gzhegow\Validator\ValidatorFactoryInterface;
use Gzhegow\Validator\Exception\RuntimeException;
use Gzhegow\Validator\Validation\ValidationInterface;


class ValidatorProcessor implements ValidatorProcessorInterface
{
    /**
     * @var ValidatorFactoryInterface
     */
    protected $factory;


    public function __construct(
        ValidatorFactoryInterface $factory
    )
    {
        $this->factory = $factory;
    }


    /**
     * @return array{ 0?: mixed }
     */
    public function processFilter(
        GenericFilter $filter,
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : array
    {
        if ([] === $value) return [];

        $callable = $this->extractFilterCallable($filter);

        $callableArgs = [
            0 => $value[ 0 ],
            1 => $key,
            2 => $path,
            3 => $validation,
        ];

        $callableArgs += [
            'filter'     => $filter,
            'value'      => $value[ 0 ],
            'key'        => $key,
            'path'       => $path,
            'validation' => $validation,
        ];

        $result = $this->callUserFuncArray(
            $callable,
            $callableArgs
        );

        return (null === $result)
            ? []
            : [ $result ];
    }


    protected function callUserFuncArray($fn, array $args)
    {
        $theArr = Lib::arr();

        [ $list ] = $theArr->kwargs($args);

        $result = call_user_func_array($fn, $list);

        return $result;
    }


    /**
     * @return callable
     */
    protected function extractFilterCallable(GenericFilter $filter)
    {
        $fn = null;

        if ($filter->isClosure()) {
            $fn = $filter->getClosureObject();

        } elseif ($filter->isMethod()) {
            $object = null
                ?? ($filter->hasMethodObject() ? $filter->getMethodObject() : null)
                ?? $this->factory->newFilterObject($filter->getMethodClass());

            $method = $filter->getMethodName();

            $fn = [ $object, $method ];

        } elseif ($filter->isInvokable()) {
            $object = null
                ?? ($filter->hasInvokableObject() ? $filter->getInvokableObject() : null)
                ?? ($this->factory->newFilterObject($filter->getInvokableClass()));

            $fn = $object;

        } elseif ($filter->isFunction()) {
            if ($filter->hasFunctionStringNonInternal()) {
                $fn = $filter->getFunctionStringNonInternal();

            } else {
                $fn = $filter->getFunctionStringInternal();
                $fn = static function ($value) use ($fn) {
                    return Lib::func()->call_user_func($fn, $value);
                };
            }
        }

        if (! is_callable($fn)) {
            throw new RuntimeException(
                [
                    ''
                    . 'Unable to extract callable from handler. '
                    . 'Filter: ' . $filter->getKey(),
                    //
                    $filter,
                ]
            );
        }

        return $fn;
    }
}
