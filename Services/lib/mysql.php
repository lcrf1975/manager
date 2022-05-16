<?php

require_once 'lib/log.php';

function Lib_MySQL_Connect ($P_Server, $P_User, $P_Pwd, $P_DB)
{

 $DBLink = mysql_connect ($P_Server, $P_User, $P_Pwd, MYSQL_CLIENT_IGNORE_SPACE);
 if ($DBLink)
 {
  Lib_Log_Write ("Database Connection Started On " . mysql_get_host_info ($DBLink) . " (Id: ". mysql_thread_id ($DBLink) . ").\n");
  $SelectResult = mysql_select_db ($P_DB, $DBLink);
  if (!$SelectResult)
  {
   Lib_Log_Write ("Error: " . mysql_error () . ".\n");
   return false;
  }
  else
  {
   return $DBLink;
  }
 }
 else
 {
  Lib_Log_Write ("Error: " . mysql_error () . ".\n");
 }
}

function Lib_MySQL_Close ($P_DBLink)
{
 $ThreadId = mysql_thread_id ($P_DBLink); 
 $CloseResult =  mysql_close ($P_DBLink);
 if ($CloseResult)
 {
  Lib_Log_Write ("Database Server Connection Finished (Id: " . $ThreadId .  "). \n");
 }
 else
 {
  Lib_Log_Write ("Error: " . mysql_error () . ".\n");
 }
}

function Lib_MySQL_Query ($P_DBLink, $P_Query)
{
 $QueryResult = mysql_query ($P_Query, $P_DBLink);
 if ($QueryResult)
 {
  return $QueryResult;
 }
 else
 {
  Lib_Log_Write ("Error: " . mysql_error () . ".\n");
  Lib_Log_Write ("SQL..: " . $P_Query . ".\n");
  return false;
 }
}

function Lib_MySQL_RowCount ($P_Result)
{
 $RowCount = mysql_num_rows ($P_Result);
 return $RowCount;
}

function Lib_MySQL_Clean ($P_Result)
{
 $Result = mysql_free_result($P_Result);
 return $Result;
}

?>
