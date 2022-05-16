#!/usr/bin/php -q

<?php

error_reporting (E_ALL);
// error_reporting (E_ERROR);

require_once 'config.php';
require_once 'main.res.php';
require_once 'lib/mysql.php';
require_once 'lib/log.php';

$Service_Pid = getmypid ();

Lib_Log_Write ("Manager Service " . $Config_Version . " (Pid: " . $Service_Pid . ") Started.\n");

$RunService = ResMain_Init_TbObj_Status ($Config_Server, $Config_User, $Config_Pwd, $Config_DB);

while ($RunService && (time () < strtotime ("23:59:59")))
{
 $Service_DBLink = Lib_MySQL_Connect ($Config_Server, $Config_User, $Config_Pwd, $Config_DB);
 if ($Service_DBLink)
 {
  Lib_Log_Write ("Searching Objects...\n");
  // ativo = 1 = TRUE
  $OBJ_Query = "SELECT id_seq, nome, end_rede, publica AS chpub, privada AS chpriv, thread_oid, thread_serv " .
               "FROM tb_obj " .
               "WHERE ativo = 1;";
  $OBJ_QueryResult = Lib_MySQL_Query ($Service_DBLink, $OBJ_Query);
  $OBJ_ResultCount = Lib_MySQL_RowCount ($OBJ_QueryResult);
  Lib_Log_Write ("Found " . $OBJ_ResultCount . " Object(s) To Process.\n"); 
  if ($OBJ_ResultCount > 0)
  {
   $SQL_WA = "SELECT T.valor AS valor FROM tb_param T WHERE T.id IN (0, 5);";

   $SQL_QueryResult_WA = Lib_MySQL_Query ($Service_DBLink, $SQL_WA);
   $SQL_QueryRow_WA = mysql_fetch_assoc ($SQL_QueryResult_WA);
   if ($SQL_QueryRow_WA ['valor'] == 0)
   {
    exec ("./thread.sh class/cmd.php " . Lib_Log_FilePath ());
   }

   $SQL_QueryResult_WA = Lib_MySQL_Query ($Service_DBLink, $SQL_WA);
   $SQL_QueryRow_WA = mysql_fetch_assoc ($SQL_QueryResult_WA);
   if ($SQL_QueryRow_WA ['valor'] == 0)
   {
    exec ("./thread.sh class/alert.php " . Lib_Log_FilePath ());
   }

   while ($OBJ_QueryRow = mysql_fetch_assoc ($OBJ_QueryResult))
   {
    $OBJ_RowField_Id = $OBJ_QueryRow ['id_seq'];
    $OBJ_RowField_Nome = $OBJ_QueryRow ['nome'];
    $OBJ_RowField_End_Rede = $OBJ_QueryRow ['end_rede'];
    $OBJ_RowField_Pub = $OBJ_QueryRow ['chpub'];
    $OBJ_RowField_Priv = $OBJ_QueryRow ['chpriv'];
    $OBJ_RowField_Flag_OID = $OBJ_QueryRow ['thread_oid'];
    $OBJ_RowField_Flag_Serv = $OBJ_QueryRow ['thread_serv'];

    Lib_Log_Write ("Name...: " . $OBJ_RowField_Nome . "\n");
    Lib_Log_Write ("Address: " . $OBJ_RowField_End_Rede . "\n");

    if ($OBJ_RowField_Flag_OID == 0)
    {
     exec ("./thread4p.sh class/oidread.php " . escapeshellarg ($OBJ_RowField_Id) . " " . escapeshellarg ($OBJ_RowField_End_Rede) . " " . escapeshellarg ($OBJ_RowField_Pub) . " " . escapeshellarg ($OBJ_RowField_Priv) . " " . Lib_Log_FilePath ());
    }
    else
    {
     Lib_Log_Write ("Object " . $OBJ_RowField_Nome . " OID Status - BUSY.\n");
    }

    if ($OBJ_RowField_Flag_OID == 0)
    {
     exec ("./thread2p.sh class/serv.php " . escapeshellarg ($OBJ_RowField_Id) . " " . escapeshellarg ($OBJ_RowField_End_Rede) . " " . Lib_Log_FilePath ());
    }
    else
    {
     Lib_Log_Write ("Object " . $OBJ_RowField_Nome . " Service Status - BUSY.\n");
    }
   }
  }
  $Interval = 0;
  while ($Interval < $Config_CheckInterval)
  {
   // id = 2 = Check DB Flag
   $Interval_Query = "SELECT valor " .
                     "FROM tb_param " .
                     "WHERE id = 2;";
   $Interval_QueryResult = Lib_MySQL_Query ($Service_DBLink, $Interval_Query);
   $Interval_QueryRow = mysql_fetch_assoc ($Interval_QueryResult);
   $Interval_RowField_Valor = $Interval_QueryRow ['valor'];
   if ($Interval_RowField_Valor > 0)
   {
    Lib_Log_Write ("Found Manually Update Request.\n");
    $Interval = $Config_CheckInterval;
    ResMain_Up_TbParam ($Service_DBLink, 2 , 0);
   }
   else
   {
    $Interval++;
    sleep (1);
   }
  }
  ResMain_DB_Maintenance ($Service_DBLink, $Config_DB_Hist);
  Lib_MySQL_Close ($Service_DBLink);
 }
 else
 {
  sleep ($Config_CheckInterval);
 }
}

ResMain_Up_TbParam ($Service_DBLink, 7 , 1);
Lib_Log_Write ("Manager Service " . $Config_Version . " (Pid: " . $Service_Pid . ") Finished.\n");

?>
