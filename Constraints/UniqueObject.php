<?php

namespace steevanb\SymfonyValidatorConstraints\Constraints;

use Symfony\Component\Validator\Constraint;

class UniqueObject extends Constraint
{
    const TYPE_OUT_RANGE = 'OUT_RANGE';

    /** @var string */
    protected $message = 'Non-unique object found.';

    /** @var array */
    protected $properties = [];

    /** @var array */
    protected $getters = [];

    /** @var bool */
    protected $strict = true;

    /** @var string */
    protected $uniqid;

    /**
     * @return string
     */
    public function validatedBy()
    {
        return 'steevanb\\SymfonyValidatorConstraints\\Constraints\\UniqueObjectValidator';
    }

    /**
     * @return array
     */
    public function getRequiredOptions()
    {
        return ['uniqid'];
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @return array
     */
    public function getGetters()
    {
        return $this->getters;
    }

    /**
     * @return bool
     */
    public function getStrict()
    {
        return $this->strict;
    }

    /**
     * @return string
     */
    public function getUniqid()
    {
        return $this->uniqid;
    }
}
