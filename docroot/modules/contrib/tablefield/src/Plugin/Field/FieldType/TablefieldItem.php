<?php

/**
 * @file
 * Contains \Drupal\tablefield\Plugin\Field\FieldType\Tablefield.
 */

namespace Drupal\tablefield\Plugin\Field\FieldType;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\MapDataDefinition;
use Drupal\Core\Field\FieldItemBase;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;

//use Drupal\Core\Ajax\AjaxResponse;

/**
 * Plugin implementation of the 'tablefield' field type.
 *
 * @FieldType (
 *   id = "tablefield",
 *   label = @Translation("Table Field"),
 *   description = @Translation("Stores a table of text fields"),
 *   default_widget = "tablefield",
 *   default_formatter = "tablefield"
 * )
 */

class TablefieldItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'value' => array(
          'type' => 'blob',
          'size' => 'big',
          'serialize' => TRUE,
        ),
        'format' => array(
          'type' => 'varchar',
          'length' => 255,
          'default value' => '',
        ),
      ),
    );
  }
  
   /**
   * {@inheritdoc}
   */
  
  public function getInfo() {
     return array();
  }

  /**
   * {@inheritdoc}
   */
  
  public static function defaultFieldSettings() {
    return array(
      'export' => 0,
      'restrict_rebuild' => 1,
      'restrict_import' => 1,
      'lock_values' => 0,
      'cell_processing' => 0,
      'empty_rules' => array(
        'ignore_table_structure' => 0,
        'ignore_table_header' => 0,
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $form = array();
    $settings = $this->getSettings();
    
    $form['default_message'] = array(
      '#type' => 'markup',
      '#markup' => $this->t('To specify a default table, use the &quot;Default Value&quot; above. There you can specify a default number of rows/columns and values.'),
    );
    $form['export'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Allow users to export table data as CSV'),
      '#default_value' => $settings['export'],
    );
    $form['restrict_rebuild'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Restrict rebuilding to users with the permission "rebuild tablefield"'),
      '#default_value' => $settings['restrict_rebuild'],
    );
    $form['restrict_import'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Restrict importing to users with the permission "import tablefield"'),
      '#default_value' => $settings['restrict_import'],
    );
    $form['lock_values'] = array(
      '#type' => 'checkbox',
      '#title' => 'Lock table field defaults from further edits during node add/edit.',
      '#default_value' => $settings['lock_values'],
    );
    $form['cell_processing'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Table cell processing'),
      '#default_value' => $settings['cell_processing'],
      '#options' => array(
        $this->t('Plain text'),
        $this->t('Filtered text (user selects input format)')
      ),
    );
    $form['empty_rules'] = array(
      '#type' => 'details',
      '#title' => $this->t('Rules for evaluating whether tablefield item should be considered empty'),
      '#open' => FALSE,
    );
    $form['empty_rules']['ignore_table_structure'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Ignore table structure changes'),
      '#description' => $this->t('If checked, table structure, i.e. number of rows and cols will not be considered when evaluating whether tablefield item is empty or not. If unchecked, a table structure which is different from the one set in defaults will result in the tablefield item being considered not empty.'),
      '#default_value' => $settings['empty_rules']['ignore_table_structure'],
    );
    $form['empty_rules']['ignore_table_header'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Ignore table header'),
      '#description' => $this->t('If checked, tablefield item will be considered empty even if it does have a table header, i.e. even if first row of the table contains non-empty cells.'),
      '#default_value' => $settings['empty_rules']['ignore_table_header'],
    );
    return $form;
  }
  /**
   *storageSettingsForm() to get options for select field & date field & default value of column.
   */
  public static function defaultStorageSettings() {
    return array(
      'cols' => 5,
      'column_select' => 0,
      'numbers' => 0,
      'col_opt' => array(),
      'column_date' => 0,
    );
  }
  
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    
    $element = array();
    $data = $this->getSettings(); // fetching default value.
    
    
    $element['cols'] =  array(
        '#title' => t('Enter default number of column'),
        '#type' => 'textfield',
        '#size' => 5,
        '#default_value' => $data['cols'],
    );
    $element['column_date'] =  array(
        '#title' => t('Enter column numbers which you want in date field separated with comma(1,2)'),
        '#type' => 'textfield',
        '#size' => 5,
        '#default_value' => $data['column_date'],
        '#suffix' => '<p>Note: Numbers must be in ascending order.</p>',
    );
    
    $element['column_select'] =  array(
        '#title' => t('Enter how many column you want in select options'),
        '#type' => 'textfield',
        '#size' => 5,
        '#default_value' => $data['column_select'],
    );
    
    $element['numbers'] =  array(
        '#title' => t('Enter Column numbers separated with comma(,)'),
        '#type' => 'textfield',
        '#size' => 5,
        '#default_value' => $data['numbers'],
        '#suffix' => '<p>Note: Numbers must be in ascending order.</p>',
    );
    // ajax call button.
    $element['button'] = array(
        '#type' => 'button',
        '#value' => t('Generate'),
        '#name' => 'column' . $id,
        '#ajax' => array(
          'callback' => 'Drupal\tablefield\Plugin\Field\FieldType\TablefieldItem::ajaxCallbackColumn',
          'wrapper' => 'column-wrapper',
          'progress' => array('type' => 'throbber', 'message' => "please wait"),
          'effect' => 'fade',
        ),
    );
    
    $element['col_opt'] = array(
      '#type' => 'container',
      '#attributes' => array(
      'id' => 'column-wrapper',
      ),
    );
    $form_values = $form_state->getValues(); 
    $column_select = $form_values['settings']['column_select'];   
    $column_numbers = $form_values['settings']['numbers'];   
    $col_num = explode(',',$column_numbers);
    $options = array();
     
    if(!empty($column_select)) {
        for($i=0;$i<$column_select;$i++) {
            $element['col_opt'][$i] =  array(
                '#title' => t('Enter the Options for column ' . $col_num[$i] .''),
                '#type' => 'textarea',
                '#required' => TRUE,
                '#name' => 'settings[col_opt][' . $i .']',
                '#size' => 5,
                '#default_value' => $data['col_opt'][$i],
                '#suffix' => '<p>Note: Give the options in "key|value" format & each data in new line.</p>',
            );
      }
    }
    return $element;
}
// ajax call.
    function ajaxCallbackColumn(array &$form, FormStateInterface $form_state) {
        $form_values = $form_state->getValues();
        $column_select = $form_values['settings']['column_select'];
        $column_numbers = $form_values['settings']['numbers'];      
        $col_num = explode(',',$column_numbers);       
        $options = array();
        if(!empty($column_select)) {
        for($i=0;$i<$column_select;$i++) {
            $element['col_opt'][$i] =  array(
                '#title' => t('Enter the Options for column ' . $col_num[$i] .''),
                '#type' => 'textarea',
                '#required' => TRUE,
                '#name' => 'settings[col_opt][' . $i .']',
                '#size' => 5,
                '#default_value' => $data['col_opt'][$i],
                '#suffix' => '<p>Note: Give the options in "key|value" format & each data in new line.</p>',
            );
        }
    }
    return $element['col_opt'];
}
  /**
   * {@inheritdoc}
   */

  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = MapDataDefinition::create()
      ->setLabel(t('Table data'))
      ->setDescription(t('Stores tabular data.'));

    $properties['format'] = DataDefinition::create('filter_format')
      ->setLabel(t('Text format'));

    return $properties;
  }

  public function setValue($values, $notify = TRUE) {
    if (!isset($values)) {
      return;
    }
    // we want to keep the table right under the 'value' key
    else if (!empty($values['tablefield'])) {
      $values['rebuild'] = $values['tablefield']['rebuild'];
      $values['value'] = $values['tablefield']['table'];
      unset($values['tablefield']);
      unset($values['rebuild']['rebuild']);
    }
    // in case cell_processing is enabled
    // text_format puts values under an extra 'value' key
    else if (!empty($values['value']['tablefield'])) {
      $values['rebuild'] = $values['value']['tablefield']['rebuild'];
      $values['value'] = $values['value']['tablefield']['table'];
      unset($values['rebuild']['rebuild']);
    }
    // in case this is being loaded from storage recalculate rows/cols
    else if (empty($values['rebuild'])) {
      $values['rebuild']['rows'] = isset($values['value']) ? count($values['value']) : 0;
      $values['rebuild']['cols'] = isset($values['value'][0]) ? count($values['value'][0]) : 0;
    }

    // if lock defaults is enabled the table might need sorting
    $lock = $this->getFieldDefinition()->getSetting('lock_values');
    if ($lock) {
      ksort($values['value']);
    }

    parent::setValue($values, $notify);
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    // @TODO should field definition be counted?
    return array(
      'value' => [['Header 1', 'Header 2'], ['Data 1', 'Data 2']],
      'rebuild' => ['rows' => 2, 'cols' => 2],
    );
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->getValue();

    $empty_rules = $this->getFieldDefinition()->getSetting('empty_rules');
    $in_settings = \Drupal::request()->get(RouteObjectInterface::ROUTE_NAME) == 'entity.field_config.node_field_edit_form';

    // check table data first
    if (is_array($value['value'])) {

      // ignore table header?
      if (!$in_settings && $empty_rules['ignore_table_header']) {
        array_shift($value['value']);
      }

      foreach ($value['value'] as $row) {
        foreach ($row as $cell) {
          if (!empty($cell)) {
            return FALSE;
          }
        }
      }
    }

    // if table structure is not ignored see if it differs from defaults
    // check the route to see if you are in the field settings form
    // if yes, defaults are the tablefield config defaults
    // otherwise first consider field settings defaults
    if (empty($empty_rules['ignore_table_structure'])) {
      $default_value = $this->getFieldDefinition()->getDefaultValueLiteral();

      if (!$in_settings && !empty($default_value[$this->name])) {
        $default_structure = $default_value[$this->name]['rebuild'];
      }
      else {
        $default_structure = array(
          'rows' => \Drupal::config('tablefield.settings')->get('rows'),
          'cols' => \Drupal::config('tablefield.settings')->get('cols'),
        );
      }

      if ($value['rebuild'] != $default_structure) {
        return FALSE;
      }
    }

    return TRUE;
  }
}
