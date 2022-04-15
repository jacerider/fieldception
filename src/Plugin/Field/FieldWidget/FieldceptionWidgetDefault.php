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
      'fields_per_row' => 0,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $settings = $this->getSettings();
    $field_settings = $this->getFieldSettings();
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

    $options = [0 => 'All fields in same row'];
    for ($i = 1; $i <= count($field_settings['storage']); $i++) {
      $options[$i] = $i;
    }
    $element['fields_per_row'] = [
      '#type' => 'select',
      '#options' => $options,
      '#required' => TRUE,
      '#title' => $this->t('Fields per row'),
      '#default_value' => $settings['fields_per_row'],
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $cardinality = $this->fieldDefinition->getFieldStorageDefinition()->getCardinality();
    $summary = parent::settingsSummary();
    $summary[] = $this->t('Fields per row: %value', ['%value' => empty($settings['fields_per_row']) ? 'All' : $settings['fields_per_row']]);
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
