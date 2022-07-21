<?php

namespace Drupal\fieldception\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldDefinitionInterface;

/**
 * Plugin implementation of the 'fieldception table' widget.
 *
 * @FieldWidget(
 *   id = "fieldception",
 *   label = @Translation("Deprecated"),
 *   field_types = {"fieldception"},
 * )
 */
class FieldceptionWidgetNone extends FieldceptionWidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    return FALSE;
  }

}
