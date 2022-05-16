<?php

function ResCmd_Up_TbParam_CmdStatus ($P_DBLink, $Status)
{
 $TempQuery = "UPDATE tb_param " .
              "SET valor = '" . $Status . "' " .
              "WHERE id = 0;";
 $TempQueryResult = Lib_MySQL_Query ($P_DBLink, $TempQuery);
 $TempQuery = "UPDATE tb_param " .
              "SET valor = '" . Lib_DateTime_YMDHMS () . "' " .
              "WHERE id = 1;";
 $TempQueryResult = Lib_MySQL_Query ($P_DBLink, $TempQuery);
}

function ResCmd_Up_TbOid_CmdStatus ($P_DBLink, $Reg, $Status)
{
 $TempQuery = "UPDATE tb_oid_cmd " .
              "SET status = " . $Status . ", dt_status = '" . Lib_DateTime_YMDHMS () . "' " .
              "WHERE id_seq = " . $Reg . ";";
 $TempQueryResult = Lib_MySQL_Query ($P_DBLink, $TempQuery);
}

function ResCmd_Up_TbSSH_CmdStatus ($P_DBLink, $Reg, $Status)
{
 $TempQuery = "UPDATE tb_ssh_cmd " .
              "SET status = " . $Status . ", dt_status = '" . Lib_DateTime_YMDHMS () . "' " .
              "WHERE id_seq = " . $Reg . ";";
 $TempQueryResult = Lib_MySQL_Query ($P_DBLink, $TempQuery);
}

?>
