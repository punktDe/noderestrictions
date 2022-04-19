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

use Doctrine\ORM\Query\Filter\SQLFilter as DoctrineSqlFilter;
use Doctrine\Persistence\Mapping\ClassMetadata;
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
