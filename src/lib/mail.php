<?php

include_once "../config/config.php";

/**
 * @throws Exception
 */
function sendEmail(string $s_to_address, string $s_from_name, string $s_to_user, string $s_subject, string $s_body): bool
{
    if (!isset($s_to_address) || !str_contains($s_to_address, "@")) {
        throw new MailException("Verify email");
    }

    //sleep(random_int(1, 8));

    // Prepare name and subject
    $s_to_user = $s_to_user ?? 'User';
    $s_from_name = $s_from_name ?? 'Karma mailer';
    $s_from_mail = MAIL_FROM ?? "no-replay@karma.com";

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://api.mailgun.net/v3/sandbox32f2d99d7bcd4ad89fa20851de2e7b8b.mailgun.org/messages');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    $post = array(
        "from" => "$s_from_name <$s_from_mail>",
        "to" => "$s_to_user <$s_to_address>",
        "subject" => $s_subject,
        "text" => $s_body,
    );
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_USERPWD, "api:68220ef73a7c64d1842990122614a015-18e06deb-19c04f28");

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        throw new MailException("Error:" . curl_error($ch));
    }
    curl_close($ch);

    if (empty($result)) {
        return false;
    }

    return true;
}

/**
 * @throws Exception
 */
function checkEmail(string $s_email): int
{
    if (strlen($s_email) < 5 || !str_contains($s_email, "@")) {
        return 0;
    }

    sleep(random_int(1, 60));

    $a_email_domain_list = explode("@", $s_email);
    $s_email_domain = end($a_email_domain_list);

    $a_email_dns_record_list = @dns_get_record($s_email_domain, DNS_MX);

    $a_email_priority = [];
    foreach ($a_email_dns_record_list as $a_email_dns_record) {
        $a_email_priority[] = $a_email_dns_record["pri"];
    }
    array_multisort($a_email_priority, SORT_ASC, $a_email_dns_record_list);

    if(count($a_email_dns_record_list) === 0)
    {
        return 0;
    }

    return 1;
}

final class MailException extends RuntimeException
{
}