<?php

/**
 * @file
 * Defines an entity xliff field handler for use by image fields.
 */

namespace EntityXliff\Drupal\FieldHandlers;

use EntityXliff\Drupal\Interfaces\FieldHandlerInterface;


class ImageFieldHandler implements FieldHandlerInterface {

  /**
   * {@inheritdoc}
   */
  public function getValue(\EntityMetadataWrapper $wrapper) {
    $response = array();

    $value = $wrapper->value();

    // Check for alt text.
    if (isset($value['alt']) && !empty($value['alt'])) {
      $response['alt'] = array(
        '#label' => 'Alternate text',
        '#text' => $value['alt'],
      );
    }

    // Check for title text.
    if (isset($value['title']) && !empty($value['title'])) {
      $response['title'] = array(
        '#label' => 'Title text',
        '#text' => $value['title'],
      );
    }

    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function setValue(\EntityMetadataWrapper $wrapper, $value) {
    $newValue = $wrapper->value();

    if (isset($value['alt'])) {
      $newValue['alt'] = html_entity_decode($value['alt'], ENT_QUOTES, 'utf-8');
    }
    if (isset($value['title'])) {
      $newValue['title'] = html_entity_decode($value['title'], ENT_QUOTES, 'utf-8');
    }

    $wrapper->set($newValue);
  }

}
