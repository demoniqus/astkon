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
    const BAD_DB_CONSISTENCE = 2; //Ошибка целостности БД - найдена ссылка на несуществующий элемент
    const HACKING = 3; //Попытка несанкционированного прямого доступа к методам
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
}