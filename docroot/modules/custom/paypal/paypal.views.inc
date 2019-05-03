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
  $data['paypal_payment_status']['table']['base'] = [
    // Identifier (primary) field in this table for Views.
    'field' => 'order_id',
        // Label in the UI.
    'title' => t('Candidate Payment Details'),
        // Longer description in the UI. Required.
    'help' => t('Payments id'),
    'weight' => -10,
  ];
  $data['paypal_payment_status']['order_id'] = [
    'title' => t('Order ID'),
    'help' => t('Order ID text field.'),

    'field' => [
      'id' => 'standard',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'argument' => [
      'id' => 'string',
    ],
  ];

  $data['paypal_payment_status']['user_id'] = [
    'title' => t('User ID'),
    'help' => t('User ID text field.'),

    'field' => [
      'id' => 'standard',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'argument' => [
      'id' => 'string',
    ],
  ];

  $data['paypal_payment_status']['before_amount'] = [
    'title' => t('Before Amount'),
    'help' => t('Before Amount text field.'),

    'field' => [
      'id' => 'standard',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'argument' => [
      'id' => 'string',
    ],
  ];
  $data['paypal_payment_status']['after_amount'] = [
    'title' => t('After Amount'),
    'help' => t('After Amount'),

    'field' => [
      'id' => 'standard',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'argument' => [
      'id' => 'string',
    ],
  ];
  $data['paypal_payment_status']['custom_id'] = [
    'title' => t('User Custon ID'),
    'help' => t('User Custon id.'),

    'field' => [
      'id' => 'standard',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'argument' => [
      'id' => 'string',
    ],
  ];
  $data['paypal_payment_status']['transaction_id'] = [
    'title' => t('Transaction ID'),
    'help' => t('User Transaction ID'),

    'field' => [
      'id' => 'standard',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'argument' => [
      'id' => 'string',
    ],
  ];
  $data['paypal_payment_status']['payment_status'] = [
    'title' => t('Payment Status'),
    'help' => t('User Payment Status Details.'),

    'field' => [
      'id' => 'standard',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'argument' => [
      'id' => 'string',
    ],
  ];
  $data['paypal_payment_status']['programme_id'] = [
    'title' => t('Programme ID'),
    'help' => t('Programme ID Details.'),

    'field' => [
      'id' => 'standard',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'argument' => [
      'id' => 'string',
    ],
  ];
  $data['paypal_payment_status']['currency_code'] = [
    'title' => t('Currency Code'),
    'help' => t('Currency Code Details.'),

    'field' => [
      'id' => 'standard',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'argument' => [
      'id' => 'string',
    ],
  ];
  $data['paypal_payment_status']['created'] = [
    'title' => t('Created'),
    'help' => t('Created Details.'),

    'field' => [
      'id' => 'standard',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'argument' => [
      'id' => 'string',
    ],
  ];
  $data['paypal_payment_status']['updated'] = [
    'title' => t('Updated'),
    'help' => t('Updated  Details.'),

    'field' => [
      'id' => 'standard',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'argument' => [
      'id' => 'string',
    ],
  ];
  $data['applications_list']['table']['group'] = t('Application  Details');
  $data['applications_list']['table']['base'] = [
        // Identifier (primary) field in this table for Views.
    'field' => 'id',
        // Label in the UI.
    'title' => t('Candidate Application Details'),
        // Longer description in the UI. Required.
    'help' => t('Application id field'),
    'weight' => -10,
  ];

  $data['applications_list']['id'] = [
    'title' => t('ID'),
    'help' => t('ID'),

    'field' => [
      'id' => 'standard',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'argument' => [
      'id' => 'string',
    ],
  ];

  $data['applications_list']['user_id'] = [
    'title' => t('User ID'),
    'help' => t('User ID text field.'),

    'field' => [
      'id' => 'standard',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'argument' => [
      'id' => 'string',
    ],
  ];

  $data['applications_list']['order_id'] = [
    'title' => t('Order Id'),
    'help' => t('Order Id text field.'),

    'field' => [
      'id' => 'standard',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'argument' => [
      'id' => 'string',
    ],
  ];
  $data['applications_list']['application_id'] = [
    'title' => t('Application Id'),
    'help' => t('Application Id'),

    'field' => [
      'id' => 'standard',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'argument' => [
      'id' => 'string',
    ],
  ];
  $data['applications_list']['programme_id'] = [
    'title' => t('Programme Id'),
    'help' => t('Programme Id'),

    'field' => [
      'id' => 'standard',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'argument' => [
      'id' => 'string',
    ],
  ];
  $data['applications_list']['created'] = [
    'title' => t('Created'),
    'help' => t('Created Details.'),

    'field' => [
      'id' => 'standard',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'argument' => [
      'id' => 'string',
    ],
  ];

  return $data;
}
