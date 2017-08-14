<?php

namespace PunktDe\NodeRestrictions\Security\Authorization\Privilege\Node\Doctrine;

/*
 *  (c) 2017 punkt.de GmbH - Karlsruhe, Germany - http://punkt.de
 *  All rights reserved.
 */

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter as DoctrineSqlFilter;
use Neos\Flow\Security\Authorization\Privilege\Entity\Doctrine\SqlGeneratorInterface;
use Neos\Flow\Annotations as Flow;

/**
 * A sql generator to create a sql subquery.
 */
class ParentNodePropertyGenerator implements SqlGeneratorInterface
{
    /**
     * @var SqlGeneratorInterface
     */
    protected $expression;

    /**
     * @param SqlGeneratorInterface $expression
     */
    public function __construct($expression)
    {
        $this->expression = $expression;
    }

    /**
     * @param DoctrineSqlFilter $sqlFilter
     * @param ClassMetadata $targetEntity Metadata object for the target entity to create the constraint for
     * @param string $targetTableAlias The target table alias used in the current query
     *
     * @return string
     */
    public function getSql(DoctrineSqlFilter $sqlFilter, ClassMetadata $targetEntity, $targetTableAlias)
    {
        return '(
          SELECT COUNT(*) FROM neos_contentrepository_domain_model_nodedata as parent
          WHERE ' . $targetTableAlias . '.path LIKE CONCAT(parent.path, "%")
          AND ' . $this->expression->getSql($sqlFilter, $targetEntity, 'parent') . ')';
    }
}
