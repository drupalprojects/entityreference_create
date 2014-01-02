<?php

class EntityReference_SelectionHandler_Create extends EntityReference_SelectionHandler_Generic {

  /**
   * Implements EntityReferenceHandler::getInstance().
   * Mostly copied from the parent class as that contains its class name hardcoded.
   */
  public static function getInstance($field, $instance = NULL, $entity_type = NULL, $entity = NULL) {
    $target_entity_type = $field['settings']['target_type'];

    // Check if the entity type does exist and has a base table.
    $entity_info = entity_get_info($target_entity_type);
    if (empty($entity_info['base table'])) {
      return EntityReference_SelectionHandler_Broken::getInstance($field, $instance);
    }

    if (class_exists($class_name = 'EntityReference_SelectionHandler_Generic_' . $target_entity_type)) {
      return new $class_name($field, $instance, $entity_type, $entity);
    }
    else {
      return new self($field, $instance, $entity_type, $entity);
    }
  }

  /**
   * Implements EntityReferenceHandler::validateAutocompleteInput().
   */
  public function validateAutocompleteInput($input, &$element, &$form_state, $form) {

    // Look for existing entities first.
    $entities = $this->getReferencableEntities($input, '=', 6);
    if (empty($entities)) {

      // If there's no entities available, don't fail validation.
      // Figure out if there's a function we can call to create a stub. If there is and it returns one, use it.
      $hooks = array();

      $entity_type = $this->field['settings']['target_type'];

      if (empty($this->field['settings']['handler_settings']['target_bundles'])) {
        $bundles = array();
      }
      else {
        $bundles = $this->field['settings']['handler_settings']['target_bundles'];
      }

      // If there's exactly one bundle that can be referenced by this field, also call a bundle specific hook.
      if (count($bundles)) {
        $bundle = reset($bundles);
        $hooks[] = 'entityreference_create_' . $entity_type . '_' . $bundle;
      }

      $hooks[] = 'entityreference_create_' . $entity_type;

      $entity = null;

      foreach ($hooks as $hook) {
        $entities = module_invoke_all($hook, $input, $this->field, $form_state, $form);

        // Use the first result we get. Hooks are not obliged to return an entity, so we may get an empty array back.
        if ($entities) {

          // If any modules created an entity, the return will be an array containing one entity.
          $entity = reset($entities);
          break;
        }
      }

      if ($entity) {
        // Get the ID of this new entity:
        list($id, $vid, $bundle) = entity_extract_ids($entity_type, $entity);

        // Return the new entity's id.
        return $id;
      }
      else {
        form_error($element, t('There are no entities matching "%value" and a new one could not be created automatically.', array('%value' => $input)));
      }
    }
    elseif (count($entities) > 5) {
      // Error if there are more than 5 matching entities.
      form_error($element, t('Many entities are called %value. Specify the one you want by appending the id in parentheses, like "@value (@id)"', array(
        '%value' => $input,
        '@value' => $input,
        '@id' => key($entities),
      )));
    }
    elseif (count($entities) > 1) {
      // More helpful error if there are only a few matching entities.
      $multiples = array();
      foreach ($entities as $id => $name) {
        $multiples[] = $name . ' (' . $id . ')';
      }
      form_error($element, t('Multiple entities match this reference; "%multiple"', array('%multiple' => implode('", "', $multiples))));
    }
    else {
      // Take the one and only matching entity.
      return key($entities);
    }
  }
}