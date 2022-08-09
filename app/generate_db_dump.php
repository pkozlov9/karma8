<?php
#echo phpinfo();exit();

$users = 1000000;
$buffer = 1000;

// emails
$insert_sql_emails = 'INSERT INTO `emails`(`id`,`email`,`checked`,`valid`) VALUES ';
echo '/* Data for the table `emails` */' . "\n";
echo $insert_sql_emails;

$n = 1;
$b = 1;
while ( $n < $users ) {
    echo "(". $n .",'test" . $n . "@gmail.com'," . rand(0,1) . "," . rand(0,1) . ")";
    $n++;
    $b++;
    if ( $b <= $buffer && $n < $users) {
        echo  ",";
    } else {
        echo ";\n";
        echo "COMMIT;\n";
        echo $insert_sql_emails;
        $b = 1;
    }
}
echo "(". $n .",'test" . $n . "@gmail.com'," . rand(0,1) . "," . rand(0,1) . ");\n";
echo "COMMIT;\n\n";

// users
$insert_sql_users = 'INSERT INTO `users`(`id`,`username`,`email`, `validts`,`confirmed`) VALUES ';
echo '/* Data for the table `users` */' . "\n";
echo $insert_sql_users;
$n = 1;
$b = 1;
while ( $n < $users ) {
    echo "(". $n .",'Test" . $n . "','test" . $n . "@gmail.com', '" .
        date("Y-m-d H:i:s", time()+60*60*24*rand(0,5)) . "'," . rand(0,1) . ")";
    $n++;
    $b++;
    if ( $b <= $buffer && $n < $users) {
        echo  ",";
    } else {
        echo ";\n";
        echo "COMMIT;\n";
        echo $insert_sql_users;
        $b = 1;
    }
}

echo "(". $n .",'Test" . $n . "','test" . $n . "@gmail.com', '" .
    date("Y-m-d H:i:s", time()+60*60*24*rand(0,5)) . "'," . rand(0,1) . ");\n";
echo "COMMIT;\n\n";

echo "SET unique_checks=1;\n";
echo "SET foreign_key_checks=1;\n";
echo "SET autocommit=1;\n";