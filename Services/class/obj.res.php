<?php

function ResObj_Up_TbObj_StatusOid ($P_DBLink, $Reg, $Status)
{
 $Temp_Query1 = "SELECT status_oid FROM tb_obj WHERE id_seq = " . $Reg . ";";
 $Temp_QueryResult1 =  Lib_MySQL_Query ($P_DBLink, $Temp_Query1);
 $Temp_QueryRow1 = mysql_fetch_assoc ($Temp_QueryResult1);
 $Temp_RowField_Status = $Temp_QueryRow1 ['status_oid'];

 if ($Temp_RowField_Status == 1 && $Status == 0)
 {
  $Status = 2;
 }

 $Temp_Query2 = "UPDATE tb_obj " .
                "SET status_oid = " . $Status . ", dt_status_oid = '" . Lib_DateTime_YMDHMS () . "' " .
                "WHERE id_seq = " . $Reg . ";";
 $Temp_QueryResult2 = Lib_MySQL_Query ($P_DBLink, $Temp_Query2);

 $Temp_Query = "SELECT * FROM srv_vw_oid_status WHERE _ID = " . $Reg . ";";
 $Temp_QueryResult = Lib_MySQL_Query ($P_DBLink, $Temp_Query);
 $Temp_QueryRow = mysql_fetch_assoc ($Temp_QueryResult);
 $Temp_RowField_Name = $Temp_QueryRow ['_NOME'];
 $Temp_RowField_End = $Temp_QueryRow ['_END'];
 $Temp_RowField_Tipo = $Temp_QueryRow ['_TIPO'];
 $Temp_RowField_Oid = $Temp_QueryRow ['_OID'];
 $Temp_RowField_SNMP = $Temp_QueryRow ['_SNMP'];
 $Temp_RowField_Err = $Temp_QueryRow ['_ERROS'];
 $Temp_RowField_Max = $Temp_QueryRow ['_MAX'];
 $Temp_RowField_Status = $Temp_QueryRow ['_STATUS'];

 if ($Status == 0 || $Status == 2) // 0 -> ERROR / 2 -> FAIL
 {
  if ($Temp_RowField_Err > $Temp_RowField_Max)
  {
   $Msg = "Alert Message Text:\n";
   $Msg = $Msg . "\n";
   $Msg = $Msg . "Object.: " . $Temp_RowField_Name . "\n";
   $Msg = $Msg . "Address: " . $Temp_RowField_End . "\n";
   $Msg = $Msg . "Key....: " . $Temp_RowField_SNMP . "\n";
   $Msg = $Msg . "Type...: " . $Temp_RowField_Tipo . "\n";
   $Msg = $Msg . "\n";
   $Msg = $Msg . "Oid....: " . $Temp_RowField_Oid . "\n";
   $Msg = $Msg . "Status.: " . $Temp_RowField_Status . "\n";
   $Msg = $Msg . "Errors.: " . $Temp_RowField_Err . "\n";
   $Msg = $Msg . "Max.Err: " . $Temp_RowField_Max . "\n";

   $Temp_Query = "CALL srv_sp_new_alert (" . $Reg . ", '" . $Msg . "')";
   $Temp_QueryResult = Lib_MySQL_Query ($P_DBLink, $Temp_Query);

   Lib_Log_Write ("New [OID Alert Message] Added To Queue.\n");
   Lib_Log_Write ($Msg . "\n");

   $Temp_RowField_Err = 0;
  }
  else
  {
   if ($Temp_RowField_Err < 255) // Error Limit
   {
    $Temp_RowField_Err += 1;
   }
  }
 }
 else
 {
  $Temp_RowField_Err = 0;
 }
 
 $Temp_Query = "UPDATE tb_obj SET oid_error_cont = " . $Temp_RowField_Err . " WHERE id_seq = " . $Reg . ";";
 $Temp_QueryResult = Lib_MySQL_Query ($P_DBLink, $Temp_Query);
}

function ResObj_Up_TbObj_StatusServ ($P_DBLink, $Reg, $Status)
{
 $Temp_Query1 = "SELECT status_serv FROM tb_obj WHERE id_seq = " . $Reg . ";";
 $Temp_QueryResult1 =  Lib_MySQL_Query ($P_DBLink, $Temp_Query1);
 $Temp_QueryRow1 = mysql_fetch_assoc ($Temp_QueryResult1);
 $Temp_RowField_Status = $Temp_QueryRow1 ['status_serv'];

 if ($Temp_RowField_Status == 1 && $Status == 0)
 {
  $Status = 2;
 }

 $Temp_Query2 = "UPDATE tb_obj " .
                "SET status_serv = " . $Status . ", dt_status_serv = '" . Lib_DateTime_YMDHMS () . "' " .
                "WHERE id_seq = " . $Reg . ";";
 $Temp_QueryResult2 = Lib_MySQL_Query ($P_DBLink, $Temp_Query2);
}

?>
