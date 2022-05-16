#!/bin/bash

echo Debuging...

rm log/* 2> /dev/null > /dev/nul

logfile=$(date "+%Y%m%d")
logfile='log/'$logfile'.out'

killall -e main.php  2> /dev/null >> $logfile

./main.php >> $logfile &

tail -f $logfile | grep -v "^ *$"
