<?php

/**
 * @file
 * Defines the default entity xliff field handler, used by most scalar field
 * types.
 */

namespace EntityXliff\Drupal\FieldHandlers;

use EntityXliff\Drupal\Interfaces\FieldHandlerInterface;


class DefaultFieldHandler implements FieldHandlerInterface {

  /**
   * {@inheritdoc}
   */
  public function getValue(\EntityMetadataWrapper $wrapper) {
    return $wrapper->value();
  }
}
