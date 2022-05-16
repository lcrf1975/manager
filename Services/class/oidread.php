#!/usr/bin/php -q

<?php

error_reporting (E_ALL);
// error_reporting (E_ERROR);

require_once 'config.php';
require_once 'lib/datetime.php';
require_once 'lib/log.php';
require_once 'lib/mysql.php';
require_once 'lib/snmp.php';
require_once 'class/oid.res.php';
require_once 'class/obj.res.php';

$Thread_Pid = getmypid ();

$Thread_DBLink = Lib_MySQL_Connect ($Config_Server, $Config_User, $Config_Pwd, $Config_DB);

$Obj_Id = $argv[1];
$End_Rede = $argv[2];
$Pub = $argv[3];
$Priv = $argv[4];

Lib_Log_Write ("OID Read Thread (Pid: " . $Thread_Pid . ") Started.\n");

if ($Thread_DBLink)
{
 ResOid_Up_TbObj_OidReadStatus ($Thread_DBLink, $Obj_Id, 1); // RUNNING

 Lib_Log_Write ("Searching OID's...\n");
 $OID_Query = "SELECT OID.id_seq, OID.oid, OID.nome, OID.tolerancia, OID.tentativas, OID.tipo, OID.tempo, OID.dt_valor, OBJ.snmp_ver, OBJ.status_oid " .
              "FROM tb_oid OID " .
              "INNER JOIN tb_obj OBJ ON OBJ.id_seq = OID.id_obj " .
              "WHERE OID.id_obj = " . $Obj_Id . " " . 
              "ORDER BY OID.prioridade DESC;";
 $OID_QueryResult = Lib_MySQL_Query ($Thread_DBLink, $OID_Query);
 $OID_ResultCount = Lib_MySQL_RowCount ($OID_QueryResult);
 Lib_Log_Write ("Found " . $OID_ResultCount . " OID(s). To Read.\n");

 if ($OID_ResultCount > 0)
 {
  while ($OID_QueryRow = mysql_fetch_assoc ($OID_QueryResult))
  {
   $OID_RowField_Id = $OID_QueryRow ['id_seq'];
   $OID_RowField_OID = $OID_QueryRow ['oid'];
   $OID_RowField_Nome = $OID_QueryRow ['nome'];
   $OID_RowField_Tipo = $OID_QueryRow ['tipo'];
   $OID_RowField_Tempo = $OID_QueryRow ['tempo'];
   $OID_RowField_Dt_Valor = $OID_QueryRow ['dt_valor'];
   $OID_RowField_Tolerancia = $OID_QueryRow ['tolerancia'];
   $OID_RowField_Tentativas = $OID_QueryRow ['tentativas'];
   $OID_RowField_Status = $OID_QueryRow ['status_oid'];
   $OID_RowField_SNMP_Ver = $OID_QueryRow ['snmp_ver'];

   $TimeDiff = Lib_SecDiff ($OID_RowField_Dt_Valor);
   if (($TimeDiff >= $OID_RowField_Tempo) || ($OID_RowField_Status == 0)) // 0: ERROR
   {
    $OID_Valor = Lib_SNMP_Read ($End_Rede, $OID_RowField_SNMP_Ver, $Pub, $OID_RowField_OID, $OID_RowField_Tolerancia, $OID_RowField_Tentativas);
    if ($OID_Valor == NULL) // NO ANSWER
    {
     $OID_Valor =  0; // ERROR
    }
    else
    {
     if (in_array ($OID_Valor, $Config_OID_Bad_Answers) && ($OID_RowField_Tipo == 0)) // BAD ANSWER
     {
      $OID_Valor = 0;
     }
     ResOid_Up_TbOid_Valor ($Thread_DBLink, $OID_RowField_Id, $OID_Valor);
     $OID_Valor = 1; // OK
    }
    ResObj_Up_TbObj_StatusOid ($Thread_DBLink, $Obj_Id, $OID_Valor);
   }
   else
   {
    Lib_Log_Write ("SNMP OID [" . $OID_RowField_Nome . "] Check Skipped. Tolerance Time Left: " . ($OID_RowField_Tempo - $TimeDiff) . " Sec(s).\n");
   }
  }
 }
 ResOid_Up_TbObj_OidReadStatus ($Thread_DBLink, $Obj_Id, 0); // NOT RUNNING

 Lib_MySQL_Close ($Thread_DBLink); 
}

Lib_Log_Write ("OID Read Thread (Pid: " . $Thread_Pid . ") Finished.\n");

?>
