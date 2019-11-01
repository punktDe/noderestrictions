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

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Security\Authorization\Privilege\Entity\Doctrine\FalseConditionGenerator;
use Neos\ContentRepository\Security\Authorization\Privilege\Node\Doctrine\ConditionGenerator as NeosContentRepositoryConditionGenerator;
use Neos\Flow\Security\Authorization\Privilege\Entity\Doctrine\SqlGeneratorInterface;


/**
 * {@inheritdoc}
 */
class ConditionGenerator extends NeosContentRepositoryConditionGenerator
{
    /**
     * @Flow\Inject
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @param string $property
     * @param mixed $value
     *
     * @return SqlGeneratorInterface
     * @throws DBALException
     */
    public function nodePropertyIs(string $property, $value): SqlGeneratorInterface
    {
        $propertyConditionGenerator = new PropertyConditionGenerator('properties');

        if (!is_string($property) || is_array($value)) {
            return new FalseConditionGenerator();
        }

        if ($this->entityManager->getConnection()->getDatabasePlatform()->getName() === "postgresql") {
            return $propertyConditionGenerator->postgresJsonContains('{"' . trim($property) . '": ' . json_encode($value) . '}');
        }

        return $propertyConditionGenerator->like('%"' . trim($property) . '": ' . json_encode($value) . '%');
    }

    /**
     * @param string $property
     * @param mixed $value
     *
     * @return SqlGeneratorInterface
     * @throws DBALException
     */
    public function parentNodePropertyIs(string $property, $value): SqlGeneratorInterface
    {
        $propertiesConditionGenerator = $this->nodePropertyIs($property, $value);
        $subQueryGenerator = new ParentNodePropertyGenerator($propertiesConditionGenerator);

        return $subQueryGenerator;
    }
}
