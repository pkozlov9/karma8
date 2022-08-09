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

class CronCheckEmail
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
    
    public function checker($limit)
    {
        echo date('d-m-Y H:i:s') . "\tНачинаем считать очередной буфер в " . $limit . " записей " . "\n";
        
        $select_sql = '
SELECT
    `id`,
    `email`
FROM
    `emails`
WHERE
    `checked` = 0
ORDER BY
    `id` ASC
LIMIT ?i';
    
        $emails = $this->db->getAll($select_sql, $limit);
        
        if (count($emails) != 0) {
            
            $update_sql = '
INSERT INTO `emails` (`id`, `email`, `checked`, `valid`) VALUES ';
            
            $i = 1;
            foreach ($emails as $email) {
                echo date('d-m-Y H:i:s') . "\t" . $i++ . ". ";
                $valid = Functions::check_email($email['email']);
                $update_sql .= '(' . $email['id'] . ',\'' . $email['email'] . '\',' .
                    1 . ',' . (int) $valid . '),';
            }
            
            $update_sql = substr($update_sql, 0, -1);
            $update_sql .= ' ON DUPLICATE KEY UPDATE `checked` = VALUES(`checked`), `valid` = VALUES(`valid`)';

            $this->db->executeQuery($update_sql);
            
            $result = [
                'checked' => [
                    'count' => count($emails)
                ]
            ];

            unset($emails, $email, $select_sql, $update_sql);

            return $result;
        }
    }
}

if ((int) shell_exec('ps -aux | grep -E \'cron_check_email\.log\' | grep php | grep -v grep | wc -l') > 1) {
    echo date('d-m-Y H:i:s') . "\tЗапущена другая копия CronCheckEmail\n";
    exit();
}

echo date('d-m-Y H:i:s') . "\tСтарт\n";

$cron = new CronCheckEmail;

while (true) {
    if (
        // Можно выключить проверку email'ов в конфиге
        ( int ) shell_exec('cat /var/www/app/config.php' . ' | grep CRON_SEND_EMAIL | grep false | wc -l') > 0/* ||
        // Выключаем проверку email'ов в часы пиковой нагрузки
        ( date("H") >= 19 || date("H") < 2 )*/
    ) {
        echo date('d-m-Y H:i:s') . "\tПроверка email'ов выключена\n";
        echo date('d-m-Y H:i:s') . "\tСтоп\n\n\n";
        shell_exec('ps -aux | grep CronCheckEmail | grep php | grep -v grep | awk \'{print $2}\' | xargs kill -9');
        exit();
    }
    
    try {
        $cron->db->executeQuery('START TRANSACTION');
        // Подписка истекает через 3 дня или раньше
        $result = $cron->checker(5);
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