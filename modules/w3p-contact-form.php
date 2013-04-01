<?php
function display_pp_contact_form() {
	$w3p_email = get_option('w3p_email');
	$display = '';

	if(isset($_POST['w3p_pp'])) {
		$w3p_pp_name = $_POST['w3p_pp_name'];
		$w3p_pp_email = $_POST['w3p_pp_email'];
		$w3p_pp_message = $_POST['w3p_pp_message'];
		$w3p_pp_subject = 'Web Contact Form | New Message';

		$headers = '';
		$headers .= 'From: ' . $w3p_pp_name . ' <' . $w3p_pp_email . '>' . "\r\n";
		$headers .= "Reply-To: " . $w3p_pp_email . "\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=\"" . get_settings('blog_charset') . "\"\r\n";

		$mail = wp_mail($w3p_email, $w3p_pp_subject, $w3p_pp_message, $headers);

		if($mail)
			$display = '<p><strong>' . __('Thank you. Your message has been sent.', 'w3p') . '</strong></p>';
		else
			$display = '<p><strong>' . __('There was an error with your message. Please try again.', 'w3p') . '</strong></p>';
	}
	else {
		$display = '
			<form method="post">
				<p>' . __('Name', 'w3p') . '<br><input type="text" name="w3p_pp_name"></p>
				<p>' . __('Email', 'w3p') . '<br><input type="email" name="w3p_pp_email"></p>
				<p>' . __('Message', 'w3p') . '<br><textarea name="w3p_pp_message" rows="8" style="width: 90%;"></textarea></p>
				<p><input type="submit" name="w3p_pp" value="' . __('Send Message', 'w3p') . '"></p>
			</form>
		';
	}
	return $display;
}

add_shortcode('pp_contact_form', 'display_pp_contact_form');
?>
