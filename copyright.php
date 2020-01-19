<?

include('lib/init.php');

if (isset($_POST['fname'])) {

	$headers = 'MIME-Version: 1.0' . "\n" . 'From: copyright@snaffed.com' . "\n" .
    'Reply-To: ' . $_POST[email] . "\n" .
    'X-Mailer: PHP/' . phpversion();


	$subj = 'Copyright Infringement Notice - snaffed.com';
	$message = '<br />First name: ' . $_POST['fname'];
	$message .= '\nLast name: ' . $_POST['lname'];
	$message .= '\nAddress: ' . $_POST['address'];
	$message .= '<br />Phone: ' . $_POST['phone'];
	$message .= '<br />Email: ' . $_POST['email'];
	$message .= '<br /><br />';
	$message .= 'Work infringed: ' . $_POST['infringed'];
	$message .= '<br /><br />Infringer: ' . $_POST['infringer'];
	$message .= '<br /><br />Description: ' . $_POST['description'];
	mail('derek@disflux.com', $subj, $message, $headers);
	$content .= '<h1>Thank you</h1>';
	$content .= '<p>You will be contacted shortly about the status of your case.</p>';
	include('layouts/default.php');
	die();
}

$content .= '<h1>Notification of Alleged Copyright Infringement</h1>
<p>This form for is for reporting instances of copyright infringement only. If you believe that your own copyrighted work is accessible on snaffed.com in violation of your copyright, please fill in the following information, carefully read the statement below, and submit the form. To submit a valid Notification of Alleged Copyright Infringement, the information you provide to us must substantially comply with the requirements listed below.</p>

<p>Please note that you may be liable for damages, including court costs and attorneys fees, if you materially misrepresent that content on our website and/or service is copyright infringing. Upon receiving the proper Notification of Alleged Copyright Infringement, we will remove or disable access to the alleged infringing material and promptly notify the alleged infringer of your claim as well as the DMCA statutory procedure by which the alleged infringer may respond to your claim.</p>
<form action="' . $PHP_SELF . '" method="post">
<table border="0" cellpadding="10" cellspacing="5">
<tr><td>First Name:</td><td><input type="text" name="fname"</td></tr>
<tr><td>Last Name:</td><td><input type="text" name="lname"</td></tr>
<tr><td>Mailing Address:</td><td><textarea rows="5" cols="20" name="address"></textarea></td></tr>
<tr><td>Telephone:</td><td><input type="text" name="phone"></td></tr>
<tr><td>Email:</td><td><input type="text" name="email"></td></tr>
<tr><td>Identify the work that you claim has been infringed:</td><td><textarea rows="5" cols="20" name="infringed"></textarea></td></tr>
<tr><td>Identify the URL of the infringing material:</td><td><textarea rows="5" cols="20" name="infringer"></textarea></td></tr>
<tr><td>Describe how your copyright has been infringed upon:</td><td><textarea rows="5" cols="20" name="description"></textarea></td></tr>
<tr><td><input type="submit" value="Submit" /></td></tr></table></form>';

include('layouts/default.php');
