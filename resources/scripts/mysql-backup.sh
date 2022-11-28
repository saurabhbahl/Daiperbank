#!/bin/sh

#----------------------------------------------------------
# a simple mysql database backup script.
# version 2, updated March 26, 2011.
# copyright 2011 alvin alexander, http://alvinalexander.com
#----------------------------------------------------------
# This work is licensed under a Creative Commons
# Attribution-ShareAlike 3.0 Unported License;
# see http://creativecommons.org/licenses/by-sa/3.0/
# for more information.
#----------------------------------------------------------

# (1) set up all the mysqldump variables
FILE=hsdb.`date +"%Y%m%d"`.sql
PATH=/home/forge/app.healthystepsdiaperbank.com/storage/backups/mysql
DBSERVER=127.0.0.1
DATABASE=hsdb
USER=hsdb
PASS=yo[M}u7.wEjznw6h6wAu

MYSQLDUMP_BIN=/usr/bin/mysqldump
GZIP_BIN=/bin/gzip

# (2) in case you run this more than once a day, remove the previous version of the file
unalias rm     2> /dev/null
rm ${PATH}/${FILE}     2> /dev/null
rm ${PATH}/${FILE}.gz  2> /dev/null

# (3) do the mysql database backup (dump)

# use this command for a database server on a separate host:
#mysqldump --opt --protocol=TCP --user=${USER} --password=${PASS} --host=${DBSERVER} ${DATABASE} > ${FILE}

# use this command for a database server on localhost. add other options if need be.
${MYSQLDUMP_BIN} --opt --user=${USER} --password=${PASS} ${DATABASE} > ${PATH}/${FILE}

# (4) gzip the mysql database dump file
${GZIP_BIN} ${PATH}/$FILE

# (5) show the user the result
echo "${FILE}.gz was created:"
# ls -l ${PATH}/${FILE}.gz