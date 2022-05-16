<?php

require_once 'lib/log.php';

function Lib_SNMP_Read ($P_IPAddress, $P_Ver, $P_Community, $P_Object, $P_Timeout, $P_Retry)
{
 $Timeout = $P_Timeout * 1000000;
 snmp_set_valueretrieval (SNMP_VALUE_PLAIN);
 
 try
 {
  $T = microtime (TRUE);
  switch ($P_Ver)
  {
   case 3:
    $Value = snmp3_get ($P_IPAddress, $P_Community, "." . $P_Object, $Timeout, $P_Retry);
    break;
   case 2:
    $Value = snmp2_get ($P_IPAddress, $P_Community, "." . $P_Object, $Timeout, $P_Retry);
    break;
   default:
    $Value = snmpget ($P_IPAddress, $P_Community, "." . $P_Object, $Timeout, $P_Retry);
    break;
  }
 
  $T = (microtime (TRUE) - $T);
 
  if ($T < ($P_Timeout * $P_Retry))
  {
   Lib_Log_Write ("SNMP OID " . $P_IPAddress . "@" . $P_Community . "." . $P_Object  . " << [" . $Value . "].\n");
   return $Value;
  }
  else
  {
   Lib_Log_Write ("SNMP OID " . $P_IPAddress . "@" . $P_Community . "." . $P_Object . " << [*** Timeout ***].\n");
   return NULL;
  }
 }
 
 catch (Exception $Erro)
 {
  Lib_Log_Write ("Error: " . $Erro . " (Reading SNMP OID: " . $P_IPAddress . "@" . $P_Community . "." . $P_Object  . ").\n");
  return NULL;
 }

}

function Lib_SNMP_Write ($P_IPAddress, $P_Ver, $P_Community, $P_Object, $P_Type, $P_Value, $P_Timeout, $P_Retry)
{

 $P_Timeout = $P_Timeout * 1000000;

 switch ($P_Type)
 {
  case 0:
   $P_Type = "i";
   break;
  default:
   $P_Type = "s";
   break;
 }

  switch ($P_Ver)
  {
   case 3:
    $Return = snmp3_set ($P_IPAddress, $P_Community, "." . $P_Object, $P_Type, $P_Value, $P_Timeout, $P_Retry);
    break;
   case 2:
    $Return = snmp2_set ($P_IPAddress, $P_Community, "." . $P_Object, $P_Type, $P_Value, $P_Timeout, $P_Retry);
    break;
   default:
    $Return = snmpset ($P_IPAddress, $P_Community, "." . $P_Object, $P_Type, $P_Value, $P_Timeout, $P_Retry);
    break;
  }

 $Return = snmpset ($P_IPAddress, $P_Community, "." . $P_Object, $P_Type, $P_Value, $P_Timeout, $P_Retry);

 if (!$Return)
 {
  Lib_Log_Write ("Error Writting SNMP OID: " . $P_IPAddress . "@" . $P_Community . "." . $P_Object  . ".\n");
 }
 else
 {
  Lib_Log_Write ("SNMP OID: " . $P_IPAddress . "@" . $P_Community . "." . $P_Object  . " >> [" . $P_Value . "]\n");
 }
 return $Return;
}

?>
