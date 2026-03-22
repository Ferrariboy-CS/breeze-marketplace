<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';

// Shared DB instance for public pages without auth enforcement.
$query = new Database();
