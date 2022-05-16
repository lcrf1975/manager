#!/usr/bin/php -q

<?php

error_reporting (E_ALL);
// error_reporting (E_ERROR);

require_once 'config.php';
require_once 'lib/log.php';
require_once 'lib/mysql.php';
require_once 'lib/snmp.php';
require_once 'lib/ssh.php';
require_once 'class/oid.res.php';
require_once 'class/cmd.res.php';

$Thread_Pid = getmypid ();

$Thread_DBLink = Lib_MySQL_Connect ($Config_Server, $Config_User, $Config_Pwd, $Config_DB);

Lib_Log_Write ("Command Thread (Pid: " . $Thread_Pid . ") Started.\n");

if ($Thread_DBLink)
{
 ResCmd_Up_TbParam_CmdStatus ($Thread_DBLink, 1); // RUNNING

 Lib_Log_Write ("Searching OID Command(s)...\n");
 $CMD_Query = "SELECT * FROM srv_vw_oid_cmd;";
 $CMD_QueryResult = Lib_MySQL_Query ($Thread_DBLink, $CMD_Query);
 $CMD_ResultCount = Lib_MySQL_RowCount ($CMD_QueryResult);
 Lib_Log_Write ("Found " . $CMD_ResultCount . " OID Commands(s) To Send.\n");

 if ($CMD_ResultCount > 0)
 {
  while ($CMD_QueryRow = mysql_fetch_assoc ($CMD_QueryResult))
  {
   $CMD_RowFiel_Id = $CMD_QueryRow ['id_seq'];
   $CMD_RowField_OID = $CMD_QueryRow ['oid'];
   $CMD_RowField_Status_OID = $CMD_QueryRow ['status_oid'];
   $CMD_RowField_Valor = $CMD_QueryRow ['valor'];
   $CMD_RowField_Tipo = $CMD_QueryRow ['tipo'];
   $CMD_RowField_Tolerancia = $CMD_QueryRow ['tolerancia'];
   $CMD_RowField_Tentativas = $CMD_QueryRow ['tentativas'];
   $CMD_RowField_End = $CMD_QueryRow ['end_rede'];
   $CMD_RowField_Chave = $CMD_QueryRow ['chpriv'];
   $CMD_RowField_SNMP_Ver = $CMD_QueryRow ['snmp_ver'];

   if ($CMD_RowField_Status_OID == 0) // NOT RUNNING
   {
    ResCmd_Up_TbOid_CmdStatus ($Thread_DBLink, $CMD_RowFiel_Id, 1); // PROCESSING
    $SNMP_Result = Lib_SNMP_Write ($CMD_RowField_End, $CMD_RowField_SNMP_Ver, $CMD_RowField_Chave, $CMD_RowField_OID, $CMD_RowField_Tipo, $CMD_RowField_Valor, $CMD_RowField_Tolerancia, $CMD_RowField_Tentativas);
    if ($SNMP_Result)
    {
     ResCmd_Up_TbOid_CmdStatus ($Thread_DBLink, $CMD_RowFiel_Id, 2); // OK
    }
    else
    {
     ResCmd_Up_TbOid_CmdStatus ($Thread_DBLink, $CMD_RowFiel_Id, 99); // ERROR
    }
   }
  }
 }
 Lib_Log_Write ("Searching SSH Command(s)...\n");

 $CMD_Query = "SELECT * FROM srv_vw_ssh_cmd;";
 $CMD_QueryResult = Lib_MySQL_Query ($Thread_DBLink, $CMD_Query);
 $CMD_ResultCount = Lib_MySQL_RowCount ($CMD_QueryResult);

 Lib_Log_Write ("Found " . $CMD_ResultCount . " SSH Commands(s) To Send.\n");

 if ($CMD_ResultCount > 0)
 {
  while ($CMD_QueryRow = mysql_fetch_assoc ($CMD_QueryResult))
  {
   $CMD_RowFiel_Id = $CMD_QueryRow ['id_seq'];
   $CMD_RowField_Cmd = $CMD_QueryRow ['cmd'];
   $CMD_RowField_End = $CMD_QueryRow ['end_rede'];
   $CMD_RowField_User = $CMD_QueryRow ['user'];
   $CMD_RowField_Pwd = $CMD_QueryRow ['pwd'];
   $CMD_RowField_Port = $CMD_QueryRow ['port'];

   ResCmd_Up_TbSSH_CmdStatus ($Thread_DBLink, $CMD_RowFiel_Id, 1); // PROCESSING
   $SSH_Result = Lib_SSH_Cmd ($CMD_RowField_End, $CMD_RowField_Port, $CMD_RowField_User, $CMD_RowField_Pwd, $CMD_RowField_Cmd);
   if ($SSH_Result)
   {
    ResCmd_Up_TbSSH_CmdStatus ($Thread_DBLink, $CMD_RowFiel_Id, 2); // OK
   }
   else
   {
    ResCmd_Up_TbSSH_CmdStatus ($Thread_DBLink, $CMD_RowFiel_Id, 99); // ERROR
   }
  }
 }
 ResCmd_Up_TbParam_CmdStatus ($Thread_DBLink, 0); // NOT RUNNING

 Lib_MySQL_Close ($Thread_DBLink); 
}

Lib_Log_Write ("Command Thread (Pid: " . $Thread_Pid . ") Finished.\n");

?>
