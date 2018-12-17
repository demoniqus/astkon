<?php
namespace Astkon;


/*
 * Главный файл с подключаемыми модулями
 */
/*
 * Устанавливаем кодировку данных
 */
header('Content-Type: text/html; charset=utf-8');
require_once 'core/Model.php';
require_once 'core/Controller.php';
require_once 'core/View.php';
require_once 'base64.php';
require_once 'db.php';
require_once 'linq.php';
require_once 'types_processor.php';
require_once 'file_system.php';
require_once 'url.php';
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
    const HostUser = 'root';
    const HostPass = '1';
    /**
     * Наименование параметра, определяющего тип запроса - получение данных или получение html-страницы
     * @var string
     */
    const DataModeKey = 'mode';
    /**
     * Наименование параметра, определяющего запрашиваемые данные
     * @var string
     */
    const DataKeyKey = 'datakey';
    /**
     * Наименование параметра, определяющего запрашиваемую html-страницу
     * @var string
     */
    const RequiredPageKey = 'page';
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
    /**
     * Каталог для создания частичных классов, реализующих сущности базы данных
     * @var string
     */
    const PartialClassDirectory = 'php' . DIRECTORY_SEPARATOR . 'partialModels';
    /**
     * Каталог для классов, реализующих частичные классы
     * @var string
     */
    const ClassDirectory = 'php' . DIRECTORY_SEPARATOR . 'models';
    /**
     * Файл для регистрации моделей, реализующих сущности базы данных
     */
    const ClassRegistry = 'php' . DIRECTORY_SEPARATOR . 'models.php';
    /**
     * Директория представлений
     */
    const ViewsDirectory = 'php' . DIRECTORY_SEPARATOR . 'views';
    const DefHeaderView = GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . '_defheader.php';
    const DefFooterView = GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . '_deffooter.php';
    const ViewDefCharset = 'utf-8';
    const FOpenMode = 'wt';

}


