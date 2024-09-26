<?php
declare(strict_types=1);

namespace PunktDe\NodeRestrictions\Security\Authorization\Privilege\Node\Doctrine;

/*
 * This file is part of the PunktDe.NodeRestrictions package.
 *
 * This package is open source software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Annotations as Flow;
use Doctrine\ORM\Query\Filter\SQLFilter as DoctrineSqlFilter;
use Neos\Flow\Security\Authorization\Privilege\Entity\Doctrine\PropertyConditionGenerator as NeosPropertyConditionGenerator;

/**
 * {@inheritdoc}
 */
class PropertyConditionGenerator extends NeosPropertyConditionGenerator
{
    /**
     * @param mixed $operandDefinition
     * @return PropertyConditionGenerator the current instance to allow for method chaining
     */
    public function postgresJsonContains($operandDefinition): PropertyConditionGenerator
    {
        $this->operator = '@>';
        $this->operandDefinition = $operandDefinition;
        $this->operand = $this->getValueForOperand($operandDefinition);
        return $this;
    }

    /**
     * @param DoctrineSqlFilter $sqlFilter
     * @param string $propertyPointer
     * @param string $operandDefinition
     * @return string
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function getConstraintStringForSimpleProperty(DoctrineSqlFilter $sqlFilter, $propertyPointer, $operandDefinition = null)
    {
        try {
            $parentReturn = parent::getConstraintStringForSimpleProperty($sqlFilter, $propertyPointer, $operandDefinition);
        } catch (\Exception $e) {
            if ($this->operator == '@>') {
                $parentReturn = null;
            } else {
                throw $e;
            }
        }

        if ($this->entityManager->getConnection()->getDatabasePlatform()->getName() == "postgresql") {
            if ($parentReturn === null && $this->operator == '@>') {

                $operandDefinition = ($operandDefinition === null ? $this->operandDefinition : $operandDefinition);

                if ($this->getRawParameterValue($operandDefinition) !== null) {
                    $parameter = $sqlFilter->getParameter($operandDefinition);
                    return $propertyPointer . ' @> ' . $parameter;
                }
            }
        }

        return $parentReturn;
    }
}
