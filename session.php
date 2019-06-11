<?php

session_start();

$is_auth = 0;
$user_name = '';
$user_id = 0;
if (isset($_SESSION['username'])) {
    $is_auth = 1;
    $user_name = $_SESSION['username'];
    $user_id = $_SESSION['user_id'];
}