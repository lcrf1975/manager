<?php

require 'config.php';
require_once 'lib/datetime.php';

function Lib_Log_FilePath()
{
 global $Config_LogPath, $Config_LogExt;
 return $Config_LogPath . Lib_DateTime_Seq () . $Config_LogExt;
}

function Lib_Log_Write ($P_Text)
{
 $P_Text = Lib_DateTime_TimeStamp () . ": " . $P_Text;
 file_put_contents (Lib_Log_FilePath(), $P_Text, FILE_APPEND);
}

?>
