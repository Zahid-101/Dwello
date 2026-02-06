<?php
$regex = '/(.+)@(.+)\.(.+)/i';
$emails = [
    'ish@gmail',
    'ish@gmail.com',
    'test@sub.domain.co.uk',
    'invalid',
    'user@localhost'
];

foreach ($emails as $email) {
    if (preg_match($regex, $email)) {
        echo "PASS: $email matches\n";
    } else {
        echo "FAIL: $email does not match\n";
    }
}
