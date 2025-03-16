<?php

namespace Gzhegow\Validator;

use Gzhegow\Validator\Core\Validation\Validation;


class Validator
{
    public static function builder() : Validation
    {
        return static::$facade->builder();
    }

    /**
     * @param array<string, callable|callable[]> $filters
     */
    public static function make(
        array $data,
        //
        array $rules = [],
        array $filters = [],
        array $messages = [],
        array $attributes = []
    ) : Validation
    {
        return static::$facade->make(
            $data,
            //
            $rules,
            $filters,
            $messages,
            $attributes
        );
    }


    public static function setFacade(ValidatorInterface $facade) : ?ValidatorInterface
    {
        $last = static::$facade;

        static::$facade = $facade;

        return $last;
    }

    /**
     * @var ValidatorInterface
     */
    protected static $facade;
}
