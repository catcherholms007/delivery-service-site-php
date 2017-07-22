<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package        CodeIgniter
 * @author        ExpressionEngine Dev Team
 * @copyright    Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license        http://codeigniter.com/user_guide/license.html
 * @link        http://codeigniter.com
 * @since        Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------
/*
		 * SimpleModal Contact Form
		 * http://www.ericmmartin.com/projects/simplemodal/
		 * http://code.google.com/p/simplemodal/
		 *
		 * Copyright (c) 2009 Eric Martin - http://ericmmartin.com
		 *
		 * Licensed under the MIT license:
		 *   http://www.opensource.org/licenses/mit-license.php
		 *
		 * Revision: $Id: contact-dist.php 254 2010-07-23 05:14:44Z emartin24 $
		 *
		 */

Class CI_Contact
{
    // User settings
    var $to = 'someone@example.com';
    var $subject = 'Тест Email';
    var $extra = array(
        "form_subject" => true,
        "form_cc" => true,
        "ip" => true,
        "user_agent" => true
    );

    // Validate and send email
    function smcf_send($name, $phone, $email, $usluga, $message)
    {
        // Filter and validate fields
        $name = $this->smcf_filter($name);
        $name = $this->filterPack($name);
        $phone = $this->smcf_filter($phone);
        $phone = $this->filterPack($phone);
        $usluga = $this->smcf_filter($usluga);
        $usluga = $this->filterPack($usluga);
        $email = $this->smcf_filter($email);
        $email = $this->filterPack($email);
        if (!$this->smcf_validate_email($email)) {
            $this->subject .= " - invalid email";
            $message .= "\n\nBad email: $email";
            $email = $this->to;
            //$cc = 0; // do not CC "sender"
        }
        // Add additional info to the message
        if ($this->extra["ip"]) {
            $message .= "\n\nIP-адресс: " . $_SERVER["REMOTE_ADDR"];
        }
        if ($this->extra["user_agent"]) {
            $message .= "\n\nБраузер: " . $_SERVER["HTTP_USER_AGENT"];
        }
        // Set and wordwrap message body
        $body = "Фамилия, имя: $name \n\n";
        $body .= "Номер телефона: $phone \n\n";
        $body .= "Контактный e-mail: $email \n\n";
        $body .= "Вид услуги: $usluga \n\n";
        $body .= "Сообщение: $message";
        // Send email
        $arr['email'] = $email;
        $arr['name'] = $name;
        $arr['body'] = $body;
        /*$this->load->library('email');
        $this->email->from($email, $name);
        $this->email->to('mail@dostavka.com');
        $this->email->subject('Заказ на доставку');
        $this->email->message($body);
        $this->email->send();*/
        return $arr;
    }

    function filterPack($str)
    {
        $str = str_replace("'", "(0q)", $str);
        $str = str_replace('"', "(0dbq)", $str);
        $str = str_replace("#", "(0dies)", $str);
        $str = str_replace("*", "(0star)", $str);
        $str = str_replace("<", "(0lt)", $str);
        $str = str_replace(">", "(0gt)", $str);
        $str = str_replace("$", "(0dol)", $str);
        $str = str_replace("&", "(0amp)", $str);
        return $str;
    }

    // Remove any un-safe values to prevent email injection
    function smcf_filter($value)
    {
        $pattern = array("/\n/", "/\r/", "/content-type:/i", "/to:/i", "/from:/i", "/cc:/i");
        $value = preg_replace($pattern, "", $value);
        return $value;
    }

    // Validate email address format in case client-side validation "fails"
    function smcf_validate_email($email)
    {
        $at = strrpos($email, "@");

        // Make sure the at (@) sybmol exists and
        // it is not the first or last character
        if ($at && ($at < 1 || ($at + 1) == strlen($email)))
            return false;

        // Make sure there aren't multiple periods together
        if (preg_match("/(\.{2,})/", $email))
            return false;

        // Break up the local and domain portions
        $local = substr($email, 0, $at);
        $domain = substr($email, $at + 1);


        // Check lengths
        $locLen = strlen($local);
        $domLen = strlen($domain);
        if ($locLen < 1 || $locLen > 64 || $domLen < 4 || $domLen > 255)
            return false;

        // Make sure local and domain don't start with or end with a period
        if (preg_match("/(^\.|\.$)/", $local) || preg_match("/(^\.|\.$)/", $domain))
            return false;

        // Check for quoted-string addresses
        // Since almost anything is allowed in a quoted-string address,
        // we're just going to let them go through
        if (!preg_match('/^"(.+)"$/', $local)) {
            // It's a dot-string address...check for valid characters
            if (!preg_match('/^[-a-zA-Z0-9!#$%*\/?|^{}`~&\'+=_\.]*$/', $local))
                return false;
        }

        // Make sure domain contains only valid characters and at least one period
        if (!preg_match("/^[-a-zA-Z0-9\.]*$/", $domain) || !strpos($domain, "."))
            return false;

        return true;
    }
}