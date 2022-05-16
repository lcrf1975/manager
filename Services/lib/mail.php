<?php

require_once 'lib/datetime.php';

function Lib_Mail_Send($P_To, $P_Subject, $P_Message)
{
 $Header = "From: noreply@itbr.com.br" . "\n" .
           "Reply-To: noreply@itbr.com.br" . "\n" .
           "X-Mailer: PHP/" . phpversion();

 return mail ($P_To, $P_Subject, $P_Message, $Header);
}

?>
