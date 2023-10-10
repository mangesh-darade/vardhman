<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Testing extends MY_Controller {

    public function index() {

        if ($_POST) {
            $subject = $_POST['subject'];
            $message = $_POST['message'];
            $this->load->library('email');
            $config = array();
            $config['useragent'] = "Stock Manager Advance";
            $config['mailtype'] = "html";
            $config['crlf'] = "\r\n";
            $config['newline'] = "\r\n";
            $config['protocol'] = $_POST['protocol']; //sendmail
            $config['smtp_host'] = $_POST['smtp_host']; //'ssl://smtp.googlemail.com';
            $config['smtp_user'] = $_POST['smtp_user']; //'gti.chetansonkusare@gmail.com';
            $config['smtp_pass'] = $_POST['smtp_pass']; //'Chetan@9049';
            $config['smtp_port'] = $_POST['smtp_port']; //25;
            $this->email->initialize($config);
            $this->email->set_newline("\r\n");
            $this->email->from($_POST['from'], $_POST['name']);
            $this->email->to($_POST['to']);
            // Subject of email
            $this->email->subject($subject);
            // Message in email
            $this->email->message($message);
    
           // Attachment
           $this->email->attach($_FILES['attachment']['tmp_name'],'',$_FILES['attachment']['name']);
           
            // It returns boolean TRUE or FALSE based on success or failure
            if (!$this->email->send()) {
                echo $this->email->print_debugger();
            } else {
                echo 'Send mail Successfuly';
            }
        } else {
            $this->page_construct('testing/smtp_test', $meta, $this->data);
        }
    }

    public function mailtest() {
        $sender_email = "gti.chetansonkusare@gmail.com";
        $user_password = 'Chetan@9049';
        $username = 'Chetan';
        $receiver_email = 'chetan@simplysafe.in';
        $subject = 'Test';
        $message = 'Send Mail Testing';
        // The mail sending protocol.
        $config['protocol'] = 'smtp';
// SMTP Server Address for Gmail.
        $config['smtp_host'] = 'ssl://smtp.googlemail.com';
// SMTP Port - the port that you is required
        $config['smtp_port'] = 465;
// SMTP Username like. (abc@gmail.com)
        $config['smtp_user'] = $sender_email;
// SMTP Password like (abc***##)
        $config['smtp_pass'] = $user_password;
// Load email library and passing configured values to email library
        $this->load->library('email', $config);
// Sender email address
        $this->email->from($sender_email, $username);
// Receiver email address.for single email
        $this->email->to($receiver_email);
//send multiple email
        $this->email->to('chetansonkusare2012@gmail.com');
// Subject of email
        $this->email->subject($subject);
// Message in email
        $this->email->message($message);
// It returns boolean TRUE or FALSE based on success or failure
        if (!$this->email->send()) {
            echo $this->email->print_debugger();
        } else {
            echo 'Send';
        }
    }

}
