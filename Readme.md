# PunktDe NodeRestrictions

## What does it do

The packages enables the editor to restrict the access of a node and its subnodes to a defined role using the backend inspector.

## Configuration

### Define Roles and Privileges

Add roles and privilege targets to your projects `Policy.yaml` using the ReadNodePrivilege. 
In this example, we use the package [Flowpack.Neos.FrontendLogin](https://github.com/Flowpack/Flowpack.Neos.FrontendLogin) for authentication, so the role inherits from `Flowpack.Neos.FrontendLogin:User`.

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
	        
### Exclude Roles from Selection

Some system roles, especially the backend roles shouldn't be displayed in the backend selector. You can exclude them using the setting. Globbing is supported.: 

	PunktDe:
	  NodeRestrictions:
	    excludeRolesFromPackages:
	      - Neos.Neos
	      - Flowpack.*
        excludeSpecificRoles:
          - Neos.Neos:Editor
