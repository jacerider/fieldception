<?php

namespace Drupal\fieldception\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'fieldception' widget.
 *
 * @FieldWidget(
 *   id = "fieldception_default",
 *   label = @Translation("Default"),
 *   field_types = {"fieldception"}
 * )
 */
class FieldceptionWidgetDefault extends FieldceptionWidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'item_label' => 'Item @count',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $settings = $this->getSettings();
    $cardinality = $this->fieldDefinition->getFieldStorageDefinition()->getCardinality();
    $element = parent::settingsForm($form, $form_state);
    if ($cardinality !== 1) {
      $element['item_label'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Label for each item'),
        '#required' => TRUE,
        '#default_value' => $settings['item_label'],
      ];
    }
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $cardinality = $this->fieldDefinition->getFieldStorageDefinition()->getCardinality();
    $summary = parent::settingsSummary();
    if ($cardinality !== 1) {
      $settings = $this->getSettings();
      $summary[] = $this->t('Item label: %value', ['%value' => $settings['item_label']]);
    }
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);
    $cardinality = $this->fieldDefinition->getFieldStorageDefinition()->getCardinality();
    if ($cardinality !== 1) {
      if ($label = $this->getSetting('item_label')) {
        $element['#type'] = 'fieldset';
        // phpcs:disable
        $element['#title'] = $this->t($label, ['@count' => $delta + 1]);
      }
    }
    return $element;
  }

}
