<?php

use RapidWeb\GoogleOAuth2Handler\GoogleOAuth2Handler;
use RapidWeb\GooglePeopleAPI\GooglePeople;

require_once 'vendor/autoload.php';

$clientId = '';
$clientSecret = '';
$refreshToken = '';

$scopes = ['https://www.googleapis.com/auth/contacts', 'https://www.googleapis.com/auth/contacts.readonly', 'https://www.googleapis.com/auth/plus.login', 'https://www.googleapis.com/auth/user.addresses.read', 'https://www.googleapis.com/auth/user.birthday.read', 'https://www.googleapis.com/auth/user.emails.read', 'https://www.googleapis.com/auth/user.phonenumbers.read', 'https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/userinfo.profile'];

$googleOAuth2Handler = new GoogleOAuth2Handler($clientId, $clientSecret, $scopes, $refreshToken);

$people = new GooglePeople($googleOAuth2Handler);

$all = $people->all();

// Retrieval all contacts
/** @var  $contact */
foreach ($all as $contact) {
    if ($contact->phoneNumbers) {
        echo $contact->resourceName . ' - ';
        echo $contact->names[0]->displayName . ' - ';
        $number = $contact->phoneNumbers[0]->value;
        echo $number . ' - ';

        $corrected_number = formatPhoneNumber($number);
        if ($number != $corrected_number) {
            $contact->phoneNumbers[0]->value = $corrected_number;
            $contact->save();
            echo $corrected_number . ' - updated!';
        }

        echo PHP_EOL;
    }
    break;
}

function formatPhoneNumber($input)
{
    $normalized = preg_replace("/[^0-9]/", "", $input);
    if (strlen($normalized) == 10) {
        $normalized = '1' . $normalized;
    }
    $restructured = "+";

    $strlen = strlen($normalized);
    for ($i = 0; $i <= $strlen; $i++) {
        $char = substr($normalized, $i, 1);

        if ($i == 1) {
            $restructured = $restructured . " ";
        }
        if ($i == 4 || $i == 7) {
            $restructured = $restructured . "-";
        }
        $restructured = $restructured . $char;
    }

    return $restructured;
}

?>