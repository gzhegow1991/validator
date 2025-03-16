<?php

namespace Gzhegow\Validator;


interface ValidatorAwareInterface
{
    /**
     * @param null|ValidatorInterface $loggerman
     *
     * @return void
     */
    public function setValidator(?ValidatorInterface $loggerman) : void;
}
