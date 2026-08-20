<?php
/**
 * HIEU CEO - User Portal Entry
 */
require_once __DIR__ . '/../config/auth_user.php';

if (isUserLoggedIn()) {
    header('Location: dashboard.php');
    exit;
} else {
    header('Location: login.php');
    exit;
}
