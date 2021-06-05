<?php
$base = __DIR__ . '/../app/';
$folders = [
    'config',
    'lib',
    'middleware',
    'model',
    'routes/private',
    'routes/public'
];
foreach ($folders as $f) {
    foreach (glob($base . "$f/*.php") as $filename) {
        require $filename;
    }
}
