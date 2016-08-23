<?php

namespace steevanb\SymfonyValidatorConstraints\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueObjectValidator extends ConstraintValidator
{
    /** @var array */
    protected $traversablesValues = [];

    /**
     * @param mixed $traversable
     * @param UniqueObject $constraint
     */
    public function validate($traversable, Constraint $constraint)
    {
        $this
            ->assertIsTraversable($traversable)
            ->defineTraversablesValues($traversable, $constraint);

        foreach ($traversable as $data) {
            $this->compare($data, $constraint);
        }
    }

    /**
     * @param mixed $data
     * @throws \Exception
     * @return $this
     */
    protected function assertIsTraversable($data)
    {
        if (is_array($data) === false && $data instanceof \Traversable === false) {
            throw new \Exception(gettype($data) . ' is not iterable.');
        }

        return $this;
    }

    /**
     * @param array|\Traversable $traversable
     * @param UniqueObject $uniqueObject
     * @return $this
     */
    protected function defineTraversablesValues($traversable, UniqueObject $uniqueObject)
    {
        foreach ($traversable as $data) {
            $traversableHash = spl_object_hash($data);
            $this->traversablesValues[$traversableHash] = ['properties' => [], 'getters' => []];

            foreach ($uniqueObject->getProperties() as $property) {
                $this->traversablesValues[$traversableHash]['properties'][$property] =
                    $this->getTraversableValue($data->$property);
            }

            foreach ($uniqueObject->getGetters() as $getter) {
                $this->traversablesValues[$traversableHash]['getters'][$getter] =
                    $this->getTraversableValue($data->$getter());
            }
        }

        return $this;
    }

    /**
     * @param mixed $value
     * @return string
     */
    protected function getTraversableValue($value)
    {
        return (is_object($value)) ? spl_object_hash($value) : $value;
    }

    /**
     * @param mixed $data
     * @param UniqueObject $uniqueObject
     */
    protected function compare($data, UniqueObject $uniqueObject)
    {
        static $alreadyTested = [];
        if (in_array($uniqueObject->getUniqid(), $alreadyTested)) {
            return;
        }

        $dataHash = spl_object_hash($data);
        $dataValues = $this->traversablesValues[$dataHash];
        foreach ($this->traversablesValues as $objectHash => $values) {
            if (
                $objectHash === $dataHash
                || (
                    count($values['properties']) !== count($dataValues['properties'])
                    || count($values['getters']) !== count($dataValues['getters'])
                )
            ) {
                continue;
            }

            $compare = true;
            foreach (['properties', 'getters'] as $type) {
                foreach ($dataValues[$type] as $name => $value) {
                    if (
                        array_key_exists($name, $dataValues[$type])
                        && $this->compareValues($dataValues[$type][$name], $value, $uniqueObject->getStrict()) === false
                    ) {
                        $compare = false;
                        break 2;
                    }
                }
            }
            if ($compare) {
                $this
                    ->context
                    ->buildViolation($uniqueObject->getMessage())
                    ->atPath($this->context->getPropertyName())
                    ->addViolation();
                $alreadyTested[] = $uniqueObject->getUniqid();
                break;
            }
        }
    }

    /**
     * @param mixed $value1
     * @param mixed $value2
     * @param bool $strict
     * @return bool
     */
    protected function compareValues($value1, $value2, $strict)
    {
        return ($strict) ? $value1 === $value2 : $value1 == $value2;
    }
}
