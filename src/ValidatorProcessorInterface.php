<?php

namespace Gzhegow\Validator;

interface ValidatorProcessorInterface
{
    /**
     * @param callable $fnExtension
     */
    public function callExtension($fnExtension, array $args = []) : bool;

    /**
     * @param callable $fnReplacer
     */
    public function callReplacer($fnReplacer, array $args = []) : ?string;
}
