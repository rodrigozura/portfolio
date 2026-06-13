<?php
require_once __DIR__ . '/db.php';
session_start();
session_destroy();
app_redirect('/login');
