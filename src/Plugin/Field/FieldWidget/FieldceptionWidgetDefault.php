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
  public function form(FieldItemListInterface $items, array &$form, FormStateInterface $form_state, $get_delta = NULL) {
    $elements = parent::form($items, $form, $form_state, $get_delta);
    $elements['#attributes']['class'][] = 'fieldception-default-widget';
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);
    $settings = $this->getSettings();
    $field_settings = $this->getFieldSettings();
    $cardinality = $this->fieldDefinition->getFieldStorageDefinition()->getCardinality();
    $element['#type'] = 'container';

    if ($cardinality !== 1) {
      if ($label = $this->getSetting('item_label')) {
        $element['#type'] = 'fieldset';
        $element['#title_lock'] = TRUE;
        // phpcs:disable
        $element['#title'] = $this->t($label, ['@count' => $delta + 1]);
      }
    }

    $count = $group = 1;
    $fields_per_row = $settings['fields_per_row'];
    if ($fields_per_row > 1) {
      $element['#attributes']['class'][] = 'fieldception-groups';
      $element['#attributes']['class'][] = 'fieldception-groups-' . $fields_per_row;

      foreach ($field_settings['storage'] as $subfield => $config) {
        if (!isset($element['group_' . $group])) {
          $element['group_' . $group] = [
            '#type' => 'container',
            '#process' => [[get_class(), 'processParents']],
            '#attributes' => ['class' => ['fieldception-group']],
          ];
        }

        $element['group_' . $group][$subfield] = $element[$subfield];
        unset($element[$subfield]);

        $element['#group_count'] = $group;
        if ($fields_per_row && $count >= $fields_per_row) {
          $count = 1;
          $group++;
        }
        else {
          $count++;
        }
      }
    }
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function processParents(&$element, FormStateInterface $form_state, &$complete_form) {
    array_pop($element['#parents']);
    return $element;
  }

}
