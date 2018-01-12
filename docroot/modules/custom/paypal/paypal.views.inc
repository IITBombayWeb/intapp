<?php

/**
 * @file
 * Views hooks for paypal payment.
 */

/**
 * Implements hook_views_data().
 */
function paypal_views_data() {
    $data = [];
    $data['paypal_payment_status']['table']['group'] = t('Payment Details');
    $data['paypal_payment_status']['table']['base'] = array(
        // Identifier (primary) field in this table for Views.
        'field' => 'order_id',
        // Label in the UI.
        'title' => t('Candidate Payment Details'),
        // Longer description in the UI. Required.
        'help' => t('Payments id '),
        'weight' => -10,
     );
    
   $data['paypal_payment_status']['order_id'] = array(
    'title' => t('Order ID'),
    'help' => t('Order ID text field.'),

    'field' => array(      
      'id' => 'standard',
    ),
    'sort' => array(
      'id' => 'standard',
    ),
    'filter' => array(
      'id' => 'string',
    ),
    'argument' => array(
      'id' => 'string',
    ),
  );
   
  $data['paypal_payment_status']['user_id'] = array(
    'title' => t('User ID'),
    'help' => t('User ID text field.'),

    'field' => array(      
      'id' => 'standard',
    ),
    'sort' => array(
      'id' => 'standard',
    ),
    'filter' => array(
      'id' => 'string',
    ),
    'argument' => array(
      'id' => 'string',
    ),
  );
  
  $data['paypal_payment_status']['before_amount'] = array(
    'title' => t('Before Amount'),
    'help' => t('Before Amount text field.'),
    
    'field' => array(      
      'id' => 'standard',
    ),
    'sort' => array(
      'id' => 'standard',
    ),
    'filter' => array(
      'id' => 'string',
    ),
    'argument' => array(
      'id' => 'string',
    ),
  );
    $data['paypal_payment_status']['after_amount'] = array(
    'title' => t('After Amount'),
    'help' => t('After Amount'),

     'field' => array(      
      'id' => 'standard',
    ),
    'sort' => array(
      'id' => 'standard',
    ),
    'filter' => array(
      'id' => 'string',
    ),
    'argument' => array(
      'id' => 'string',
    ),
  );
 $data['paypal_payment_status']['custom_id'] = array(
    'title' => t('User Custon ID'),
    'help' => t('User Custon id.'),

     'field' => array(      
      'id' => 'standard',
    ),
    'sort' => array(
      'id' => 'standard',
    ),
    'filter' => array(
      'id' => 'string',
    ),
    'argument' => array(
      'id' => 'string',
    ),
  );
  $data['paypal_payment_status']['transaction_id'] = array(
    'title' => t('Transaction ID'),
    'help' => t('User Transaction ID'),

     'field' => array(      
      'id' => 'standard',
    ),
    'sort' => array(
      'id' => 'standard',
    ),
    'filter' => array(
      'id' => 'string',
    ),
    'argument' => array(
      'id' => 'string',
    ),
  );
   $data['paypal_payment_status']['payment_status'] = array(
    'title' => t('Payment Status'),
    'help' => t('User Payment Status Details.'),

     'field' => array(      
      'id' => 'standard',
    ),
    'sort' => array(
      'id' => 'standard',
    ),
    'filter' => array(
      'id' => 'string',
    ),
    'argument' => array(
      'id' => 'string',
    ),
  );
    $data['paypal_payment_status']['programme_id'] = array(
    'title' => t('Programme ID'),
    'help' => t('Programme ID Details.'),

     'field' => array(      
      'id' => 'standard',
    ),
    'sort' => array(
      'id' => 'standard',
    ),
    'filter' => array(
      'id' => 'string',
    ),
    'argument' => array(
      'id' => 'string',
    ),
  );
    $data['paypal_payment_status']['currency_code'] = array(
   'title' => t('Currency Code'),
   'help' => t('Currency Code Details.'),

    'field' => array(      
     'id' => 'standard',
   ),
   'sort' => array(
     'id' => 'standard',
   ),
   'filter' => array(
     'id' => 'string',
   ),
   'argument' => array(
     'id' => 'string',
   ),
  );
   $data['paypal_payment_status']['created'] = array(
   'title' => t('Created'),
   'help' => t('Created Details.'),

    'field' => array(      
     'id' => 'standard',
   ),
   'sort' => array(
     'id' => 'standard',
   ),
   'filter' => array(
     'id' => 'string',
   ),
   'argument' => array(
     'id' => 'string',
   ),
  );
  $data['paypal_payment_status']['updated'] = array(
   'title' => t('Updated '),
   'help' => t('Updated  Details.'),

    'field' => array(      
     'id' => 'standard',
   ),
   'sort' => array(
     'id' => 'standard',
   ),
   'filter' => array(
     'id' => 'string',
   ),
   'argument' => array(
     'id' => 'string',
   ),
  );
  
  
  $data['applications_list']['table']['group'] = t('Application  Details');
    $data['applications_list']['table']['base'] = array(
        // Identifier (primary) field in this table for Views.
        'field' => 'id',
        // Label in the UI.
        'title' => t('Candidate Application Details '),
        // Longer description in the UI. Required.
        'help' => t('Application id field'),
        'weight' => -10,
     );
    
   $data['applications_list']['id'] = array(
    'title' => t(' ID'),
    'help' => t(' ID '),

    'field' => array(      
      'id' => 'standard',
    ),
    'sort' => array(
      'id' => 'standard',
    ),
    'filter' => array(
      'id' => 'string',
    ),
    'argument' => array(
      'id' => 'string',
    ),
  );
   
  $data['applications_list']['user_id'] = array(
    'title' => t('User ID'),
    'help' => t('User ID text field.'),

    'field' => array(      
      'id' => 'standard',
    ),
    'sort' => array(
      'id' => 'standard',
    ),
    'filter' => array(
      'id' => 'string',
    ),
    'argument' => array(
      'id' => 'string',
    ),
  );
  
  $data['applications_list']['order_id'] = array(
    'title' => t('Order Id'),
    'help' => t(' Order Id text field.'),
    
    'field' => array(      
      'id' => 'standard',
    ),
    'sort' => array(
      'id' => 'standard',
    ),
    'filter' => array(
      'id' => 'string',
    ),
    'argument' => array(
      'id' => 'string',
    ),
  );
    $data['applications_list']['application_id'] = array(
    'title' => t('Application Id'),
    'help' => t('Application Id'),

     'field' => array(      
      'id' => 'standard',
    ),
    'sort' => array(
      'id' => 'standard',
    ),
    'filter' => array(
      'id' => 'string',
    ),
    'argument' => array(
      'id' => 'string',
    ),
  );
 $data['applications_list']['programme_id'] = array(
    'title' => t('Programme Id'),
    'help' => t('Programme Id'),

     'field' => array(      
      'id' => 'standard',
    ),
    'sort' => array(
      'id' => 'standard',
    ),
    'filter' => array(
      'id' => 'string',
    ),
    'argument' => array(
      'id' => 'string',
    ),
  );
  $data['applications_list']['created'] = array(
   'title' => t('Created'),
   'help' => t('Created Details.'),

    'field' => array(      
     'id' => 'standard',
   ),
   'sort' => array(
     'id' => 'standard',
   ),
   'filter' => array(
     'id' => 'string',
   ),
   'argument' => array(
     'id' => 'string',
   ),
  );
   
  return $data;
}
