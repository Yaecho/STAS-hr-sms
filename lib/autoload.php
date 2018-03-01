<?php
// 自动加载
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/' . $class . '.php';
    // Linux需要转义斜杠;
    $file = str_replace('\\', '/', $file);

    if (is_file($file)) {
        include($file);
    }
});
