<?php

namespace Cartbeforehorse\Validation;


class CodingError {

    private function __construct() {
    }


    ////////////////////////////////////////////////////////////////////////////////////
    // Although Laravel manages runtime errors by raising exceptions, on the application
    // level, there are some cases where we want to prevent down-stream programmers from
    // coding a certain way. This means that we need to force them to use some functions
    // in a certain way, or as much as we can, prevent them from programming against the
    // grain, so to speak. In order to discourage this kind of situation, this class has
    // been created, allowing us to write user errors into the core application where we
    // require functions to be used in a limited way.
    //
    // 3 levels of user error can be generated:
    //   -> NOTICE, WARNING, ERROR as documented here:
    //   ->     http://www.php.net/manual/en/errorfunc.constants.php
    //
    public static function RaiseCodingNotice ($err_message) {
        trigger_error( htmlentities ('Coding Tip: ' . $err_message, ENT_QUOTES, 'UTF-8'), E_USER_NOTICE);
    }
    public static function RaiseCodingWarning ($err_message) {
        trigger_error( htmlentities ('Coding Warning: ' . $err_message, ENT_QUOTES, 'UTF-8'), E_USER_WARNING);
    }
    public static function RaiseCodingError ($err_message) {
        trigger_error( htmlentities ('Coding Error: ' . $err_message, ENT_QUOTES, 'UTF-8'), E_USER_ERROR);
    }
}
