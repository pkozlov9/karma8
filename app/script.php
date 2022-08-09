<!DOCTYPE html>
<html>
    <body>
    <h2>/var/log/cron_send_email.log (последние 10 строк):</h2>
    <p id="cron_send_emails">Здесь скоро появится лог <b>отправки</b> email'ов <b>/var/log/cron_send_email.log</b></p>
    <h2>/var/log/cron_check_email.log (последние 10 строк):</h2>
    <p id="cron_check_emails">Здесь скоро появится лог <b>проверки</b> email'ов <b>/var/log/cron_check_email.log</b></p>
    </body>
</html>

<script type="text/javascript">
    function UpdateSendEmailLog() {
        const xhttp = new XMLHttpRequest();
        xhttp.onload = function() {
            if (this.responseText != '') {
                document.getElementById("cron_send_emails").innerHTML = '<code>'+this.responseText+'</code>';
            }
        }
        xhttp.open("GET", "CronSendEmailLog.php");
        xhttp.send();
    }
    function UpdateCheckEmailLog() {
        const xhttp = new XMLHttpRequest();
        xhttp.onload = function() {
            if (this.responseText != '') {
                document.getElementById("cron_check_emails").innerHTML = '<code>'+this.responseText+'</code>';
            }
        }
        xhttp.open("GET", "CronCheckEmailLog.php");
        xhttp.send();
    }
    setInterval(UpdateSendEmailLog, 1000);
    setInterval(UpdateCheckEmailLog, 1000);
</script>

<style>
    #cron_send_emails, #cron_check_emails {
		background-color: #EDEDED;
    }
</style>