<?php

namespace Gzhegow\Validator\Translator;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\RuleInterface;
use Gzhegow\Validator\Exception\RuntimeException;


class ValidatorPassTranslator implements ValidatorTranslatorInterface
{
    /**
     * @var string
     */
    protected $dirRoot;


    public function setDirRoot(?string $dirRoot) : void
    {
        if (null !== $dirRoot) {
            if (! Lib::fs()->type_dirpath_realpath($realpath, $dirRoot)) {
                throw new RuntimeException(
                    [ 'The `dirRoot` should be existing directory', $dirRoot ]
                );
            }
        }

        $this->dirRoot = $realpath ?? null;
    }


    public function translate(
        ?string $message, ?\Throwable $throwable,
        array $theValue, $theKey, array $thePath,
        RuleInterface $rule, array $ruleParameters
    ) : string
    {
        if (null !== $throwable) {
            // $file = $throwable->getFile();
            // $line = $throwable->getLine();
            //
            // if (null !== $this->dirRoot) {
            //     $file = Lib::fs()->path_relative($file, $this->dirRoot);
            // }
            //
            // $message = "[ ERROR ] {$message}\n{$file} : {$line}";

            throw new RuntimeException(
                [ 'Unable to ' . __METHOD__ ], $throwable
            );
        }

        return $message ?? 'validation.fatal';
    }
}
