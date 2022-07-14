<?php

const MODULE_NAME = "subscriptionCheck";

/** @var PDO $db */
$db = include "../lib/mysql.php";
include_once "../lib/mail.php";
include_once "cron.php";

$pid = getPid(MODULE_NAME);
if($pid > 0 && posix_kill($pid, 0))
{
    die("Die, cron start is locked\n");
}

setPid(MODULE_NAME, getmypid());

$db->beginTransaction();

$s_current_time = time();
$s_limit_time = strtotime('+3 day');
$o_database_state = $db->query("SELECT * FROM users
LEFT JOIN emails ON emails.email = users.email
WHERE users.validts > $s_current_time
  AND users.validts <= $s_limit_time
  AND emails.valid = 1
  AND NOT EXISTS (
      SELECT email_notify.email
      FROM email_notify
      WHERE users.email = email_notify.email
  )
LIMIT 3 FOR UPDATE");

$a_user_data = [];
while($a_database_row = $o_database_state->fetch())
{
    $a_user_data[] = [
        "username" => $a_database_row["username"],
        "email" => $a_database_row["email"],
    ];
}

foreach ($a_user_data as $a_user_info)
{
    $s_current_datetime = date('Y-m-d H:i:s');
    $db->prepare("INSERT INTO email_notify (email, created_date) VALUES ('{$a_user_info["email"]}', '$s_current_datetime')")->execute();

    sendEmail(
        $a_user_info["email"],
        EMAIL_SENDER_NAME,
        $a_user_info["username"],
        "Subscription expired",
        "{$a_user_info['username']}, your subscription is expiring soon.");
}
$db->commit();

function shutdown(PDO $db): void
{
    if($db->inTransaction())
    {
        $db->rollBack();
    }
    killCron(MODULE_NAME);
}
register_shutdown_function('shutdown', $db);