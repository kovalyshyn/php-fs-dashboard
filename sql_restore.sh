#!/bin/bash

psql -U switch switch < /data/sql/empty_dump.sql
psql -U switch switch < /data/sql/dump_2015-04-19.sql
psql -U switch switch < /data/sql/fn_cdrupdatebillings.sql
