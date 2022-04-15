<?php

namespace Drupal\fieldception\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Markup;

/**
 * Plugin implementation of the 'fieldception table' widget.
 *
 * @FieldWidget(
 *   id = "fieldception_table",
 *   label = @Translation("Table"),
 *   field_types = {"fieldception"}
 * )
 */
class FieldceptionWidgetTable extends FieldceptionWidgetBase {

  /**
   * {@inheritdoc}
   */
  protected function formMultipleElements(FieldItemListInterface $items, array &$form, FormStateInterface $form_state) {
    $elements = parent::formMultipleElements($items, $form, $form_state);
    $field_settings = $this->getFieldSettings();
    $elements['#type'] = 'table';

    if (isset($elements['#theme']) && $elements['#theme'] == 'field_multiple_value_form') {
      $elements['#header']['_drag'] = '';
    }

    foreach ($field_settings['storage'] as $subfield => $config) {
      $elements['#header'][$subfield] = $config['label'];
      if (!empty($field_settings['fields'][$subfield]['required'])) {
        $elements['#header'][$subfield] .= '<span class="js-form-required form-required"></span>';
      }
      $elements['#header'][$subfield] = Markup::create($elements['#header'][$subfield]);
    }

    if (isset($elements['#theme']) && $elements['#theme'] == 'field_multiple_value_form') {
      $elements['#tabledrag'] = [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'table-sort-weight',
        ],
      ];
      $elements['#header']['_weight'] = $this->t('Weight');
      unset($elements['#theme']);
    }

    if (!empty($elements['#is_multiple'])) {
      $elements['#header']['_more'] = '';
    }
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $settings = $this->getSettings();
    if (!empty($element['#allow_more']) && !empty($settings['draggable'])) {
      $element['_drag'] = [
        '#plain_text' => '',
      ];
      $element['#attributes']['class'][] = 'draggable';
    }
    $element = parent::formElement($items, $delta, $element, $form, $form_state);
    $element['#type'] = 'container';
    return $element;
  }

  /**
   * Build subfield element.
   */
  protected function formSubfieldElement(FieldItemListInterface $subfield_items, array $config, WidgetInterface $subfield_widget, array &$form, FormStateInterface $form_state) {
    $element = [
      '#title' => '',
      '#description' => '',
      '#required' => FALSE,
      '#field_name' => $this->fieldDefinition->getName(),
      '#field_parents' => $form['#parents'],
      '#delta' => 0,
      '#weight' => 0,
      // Integrations with field_labels module.
      '#title_lock' => TRUE,
      '#element_validate' => [[get_class($this), 'formElementValidate']],
      '#subfield_config' => $config,
    ];
    $element = $subfield_widget->formElement($subfield_items, 0, $element, $form, $form_state);
    return $element;
  }

}
