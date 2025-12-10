<?php
/**
 * Logout Page
 * MyTabungan - Personal Finance Management
 */

require_once __DIR__ . '/auth/auth.php';

// Log out the user
logoutUser();

// Redirect to login
setFlashMessage('success', 'Anda telah berhasil keluar.');
redirect('login.php');
