<?php

namespace Drupal\example\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;

class example extends FormBase
{
    public function contact($email_form, $fn_form, $ln_form){
        $arr = array(
            'properties' => array(
                array(
                    'property' => 'email',
                    'value' => $email_form
                ),
                array(
                    'property' => 'firstname',
                    'value' => $fn_form
                ),
                array(
                    'property' => 'lastname',
                    'value' => $ln_form
                )
        )
        );
        $post_json = json_encode($arr);    //сама строка запроса
        $hapikey = '5324fadc-d0f8-43e4-abe7-ac5c241d8b5c';
        $endpoint = 'https://api.hubapi.com/contacts/v1/contact/?hapikey=' . $hapikey;
        $ch=@curl_init();
        @curl_setopt($ch, CURLOPT_POST, true);
        @curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json); //Строка, содержащая данные для HTTP POST запроса
        @curl_setopt($ch, CURLOPT_URL, $endpoint);
        @curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); //Массив с HTTP заголовками
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = @curl_exec($ch);
        $status_code = @curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_errors = curl_error($ch);
        @curl_close($ch);

        /*if ($status_code==0) */ \Drupal::logger('example')->error("1. $response "."2. $status_code "."3. $curl_errors"." $endpoint");
    }

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
        $adress=$form_state->getValue('email');
        if (simple_mail_send('alina.zhulanava@gmail.com', $adress, $form_state->getValue('subject'), $form_state->getValue('message'))==true)
       {
             drupal_set_message($this->t('Thank you, your message will be sent'));

            \Drupal::logger('example')->notice('Message was sent sucsessful: @email', array(
            '@email' => $form_state->getValue('email')));
           $this->contact($adress, $form_state->getValue('first_name'), $form_state->getValue('last_name'));
       }
    }

}