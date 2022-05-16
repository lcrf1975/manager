<?php

require_once 'lib/log.php';

function Lib_SSH_Cmd ($P_End, $P_Port, $P_User, $P_Pwd, $P_Cmd)
{
 Lib_Log_Write ("SSH Connection To Server " . $P_End . ":" . $P_Port . " Started.\n");
 $Conn = ssh2_connect ($P_End, $P_Port);

 $Auth_Methods = ssh2_auth_none ($Conn, $P_User);

 if (in_array ("password", $Auth_Methods))
 {
  if (ssh2_auth_password ($Conn, $P_User, $P_Pwd))
  {
   $Stream = ssh2_exec ($Conn, $P_Cmd);
   $ErrorStream = ssh2_fetch_stream ($Stream, SSH2_STREAM_STDERR);
   stream_set_blocking ($Stream, true);
   stream_set_blocking ($ErrorStream, true);
   $Output = stream_get_contents ($Stream);
   $Error = stream_get_contents ($ErrorStream);
   fclose ($ErrorStream);
   fclose ($Stream);
   $Result = false;
   if ($Error == "")
   {
    $Result = true;
    Lib_Log_Write ("SSH Command OK [" . $P_Cmd . "] >> " . $P_End . ":" . $P_Port . ".\n");
   }
   else
   {
    Lib_Log_Write ("SSH Error: Server " . $P_End . ":" . $P_Port . " - " . $Error . "\n");
   }
  }
  else
  {
   Lib_Log_Write ("SSH Error: Server " . $End_Rede . ":" . $P_Port . " - Authentication Failed.\n");
  }
 }

 return $Result;
}

?>
