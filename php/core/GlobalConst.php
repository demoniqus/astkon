<?php

namespace Astkon;

require_once 'DataBaseGlobalConst.php';
/*Глобальные константы*/
final class GlobalConst extends  DataBaseGlobalConst {
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