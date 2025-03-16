<?php

namespace Gzhegow\Validator;

use Gzhegow\Lib\Lib;


class ValidatorProcessor implements ValidatorProcessorInterface
{
    /**
     * @param callable $fnExtension
     */
    public function callExtension($fnExtension, array $args = []) : bool
    {
        [ $list ] = Lib::arr()->kwargs($args);

        return call_user_func_array($fnExtension, $list);
    }

    /**
     * @param callable $fnReplacer
     */
    public function callReplacer($fnReplacer, array $args = []) : ?string
    {
        [ $list ] = Lib::arr()->kwargs($args);

        return call_user_func_array($fnReplacer, $list);
    }
}
