#!/bin/bash

#	================================================
#	fakemail.sh
#	Command Line Usage:
#		echo … | fakemail.sh
#		echo … | fakemail.sh /path/to/file.txt
#	php.ini Usage:
#		sendmail_path /path/to/fakemail.sh
#		sendmail_path /path/to/fakemail.sh /path/to/file.txt
#	You may need to
#		chmod 755 fakemail.sh
#
#	DIR=$(cd `dirname "$0"`; pwd -P | sed 's/ /\\ /g');
#
#	================================================

log() {
    echo "$@" >>~mark/Downloads/notes.txt
}

DATE=`date`
DIR=`dirname "$0"`

if [ -z "$1" ]; then
	FILE="${DIR}/mail.txt"
else
	if [[ "$1" == /* ]]; then
		FILE="$1"
	else
		FILE="$DIR/$1"
	fi
fi

log "FILE: $FILE"

echo ================================================ >> "$FILE"
date >> "$FILE"
echo ------------------------------------------------ >> "$FILE"
while read LINE
do
	echo "$LINE" >> "$FILE"
done
echo ------------------------------------------------ >> "$FILE"
echo >> "$FILE"
