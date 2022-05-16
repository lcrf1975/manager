<?php

function ResOid_Up_TbObj_OidReadStatus ($P_DBLink, $Reg, $Status)
{
 $TempQuery = "UPDATE tb_obj " .
              "SET thread_oid = " . $Status . " " .
              "WHERE id_seq = " . $Reg . ";";
 $TempQueryResult = Lib_MySQL_Query ($P_DBLink, $TempQuery);
}

function ResOid_Up_TbOid_Valor ($P_DBLink, $Reg, $Value)
{
 $TempQuery = "UPDATE tb_oid " .
              "SET valor = " . $Value . ", dt_valor = '" . Lib_DateTime_YMDHMS () . "' " .
              "WHERE id_seq = " . $Reg . ";";
 $TempQueryResult = Lib_MySQL_Query ($P_DBLink, $TempQuery);
}

?>
