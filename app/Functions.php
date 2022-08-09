<?php

namespace App;

class Functions
{
    /**
     * Проверяет емейл на валидность и возвращает 0 или 1.
     * Функция работает от 1 секунды до 1 минуты. Вызов функции платный.
     *
     * @param $email
     * @return bool
     */
    public function check_email($email) {
        $sleep = rand(1, 60);
        echo 'check_email ' . $email . " SLEEP:" . $sleep . "s\n";
        sleep($sleep);
        return true;
    }
    
    /**
     * Отсылает емейл. Функция работает от 1 секунды до 10 секунд.
     *
     * @param $email
     * @param $from
     * @param $to
     * @param $subj
     * @param $body
     * @return void
     */
    public function send_email($email, $from, $to, $subj, $body) {
        $sleep = rand(1, 10);
        echo 'send_email EMAIL:' . $email . ' FROM:' . $from . ' TO:' .
            $to . ' SUBJ:' . $subj . ' BODY:' . $body . ' SLEEP:' . $sleep . "s\n";
        sleep($sleep);
    }
}