<?php

namespace Gzhegow\Validator;

use Gzhegow\Validator\Validation\ValidationInterface;


class Validator
{
    private function __construct()
    {
    }


    public static function new() : ValidationInterface
    {
        return static::$facade->new();
    }


    public static function setFacade(?ValidatorInterface $facade) : ?ValidatorInterface
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
