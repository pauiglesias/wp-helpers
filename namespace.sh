#!/bin/bash

filename="./helpers/admin.php"

search="namespace MicroDeploy\Package\Helpers;"

read -p "Enter the namesplace string: " replace

if [[ $replace != "" ]]; then
	sed -i "s/$search/namespace\s$replace/" $filename
fi