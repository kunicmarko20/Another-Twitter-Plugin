<?php

/**
 * Use to autoload needed classes without Composer.
 *
 * @param string $class The fully-qualified class name.
 * @return void
 */
spl_autoload_register(function ($class) {

    // project-specific namespace prefix



    // does the class use the namespace prefix?


    // get the relative class name

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = __DIR__ .'/src/'. $class . '.php';

    // if the file exists, require it
    if (file_exists($file)) {

        require $file;
    }
});
