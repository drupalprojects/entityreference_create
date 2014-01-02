<?php

/**
 * @file
 * API documentation for Entity Reference Create
 */

/**
 * Called to create an entity programmatically from an autocomplete field.
 * You should implement this hook or the alternative form hook_entityreference_create_ENTITY_TYPE_BUNDLE_NAME() to use
 * this module.
 *
 * Field, Form state and Form arrays are passed to provide you useful context with which to populate your object.
 * EG: $field['settings'] contains the allowed entity types for the field and other info.
 * $form_state could be used to read other field values, etc.
 *
 * If you do not return an object from your hook, validation will fail. The first object to be returned from the hook
 * invocation will be used, so if more than one module implements a hook, the result from the module with the lowest
 * weight will be used.
 *
 * @param $input
 *   Search term used in the field.
 *
 * @param $field
 *   Field array.
 *
 * @param $form_state
 *   Form state array.
 *
 * @param $form
 *   Form array.
 *
 * @return Object entity.
 */
function hook_entityreference_create_ENTITY_TYPE($input, $field, $form_state, $form) {

  $entity = entity_create('vehicle', array(
    'type' => 'car',
    'name' => $input,
  ));

  entity_save('vehicle', $entity);
  return $entity;
}

/**
 * Called to create an entity programmatically from an autocomplete field.
 * @see hook_entityreference_create_ENTITY_TYPE() for full documentation.
 */
function hook_entityreference_create_ENTITY_TYPE_BUNDLE_NAME($input, $field, $form_state, $form) {

  $entity = entity_create('vehicle', array(
    'type' => 'car',
    'name' => $input,
  ));

  entity_save('vehicle', $entity);
  return $entity;
}