<?php

namespace App;

//@error_reporting ( 0 );
//@ini_set ( 'error_reporting', false );
//@ini_set ( 'display_errors', false );
//@ini_set ( 'html_errors', false );

@error_reporting ( E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE );
@ini_set ( 'error_reporting', E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE );
@ini_set ( 'display_errors', true );
@ini_set ( 'html_errors', true );

include 'config.php';
include 'SafeMySQL.php';
include 'Functions.php';

class CronSendEmail
{
    public $db;

    function __construct()
    {
        $this->db = new SafeMySQL([
            "db" => "karma8_db",
            "host" => "10.10.0.20",
            "user" => "karma8_db_user",
            "pass" => "secret"
        ]);
    }

    public function checker($date_to, $limit)
    {
        echo date('d-m-Y H:i:s') . "\tНачинаем считать очередной буфер в " . $limit . " записей " . "\n";

        $select_sql = '
SELECT
    `users`.`id` AS `id`,
    `users`.`username` AS `username`,
    `users`.`email` AS `email`,
    `notify` as `notify`
FROM
    `users`
INNER JOIN
    `emails`
        ON `users`.`email`=`emails`.email
WHERE
    `users`.`validts` <= ?s AND `users`.`confirmed` = 1 AND `users`.`notify` < 1 AND `emails`.`checked` = 1
ORDER BY
    `users`.`validts` ASC
LIMIT ?i';

        $users = $this->db->getAll($select_sql, $date_to, $limit);

        if (count($users) != 0) {

            $update_sql = '
INSERT INTO `users` (`id`, `email`, `notify`) VALUES ';

            $i = 1;
            foreach ($users as $user) {
                $update_sql .= '(' . $user['id'] . ',\'' . $user['email'] . '\',' . $user['notify'] . '),';
                echo date('d-m-Y H:i:s') . "\t" . $i++ . ". ";
                Functions::send_email($user['email'], 'karma8@gmail.com', $user['email'], "karma8 subscription expiring soon", $user['username'] . ", your subscription is expiring soon");
            }

            $update_sql = substr($update_sql, 0, -1);
            $update_sql .= ' ON DUPLICATE KEY UPDATE `notify` = VALUES(`notify`)+1';

            $this->db->executeQuery($update_sql);
            
            $result = [
                'checked' => [
                    'count' => count($users)
                ]
            ];

            unset($users, $user, $select_sql, $update_sql);

            return $result;
        }
    }
}

if ((int) shell_exec('ps -aux | grep -E \'cron_send_email\.log\' | grep php | grep -v grep | wc -l') > 1) {
    echo date('d-m-Y H:i:s') . "\tЗапущена другая копия CronSendEmail\n";
    exit();
}

echo date('d-m-Y H:i:s') . "\tСтарт\n";

$cron = new CronSendEmail;

while (true) {
    if (
        // Можно выключить проверку email'ов в конфиге
        ( int ) shell_exec('cat /var/www/app/config.php' . ' | grep CRON_SEND_EMAIL | grep false | wc -l') > 0/* ||
        // Выключаем проверку email'ов в часы пиковой нагрузки
        ( date("H") >= 19 || date("H") < 2 )*/
    ) {
        echo date('d-m-Y H:i:s') . "\tПроверка email'ов выключена\n";
        echo date('d-m-Y H:i:s') . "\tСтоп\n\n\n";
        shell_exec('ps -aux | grep CronSendEmail | grep php | grep -v grep | awk \'{print $2}\' | xargs kill -9');
        exit();
    }

    try {
        $cron->db->executeQuery('START TRANSACTION');
        // Подписка истекает через 3 дня или раньше
        $result = $cron->checker(date('Y-m-d H:i:s', (time() + 60*60*24*3)),10);
    } catch (Exception $e) {
        echo "Исключение:\t",  $e->getMessage(), "\n";
        var_dump($e);
        $cron->db->executeQuery('ROLLBACK');
        continue;
    }

    if (
        ( !isset($result['checked']['count']) || $result['checked']['count'] == 0 )
    ) {
        break;
    }

    if ( $result['checked']['count'] != 0 ) {
        echo date('d-m-Y H:i:s') . "\tОповещено пользователей: " . $result['checked']['count'] . ", БД обновлена\n";
    }
//    exit();
    $cron->db->executeQuery('COMMIT');
    
    // Код для снижения нагрузки в определённые часы
//    if (
//        ( date("H") >= 3 || date("H") < 10 )
//    ) {
//    sleep(10);
//        echo date('d-m-Y H:i:s') . "\tЖдём 10 секунд "  . "\n";
//    } else {
//        sleep(20);
//        echo date('d-m-Y H:i:s') . "\tЖдём 20 секунд "  . "\n";
//    }
}

echo date('d-m-Y H:i:s') . "\tСтоп\n\n\n";
exit();