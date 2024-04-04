<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: access');
header('Access-Control-Allow-Methods: POST');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/sendJson.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') :
    $data = json_decode(file_get_contents('php://input'));
    if (
        !isset($data->ten) ||
        !isset($data->email) ||
        !isset($data->phone) ||
        empty(trim($data->ten)) ||
        empty(trim($data->email)) ||
        empty(trim($data->phone))
    ) :
        sendJson(
            422,
            'Please fill all the required fields & None of the fields should be empty.',
            ['required_fields' => ['ten', 'email', 'phone']]
        );
    endif;

    $ten = mysqli_real_escape_string($connection, htmlspecialchars(trim($data->ten)));
    $email = mysqli_real_escape_string($connection, trim($data->email));
    $phone = trim($data->phone);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) :
        sendJson(422, 'Invalid Email Address!');

    elseif (strlen($phone) < 8) :
        sendJson(422, 'Your phone must be at least 8 characters long!');

    elseif (strlen($ten) < 3) :
        sendJson(422, 'Your ten must be at least 3 characters long!');

    endif;

    // $hash_phone = phone_hash($phone, phone_DEFAULT);
    $sql = "SELECT `email` FROM `users` WHERE `email`='$email'";
    $query = mysqli_query($connection, $sql);
    $row_num = mysqli_num_rows($query);

    if ($row_num > 0) sendJson(422, 'This E-mail already in use!');

    $sql = "INSERT INTO `users`(`ten`,`email`,`phone`) VALUES('$ten','$email','$phone')";
    $query = mysqli_query($connection, $sql);
    if ($query) sendJson(201, 'You have successfully registered.');
    sendJson(500, 'Something going wrong.');
endif;

sendJson(405, 'Invalid Request Method. HTTP method should be POST');
