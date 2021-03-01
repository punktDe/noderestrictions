# PunktDe NodeRestrictions

[![Latest Stable Version](https://poser.pugx.org/punktDe/noderestrictions/v/stable)](https://packagist.org/packages/punktDe/noderestrictions) [![Total Downloads](https://poser.pugx.org/punktDe/noderestrictions/downloads)](https://packagist.org/packages/punktDe/noderestrictions) [![License](https://poser.pugx.org/punktDe/noderestrictions/license)](https://packagist.org/packages/punktDe/noderestrictions)

## What does it do

The packages enables the editor to restrict the access of a node and its subnodes to a defined role using the backend inspector.

## Configuration

### Define Roles and Privileges

Add roles and privilege targets to your projects `Policy.yaml` using the ReadNodePrivilege. 
In this example, we use the package [Flowpack.Neos.FrontendLogin](https://github.com/Flowpack/Flowpack.Neos.FrontendLogin) for authentication, so the role inherits from `Flowpack.Neos.FrontendLogin:User`.

```yaml
privilegeTargets:
  'PunktDe\NodeRestrictions\Security\Authorization\Privilege\Node\ReadNodePrivilege':
    'Vendor.Customer:RestrictNodeToRole1':
      matcher: 'nodePropertyIs("accessRestriction", "Vendor.Customer:Role1") || parentNodePropertyIs("accessRestriction", "Vendor.Customer:Role1")'
    
roles:
  'Vendor.Customer:Role1':
    parentRoles: ['Flowpack.Neos.FrontendLogin:User']
    privileges:
      -
        privilegeTarget: 'Vendor.Customer:RestrictNodeToRole1'
        permission: GRANT

  'Neos.Neos:AbstractEditor':
    privileges:
      -
        privilegeTarget: 'Vendor.Customer:RestrictNodeToRole1'
        permission: GRANT
```

### Exclude Roles from Selection

Some system roles, especially the backend roles shouldn't be displayed in the backend selector. You can exclude them using the setting. Globbing is supported.: 

```yaml
PunktDe:
  NodeRestrictions:
    excludeRolesFromPackages:
	  - Neos.Neos
      - Flowpack.*
    excludeSpecificRoles:
      - Neos.Neos:Editor
```

### Translate and change the icon of a Role

You can translate and change the icon of a role by adjusting the settings.

```yaml
PunktDe:
  NodeRestrictions:
    overwriteRoles:
      'Foo.Bar:DemoRole':
        source: 'Main'
        package: 'Foo.Bar'
        icon: 'fas fa-cogs'
```

Or if you created the role by your own you can use a XLF-File in the package of the role (package key name).
The Name of the XLF-File is `Roles.xlf`.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<xliff xmlns="urn:oasis:names:tc:xliff:document:1.2" version="1.2">
    <file original="" product-name="Foo.Bar" source-language="en" datatype="plaintext">
        <body>
            <trans-unit id="DemoRole" xml:space="preserve" approved="yes">
                <source>Demo</source>
            </trans-unit>
        </body>
    </file>
</xliff>
```