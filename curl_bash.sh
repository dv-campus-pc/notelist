#!/bin/bash

for i in {1..1000} ; do
  echo ' -o /dev/null https://dv-campus-21-22-notelist.local/ -:'
done | xargs curl -s
