<?php
namespace PunktDe\NodeRestrictions\Security\Authorization\Privilege\Node;

/*
 *  (c) 2017 punkt.de GmbH - Karlsruhe, Germany - http://punkt.de
 *  All rights reserved.
 */

use PunktDe\NodeRestrictions\Security\Authorization\Privilege\Node\Doctrine\ConditionGenerator;
use Neos\ContentRepository\Security\Authorization\Privilege\Node\ReadNodePrivilege as NeosContentRepositoryReadNodePrivilege;

/**
 * {@inheritdoc}
 */
class ReadNodePrivilege extends NeosContentRepositoryReadNodePrivilege
{
    /**
     * {@inheritdoc}
     *
     * @return ConditionGenerator
     */
    protected function getConditionGenerator()
    {
        return new ConditionGenerator();
    }
}
