<?php

namespace PunktDe\NodeRestrictions\Security\Authorization\Privilege\Node\Doctrine;

/*
 *  (c) 2017 punkt.de GmbH - Karlsruhe, Germany - http://punkt.de
 *  All rights reserved.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Security\Authorization\Privilege\Entity\Doctrine\SQLFilter;
use Neos\Flow\Security\Authorization\Privilege\Entity\Doctrine\PropertyConditionGenerator as NeosPropertyConditionGenerator;

/**
 * {@inheritdoc}
 */
class PropertyConditionGenerator extends NeosPropertyConditionGenerator
{
    /**
     * @var string
     */
    protected $jsonbProperty;

    /**
     * @param mixed $operandDefinition
     * @return PropertyConditionGenerator the current instance to allow for method chaining
     */
    public function postgresJsonContains($operandDefinition)
    {
        $this->operator = '@>';
        $this->operandDefinition = $operandDefinition;
        $this->operand = $this->getValueForOperand($operandDefinition);
        return $this;
    }

    /**
     * @param SQLFilter $sqlFilter
     * @param string $propertyPointer
     * @param string $operandDefinition
     * @return string
     */
    protected function getConstraintStringForSimpleProperty(SQLFilter $sqlFilter, $propertyPointer, $operandDefinition = null)
    {
        $parentReturn = parent::getConstraintStringForSimpleProperty($sqlFilter, $propertyPointer, $operandDefinition);

        if ($parentReturn === null && $this->operator == '@>') {

            $operandDefinition = ($operandDefinition === null ? $this->operandDefinition : $operandDefinition);

            if ($this->getRawParameterValue($operandDefinition) !== null) {
                $parameter = $sqlFilter->getParameter($operandDefinition);
                return $propertyPointer . ' @> ' . $parameter;
            }
        }

        return $parentReturn;
    }
}