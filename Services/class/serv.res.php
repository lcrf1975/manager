<?php

require_once 'obj.res.php';

function ResServ_Up_TbObj_ThreadServ ($P_DBLink, $Reg, $Status)
{
 $TempQuery = "UPDATE tb_obj " .
              "SET thread_serv = " . $Status . " " .
              "WHERE id_seq = " . $Reg . ";";
 $TempQueryResult = Lib_MySQL_Query ($P_DBLink, $TempQuery);
}

function ResServ_Up_TbServ_Status ($P_DBLink, $Obj, $Reg, $Status)
{
 $Temp_Query = "SELECT * FROM srv_vw_serv_status WHERE _ID = " . $Reg . ";";
 $Temp_QueryResult = Lib_MySQL_Query ($P_DBLink, $Temp_Query);
 $Temp_QueryRow = mysql_fetch_assoc ($Temp_QueryResult);
 $Temp_RowField_Name = $Temp_QueryRow ['_NOME'];
 $Temp_RowField_End = $Temp_QueryRow ['_END'];
 $Temp_RowField_Tipo = $Temp_QueryRow ['_TIPO'];
 $Temp_RowField_Serv = $Temp_QueryRow ['_SERV'];
 $Temp_RowField_Port = $Temp_QueryRow ['_PORTA'];
 $Temp_RowField_Err = $Temp_QueryRow ['_ERROS'];
 $Temp_RowField_Max = $Temp_QueryRow ['_MAX'];
 $Temp_RowField_Status = $Temp_QueryRow ['_STATUS'];

 if ($Status == 0 || $Status == 2) // 0 -> ERROR / 2 -> FAIL
 {
  if ($Temp_RowField_Err > $Temp_RowField_Max)
  {
   $Msg = "Alert Message Text:\n";
   $Msg = $Msg . "--------------------\n";
   $Msg = $Msg . "Object.: " . $Temp_RowField_Name . "\n";
   $Msg = $Msg . "Address: " . $Temp_RowField_End . "\n";
   $Msg = $Msg . "Port...: " . $Temp_RowField_Port . "\n";
   $Msg = $Msg . "Type...: " . $Temp_RowField_Tipo . "\n";
   $Msg = $Msg . "\n";
   $Msg = $Msg . "Service: " . $Temp_RowField_Serv . "\n";
   $Msg = $Msg . "Status.: " . $Temp_RowField_Status . "\n";
   $Msg = $Msg . "Errors.: " . $Temp_RowField_Err . "\n";
   $Msg = $Msg . "Max.Err: " . $Temp_RowField_Max . "\n";

   $Temp_Query = "CALL srv_sp_new_alert (" . $Obj . ", '" . $Msg . "')";
   $Temp_QueryResult = Lib_MySQL_Query ($P_DBLink, $Temp_Query);

   Lib_Log_Write ("New [Service Alert Message] Added To Queue.\n");
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

 $Temp_Query = "UPDATE tb_serv SET error_cont = " . $Temp_RowField_Err . ", status = " . $Status . ", dt_status = '" . Lib_DateTime_YMDHMS () . "' WHERE id_seq = " . $Reg . ";";
 $Temp_QueryResult = Lib_MySQL_Query ($P_DBLink, $Temp_Query);

 ResObj_Up_TbObj_StatusServ ($P_DBLink, $Obj, $Status);
}

?>
