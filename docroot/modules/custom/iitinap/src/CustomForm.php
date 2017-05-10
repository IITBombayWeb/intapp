<?php

namespace Drupal\iitinap;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class CustomForm extends ConfigFormBase {
    
    function getFormId() {
     return 'custom_form';
    }
    
      protected function getEditableConfigNames() {
    return [
      'iitinap.settings',
    ];
  }
function buildForm(array $form, FormStateInterface $form_state) {
}
}