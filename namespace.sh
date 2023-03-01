#!/bin/bash

dir_path="$PWD/helpers"
if [ ! -d "$dir_path" ]; then
  echo "Dir helpers not found: $dir_path"
  exit 1
fi

input_string=$1
if [[ $input_string == "" ]]; then
	echo ""
	echo "Empty replacer, example command: (must be splitted by + signs)"
	echo ""
	echo "./namespace.sh MyCompany+MyPackage"
	echo ""
	exit 1
fi

search="namespace MicroDeploy\\\Package\\\Helpers;"

backslash_old="+"
backslash_new="\\\\"
replacer="${input_string//${backslash_old}/${backslash_new}}"

new_string="namespace $replacer\\\Helpers;"

for entry_path in "$dir_path"/*.php
do
	sed -i "s/$search/$new_string/" "$entry_path"
done

echo 'finished'