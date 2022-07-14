<?php

function getPid(string $s_queue_name, string $path = null):int {

    if ($path === null) {
        $path = getLockFile($s_queue_name);
    }

    return @(int)file_get_contents($path);
}

function setPid(string $s_queue_name, int $pid):void {

    $path = getLockFile($s_queue_name);
    file_put_contents($path, $pid);
}

function getLockFile(string $s_queue_name): string
{

    global $argv;

    return "../../cache/" . md5($argv[0]) . "_{$s_queue_name}.lock";
}

function killCron(string $s_queue_name)
{
    global $argv;

    echo "Try kill queue name = {$s_queue_name}\n";
    $pid = getPid(getLockFile($s_queue_name));
    if ($pid > 0) {

        // убиваем крон
        posix_kill($pid, 9);

        $s_message = "[SYSTEM] CRON START WITH PARAM - " . $argv[1];
        echo "$s_message -- $s_queue_name\n";
    }
}
