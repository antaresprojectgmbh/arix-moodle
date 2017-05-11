#!/bin/bash

while [[ $# -gt 1 ]]
do
key="$1"

case $key in
    -i|--institution)
    INSTITUTION="$2"
    shift
    ;;
    -t|--token)
    TOKEN="$2"
    shift
    ;;
    *)
            # unknown option
    ;;
esac

shift # past argument or value
done
echo INSTITUTION = "${INSTITUTION}"
echo TOKEN  = "${TOKEN}"

mkdir arix
cp -R arix.php  db  Encoding.php  lang  lib.php  LICENSE  pix redirect.php  version.php arix
cp arix/arix.php arix/arix.php.bak
sed s/\<token\>/\"${TOKEN}\"/g arix/arix.php.bak > arix/arix.php
rm arix/arix.php.bak

zip -r arix-moodle-${INSTITUTION}.zip arix
rm -rf arix
