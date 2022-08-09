#!/usr/bin/env sh

if grep -E 'karma8.dev|\n10.10.0.10' /etc/hosts;
then
  echo "\nhost 10.10.0.10\tkarma8.dev already exists";
else
  echo "\n\n10.10.0.10\tkarma8.dev" >> /etc/hosts ;
fi

if grep -E 'karma8.dev.db|\n10.10.0.20' /etc/hosts;
then
  echo "\nhost 10.10.0.20\tkarma8.dev.db already exists";
else
  echo "10.10.0.20\tkarma8.dev.db" >> /etc/hosts ;
fi

service network-manager restart