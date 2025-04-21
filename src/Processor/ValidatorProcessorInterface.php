<?php

namespace Gzhegow\Validator\Processor;

use Gzhegow\Validator\Filter\GenericFilter;
use Gzhegow\Validator\Validation\ValidationInterface;


interface ValidatorProcessorInterface
{
    /**
     * @return array{ 0?: mixed }
     */
    public function processFilter(
        GenericFilter $filter,
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : array;
}
