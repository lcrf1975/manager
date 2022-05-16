#!/usr/bin/php -q

<?php

error_reporting (E_ALL);
// error_reporting (E_ERROR);

require_once 'config.php';
require_once 'lib/datetime.php';
require_once 'lib/log.php';
require_once 'lib/mail.php';
require_once 'lib/mysql.php';
require_once 'class/alert.res.php';

$Thread_Pid = getmypid ();

$Thread_DBLink = Lib_MySQL_Connect ($Config_Server, $Config_User, $Config_Pwd, $Config_DB);

Lib_Log_Write ("Alert Thread (Pid: " . $Thread_Pid . ") Started.\n");

if ($Thread_DBLink)
{
 ResAlert_Up_TbParam_AlertStatus ($Thread_DBLink, 1); // Running

 Lib_Log_Write ("Searching Alert Message(s)...\n");
 $CMD_Query = "SELECT * FROM srv_vw_alert;";
 $CMD_QueryResult = Lib_MySQL_Query ($Thread_DBLink, $CMD_Query);
 $CMD_ResultCount = Lib_MySQL_RowCount ($CMD_QueryResult);
 Lib_Log_Write ("Found " . $CMD_ResultCount . " Alert(s) Message(s) To Send.\n");

 if ($CMD_ResultCount > 0)
 {
  while ($CMD_QueryRow = mysql_fetch_assoc ($CMD_QueryResult))
  {
   $CMD_RowFiel_Id = $CMD_QueryRow ['ID_SEQ'];
   $CMD_RowFiel_Obj = $CMD_QueryRow ['OBJ'];
   $CMD_RowFiel_Msg = $CMD_QueryRow ['MSG'];
   $CMD_RowFiel_Status = $CMD_QueryRow ['STATUS'];
   $CMD_RowFiel_Dt_Status = $CMD_QueryRow ['DT_STATUS'];
   $CMD_RowFiel_EMail = $CMD_QueryRow ['EMAIL'];
   $CMD_RowFiel_Fone = $CMD_QueryRow ['FONE'];
   $CMD_RowFiel_Alerta = $CMD_QueryRow ['ALERTA'];
   $MAIL = array (1, 3);
   $SMS = array (2, 3);
   if (in_array ($CMD_RowFiel_Alerta, $MAIL))
   {
    Res_Send_Mail ($Thread_DBLink, $CMD_RowFiel_Id, $CMD_RowFiel_EMail, "Object (" . $CMD_RowFiel_Obj . ") Alert - [" . Lib_DateTime_TimeStamp () . "]", $CMD_RowFiel_Msg);    
   }
  }
 }
 ResAlert_Up_TbParam_AlertStatus ($Thread_DBLink, 0); // NOT RUNNING

 Lib_MySQL_Close ($Thread_DBLink); 
}

Lib_Log_Write ("Alert Thread (Pid: " . $Thread_Pid . ") Finished. \n");

?>
