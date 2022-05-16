<?php

function Lib_DateTime_Seq ()
{
 return date ('Ymd');
}

function Lib_DateTime_TimeStamp ()
{
 return date ('d/m - H:i:s');
}

function Lib_DateTime_YMDHMS ()
{
 return date ('Y/m/d H:i:s');
}

function Lib_SecDiff ($DateTime)
{
 $StartDate = strtotime ($DateTime);
 $EndDate = strtotime (Lib_DateTime_YMDHMS ());

 return ($EndDate - $StartDate);
} 
?>
