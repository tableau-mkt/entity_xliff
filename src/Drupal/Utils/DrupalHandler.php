<?php

/**
 * @file
 * Contains the DrupalHandler class, which is a glorified OO wrapper around
 * procedural Drupal functions that our code depends upon. This vastly
 * simplifies unit testing.
 */

namespace EntityXliff\Drupal\Utils;


class DrupalHandler {

  CONST WATCHDOG_WARNING = 4;
  CONST LANGUAGE_NONE = 'und';

  /**
   * Sets a value in a nested array with variable depth.
   * @param array $array
   * @param array $parents
   * @param mixed $value
   * @param bool $force
   * @see drupal_array_set_nested_value()
   */
  public function arraySetNestedValue(array &$array, array $parents, $value, $force = FALSE) {
    drupal_array_set_nested_value($array, $parents, $value, $force);
  }

  /**
   * Identifies the children of an element array, optionally sorted by weight.
   * @param array $elements
   * @param bool $sort
   * @return array
   * @see element_children()
   */
  public function elementChildren(&$elements, $sort = FALSE) {
    return element_children($elements, $sort);
  }

  /**
   * Get the entity info array of an entity type.
   * @param string $entityType
   * @return array
   * @see entity_get_info()
   */
  public function entityGetInfo($entityType = NULL) {
    return entity_get_info($entityType);
  }

  /**
   * Load entities from the database.
   * @param $entity_type
   * @param bool $ids
   * @param array $conditions
   * @param bool $reset
   * @return mixed
   * @see entity_load()
   */
  public function entityLoad($entity_type, $ids = FALSE, $conditions = array(), $reset = FALSE) {
    return entity_load($entity_type, $ids, $conditions, $reset);
  }

  /**
   * Returns a property wrapper for the given data.
   * @param string $type
   * @param mixed $data
   * @param array $info
   * @return \EntityMetadataWrapper
   * @see entity_metadata_wrapper()
   */
  public function entityMetadataWrapper($type, $data = NULL, array $info = array()) {
    return entity_metadata_wrapper($type, $data, $info);
  }

  /**
   * Returns entity xliff field handlers declared by installed Drupal modules.
   * @return array
   */
  public function entityXliffGetFieldHandlers() {
    return entity_xliff_get_field_handlers();
  }

  /**
   * Determines whether a given module exists.
   * @param string $module
   * @return bool
   * @see module_exists()
   */
  public function moduleExists($module) {
    return module_exists($module);
  }

  /**
   * Loads a node object from the database.
   * @param null $nid
   * @param null $vid
   * @param bool $reset
   * @see node_load()
   */
  public function nodeLoad($nid = NULL, $vid = NULL, $reset = FALSE) {
    return node_load($nid, $vid, $reset);
  }

  /**
   * Saves changes to a node or adds a new node.
   * @param object $node
   * @throws \Exception
   * @see node_save()
   */
  public function nodeSave($node) {
    node_save($node);
  }

  /**
   * Determines whether to save session data of the current request.
   * @param bool $status
   * @see drupal_save_session()
   */
  public function saveSession($status = NULL) {
    return drupal_save_session($status);
  }

  /**
   * Resets one or all centrally stored static variable(s).
   * @param string $name
   * @see drupal_static_reset()
   */
  public function staticReset($name = NULL) {
    drupal_static_reset($name);
  }

  /**
   * Gets all nodes in a given translation set.
   * @param int $tnid
   * @return array
   * @see translation_node_get_translations
   */
  public function translationNodeGetTranslations($tnid) {
    return translation_node_get_translations($tnid);
  }

  /**
   * Returns whether the given content type has support for translations.
   * @param string $type
   * @return bool
   */
  public function translationSupportedType($type) {
    return translation_supported_type($type);
  }

  /**
   * Loads a user object.
   * @param int $uid
   * @param bool $reset
   * @return mixed
   * @see user_load()
   */
  public function userLoad($uid, $reset = FALSE) {
    return user_load($uid, $reset);
  }
  /**
   * Logs a system message.
   * @param $type
   * @param $message
   * @param array $variables
   * @param null $severity
   * @param null $link
   * @see watchdog()
   */
  public function watchdog($type, $message, $variables = array(), $severity = NULL, $link = NULL) {
    watchdog($type, $message, $variables, $severity, $link);
  }

}
