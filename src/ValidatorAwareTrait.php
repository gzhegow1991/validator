<?php

namespace Gzhegow\Validator;


trait ValidatorAwareTrait
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;


    /**
     * @param null|ValidatorInterface $validator
     *
     * @return void
     */
    public function setValidator(?ValidatorInterface $validator) : void
    {
        $this->validator = $validator;
    }
}
