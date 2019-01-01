<?php
/**
 * Created by PhpStorm.
 * User: demonius
 * Date: 20.12.18
 * Time: 14:16
 */

namespace Astkon;

abstract class ErrorCode
{
    const PROGRAMMER_ERROR = 1;
    const BAD_DB_CONSISTENCE = 2;
    const HACKING = 3;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
}