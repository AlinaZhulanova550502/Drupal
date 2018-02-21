<?php

namespace Drupal\example\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;

class example extends FormBase
{
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['first_name'] = array(            //FIRST NAME
            '#type' => 'textfield',
            '#text_format' => 'text',
            '#title' => $this->t('First name'),
            '#required' => TRUE
        );
        $form['last_name'] = array(             //LAST NAME
            '#type' => 'textfield',
            '#text_format' => 'text',
            '#title' => $this->t('Last Name'),
            '#required' => TRUE
        );
        $form['subject'] = array(               //SUBJECT
            '#type' => 'textfield',
            '#text_format' => 'plain_text',
            '#title' => $this->t('Subject'),
            '#required' => TRUE
        );
        $form['message'] = array(              //MESSAGE
            '#type' => 'textarea',
            '#text_format' => 'long_text',
            '#title' => $this->t('Message'),
            '#required' => TRUE
        );
        $form['email'] = array(                 //EMAIL
            '#type' => 'email',
            '#title' => $this->t('Email'),
            '#required' => TRUE
        );

        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit form'),
        ];

        return $form;
    }

    public function getFormId()
    {
        return 'example_form';
    }

    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        $email = $form_state->getValue('email');
        if (!(filter_var($email, FILTER_VALIDATE_EMAIL)))
            $form_state->setErrorByName($email, $this->t('email isn\'t correct'));
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $to=$form_state->getValue('email');
      if (simple_mail_send('', $to, $form_state->getValue('subject'), $form_state->getValue('message'))==true)    
       {
             drupal_set_message($this->t('Thank you, @first_name, your message will be sent', array(
            '@first_name' => $form_state->getValue('first_name'))));

            \Drupal::logger('example')->notice('Message to @email was sent sucsessful!', array(
            '@email' => $form_state->getValue('email')));  
       }
    }
}