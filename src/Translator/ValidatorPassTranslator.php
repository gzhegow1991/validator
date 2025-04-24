<?php

namespace Gzhegow\Validator\Translator;

use Gzhegow\Validator\Exception\RuntimeException;


class ValidatorPassTranslator implements ValidatorTranslatorInterface
{
    /**
     * @param array<string, array[]> $errorsByKey
     *
     * @return array<string, string[]>
     */
    public function translate(array $errorsByKey) : array
    {
        // > переводчик должен переводить все фразы batch-запросами

        $list = [];
        foreach ( $errorsByKey as &$errors ) {
            foreach ( $errors as &$error ) {
                $list[] =& $error;
            }
            unset($error);
        }
        unset($errors);

        foreach ( $list as $i => $error ) {
            $list[ $i ] = $this->translateError($error);
        }
        unset($error);

        return $errorsByKey;
    }

    protected function translateError(array $error) : string
    {
        $message = $error[ 'message' ] ?? null;
        $throwable = $error[ 'throwable' ] ?? null;

        if (null !== $throwable) {
            throw new RuntimeException('Unable to ' . __METHOD__, $throwable);
        }

        return (is_string($message) && ('' !== $message))
            ? $message
            : 'validation.fatal';
    }
}
