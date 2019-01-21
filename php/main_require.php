<?php
namespace Astkon;


/*
 * Главный файл с подключаемыми модулями
 */
/*
 * Устанавливаем кодировку данных
 */
header('Content-Type: text/html; charset=utf-8');

require_once 'core/GlobalConst.php';
require_once 'traits.php';
require_once 'core/Model.php';
require_once 'core/Controller.php';
require_once 'core/View.php';
require_once 'core/error_code.php';
require_once 'core/DataBase.php';
require_once 'core/linq.php';
require_once 'core/DocComment.php';
require_once 'lib.php';
require_once 'types_processor.php';
require_once 'file_system.php';
require_once 'models.php';
require_once 'controllers.php';






