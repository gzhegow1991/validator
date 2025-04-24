<?php

namespace Gzhegow\Validator\Translator;


interface ValidatorTranslatorInterface
{
    /**
     * @param array<string, array[]> $errorsByKey
     *
     * @return array<string, string[]>
     */
    public function translate(array $errorsByKey) : array;
}
