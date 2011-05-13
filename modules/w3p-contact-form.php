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
		if ($success){
			echo '<p>Thank you. Your message has been sent.</p>';
		} else {
			echo '<p>There was an error with your message. Please try again.<br /><br /><a href="'; bloginfo("url"); echo "/".$contactpage.'">Return to contact page</a>.</p>';
		}
	} else {
		?>
	    <form method="post" action="?first=no">
	    <table>
	    <tr>
	    <td>
	        <label for="Name" id="Name">Name <small><em>(required)</em></small></label>
	        <div><input type="text" name="Name" /></div>
	    </td>
	    </tr>
	    <tr>
	    <td>
	        <label for="Email" id="Email">Email <small><em>(required)</em></small></label>
	        <div><input type="text" name="Email" /></div>
	    </td>
	    </tr>
	    <tr>
	    <td>	
	        <label for="Message" id="Message">Message <small><em>(required)</em></small></label>
	        <div><textarea name="Message" rows="8" cols="60"></textarea></div>
	    </td>
	    </tr>
	    </table>
	    <input type="submit" name="submit" value="Send message" class="submit-button" />
	    </form>
	<?php
    }
}

add_shortcode('pp_contact_form', 'display_pp_contact_form');
?>
