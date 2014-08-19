OroCRMChannelBundle
===================

Entity data block:
------------------

This bundle brings "channel entity" to the system. Channel is a set of features which might be included in CRM.
Also channel may come with "customer datasource", it's basically integration that brings business entities into system.
_Feature_ means a set of entities and integration that covers business direction needs.

**For example**:
>Customer has B2B business and needs CRM that will provide complex B2B solution. So, in order to met this
requirements B2B channel could be created that will enable _leads_ and _opportunities_ or any other B2B feature in scope of this channel.
After this, the Sales menu appears on the UI and has Leads and Opportunities menus.

By default all specific to business direction features should be disabled, and will not be visible in reports, segments, menu etc.(except entity configuration)
In order to implement ability to enable feature in scope of channel - configuration file should be created.

**Config example:**
```yml
      orocrm_channel:
          entity_data:
             -
                name: OroCRM\Bundle\SomeEntity\Entity\RealEntity                # Entity FQCN
                dependent:                                                      # Service entities that dependent on availability of main entity
                      - OroCRM\Bundle\SomeEntity\Entity\RealEntityStatus
                      - OroCRM\Bundle\SomeEntity\Entity\RealEntityCloseReason
                navigation_items:                                               # Navigation items that responsible for entity visibility
                      - menu.tab.real_entity_list

             -
                name: OroCRM\Bundle\AcmeDemoBundle\Entity\AnotherEntity
                dependent: ~
                navigation_items:
                    - menu.tab.entity_funnel_list
                    - menu.tab.some_tab.some_tab.some_value
                belongs_to:
                    integration: integration_type_name                   # If entity belongs to integration, correspondent node should be set
                    connector:   another                                 # connector name
```

 - `name` - entity name
 - `dependent` - list of entities which will be shown/hidden too. (Related entities to the entity in field 'name')
 - `navigation_items` - list of menu items which should be enabled/disabled in any menu.
 - `belongs_to.integration` - integration type name
 - `belongs_to.connector`   - integration connector name

Menu item should be hidden by default in navigation configuration using parameter 'display' with value 'false'.

**Example:**
```yml
    oro_menu_config:
        items:
            menu_item:
                label: 'orocrm.some_entity.menu.tab.label'
                display: false
        tree:
            application_menu:
                children:
                    menu_item: ~
```

Channel types block:
--------------------

Channel is configured by "Channel Type", "Customer Identity" and "Entities" fields. Some types of channels that bring customers, also bring the "integration" field to configure the integration. It should be described in configuration block:

**Config example:**
```yml
  channel_types:
        customer_channel_type:
            label: Channel type name
            entities:
                - OroCRM\Bundle\AcmeBundle\Entity\Entity
                - OroCRM\Bundle\AcmeBundle\Entity\Customer
            integration_type: some_type
            customer_identity: OroCRM\Bundle\AcmeBundle\Entity\Customer
            is_customer_identity_user_defined: false
```

If you want to add "Integration" to the channel you should define "integration_type", ["customer_identity"], ["is_customer_identity_user_defined"].

* "label" - channel type label;

* "entities" describe which fields will be defined in "Entities" filed after channel type has been selected;

* "integration_type" describe which integration type appear in "Channel Type" select. When "integration_type" will have defined in config and you have selected your type in "Channel Type" selector, the Integration field has appeared in channel form like link, by clicking on it the dialog box will open;

* When "Channel Type" has selected you can have predefined options in "Entities" field which you should describe in "entities";

* Also if you define "customer_identity" option in your config, field "Cusomer Identity" will have parameter "read-only" and you can't change it. You can't remove it from the "Entities" field if "is_customer_identity_user_defined" is false.

* Entity in "customer_identity" must also be in "entities" block;
