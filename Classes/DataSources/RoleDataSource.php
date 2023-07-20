<?php
declare(strict_types=1);

namespace PunktDe\NodeRestrictions\DataSources;

/*
 * This file is part of the PunktDe.NodeRestrictions package.
 *
 * This package is open source software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Security\Policy\PolicyService;
use Neos\Neos\Service\DataSource\AbstractDataSource;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\I18n\Translator;

class RoleDataSource extends AbstractDataSource
{
    /**
     * @var string
     */
    static protected $identifier = 'punktde-noderestrictions-roles';

    /**
     * @Flow\InjectConfiguration(path="excludeRolesFromPackages")
     * @var array
     */
    protected $excludedPackages;

    /**
     * @Flow\InjectConfiguration(path="excludeSpecificRoles")
     * @var array
     */
    protected $excludedRoles;

    /**
     * @Flow\Inject
     * @var PolicyService
     */
    protected $policyService;

    /**
     * @Flow\Inject
     * @var Translator
     */
    protected $translator;

    /**
     * @Flow\InjectConfiguration(package="PunktDe.NodeRestrictions")
     * @var array
     */
    protected $roleSettings;


    /**
     * @param NodeInterface|null $node
     * @param array $arguments
     * @return array
     */
    public function getData(NodeInterface $node = null, array $arguments = [])
    {
        if (key_exists('translation', $this->roleSettings)) {
            $defaultTranslationSetting = $this->roleSettings['translation'];

            $defaultValue = $this->translator->translateById($defaultTranslationSetting['id'], [], null, null, $defaultTranslationSetting['source'], $defaultTranslationSetting['package']);
        }

        if (!$defaultValue) {
            $defaultValue = 'Not restricted';
        }

        $roles = ['' => ['label' => $defaultValue]];

        $matchPatternArray = function ($patternArray, $identifier): bool {
            foreach ($patternArray as $pattern) {
                if (fnmatch($pattern, $identifier) === true) {
                    return true;
                }
            }
            return false;
        };

        foreach ($this->policyService->getRoles() as $role) {
            if (!$matchPatternArray($this->excludedPackages, $role->getPackageKey()) && !$matchPatternArray($this->excludedRoles, $role->getIdentifier())) {
                $roles[$role->getIdentifier()] = $this->getDataByFullName($role->getPackageKey(), $role->getName());
            }
        }

        return $roles;
    }

    /**
     * Get the translation and icon of a role by his name and package key
     *
     * @param string $packageName
     * @param string $roleName
     * @return array
     */
    protected function getDataByFullName(string $packageName, string $roleName): array
    {
        $translationSettings = $this->roleSettings['overwriteRoles'];
        $translation = null;
        $icon = 'icon-users';
        $fullRoleName = $packageName . ':' . $roleName;

        // Get the translation from the settings
        if (key_exists($fullRoleName, $translationSettings)) {
            $translate = $translationSettings[$fullRoleName];

            if (key_exists('source', $translate) && key_exists('package', $translate)) {
                $translation = $this->translator->translateById($roleName, [], null, null, $translate['source'], $translate['package']);
            }

            if (key_exists('icon', $translate)) {
                $icon = $translate['icon'];
            }
        }

        if (!$translation) {
            // Get the default translation by a static source
            $translation = $this->translator->translateById($roleName, [], null, null, 'Roles', $packageName);

            // Fallback: Use the role name
            if (!$translation) {
                $translation = $roleName;
            }
        }

        return [
            'label' => $translation,
            'icon' => $icon
        ];
    }
}