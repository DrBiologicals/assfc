<?php

require_once 'session.php';
require_once 'localsettings.php';

    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $headers = 'From: ' . $name . "\r\n" . 'Reply-To: ' . $email;
    
    if(isset($_POST['send'])){
        mail('john.doe@hera-verified.co.nz',$subject,$message,$headers);
    }

include $templateDir . 'contact.tpl.php';
?>
