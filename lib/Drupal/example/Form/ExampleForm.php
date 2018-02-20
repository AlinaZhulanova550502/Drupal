<?php

/**
 * @file
 * Contains \Drupal\example\Form|ExampleForm.
 */

namespace Drupal\example\Form;      //пространство имен формы
use Drupal\Core\Form\FormStateInterface;  //будет наследование от этого

/**
 * Implements an example form.
 */
class ExampleForm extends FormBase {  //объявление формы, наследуясь от FormBase

    /**
     * {#inheritdoc}.
     */
    public function getFormID() {
        return 'example_form';  //возврат названия формы
    }

    /**
     * {#inheritdoc}.
     */
    public function buildForm(array $form, FormStateInterface &$form_state) {    //создание формы      ??????

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
            '#type' => 'textfield',
            '#text_format' => 'long_text',
            '#title' => $this->t('Message'),
            '#required' => TRUE
        );
        $form['email'] = array(                 //EMAIL
            '#type' => 'email',
            '#title' => $this->t('Email'),
            '#required' => TRUE
        );

        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = array( //кнопка отправки
            '#type' => 'submit',
            '#value' => $this->t('Save'),
            'button_type' => 'primary'
        );

        return $form;
    }

    /**
     * {#inheritdoc}.
     */

    public function validateForm(array &$form, FormStateInterface $form_state) {        //валидация
        $regexp='/^[-a-z0-9!#$%&\'*+/=?^_`{|}~]+(\.[-a-z0-9!#$%&\'*+/=?^_\`{|}~]+)*@([a-z0-9]([-a-z0-9]{0,61}[a-z0-9])?\.)*[a-z][a-z]{1,3}$/';
        if(!preg_match($regexp, $form[email])) {                       //если нет совпадений
            $form[email]->setErrorByName('email', $this->t('email isn\'t correct.'));
        }
    }

    function send_email($email, $subject, $message)
    {
        $boundary = md5( uniqid() . microtime() );      //хэш строки
        $body = "--$boundary\r\n" .
            "Content-Type: text/html; charset=ISO-8859-1\r\n" .
            "Content-Transfer-Encoding: base64\r\n\r\n";
        $body .= chunk_split( base64_encode( $message ) );
        $body .= "--$boundary--";

        return mail($email, $subject, $body);               //отправка почты
    }

    public function submitForm(array &$form, FormStateInterface $form_state) {
        drupal_set_message($this->t('Thank you @first_name, your message will be sent', array(
            '@first_name' => $form_state->getValue('first_name')
        )));
        if (send_email(form[email], form[subject], form[message])==true){
            \Drupal::logger('example')->notice('Message to $form[email] was sent sucsessful!');     //запись в лог
        }
    }
}