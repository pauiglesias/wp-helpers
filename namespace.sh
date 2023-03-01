#!/bin/bash

dir_path="$PWD/helpers"
if [ ! -d "$dir_path" ]; then
  echo "Dir helpers not found: $dir_path"
  exit 1
fi

input_test=$1
if [[ $input_test == "" ]]; then
	echo ""
	echo "Empty replacer, example command: (must be splitted by + signs)"
	echo ""
	echo "./namespace.sh MyCompany+MyPackage"
	echo ""
	exit 1
fi


# input

backslash_old="+"
backslash_new="\\\\"
input_string="${input_test//${backslash_old}/${backslash_new}}"


# search 1

search1="namespace MicroDeploy\\\Package\\\Helpers;"
new_string1="namespace $input_string\\\Helpers;"


# search 2

search2="use \\\MicroDeploy\\\Package "
new_string2="use ${backslash_new}${input_string} "


# replacing

for entry_path in "$dir_path"/*.php
do
	sed -i "s/$search1/$new_string1/" "$entry_path"
	sed -i "s/$search2/$new_string2/" "$entry_path"
done

echo 'finished'