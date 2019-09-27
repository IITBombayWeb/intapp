<?php

/**
 * @file
 * Contains \Drupal\Core\Render\Element\Textarea.
 */

namespace Drupal\tablefield\Element;
use Drupal\Core\Render\Element\FormElement;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\NestedArray;

/**
 * Provides a form element for tabular data.
 *
 * @FormElement("tablefield")
 */

class Tablefield extends FormElement {
  
  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return array(
      '#input' => TRUE,
      '#cols' => 5,
      '#rows' => 5,
      '#lock' => FALSE,
      '#locked_cells' => array(),
      '#rebuild' => FALSE,
      '#import' => FALSE,
      //declare variable.
      '#addrow' => FALSE,
      '#colnum' => FALSE,
      '#col_opt' => FALSE,
      '#col_date' => FALSE,
      '#process' => array(
        array($class, 'processTablefield'),
      ),
      '#theme_wrappers' => array('form_element'),
    );
  }

  /**
   * Processes a checkboxes form element.
   */
  public static function processTablefield(&$element, FormStateInterface $form_state, &$complete_form) {
    $parents = $element['#parents'];
    $value = is_array($element['#value']) ? $element['#value'] : array();
    // string to uniquely identify DOM elements
    $id = implode('-', $element['#parents']);

    // this is being set in rebuild and import ajax calls
    $storage = NestedArray::getValue($form_state->getStorage(), $element['#parents']);

    if ($storage) {
      $element['#cols'] = $storage['tablefield']['rebuild']['cols'];
      $element['#rows'] = $storage['tablefield']['rebuild']['rows'];
      $element['#add_row'] = $storage['tablefield']['addrow']['row_value'];  //fetching addrow value.
    }
    $element['#tree'] = TRUE;
    
    $element['tablefield'] = array(
      '#type' => 'fieldset',
      '#attributes' => array('class' => array('form-tablefield')),
      '#prefix' => '<div id="tablefield-'. $id .'-wrapper">',
      '#suffix' => '</div>',
    );
    
    $element['tablefield']['table'] = array(
      '#type' => 'table',
    );
    
    $rows = isset($element['#rows']) ? $element['#rows'] : \Drupal::config('tablefield.settings')->get('rows');
    $cols = isset($element['#cols']) ? $element['#cols'] : \Drupal::config('tablefield.settings')->get('cols');
  // get all value from setting form.
    $colnum = isset($element['#colnum']) ? $element['#colnum'] : 0;
  
    $col_opt = $element['#col_opt'];
    
    $row_value =  isset($element['#add_row']) ? $element['#add_row'] : 0; //fetch addrow value.
    
    $col_date = isset($element['#col_date']) ? $element['#col_date'] : 0;
     $date_column = explode(',',$col_date);
  
     /**
    *This code is to get with options with "|" symbol
    */
    //get column options in array for specific column.
     $col_num = explode(',',$colnum);
    $colopt = array();

    $col_len = count($col_num);
    $l=0;
    for($j=0;$j<$col_len;$j++){
      if(!empty($col_opt[$j])){
    $colopt[$j] = explode(PHP_EOL, $col_opt[$j]);
   
    array_unshift($colopt[$j] , '|-none-' );
      }
    }
  $column_options = array();
    for($j=0;$j<$col_len;$j++){
      if(!empty($colopt[$j])){
            foreach($colopt[$j] as $val) {
            if(!empty($val)){
                $col_value = explode("|",$val);
                      if((!empty($col_num[$j]))&&(!empty($col_value))){
                          $column_options[$col_num[$j]][$col_value[0]] = $col_value[1];
                      }
                }
             
            }
      }
    }
      // if addrow has value then replace it.
    if($row_value!=0) {
        $rows = $row_value;
    }
    
    for ($i = 0; $i < $rows; $i++) {
         $j=0; $m = 0;
      for ($ii = 0; $ii < $cols; $ii++) {
        if (!empty($element['#locked_cells'][$i][$ii]) && !empty($element['#lock'])) {
          $element['tablefield']['table'][$i][$ii] = array(
            '#type' => 'item',
            '#value' => $element['#locked_cells'][$i][$ii],
            '#title' => $element['#locked_cells'][$i][$ii],
          );
        }
        else { // for select column and also not for header.
                if ((!empty($col_num[$j]))&&($ii==$col_num[$j]-1) && ($i!=0)) {
                        $cell_value = isset($value[$i][$ii]) ? $value[$i][$ii] : '';
                        $element['tablefield']['table'][$i][$ii] = array(
                            '#type' => 'select',
                            '#maxlength' => 2048,
                            '#options' => $column_options[$ii+1],
                            '#default_value' =>  $cell_value,         
                            '#attributes' => array(
                              'class' => array('tablefield-row-'. $i, 'tablefield-col-'. $ii),
                              'style' => 'width:100%'
                            ),
                        );
                        $j++;
                  } // for date field.
                  else if((!empty($date_column[$m]))&&($ii == $date_column[$m]-1) && ($i!=0)){
                    $cell_value = isset($value[$i][$ii]) ? $value[$i][$ii] : '';
                    $element['tablefield']['table'][$i][$ii] = array(
                            '#type' => 'datetime', // field
                                //enable date option only
                            '#date_date_element' => 'date',
                            '#date_time_element' => 'none',
                            '#attributes' => array(
                              'class' => array('tablefield-row-'. $i, 'tablefield-col-'. $ii),
                              'style' => 'width:100%',
                            ),
                            '#default_value' => $cell_value,
                        );
                    $m++;
                  }
                else {  //default text field.
                    $cell_value = isset($value[$i][$ii]) ? $value[$i][$ii] : '';
                    $element['tablefield']['table'][$i][$ii] = array(
                      '#type' => 'textfield',
                      '#maxlength' => 2048,
                      '#size' => 0,
                      '#attributes' => array(
                        'class' => array('tablefield-row-'. $i, 'tablefield-col-'. $ii),
                        'style' => 'width:100%'
                      ),
                      '#default_value' => $cell_value,
                    );
                }
            }
        }
    }
    
    
     //To change number of rows.
    
   if (!empty($element['#addrow'])) {
      $element['tablefield']['addrow']['row_value'] = array(
        '#title' => t('How many rows'),
        '#type' => 'hidden',
        '#default_value' => $rows,
      );
      $element['tablefield']['addrow']['addrow']  = array(
        '#type' => 'submit',
        '#value' => t('AddRow'),
        '#name' => 'tablefield-addrow-'. $id,
        '#attributes' => array(
          'class' => array('tablefield-addrow'),
        ),
        '#submit' => array(array(get_called_class(), 'submitCallbackRebuild')),
        '#ajax' => array(
          'callback' => 'Drupal\tablefield\Element\Tablefield::ajaxCallbackRebuild',
          'progress' => array('type' => 'throbber', 'message' => NULL),
          'wrapper' => 'tablefield-'. $id .'-wrapper',
          'effect' => 'fade',
        ),
      );
    }

     
    // If no rebuild, we pass along the rows/cols as a value.
    // Otherwise, we will provide form elements to specify the size and ajax rebuild.
    if (empty($element['#rebuild'])) {
      $element['tablefield']['rebuild'] = array (
        '#type' => 'value',
        'cols' => array(
          '#type' => 'value',
          '#value' => $cols,
        ),
        'rows' => array(
          '#type' => 'value',
          '#value' => $rows,
        ),
      );
    }
    else {  
      $element['tablefield']['rebuild'] = array(
        '#type' => 'details',
        '#title' => t('Change number of rows/columns.'),
        '#open' => FALSE,
      );
      $element['tablefield']['rebuild']['cols'] = array(
        '#title' => t('How many Columns'),
        '#type' => 'textfield',
        '#size' => 5,
        '#default_value' => $cols,
      );
      $element['tablefield']['rebuild']['rows'] = array(
        '#title' => t('How many Rows'),
        '#type' => 'textfield',
        '#size' => 5,
        '#default_value' => $rows,
      );
      $element['tablefield']['rebuild']['rebuild'] = array(
        '#type' => 'submit',
        '#value' => t('Rebuild Table'),
        '#name' => 'tablefield-rebuild-'. $id,
        '#attributes' => array(
          'class' => array('tablefield-rebuild'),
        ),
        '#submit' => array(array(get_called_class(), 'submitCallbackRebuild')),
        '#limit_validation_errors' => [
          array_merge($parents, ['tablefield', 'rebuild', 'cols']),
          array_merge($parents, ['tablefield', 'rebuild', 'rows']),
          array_merge($parents, ['tablefield', 'rebuild', 'rebuild']),
        ],
        '#ajax' => array(
          'callback' => 'Drupal\tablefield\Element\Tablefield::ajaxCallbackRebuild',
          'progress' => array('type' => 'throbber', 'message' => NULL),
          'wrapper' => 'tablefield-'. $id .'-wrapper',
          'effect' => 'fade',
        ),
      );  
    }
  
    // Allow import of a csv file
    if (!empty($element['#import'])) {
      $element['tablefield']['import'] = array(
        '#type' => 'details',
        '#title' => t('Import from CSV'),
        '#open' => FALSE,
      );
      $element['tablefield']['import']['csv'] = array(
        '#name' => 'files['. $id .']',
        '#title' => 'File upload',
        '#type' => 'file',
      );

      $element['tablefield']['import']['import'] = array(
        '#type' => 'submit',
        '#value' => t('Upload CSV'),
        '#name' => 'tablefield-import-'. $id,
        '#attributes' => array(
          'class' => array('tablefield-rebuild'),
        ),
        '#submit' => array(array(get_called_class(), 'submitCallbackRebuild')),
        '#limit_validation_errors' => [
          array_merge($parents, ['tablefield', 'import', 'csv']),
          array_merge($parents, ['tablefield', 'import', 'import']),
        ],
        '#ajax' => array(
          'callback' => 'Drupal\tablefield\Element\Tablefield::ajaxCallbackRebuild',
          'progress' => array('type' => 'throbber', 'message' => NULL),
          'wrapper' => 'tablefield-'. $id .'-wrapper',
          'effect' => 'fade',
        ),
      );
    }
    return $element;
  }

  /**
   * AJAX callback to rebuild the number of rows/columns.
   * The basic idea is to descend down the list of #parent elements of the
   * triggering_element in order to locate the tablefield inside of the $form
   * array. That is the element that we need to return.
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public static function ajaxCallbackRebuild(array $form, FormStateInterface $form_state) {
    
    $triggering_element = $form_state->getTriggeringElement();
    
    // go as deep as 'tablefield' key, but stop there (two more keys follow)
    $parents = array_slice($triggering_element['#array_parents'], 0, -2, TRUE);
    $rebuild = NestedArray::getValue($form, $parents);

    // We don't want to re-send the format/_weight options.
    unset($rebuild['format']);
    unset($rebuild['_weight']);

    return $rebuild;
  }

  public static function submitCallbackRebuild(array $form, FormStateInterface $form_state) {
    // check what triggered this
    // we might need to rebuild or to import
    $triggering_element = $form_state->getTriggeringElement();
    $id = implode('-', array_slice($triggering_element['#parents'], 0, -3, TRUE));
    $parents = array_slice($triggering_element['#parents'], 0, -2, TRUE);
    $value = $form_state->getValue($parents);

    if (isset($triggering_element['#name']) && $triggering_element['#name'] == 'tablefield-rebuild-'. $id) {
      $parents[] = 'rebuild';
      NestedArray::setValue($form_state->getStorage(), $parents, $value['rebuild']);

      drupal_set_message(t('Table structure rebuilt.'), 'status', FALSE);
    }
    elseif (isset($triggering_element['#name']) && $triggering_element['#name'] == 'tablefield-import-'. $id) {
      // Import CSV
      $imported_tablefield = static::importCsv($id);
    
      if ($imported_tablefield) {
        $form_state->setValue($parents, $imported_tablefield);

        $input = $form_state->getUserInput();
        NestedArray::setValue($input, $parents, $imported_tablefield);
        $form_state->setUserInput($input);

        $parents[] = 'rebuild';
        NestedArray::setValue($form_state->getStorage(), $parents, $imported_tablefield['rebuild']);
      }
    }
    // row_value is defined by adding one to rebuild row.
    else if (isset($triggering_element['#name']) && $triggering_element['#name'] == 'tablefield-addrow-'. $id) {
        $parents[] = 'addrow';
       $value['addrow']['row_value'] =  $value['rebuild']['rows'] + 1;
      NestedArray::setValue($form_state->getStorage(), $parents, $value['addrow']);
    }
    $form_state->setRebuild();
  }

  /**
   * Helper function to import data from a CSV file
   * @param string $form_field_name
   * @return array $tablefield
   */
  private static function importCsv($form_field_name) {
    $file_upload = \Drupal::request()->files->get("files[$form_field_name]", NULL, TRUE);
    if (!empty($file_upload) && $handle = fopen($file_upload->getPathname(), 'r'))  {
      // Populate CSV values
      $tablefield = array();
      $max_cols = 0;
      $rows = 0;

      $separator = \Drupal::config('tablefield.settings')->get('csv_separator');
      while (($csv = fgetcsv($handle, 0, $separator)) !== FALSE) {
        foreach ($csv as $value) {
          $tablefield['table'][$rows][] = $value;
        }
        $cols = count($csv);
        if ($cols > $max_cols) {
          $max_cols = $cols;
        }
        $rows++;
      }
      fclose($handle);

      $tablefield['rebuild']['cols'] = $max_cols;
      $tablefield['rebuild']['rows'] = $rows;

      drupal_set_message(t('Successfully imported @file', array('@file' => $file_upload->getClientOriginalName())));
      return $tablefield;
    }

    drupal_set_message(t('There was a problem importing @file.', array('@file' => $file_upload->getClientOriginalName())));
    return FALSE;
  }
}
