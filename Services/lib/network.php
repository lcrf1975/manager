<?php

require_once 'lib/log.php';

function Lib_Check_Port ($P_IPAddress, $P_Port, $P_Timeout, $P_Retry)
{

 $Try = TRUE;
 $Cont = 0;
 $Flag = FALSE;

 while (($Try) And ($Cont < $P_Retry)) 
 {
  $Cont++;
  $Sock = fsockopen ($P_IPAddress, $P_Port, $ErrNum, $ErrStr, $P_Timeout);
  if (!$Sock)
  {
   Lib_Log_Write ($Cont . ") Host [" . $P_IPAddress . "] Port: " . $P_Port . " - " . $ErrStr . ". \n");
  }
  else
  {
   Lib_Log_Write ($Cont . ") Host [" . $P_IPAddress . "] Port: " . $P_Port . " - READY.\n");
   fclose ($Sock);
   $Try = FALSE;
   $Flag = TRUE;
  }
 }
 return $Flag;
}

Function Lib_Ping ($Host, $Timeout, $Retry)
{
 $Package = "\x08\x00\x7d\x4b\x00\x00\x00\x00PingHost";
 $Socket  = socket_create (AF_INET, SOCK_RAW, 1);
 socket_set_option ($Socket, SOL_SOCKET, SO_RCVTIMEO, array ('sec' => $Timeout, 'usec' => 0));

 $Try = TRUE;
 $Result = FALSE;
 $Cont = 0;

 while (($Try) And ($Cont < $Retry))
 {
  $Cont++;

  socket_connect ($Socket, $Host, null);
  $TS = microtime (TRUE);
  socket_send ($Socket, $Package, strlen ($Package), 0);
  if (socket_read ($Socket, 255))
  {
   $Try = FALSE;
   $Result = TRUE;
  }
  else
  {
   Lib_Log_Write ($Cont . ") Host [" . $Host . "] Ping - FAIL.\n");
  }

  socket_close ($Socket);
 }

 if ($Result)
 {
  Lib_Log_Write ($Cont . ") Host [" . $Host . "] Ping: " .  round ((microtime (TRUE) - $TS) * 1000) . "ms - OK.\n");
 }

 return $Result;
}

?>
