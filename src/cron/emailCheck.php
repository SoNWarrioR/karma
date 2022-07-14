<?php

const MODULE_NAME = "emailCheck";

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

$o_database_state = $db->query("SELECT * FROM users WHERE users.email NOT IN (SELECT email FROM emails) AND users.confirmed = 1 LIMIT 10");

$a_email_check_data = [];
while($a_database_row = $o_database_state->fetch())
{
    $a_email_check_data[] = [
        $a_database_row["email"],
        1,
        checkEmail($a_database_row["email"]),
    ];
}

$statement = $db->prepare("INSERT INTO emails (email, checked, valid) VALUES (?,?,?)");
try {
    $db->beginTransaction();
    foreach ($a_email_check_data as $row)
    {
        $statement->execute($row);
    }
    $db->commit();
}catch (Exception $e){
    $db->rollback();
    throw $e;
}

/**
 * @param PDO $db
 * @return void
 */
function shutdown(PDO $db): void
{
    if($db->inTransaction())
    {
        $db->rollBack();
    }
    killCron(MODULE_NAME);
}

register_shutdown_function('shutdown', $db);