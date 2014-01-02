This module allow the configuration of entity reference autocomplete fields to allow creation of entities when the
autocomplete fails to find a match. (Like how taxonomy tagging works).

To set this up:

1) Install and enable the module.
2) Create / Edit an entity reference field that uses auto complete, and choose the "Create" entity selection mode.
3) Implement a hook to perform the entity creation. EG:

/**
 * Implements hook_entityreference_create_ENTITY_TYPE
 */
function my_module_entityreference_create_vehicle_car($input, $field, $form_state, $form) {

  $entity = entity_create('vehicle', array(
    'type' => 'car',
    'name' => $input,
  ));

  $entity->save();
  return $entity;
}

This example is for a entity type of 'vehicle', with bundles for 'car', 'boat', etc.

When you type in a vehicle name, and a match is not found, entityreference_create will call a hook named for the entity
type: hook_entityreference_create_[ENTITY_TYPE](). Additionally, if the field is set up to only allow one bundle of that
entity to be referenced, a second hook will be called: hook_entityreference_create_[ENTITY_TYPE]_[BUNDLE_NAME]().

In your hook implementation, create the entity, set any of its required properties (EG for nodes you may want to set the
uid from the current user, etc), save the entity and return it. The entity will be referenced in the field like it was
an existing one.