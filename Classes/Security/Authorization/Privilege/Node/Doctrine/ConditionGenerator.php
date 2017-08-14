<?php

namespace PunktDe\NodeRestrictions\Security\Authorization\Privilege\Node\Doctrine;

/*
 *  (c) 2017 punkt.de GmbH - Karlsruhe, Germany - http://punkt.de
 *  All rights reserved.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Security\Authorization\Privilege\Entity\Doctrine\FalseConditionGenerator;
use Neos\Flow\Security\Authorization\Privilege\Entity\Doctrine\PropertyConditionGenerator;
use Neos\ContentRepository\Security\Authorization\Privilege\Node\Doctrine\ConditionGenerator as NeosContentRepositoryConditionGenerator;
use Neos\Flow\Security\Authorization\Privilege\Entity\Doctrine\SqlGeneratorInterface;

/**
 * {@inheritdoc}
 */
class ConditionGenerator extends NeosContentRepositoryConditionGenerator
{
    /**
     * @param string $property
     * @param mixed $value
     *
     * @return SqlGeneratorInterface
     */
    public function nodePropertyIs($property, $value)
    {
        $propertyConditionGenerator = new PropertyConditionGenerator('properties');

        if (!is_string($property) || is_array($value)) {
            return new FalseConditionGenerator();
        }

        return $propertyConditionGenerator->like('%"' . trim($property) . '": ' . json_encode($value) . '%');
    }

    /**
     * @param string $property
     * @param mixed $value
     *
     * @return SqlGeneratorInterface
     */
    public function parentNodePropertyIs($property, $value)
    {
        $propertiesConditionGenerator = $this->nodePropertyIs($property, $value);
        $subQueryGenerator = new ParentNodePropertyGenerator($propertiesConditionGenerator);

        return $subQueryGenerator;
    }
}
