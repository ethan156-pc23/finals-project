<?php
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/functions.php';

$_SESSION = [];
session_destroy();

session_start();
flash_set('success', 'You have been logged out.');
header('Location: ' . BASE_URL . '/index.php');
exit;
