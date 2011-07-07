<?php
function display_pp_contact_form() {
	$w3p_email = get_option('w3p_email');

	if($_GET['first'] == "no") {
		$From = trim(stripslashes($_POST['Name'])); 
		$EmailFrom = trim(stripslashes($_POST['Email'])); 
		$EmailTo = $w3p_email; // change this to your EMAIL ADDRESS
		$Subject = 'Web Contact Form | New Message!'; // change this to the name of YOUR WEBSITE
		$Name = trim(stripslashes($_POST['Name'])); 
		$Email = trim(stripslashes($_POST['Email'])); 
		$Message = trim(stripslashes($_POST['Message'])); 

		// validation
		if($Name && $Email && $Message)
			$validationOK = true;
		else
			$validationOK = false;

		if(!$validationOK) {
			echo '<h1>Contact</h1>';
			echo '<p>There was an error with your message. Please try again.</p>';
		}

		// prepare email body text
		$Body = "";
		$Body .= "Name: ";
		$Body .= $Name;
		$Body .= "\n";
		$Body .= "Email: ";
		$Body .= $Email;
		$Body .= "\n";
		$Body .= "\n";
		$Body .= "Message: ";
		$Body .= "\n";
		$Body .= $Message;

		// send email 
		$success = mail($EmailTo, $Subject, $Body, "From: $From <$EmailFrom>");

		// redirect to success page 
		if($success)
			$display = '<p>Thank you. Your message has been sent.</p>';
		else
			$display = '<p>There was an error with your message. Please try again.</p>';
	}
	else {
		$display = '
			<form method="post" action="?first=no">
				<p><label for="Name" id="Name">Name <small><em>(required)</em></small></label><br /><input type="text" name="Name" /></p>
				<p><label for="Email" id="Email">Email <small><em>(required)</em></small></label><br /><input type="text" name="Email" /></p>
				<p><label for="Message" id="Message">Message <small><em>(required)</em></small></label><br /><textarea name="Message" rows="8" cols="60"></textarea></p>
				<p><input type="submit" name="submit" value="Send message" /></p>
			</form>
		';
	}
	return $display;
}

add_shortcode('pp_contact_form', 'display_pp_contact_form');
?>
