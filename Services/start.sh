#!/bin/bash

R1=`pgrep -f main.php | wc -l`
if [ ${R1} -eq 0 ]
 then
 cd /usr/share/Manager/
  logfile=$(date "+%Y%m%d")
  logfile='log/'$logfile'.out'
  ./main.php >> $logfile &
fi
