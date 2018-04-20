<?php

/**
 * @file
 * Contains \Drupal\tablefield\Plugin\Field\FieldFormatter\TablefieldFormatter.
 */

namespace Drupal\tablefield\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Url;
use Drupal\Component\Utility\Html;
//use Drupal\tablefield\Utility\Tablefield;

/**
 * Plugin implementation of the default Tablefield formatter.
 *
 * @FieldFormatter (
 *   id = "tablefield",
 *   label = @Translation("Tabular View"),
 *   field_types = {
 *     "tablefield"
 *   }
 * )
 */
class TablefieldFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode = NULL) {

    $field = $items->getFieldDefinition();
    $field_name = $field->getName();
    $field_settings = $field->getSettings();

    $entity = $items->getEntity();
    $entity_type = $entity->getEntityTypeId();
    $entity_id = $entity->id();


    $elements = array();
  
    foreach ($items as $delta => $table) {

      if (!empty($table->value)) {
        $tabledata = $table->value;     //Tablefield::rationalizeTable($table->value);

        // Run the table through input filters
        $c=0; //declare var to check whether data is present or not.
        foreach ($tabledata as $row_key => $row) {
            $c++;   // increment $c for each row.
          foreach ($row as $col_key => $cell) {
             if(!empty($cell)) {
                $tabledata[$row_key][$col_key] = array(
                  'data' => empty($table->format) ? Html::escape($cell) : check_markup($cell, $table->format),
                  'class' => array('row_' . $row_key, 'col_' . $col_key)
                );
             }
             else {
              //remove empty cell.
                  unset($tabledata[$row_key][$col_key]);
             }
          }
        }
        // Pull the header for theming
        $header_data = array_shift($tabledata);
        
        $count=0;
        foreach ($tabledata as $row_key => $row) {
            if($row==array()){
                $count++;     // Increment $count , if table has no data.
            }
        }
      
        $head = array();    // declare $head for header data.
        $i=0;
        // Check for an empty header, if so we don't want to theme it.
        $noheader = TRUE;
        foreach ($header_data as $cell) {
          if (strlen($cell['data']) > 0) {
            $noheader = FALSE;
            $head[$i] = $cell['data'];
            $i++;
            //break;   //In header , check data is present or not in each cell.
          }
        }
  
        $header = $noheader ? NULL : $head;

        $render_array = array();

        // If the user has access to the csv export option, display it now.
        if ($field_settings['export'] && \Drupal::currentUser()->hasPermission('export tablefield')) {

          $route_params = array(
            'entity_type' => $entity_type,
            'entity_id' => $entity_id,
            'field_name' => $field_name,
            'langcode' => $items->getLangcode(),
            'delta' => $delta,
          );

          $url = Url::fromRoute('tablefield.export', $route_params);

          $render_array['export'] = array(
            '#type' => 'container',
            '#attributes' => array(
              'id' => 'tablefield-export-link-' . $delta,
              'class' => 'tablefield-export-link',
            ),
          );
          $render_array['export']['link'] = array(
            '#type' => 'link',
            '#title' => $this->t('Export Table Data'),
            '#url' => $url,
          );
        }
        // if table has data.
        if($count!=$c-1){
            $render_array['tablefield'] = array(
              '#type' => 'table',
              '#header' => $header,
              '#rows' => $tabledata,
              '#attributes' => array(
                'id' => 'tablefield-' . $delta,
                'class' => array(
                  'tablefield'
                ),
              ),
              '#prefix' => '<div id="tablefield-wrapper-'. $delta .'" class="tablefield-wrapper">',
              '#suffix' => '</div>',
            );
        }
        //if table has no data.
        else{
            $render_array['tablefield'] = array(
                '#type' => 'markup',
                '#markup' => "No Data",
            );
        }

        $elements[$delta] = $render_array;
      }

    }
    return $elements;
  }

}
