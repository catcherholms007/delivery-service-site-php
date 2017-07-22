<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

Class Contact
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


class Send extends CI_Controller
{

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     *        http://example.com/index.php/welcome
     *    - or -
     *        http://example.com/index.php/welcome/index
     *    - or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */


    public function index()
    {
        $this->load->library('email');
//		$this->load->library('contact');
        $action = isset($_POST["action"]) ? $_POST["action"] : "";
        if (empty($action)) {
            // Send back the contact form HTML
            $output = "<div style='display:none'>
			<div class='contact-top'>
			<h1 class='contact-title' id='s'>ЗАЯВКА НА ЗАКАЗ</h1>
			</div>
			<div class='contact-content'>
				<div class='contact-loading' style='display:none'></div>
				<div class='contact-message' style='display:none'></div>
				<form action='#' style='display:none'>
					<input type='text' id='contact-name' class='contact-input' name='name' tabindex='1001' value='Фамилия Имя'/>
					<input type='text' id='contact-phone' class='contact-input' name='phone' tabindex='1002' value='Телефон'/>
					<input type='text' id='contact-email' class='contact-input' name='email' tabindex='1003' value='E-mail'/>";

            if ($this->contact->extra["form_subject"]) {
                $output .= "
					<input type='text' id='contact-usluga' class='contact-input' name='usluga' tabindex='1004' value='Вид услуги' />";
            }

            $output .= "
					<textarea id='contact-message' class='contact-input' name='message' cols='40' rows='4' tabindex='1005'>Сообщение</textarea>
					<br/>";

            if ($this->contact->extra["form_cc"]) {
                $output .= "
					";
            }

            $output .= "
					<div id='info'>
					Оставьте ваши контактные данные и наши менеджеры свяжутся с вами в ближайшее время для подтверждения заказа.
					</div>
					<button type='submit' class='contact-send contact-button' tabindex='1006'>ЗАКАЗАТЬ</button>
					<br/>
				</form>
			</div>
			<div class='contact-bottom'></div>
		</div>";

            echo $output;
        } else if ($action == "send") {
            // Send the email
            $name = isset($_POST["name"]) ? $_POST["name"] : "";
            $phone = isset($_POST["phone"]) ? $_POST["phone"] : "";
            $email = isset($_POST["email"]) ? $_POST["email"] : "";
            $usluga = isset($_POST["usluga"]) ? $_POST["usluga"] : "";
            $message = isset($_POST["message"]) ? $_POST["message"] : "";
            // make sure the token matches
            $a = $this->contact->smcf_send($name, $phone, $email, $usluga, $message);
            $this->load->library('email');
            $this->email->from($a['email'], $a['name']);
            $this->email->to('mail@dostavka.com');
            $this->email->subject('Заказ на доставку');
            $this->email->message($a['body']);
            $this->email->send();
            echo "Сообщение успешно отправлено.";
        }
        exit;
    }
}