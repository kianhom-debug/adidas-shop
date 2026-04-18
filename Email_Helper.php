<?php
require_once 'library/PHPMailer.php';
require_once 'library/SMTP.php';

function root($path = '') {
    return __DIR__ . '/' . ltrim($path, '/');
}

function base($path = '') {
    return 'http://localhost:3000/' . ltrim($path, '/');
}

function get_mail() {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->Host       = 'smtp.gmail.com';
    $mail->Port       = 587;
    $mail->Username   = 't4621445@gmail.com';      
    $mail->Password   = 'mbra hbwc dame ilaz';   
    $mail->setFrom('t4621445@gmail.com', 'Adidas Shop');
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    return $mail;
}
?>