#!/usr/bin/php -q

<?php

error_reporting (E_ALL);
// error_reporting (E_ERROR);

require_once 'config.php';
require_once 'lib/datetime.php';
require_once 'lib/log.php';
require_once 'lib/mysql.php';
require_once 'lib/network.php';
require_once 'class/serv.res.php';

$Thread_Pid = getmypid ();

$Thread_DBLink = Lib_MySQL_Connect ($Config_Server, $Config_User, $Config_Pwd, $Config_DB);

$Obj_Id = $argv[1];
$End_Rede = $argv[2];

Lib_Log_Write ("Services Thread (Pid: " . $Thread_Pid . ") Started.\n");

if ($Thread_DBLink)
{
 ResServ_Up_TbObj_ThreadServ ($Thread_DBLink, $Obj_Id, 1); // RUNNING

 Lib_Log_Write ("Searching Services...\n");
 $SERV_Query = "SELECT SERV.id_seq, SERV.nome, SERV.porta, SERV.tolerancia, " .
               "SERV.tentativas, SERV.status, SERV.dt_status, SERV.tempo " .
               "FROM tb_serv AS SERV " .
               "WHERE SERV.id_obj = " . $Obj_Id . " " . 
               "ORDER BY SERV.prioridade DESC;";
 $SERV_QueryResult = Lib_MySQL_Query ($Thread_DBLink, $SERV_Query);
 $SERV_ResultCount = Lib_MySQL_RowCount ($SERV_QueryResult);
 Lib_Log_Write ("Found " . $SERV_ResultCount . " Service(s). To Check.\n");

 if ($SERV_ResultCount > 0)
 {
  while ($SERV_QueryRow = mysql_fetch_assoc ($SERV_QueryResult))
  {
   $SERV_RowField_Id = $SERV_QueryRow ['id_seq'];
   $SERV_RowField_Nome = $SERV_QueryRow ['nome'];
   $SERV_RowField_Porta = $SERV_QueryRow ['porta'];
   $SERV_RowField_Tolerancia = $SERV_QueryRow ['tolerancia'];
   $SERV_RowField_Tentativas = $SERV_QueryRow ['tentativas'];
   $SERV_RowField_Status = $SERV_QueryRow ['status'];
   $SERV_RowField_Dt_Status = $SERV_QueryRow ['dt_status'];
   $SERV_RowField_Tempo = $SERV_QueryRow ['tempo'];

   $TimeDiff =  Lib_SecDiff ($SERV_RowField_Dt_Status);
   if (($TimeDiff >= $SERV_RowField_Tempo) || ($SERV_RowField_Status != 1)) // 1: OK
   {
    if ($SERV_RowField_Porta > 0)
    {
     if (Lib_Check_Port ($End_Rede, $SERV_RowField_Porta, $SERV_RowField_Tolerancia, $SERV_RowField_Tentativas))
     {
      $Serv_Status = 1; // OK
     }
     else
     {
      $Serv_Status = 0; // ERROR
     }
    }
    else
    {
     if (Lib_Ping ($End_Rede, $SERV_RowField_Tolerancia, $SERV_RowField_Tentativas))
     {
      $Serv_Status = 1; // OK
     }
     else
     {
      $Serv_Status = 0; // ERROR
     }
    }
    ResServ_Up_TbServ_Status ($Thread_DBLink, $Obj_Id, $SERV_RowField_Id, $Serv_Status);
   }
   else
   {
    Lib_Log_Write ("Service [" . $SERV_RowField_Nome . "] Check Skipped. Tolerance Time Left: " . ($SERV_RowField_Tempo - $TimeDiff) . " Sec(s).\n");
   }
  }
 }
 ResServ_Up_TbObj_ThreadServ ($Thread_DBLink, $Obj_Id, 0); // NOT RUNNING

 Lib_MySQL_Close ($Thread_DBLink); 
}

Lib_Log_Write ("Services Thread (Pid: " . $Thread_Pid . ") Finished.\n");

?>
