#!/usr/bin/env sh

# Install Additional Tools
apt-get update -y --fix-missing
apt-get install --no-install-recommends -y \
  wget vim nano htop wget net-tools iproute2
apt update
apt -y install cron mc less

# Cleanup to reduce image size
rm -rf /var/lib/apt/lists/* /tmp/*

# Set Timezone
ln -sf /usr/share/zoneinfo/Europe/Moscow /etc/localtime