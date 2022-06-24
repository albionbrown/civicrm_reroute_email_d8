<?php

namespace Drupal\civicrm_reroute_email\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ConfigureForm.
 */
class ConfigureForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'configure_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = \Drupal::config('civicrm_reroute_email.settings');

    $form['civicrm_reroute_email_enable'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable CiviCRM rerouting'),
      '#description' => $this->t('Check this box if you want to enable CiviCRM email rerouting. Uncheck to disable rerouting.'),
      '#weight' => '0',
      '#default_value' => $config->get('enable'),
    ];

    $form['civicrm_reroute_email_address'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Email address'),
      '#description' => $this->t('Provide an email address to reroute all emails to this address.'),
      '#maxlength' => 128,
      '#size' => 64,
      '#weight' => '0',
      '#default_value' => $config->get('email_address')
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    $input = $form_state->getUserInput();

    if ($form_state->getValue('civicrm_reroute_email_enable') && empty($input['civicrm_reroute_email_address'])) {
      $form_state->setErrorByName('civicrm_reroute_email_address', t('You must enter an email address if email rerouting is enabled.'));
    }

    // Validate email
    if (!empty($input['civicrm_reroute_email_address']) && !filter_var($input['civicrm_reroute_email_address'], FILTER_VALIDATE_EMAIL)) {
      $form_state->setErrorByName('civicrm_reroute_email_address', t('Please enter a valid email address.'));
    }

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    
    $configFactory = \Drupal::configFactory()->getEditable('civicrm_reroute_email.settings');
    $configFactory->set('enable', $form_state->getValue('civicrm_reroute_email_enable'))->save();
    $configFactory->set('email_address', $form_state->getUserInput()['civicrm_reroute_email_address'])->save();
    $configFactory->save();
  }
}
