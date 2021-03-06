<?php

/**
 * @file
 * Performs test set up tasks to get Drupal in a state where Behat can do its
 * thing without much hassle.
 */


// Create new languages.
try {
  locale_add_language('fr', 'French', 'Français', LANGUAGE_LTR);
  locale_add_language('de', 'German', 'Deutsch', LANGUAGE_LTR);
}
catch (PDOException $e) {
  echo "Language already installed.\n";
}

// Enable path prefix-based language negotiation.
$negotiation = array(
  'locale-url' => array(
    'callbacks' => array(
      'language' => 'locale_language_from_url',
      'switcher' => 'locale_language_switcher_url',
      'url_rewrite' => 'locale_language_url_rewrite_url',
    ),
    'file' => 'includes/locale.inc',
  ),
  'language-default' => array(
    'callbacks' => array(
      'language' => 'language_from_default',
    ),
  ),
);
variable_set('language_negotiation_language', $negotiation);
variable_set('language_negotiation_language_content', $negotiation);

// Enable translation for the page content type.
variable_set('language_content_type_page', TRANSLATION_ENABLED);

// Enable revisions on the page content type.
variable_set('node_options_page', array('status', 'revision', 'moderation'));
variable_set('workbench_moderation_default_state_page', 'published');

// Set up Paragraphs bundles.
paragraphs_bundle_save((object) array('bundle' => 'bundle_1', 'name' => 'Bundle 1', 'locked' => 1));
paragraphs_bundle_save((object) array('bundle' => 'bundle_2', 'name' => 'Bundle 2', 'locked' => 1));
add_long_text_field('paragraphs_item', 'bundle_1');
add_paragraphs_field('paragraphs_item', 'bundle_2', TRUE);

// Add relevant fields to the page content type:
add_long_text_field('node', 'page');
add_link_field('node', 'page');
add_image_field();
add_text_field_with_cardinality('node', 'page');
add_field_collection_field();
add_paragraphs_field('node', 'page');
add_entity_reference_field('node', 'page');
add_node_reference_field('node', 'page');

// Enable entity field translation for appropriate entities.
$etypes = variable_get('entity_translation_entity_types', array());
$etypes['node'] = 'node';
$etypes['user'] = 'user';
$etypes['comment'] = 'comment';
$etypes['taxonomy_term'] = 'taxonomy_term';
variable_set('entity_translation_entity_types', $etypes);

// Also enable entity translation for the 'tags' vocabulary.
variable_set('entity_translation_taxonomy', array('tags' => 1));

// Enable entity field translation for article nodes, as well as fields.
variable_set('language_content_type_article', ENTITY_TRANSLATION_ENABLED);
make_field_translatable('body');
make_field_translatable('field_image');

// Add relevant fields to those entities.
add_link_field('user', 'user');
add_text_field_with_cardinality('taxonomy_term', 'tags');

// Make the comment body translatable.
make_field_translatable('comment_body');

/**
 * Adds a long text field to the page content type.
 */
function add_long_text_field($entity, $bundle) {
  if (!$field = field_read_field('field_long_text')) {
    try {
      $field_definition = array(
        'field_name' => 'field_long_text',
        'type' => 'text_long',
        'cardinality' => 1,
        'module' => 'text',
      );
      $field = field_create_field($field_definition);
    }
    catch (FieldException $e) {
      echo "Unable to create long text field.\n";
    }
  }

  $field_instance_definition = array(
    'label' => 'Long Text',
    'field_id' => $field['id'],
    'required' => FALSE,
    'description' => '',
    'default_value' => NULL,
    'field_name' => 'field_long_text',
    'entity_type' => $entity,
    'settings' => array(
      'text_processing' => 1,
    ),
    'display' => array(
      'default' => array(
        'label' => 'above',
        'type' => 'text_default',
        'settings' => array(),
        'module' => 'text',
        'weight' => 1,
      ),
      'teaser' => array(
        'label' => 'above',
        'type' => 'text_default',
        'weight' => 3,
        'settings' => array(),
        'module' => 'text',
      ),
    ),
  );

// Don't bother if the field already exists.
  if (!field_read_instance($entity, 'field_long_text', $bundle)) {
    // Apply bundle-specific settings.
    $field_instance_definition['bundle'] = $bundle;

    try {
      field_create_instance($field_instance_definition);
    }
    catch (FieldException $e) {
      echo "Unable to create link field instance.\n";
    }
  }
}

/**
 * Adds a link field (label "Link", name "field_link") to the entity/bundle.
 */
function add_link_field($entity, $bundle) {
  if (!$field = field_read_field('field_link')) {
    try {
      $field_definition = array(
        'field_name' => 'field_link',
        'type' => 'link_field',
        'module' => 'link',
        'settings' => array(
          'attributes' => array(
            'target' => 'default',
            'class' => '',
            'rel' => '',
          ),
          'url' => 0,
          'title' => 'optional',
          'title_value' => '',
          'title_maxlength' => 128,
          'enable_tokens' => 1,
          'display' => array('url_cutoff' => 80),
        ),
        'translatable' => TRUE,
      );
      $field = field_create_field($field_definition);
    }
    catch (FieldException $e) {
      echo "Unable to create link field.\n";
    }
  }

  $field_instance_definition = array(
    'label' => 'Link',
    'field_id' => $field['id'],
    'required' => FALSE,
    'description' => '',
    'default_value' => NULL,
    'field_name' => 'field_link',
    'entity_type' => $entity,
  );

// Don't bother if the field already exists.
  if (!field_read_instance($entity, 'field_link', $bundle)) {
    // Apply bundle-specific settings.
    $field_instance_definition['bundle'] = $bundle;

    try {
      field_create_instance($field_instance_definition);
    }
    catch (FieldException $e) {
      echo "Unable to create link field instance.\n";
    }
  }
}

/**
 * Adds an image field to the page content type.
 * Note: Doesn't need to create the field because standard profile's got it.
 */
function add_image_field() {
  $instance_definition = field_read_instance('node', 'field_image', 'article');
  $instance_definition['description'] = '';
  unset($instance_definition['id']);

// Don't bother if the field already exists.
  if (!field_read_instance('node', 'field_image', 'page')) {
    // Apply bundle-specific settings.
    $instance_definition['bundle'] = 'page';

    try {
      field_create_instance($instance_definition);
    }
    catch (FieldException $e) {
      echo "Unable to create image field instance.\n";
    }
  }
}

/**
 * Adds a text field with cardinality to the given entity/bundle.
 */
function add_text_field_with_cardinality($entity, $bundle, $required = FALSE) {
  if (!$field = field_read_field('field_text')) {
    try {
      $field_definition = array(
        'field_name' => 'field_text',
        'type' => 'text',
        'cardinality' => 2,
        'module' => 'text',
        'settings' => array(
          'max_length' => 255,
        ),
        'translatable' => TRUE,
      );
      $field = field_create_field($field_definition);
    }
    catch (FieldException $e) {
      echo "Unable to create text field with cardinality.\n";
    }
  }

  $field_instance_definition = array(
    'label' => 'Text (With Cardinality)',
    'field_id' => $field['id'],
    'required' => $required,
    'description' => '',
    'default_value' => NULL,
    'field_name' => 'field_text',
    'entity_type' => $entity,
  );

  // Don't bother if the field already exists.
  if (!field_read_instance($entity, 'field_text', $bundle)) {
    // Apply bundle-specific settings.
    $field_instance_definition['bundle'] = $bundle;

    try {
      field_create_instance($field_instance_definition);
    }
    catch (FieldException $e) {
      echo "Unable to create text field instance.\n";
    }
  }
}

/**
 * Adds a field collection field which itself contains a text field.
 */
function add_field_collection_field() {
  if (!$field = field_read_field('field_field_collection')) {
    try {
      $field_definition = array(
        'field_name' => 'field_field_collection',
        'type' => 'field_collection',
        'cardinality' => -1,
        'module' => 'field_collection',
        'translatable' => FALSE,
        'settings' => array(
          'hide_blank_items' => TRUE,
          'path' => '',
          'entity_translation_sync' => FALSE,
        ),
      );
      $field = field_create_field($field_definition);
    }
    catch (FieldException $e) {
      echo "Unable to create field collection field.\n";
    }
  }

  $field_instance_definition = array(
    'label' => 'Field Collection Field',
    'field_id' => $field['id'],
    'required' => FALSE,
    'description' => '',
    'default_value' => NULL,
    'field_name' => 'field_field_collection',
    'entity_type' => 'node',
    'widget' => array(
      'type' => 'field_collection_embed',
      'module' => 'field_collection',
    ),
  );

  // Don't bother if the field already exists.
  if (!field_read_instance('node', 'field_field_collection', 'page')) {
    // Apply bundle-specific settings.
    $field_instance_definition['bundle'] = 'page';

    try {
      field_create_instance($field_instance_definition);
    }
    catch (FieldException $e) {
      echo "Unable to create text field instance.\n";
    }
  }

  // Attach a long text field to this field collection type.
  add_long_text_field('field_collection_item', 'field_field_collection');
}

/**
 * Adds a paragraphs field to the given entity/bundle combo.
 */
function add_paragraphs_field($entity, $bundle, $required = FALSE) {
  if (!$field = field_read_field('field_paragraphs')) {
    try {
      $field_definition = array(
        'field_name' => 'field_paragraphs',
        'type' => 'paragraphs',
        'cardinality' => -1,
        'module' => 'paragraphs',
        'translatable' => FALSE,
        'settings' => array(
          'entity_translation_sync' => FALSE,
        ),
      );
      $field = field_create_field($field_definition);
    }
    catch (FieldException $e) {
      echo "Unable to create paragraphs field.\n";
    }
  }

  $allowed_bundles = array('bundle_1' => 'bundle_1', 'bundle_2' => 'bundle_2');
  $add_mode = 'button';
  if ($required) {
    $allowed_bundles['bundle_2'] = -1;
    $add_mode = 'select';
  }

  $field_instance_definition = array(
    'label' => 'Paragraphs',
    'field_id' => $field['id'],
    'required' => $required,
    'description' => '',
    'default_value' => NULL,
    'field_name' => 'field_paragraphs',
    'entity_type' => $entity,
    'widget' => array(
      'type' => 'paragraphs_embed',
      'module' => 'paragraphs',
    ),
    'settings' => array(
      'title' => 'Paragraph',
      'title_multiple' => 'Paragraphs',
      'default_edit_mode' => 'open',
      'add_mode' => $add_mode,
      'allowed_bundles' => $allowed_bundles,
    ),
  );

  // Don't bother if the field already exists.
  if (!field_read_instance($entity, 'field_paragraphs', $bundle)) {
    // Apply bundle-specific settings.
    $field_instance_definition['bundle'] = $bundle;

    try {
      field_create_instance($field_instance_definition);
    }
    catch (FieldException $e) {
      echo "Unable to create paragraphs field instance.\n";
    }
  }
}

/**
 * Adds an entity reference field to the given entity/bundle combination.
 */
function add_entity_reference_field($entity, $bundle) {
  if (!$field = field_read_field('field_reference')) {
    try {
      $field_definition = array(
        'field_name' => 'field_reference',
        'type' => 'entityreference',
        'cardinality' => '1',
        'module' => 'entityreference',
        'translatable' => FALSE,
        'settings' => array(
          'target_type' => 'node',
          'handler' => 'base',
          'handler_settings' => array(
            'target_bundles' => array(
              'article' => 'article',
              'page' => 'page',
            ),
            'sort' => array(
              'type' => 'node',
            )
          ),
          'entity_translation_sync' => FALSE,
        ),
      );
      $field = field_create_field($field_definition);
    }
    catch (FieldException $e) {
      echo "Unable to create field collection field.\n";
    }
  }

  $field_instance_definition = array(
    'label' => 'Entity Reference',
    'field_id' => $field['id'],
    'required' => FALSE,
    'description' => '',
    'field_name' => 'field_reference',
    'entity_type' => $entity,
    'widget' => array(
      'type' => 'options_select',
      'module' => 'options',
    ),
    'display' => array(
      'default' => array(
        'label' => 'above',
        'type' => 'entityreference_entity_view',
        'settings' => array(
          'view_mode' => 'default',
          'links' => FALSE,
        ),
        'module' => 'entityreference',
      )
    ),
  );

  // Don't bother if the field already exists.
  if (!field_read_instance($entity, 'field_reference', $bundle)) {
    // Apply bundle-specific settings.
    $field_instance_definition['bundle'] = $bundle;

    try {
      field_create_instance($field_instance_definition);
    }
    catch (FieldException $e) {
      echo "Unable to create text field instance.\n";
    }
  }
}

/**
 * Adds an entity reference field to the given entity/bundle combination.
 */
function add_node_reference_field($entity, $bundle) {
  if (!$field = field_read_field('field_node_reference')) {
    try {
      $field_definition = array(
        'field_name' => 'field_node_reference',
        'type' => 'node_reference',
        'cardinality' => '1',
        'module' => 'node_reference',
        'translatable' => FALSE,
        'settings' => array(
          'referenceable_types' => array(
            'page',
          ),
          'entity_translation_sync' => FALSE,
        ),
      );
      $field = field_create_field($field_definition);
    }
    catch (FieldException $e) {
      echo "Unable to create field collection field.\n";
    }
  }

  $field_instance_definition = array(
    'label' => 'Node Reference',
    'field_id' => $field['id'],
    'required' => FALSE,
    'description' => '',
    'field_name' => 'field_node_reference',
    'entity_type' => $entity,
    'widget' => array(
      'type' => 'options_select',
      'module' => 'options',
    ),
    'display' => array(
      'default' => array(
        'label' => 'above',
        'type' => 'node_reference_node',
        'module' => 'node_reference',
        'weight' => 8,
        'settings' => array(
          'node_reference_view_mode' => 'teaser',
        ),
      ),
      'teaser' => array(
        'label' => 'above',
        'type' => 'node_reference_node',
        'weight' => 2,
        'settings' => array(
          'node_reference_view_mode' => 'teaser',
        ),
        'module' => 'node_reference',
      ),
    ),
  );

  // Don't bother if the field already exists.
  if (!field_read_instance($entity, 'field_node_reference', $bundle)) {
    // Apply bundle-specific settings.
    $field_instance_definition['bundle'] = $bundle;

    try {
      field_create_instance($field_instance_definition);
    }
    catch (FieldException $e) {
      echo "Unable to create text field instance.\n";
    }
  }
}

/**
 * Makes the provided field translatable.
 *
 * @param string $name
 *   The name of the field to make translatable.
 *
 * @throws FieldException
 */
function make_field_translatable($name) {
  if ($field = field_read_field($name)) {
    $field['translatable'] = TRUE;
    field_update_field($field);
  }
}
