<?php

/*
 * send error message
 */
function dw_send_error_mail($subject, $message) {
    $to = $GLOBALS['dw_config']['email']['log'];
    $from = $GLOBALS['dw_config']['email']['error'];
    DatawrapperHooks::execute(DatawrapperHooks::SEND_EMAIL,
        $to, $subject, $message, 'From: '.$from
    );
}

/**
 * Send email an email with attachements 
 * @param $files - array of files to send
 *      ex: array(
 *              "my_image.png" => array(
 *                  "path" => "/home/datawrapper/my_image.png",
 *                  "format" => "image/png"
 *              )
 *          )
 *
 */
function dw_send_mail_attachment($to, $from, $subject, $body, $files) {
    $random_hash = md5(date('r', time()));
       // $random_hash = md5(date('r', time()));
    $random_hash = '-----=' . md5(uniqid(mt_rand())); 

    // headers 
    $headers =  'From: '.$from."\n"; 
    // $headers .= 'Return-Path: <'.$email_reply.'>'."\n"; 
    $headers .= 'MIME-Version: 1.0'."\n"; 
    $headers .= 'Content-Type: multipart/mixed; boundary="'.$random_hash.'"'; 

    // message 
    $message = 'This is a multi-part message in MIME format.'."\n\n"; 

    $message .= '--'.$random_hash."\n"; 
    $message .= 'Content-Type: text/plain; charset="iso-8859-1"'."\n"; 
    $message .= 'Content-Transfer-Encoding: 8bit'."\n\n"; 
    $message .= $body."\n\n"; 

    // attached files
    foreach ($files as $fn => $file) {
        $path   = $file["path"];
        $format = $file["format"];
        $attachment = chunk_split(base64_encode(file_get_contents($path)));
        
        $message .= '--'.$random_hash."\n"; 
        $message .= 'Content-Type: '. $format .'; name="'. $fn .'"'."\n"; 
        $message .= 'Content-Transfer-Encoding: base64'."\n"; 
        $message .= 'Content-Disposition:attachement; filename="'. $fn . '"'."\n\n"; 
        $message .= $attachment."\n"; 
    }

    DatawrapperHooks::execute(DatawrapperHooks::SEND_EMAIL,
        $to, $subject, $message, $headers
    );
}