<?php
namespace Astkon;


/*
 * Главный файл с подключаемыми модулями
 */
/*
 * Устанавливаем кодировку данных
 */
header('Content-Type: text/html; charset=utf-8');
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


/*Глобальные константы*/
final class GlobalConst {
    /**
     * Наименование хоста, на котором располагается БД
     * @var string
     */
    const Host = 'localhost';
    /**
     * Наименование БД
     * @var string
     */
    const DbName = 'astkon';
    /**
     * Логин и пароль для подключения к БД
     * @var string
     */
    const HostUser = 'developer';
    const HostPass = '1234567890';
    /**
     * Уровень прав для вновь создаваемых каталогов:
     * 0777 полный доступ на чтение и изменение каталога всеми пользователями
     * и группами пользователей
     * @var int
     */
    const DefDirAccess = 0777;
    /**
     * Временный каталог проекта
     * @var string
     */
    const TmpDirName = 'tmp';
    const CoreDirectory = 'php' . DIRECTORY_SEPARATOR . 'core';
    const ControllersDirectory = 'php' . DIRECTORY_SEPARATOR . 'controllers';
    /**
     * Каталог для создания частичных классов, реализующих сущности базы данных
     * @var string
     */
    const PartialModelsDirectory = 'php' . DIRECTORY_SEPARATOR . 'partialModels';
    /**
     * Каталог для классов, реализующих частичные классы
     * @var string
     */
    const ModelsDirectory = 'php' . DIRECTORY_SEPARATOR . 'models';
    /**
     * Файл для регистрации моделей, реализующих сущности базы данных
     */
    const ModelsRegistry = 'php' . DIRECTORY_SEPARATOR . 'models.php';
    /**
     * Директория представлений
     */
    const ViewsDirectory = 'php' . DIRECTORY_SEPARATOR . 'views';
    /**
     * Директория трейтов
     */
    const TraitsDirectory = 'php' . DIRECTORY_SEPARATOR . 'traits';
    /**
     * Файлы дефолтных заголовока и подвала страницы
     */
    const DefHeaderView = GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . '_defheader.php';
    const DefFooterView = GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . '_deffooter.php';

    const ViewDefCharset = 'utf-8';
    const FOpenMode = 'wt';

    const MySqlPKVal = 'PRI';
    /**
     * Проект разрабатывается под управлением Linux - в нем PHP_EOL = \n
     * При демонстрации проекта под управлением Windows константа PHP_EOL фактически равна \n\r .
     * Поэтому функция explode(PHP_EOL, $string) может е сработать
     */
    const NewLineChar = "\n";

    /**
     * Дефолтное количество элементов на странице при просмотре справочника элементов
     */
    const DefaultDictViewItemsCount = 10;

    /**
     * Дефолтное количество элементов на стрнаице при просмотре списка элементов
     */
    const DefaultListViewItemsCount = 15;

}


