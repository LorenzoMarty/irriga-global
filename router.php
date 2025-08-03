<?php
// router.php

// Arquivos estáticos
if (php_sapi_name() === 'cli-server') {
    $path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    $fullPath = __DIR__ . $path;

    if (is_file($fullPath)) {
        return false;
    }
}

// Ponto de entrada da aplicação
require_once __DIR__ . '/routes/api.php';
