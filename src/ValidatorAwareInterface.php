<?php

namespace Gzhegow\Validator;


interface ValidatorAwareInterface
{
    /**
     * @param null|ValidatorInterface $validator
     *
     * @return void
     */
    public function setValidator(?ValidatorInterface $validator) : void;
}
