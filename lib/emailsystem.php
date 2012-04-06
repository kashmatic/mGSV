<?php
function emailsystem($email) {
	global $newsession_id,$synfilename,$annfilename,$database_name;
	if($email != "") {
    $urllinks = "http://cas-bioinfo.cas.unt.edu/mgsv/summary.php?session_id=$newsession_id&annid=$addImage";
    $hash = md5($email);
    $useremail = "insert into userinfo (email,hash,synfilename,annfilename,url,session_id) values('$email','$hash','$synfilename','$annfilename','$urllinks','$newsession_id')";
    $emailuser = execute_sql($useremail);
    $to  = $email;
    // subject
    $subject = "mGSV Results";
		// message
		$message = "mGSV User,\n
Please use the URL link below to access a visual display of your recent mGSV submission.\n
Today's Synteny:
http://cas-bioinfo.cas.unt.edu/mgsv/summary.php?session_id=$newsession_id
\n
Note: This link will be invalid after 60 days
\n
Use the link below to view a complete list of synteny projects associated with this email address submitted in the last 60 days.
\n
History:
http://cas-bioinfo.cas.unt.edu/mgsv/history.php?hash=$hash
\n
Thank you for choosing to use mGSV.  We are happy to hear any questions or comments at Qunfeng.Dong@unt.edu
\n
Regards,
mGSV team
\n
Note: Do not reply to this email.
";
		mail($to, $subject, $message);
	}
}

?>
