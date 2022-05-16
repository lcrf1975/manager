#!/bin/bash

echo Cleanning...
rm log/* 2> /dev/null > /dev/null

echo Stopping...
killall -e main.php 2> /dev/null > /dev/null
killall -e thread.sh 2> /dev/null > dev/null
killall -e thread2p.sh 2> /dev/null > dev/null
killall -e thread4p.sh 2> /dev/null > dev/null 
