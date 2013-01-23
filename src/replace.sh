#!/bin/sh
#
# Replace string with another in a file
#
# Examples:
# ./replace.sh <find_word> <replace_with> <file>
#
# Recursive replace in files:
# find . -type f -exec ./replace.sh <find_word> <replace_with> "{}" \;
#
# Or with file filter:
# find . -type f -name "*.php" -exec ./replace.sh <find_word> <replace_with> "{}" \;
#
perl -p -i -e "s/$1/$2/g" $3

