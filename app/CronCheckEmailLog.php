<?php echo str_replace("\n", '<br>', shell_exec('tail -n10 /var/log/cron_check_email.log')); ?>