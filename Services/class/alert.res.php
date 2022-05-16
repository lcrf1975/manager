<?php

function ResAlert_Up_TbParam_AlertStatus ($P_DBLink, $Status)
{
 $TempQuery = "UPDATE tb_param " .
              "SET valor = '" . $Status . "' " .
              "WHERE id = 5;";
 $TempQueryResult = Lib_MySQL_Query ($P_DBLink, $TempQuery);
 $TempQuery = "UPDATE tb_param " .
              "SET valor = '" . Lib_DateTime_YMDHMS () . "' " .
              "WHERE id = 6;";
 $TempQueryResult = Lib_MySQL_Query ($P_DBLink, $TempQuery);
}

function Res_Send_Mail ($P_DBLink, $Id, $To, $Subject, $Msg)
{
  $Query = "UPDATE tb_msg " .
           "SET status = 1 " .
           "WHERE id_seq = " . $Id . ";";
  $QueryResult = Lib_MySQL_Query ($P_DBLink, $Query);

 if (Lib_Mail_Send ($To, $Subject, $Msg)) 
 {
  $Query = "UPDATE tb_msg " .
           "SET status = 2 " .
           "WHERE id_seq = " . $Id . ";";
  $QueryResult = Lib_MySQL_Query ($P_DBLink, $Query);
  Lib_Log_Write ("New Alert Mail Sent To: " . $To . " \n");
 }
 else
 {
  $Query = "UPDATE tb_msg " .
           "SET status = 99 " .
           "WHERE id_seq = " . $Id . ";";
  $QueryResult = Lib_MySQL_Query ($P_DBLink, $Query);
  Lib_Log_Write ("Error Sending Alert Mail To: " . $To . " \n");
 }
}

?>
