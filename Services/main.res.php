<?php

function ResMain_Init_TbObj_Status ($P_Config_Server, $P_Config_User, $P_Config_Pwd, $P_Config_DB)
{
 // Reset All Object Status
 $Temp_DBLink = Lib_MySQL_Connect ($P_Config_Server, $P_Config_User, $P_Config_Pwd, $P_Config_DB);

 if ($Temp_DBLink)
 {
  $SQL = "SELECT T.valor AS valor FROM tb_param T WHERE T.id = 7;";

  $SQL_QueryResult = Lib_MySQL_Query ($Temp_DBLink, $SQL);
  $SQL_QueryRow = mysql_fetch_assoc ($SQL_QueryResult);
  if ($SQL_QueryRow ['valor'] == 0)
  {
   $TempQuery = "UPDATE tb_obj " .
                "SET status_oid = 1, status_serv = 1, thread_oid = 0, thread_serv = 0 " .
                "WHERE ativo = 1;";
   $TempQueryResult = Lib_MySQL_Query ($Temp_DBLink, $TempQuery);

   $TempQuery = "UPDATE tb_param " .
                "SET valor = 0 " .
                "WHERE id IN (0, 5);";
   $TempQueryResult = Lib_MySQL_Query ($Temp_DBLink, $TempQuery);

   Lib_MySQL_Close ($Temp_DBLink);

   Lib_Log_Write ("All Object Status Sucessfull Reseted.\n");
  }
  else
  {
   ResMain_Up_TbParam ($Temp_DBLink, 7, 0);
  }
  return true;
 }
 else
 {
  return false;
 }
}

function ResMain_Up_TbParam ($P_DBLink, $Id, $Val)
{
 $TempQuery = "UPDATE tb_param " .
              "SET valor = '" . $Val . "' " .
              "WHERE id = " . $Id . ";";
 $TempQueryResult = Lib_MySQL_Query ($P_DBLink, $TempQuery);
}

function ResMain_DB_Maintenance ($P_DBLink, $Days)
{
  // DB Maintenance
  $DBM_Query = "SELECT COUNT(1) AS delrows FROM tb_oid_hist WHERE TO_DAYS(dt_valor) < (TO_DAYS(NOW()) - " . $Days . ");";
  $DBM_QueryResult = Lib_MySQL_Query ($P_DBLink, $DBM_Query);
  $DBM_RowField_Valor = $DBM_QueryResult ['delrows'];
  if ($DBM_RowField_Valor > 0)
  {
   $DBM_Query = "DELETE FROM tb_oid_hist WHERE TO_DAYS(dt_valor) < (TO_DAYS(NOW()) - " . $Days . ");";
   $DBM_QueryResult = Lib_MySQL_Query ($P_DBLink, $DBM_Query);
   Lib_Log_Write ("DB Maintenance Removed " . $DBM_RowField_Valor . " Records From Oid History.\n");
  }
  $DBM_Query = "SELECT COUNT(1) AS delrows FROM tb_serv_hist WHERE TO_DAYS(dt_status) < (TO_DAYS(NOW()) - " . $Days . ");";
  $DBM_QueryResult = Lib_MySQL_Query ($P_DBLink, $DBM_Query);
  $DBM_RowField_Valor = $DBM_QueryResult ['delrows'];
  if ($DBM_RowField_Valor > 0)
  {
  $DBM_Query = "DELETE FROM tb_serv_hist WHERE TO_DAYS(dt_status) < (TO_DAYS(NOW()) - " . $Days . ");";
  $DBM_QueryResult = Lib_MySQL_Query ($P_DBLink, $DBM_Query);
   Lib_Log_Write ("DB Maintenance Removed " . $DBM_RowField_Valor . " Records From Service History.\n");
  }
}

?>
