<?php
/**
 * Created by PhpStorm.
 * User: demonius
 * Date: 28.12.18
 * Time: 11:53
 */

namespace Astkon\Traits;

use Astkon\DataBase;
use Astkon\ErrorCode;
use Astkon\GlobalConst;
use Astkon\linq;
use Astkon\View\View;
use Exception;
use ReflectionClass;
use ReflectionProperty;

trait ModelUpdate
{
    protected static $PartialSuffix = 'Partial';
    protected static $KEY__FOREIGN_KEY = 'foreign_key';
    /**
     * Метод обновляет код моделей.
     * Если метод вызван на базовом классе Model, будут обновлены все модели, существующие в БД.
     * При этом не будут удалены классы моделей, которых уже нет в БД
     * Если метод вызван на потомке класса Model (в т.ч. и Partial-потомке), будет обновлена его модель
     * Если передан параметр $className, будет обновлена соответствующая модель независимо от точки вызова данного метода
     * @param null|string $className
     */
    public static function UpdateModelPhpCode($className = null) {
        $db = new DataBase();
        if (static::class === self::class) {
            $tables = $db->query('select `table_name` from `information_schema`.`tables` where `table_schema`=\'' . GlobalConst::DbName . '\'');
            foreach ($tables as $table) {
                $tableName = DataBase::underscoreToCamelCase($table['table_name']);
                self::generateClass($tableName, $db);
            }
        }
        else {
            $className = explode('\\', !empty($className) ? $className : static::class);
            $className = $className[count($className) - 1];
            if (preg_match('/' . self::$PartialSuffix . '$/i', $className)) {
                $className = substr($className, 0, strlen($className) - strlen(self::$PartialSuffix));
            }
            self::generateClass(DataBase::underscoreToCamelCase($className), $db);
        }
    }

    /**
     * Метод генерирует Partial-класс указанной сущности из БД. Используется для разработки.
     * @param string $className Имя таблицы из БД в CamelCase, для которой необходимо сгенерировать базовый класс для
     *                          использования в PHP
     * @param DataBase $db
     */
    private static function generateClass(string $className, DataBase $db) {
        echo 'Обновляется модель ' . $className . PHP_EOL;
        $className = DataBase::underscoreToCamelCase($className);

        $tableName = DataBase::camelCaseToUnderscore($className);

        echo 'Формируем Partial-модель ' . PHP_EOL;
        self::generatePartialModel($className, $tableName, $db);
        echo 'Формирование завершено ' . PHP_EOL;

        echo 'Формируем модель ' . PHP_EOL;
        self::generateModel($className, $db);
        echo 'Формирование завершено ' . PHP_EOL;

        echo 'Регистрируем модель ' . PHP_EOL;
        self::registerModel($className);
        echo 'Регистрация завершена ' . PHP_EOL;
        echo 'Обновление модели завершено ' . PHP_EOL;
    }

    /**
     * @param string $className
     * @return array
     */
    private static function getPartialModelFieldsDocBlock(string $className) {
        $fieldsDocs = array();
        if (class_exists($className)) {
            $reflectClass = new ReflectionClass($className);
            foreach ($reflectClass->getProperties(ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
                if ($reflectionProperty->isStatic()) {
                    continue;
                }

                $doc = array_filter(
                    explode( GlobalConst::NewLineChar, $reflectionProperty->getDocComment() ?? ''),
                    function($docLine){ return !array_key_exists(trim($docLine), array('/**' => true, '*/' => true));}
                );

                array_walk($doc, function(&$line){
                    $line = trim($line);
                    if (mb_substr($line, 0, 1) === '*') {
                        $line = trim(mb_substr($line, 1));
                    }
                });

                $fieldsDocs[$reflectionProperty->name] = array(
                    'doc' => $doc
                );
            }
        }
        return $fieldsDocs;
    }

    /**
     * @param resource $fileHandler
     */
    private static function PartialModelInstruction($fileHandler) {
        fwrite($fileHandler, '/** ');
        fwrite($fileHandler,  PHP_EOL);
        fwrite($fileHandler, ' * Файл генерируется автоматически.');
        fwrite($fileHandler,  PHP_EOL);
        fwrite($fileHandler, ' * Не допускаются произвольные изменения вручную.');
        fwrite($fileHandler,  PHP_EOL);
        fwrite($fileHandler, ' * Допускается вручную расширять doc-блок публичный полей класса. ');
        fwrite($fileHandler,  PHP_EOL);
        fwrite($fileHandler, ' * При этом разделы @var и @database_column_name будут автоматически перезаписываться.');
        fwrite($fileHandler,  PHP_EOL);
        fwrite($fileHandler, ' * Допускается вручную расширять foreign_key в $fieldsInfo. ');
        fwrite($fileHandler,  PHP_EOL);
        fwrite($fileHandler, ' * При этом ключи model и field изменять не допускается - при обновлении модели в случае их изменения может быть утрачена прочая информация');
        fwrite($fileHandler, ' */');
        fwrite($fileHandler,  PHP_EOL);

    }

    /**
     * Метод генерирует базовую модель сущности БД
     * @param string $className
     * @param string $tableName
     * @param DataBase $db
     * @throws Exception
     */
    private static function generatePartialModel(string $className, string $tableName, DataBase $db) {
        $partialModelFileName = getcwd() . DIRECTORY_SEPARATOR .
            GlobalConst::PartialModelsDirectory . DIRECTORY_SEPARATOR .
            $className . self::$PartialSuffix . '.php';

        $partialModelNS = self::getRootNameSpace() . '\\Model\\' . self::$PartialSuffix;
        $partialModelName = $className . self::$PartialSuffix;
        $partialClassName = $partialModelNS . '\\' . $partialModelName;

        $foreignKeys = self::getColumnsForeignKeys($partialClassName);


        $fieldsDoc = self::getPartialModelFieldsDocBlock($partialClassName);

        $partialHandle = fopen(
            $partialModelFileName,
            'wt'
        );

        fwrite($partialHandle, '<?php ');
        fwrite($partialHandle,  PHP_EOL . PHP_EOL);
        self::PartialModelInstruction($partialHandle);
        fwrite($partialHandle,  PHP_EOL . PHP_EOL);
        fwrite($partialHandle, 'namespace ' . $partialModelNS . ';');
        fwrite($partialHandle,  PHP_EOL . PHP_EOL);
        fwrite($partialHandle, 'use ' . self::getRootNameSpace() . '\\Model\\Model;');
        fwrite($partialHandle,  PHP_EOL . PHP_EOL);
        fwrite($partialHandle, 'abstract class ' . $partialModelName . ' extends Model {');
        fwrite($partialHandle,  PHP_EOL);
        fwrite($partialHandle, "\t" . 'const DataTable = \'' . $tableName . '\';');
        fwrite($partialHandle,  PHP_EOL);

        $columns = $db->getClassColumns($className);

        $primaryColumn = (new linq($columns))->first(function($column){ return $column['column_key'] === GlobalConst::MySqlPKVal;});
        fwrite($partialHandle, "\t" . 'const PrimaryColumnName = ' . (
            $primaryColumn ?
                '\'' .  DataBase::underscoreToCamelCase($primaryColumn['column_name']) . '\'' :
                'null'
            ) . ';');
        fwrite($partialHandle,  PHP_EOL);
        fwrite($partialHandle, "\t" . 'const PrimaryColumnKey = ' . (
            $primaryColumn ?
                '\'' .  $primaryColumn['column_name'] . '\'' :
                'null'
            ) . ';');

        fwrite($partialHandle,  PHP_EOL . PHP_EOL);

        $columns = (new linq($columns))->toAssoc(function($column){ return DataBase::underscoreToCamelCase($column['column_name']);})->getData();

        $columns = self::setColumnsForeignKeys($columns, $className, $db, $foreignKeys);

        $columns = self::setColumnsExtLinks($columns, $className, $db);

        fwrite($partialHandle, "\t" . '/** ');
        fwrite($partialHandle,  PHP_EOL);
        fwrite($partialHandle, "\t" . '* Параметр описывает свойства колонок таблиц БД. ');
        fwrite($partialHandle,  PHP_EOL);
        fwrite($partialHandle, "\t" . '* Все наименования колонок следует задавать в under_score стиле. ');
        fwrite($partialHandle,  PHP_EOL);
        fwrite($partialHandle, "\t" . '* В camelCase стиле задаются только ключи верхнего уровня. ');
        fwrite($partialHandle,  PHP_EOL);
        fwrite($partialHandle, "\t" . '* @var array');
        fwrite($partialHandle,  PHP_EOL);
        fwrite($partialHandle, "\t" . '*/');
        fwrite($partialHandle,  PHP_EOL);
        fwrite($partialHandle, 'protected static $fieldsInfo = ' . var_export($columns, true) . ';');
        fwrite($partialHandle,  PHP_EOL);

        $columns = array_values($columns);

        uasort($columns, function($a, $b){ return $a['column_name'] <=> $b['column_name'];});

        array_walk($columns, function($line) use ($partialHandle, $fieldsDoc){
            $_column_name = $line['column_name'];
            $columnName = DataBase::underscoreToCamelCase($_column_name);
            if (isset($fieldsDoc[$columnName])) {
                /*Изменяем уже существующий docComment*/
                $var = '';
                $databaseColumnName = $_column_name;
                switch ($line['data_type']) {
                    case 'int':
                    case 'year':
                    case 'bigint':
                    case 'mediumint':
                    case 'smallint':
                    case 'tinyint':
                        $var = 'int';
                        break;
                    case 'decimal':
                    case 'dec':
                    case 'double':
                    case 'float':
                    case 'real':
                        $var = 'float';
                        break;
                    case 'char':
                    case 'varchar':
                    case 'nvarchar':
                    case 'text':
                    case 'tinytext':
                    case 'mediumtext':
                        $var = 'string';
                        break;
                    case 'tinyint(1)':
                    case 'bit':
                        $var = 'bool';
                        break;
                    case 'json':
                        $var = 'array';
                        break;
                    case 'datetime':
                    case 'date':
                        $var = 'DateTime';
                        break;
                }

                fwrite($partialHandle, "\t" . '/**');
                fwrite($partialHandle,  PHP_EOL);
                foreach ($fieldsDoc[$columnName]['doc'] as $line){
                    if (mb_strpos($line, '@var') === 0) {
                        $line = '@var ' . $var;
                    }
                    else if (mb_strpos($line, '@database_column_name') === 0) {
                        $line = '@database_column_name ' . $databaseColumnName;
                    }
                    fwrite($partialHandle, "\t" . '* ' . $line);
                    fwrite($partialHandle,  PHP_EOL);
                };
                fwrite($partialHandle, "\t" . '*/');
                fwrite($partialHandle,  PHP_EOL);
            }
            else {
                /*Пишем новый docComment*/
                fwrite($partialHandle, "\t" . '/**' . PHP_EOL);
                fwrite($partialHandle, "\t" . '* @database_column_name ' . $_column_name . PHP_EOL);
                fwrite($partialHandle, "\t" . '* @caption' . PHP_EOL);
                switch ($line['data_type']) {
                    case 'int':
                    case 'year':
                    case 'bigint':
                    case 'mediumint':
                    case 'smallint':
                    case 'tinyint':
                        fwrite($partialHandle, "\t" . '* @var int');
                        break;
                    case 'decimal':
                    case 'dec':
                    case 'double':
                    case 'float':
                    case 'real':
                        fwrite($partialHandle, "\t" . '* @var float');
                        break;
                    case 'char':
                    case 'varchar':
                    case 'nvarchar':
                    case 'text':
                    case 'tinytext':
                    case 'mediumtext':
                        fwrite($partialHandle, "\t" . '* @var string');
                        break;
                    case 'tinyint(1)':
                    case 'bit':
                        fwrite($partialHandle, "\t" . '* @var bool');
                        break;
                    case 'json':
                        fwrite($partialHandle, "\t" . '* @var array');
                        break;
                    case 'datetime':
                    case 'date':
                        fwrite($partialHandle, "\t" . '* @var DateTime');
                        break;
                }
                fwrite($partialHandle, PHP_EOL . "\t*/" . PHP_EOL);
            }

            fwrite($partialHandle, "\t" . 'public $' . $columnName . ';');
            fwrite($partialHandle,  PHP_EOL . PHP_EOL);
        });

        fwrite($partialHandle, '}' . PHP_EOL);
        fclose($partialHandle);
    }

    private static function getColumnsForeignKeys(string $model) : array {
        $keys = array();
        if (class_exists($model)) {
            $reflectClass = new ReflectionClass($model);
            foreach ($reflectClass->getProperties(ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_STATIC) as $reflectionProperty) {
                if (!$reflectionProperty->isStatic()) {
                    continue;
                }
                if (strtolower($reflectionProperty->name) != 'fieldsinfo') {
                    continue;
                }

                $reflectionProperty->setAccessible(true);

                $fieldsInfo = $reflectionProperty->getValue();

                foreach ($fieldsInfo as $fieldName => $fieldInfo) {
                    if (!array_key_exists(static::$KEY__FOREIGN_KEY, $fieldInfo)) {
                        continue;
                    }
                    $keys[$fieldName] = $fieldInfo[static::$KEY__FOREIGN_KEY];
                }
                $reflectionProperty->setAccessible(false);
            }
        }
        return $keys;
    }

    private static function setColumnsForeignKeys(array $columns, string $className, DataBase $db, array $foreignKeys) {
        $references = $db->query('select '
            . '`table_name`, '
            . '`column_name`, '
            . '`referenced_table_name`, '
            . '`referenced_column_name`  '
            . ' from `information_schema`.`key_column_usage` where table_schema = \'' . GlobalConst::DbName .
            '\' AND table_name=\'' . DataBase::camelCaseToUnderscore($className) . '\' AND `referenced_column_name` IS NOT NULL'
        );
        foreach ($references as $reference) {
            $fieldName = DataBase::underscoreToCamelCase($reference['column_name']);
            $fkData = array();
            if (
                isset($foreignKeys[$fieldName]) &&
                $foreignKeys[$fieldName]['model'] === $reference['referenced_table_name'] &&
                $foreignKeys[$fieldName]['field'] === $reference['referenced_column_name']
            ) {
                $fkData = $foreignKeys[$fieldName];
            }
            $fkData['model'] = $reference['referenced_table_name'];
            $fkData['field'] = $reference['referenced_column_name'];

            $columns[DataBase::underscoreToCamelCase($reference['column_name'])][static::$KEY__FOREIGN_KEY] = $fkData;
        }
        return $columns;
    }

    private static function setColumnsExtLinks(array $columns, string $className, DataBase $db) {
        $extLinks = $db->query('select '
            . '`table_name`, '
            . '`column_name`, '
            . '`referenced_table_name`, '
            . '`referenced_column_name`  '
            . ' from `information_schema`.`key_column_usage` where table_schema = \'' . GlobalConst::DbName .
            '\' AND referenced_table_name=\'' . DataBase::camelCaseToUnderscore($className) . '\''
        );
        foreach ($extLinks as $extLink) {
            $column = &$columns[DataBase::underscoreToCamelCase($extLink['referenced_column_name'])];
            if (!array_key_exists('external_link', $column)) {
                $column['external_link'] = array();
            }
            $column['external_link'][$extLink['table_name']] = array(
                'model' => $extLink['table_name'],
                'field' => $extLink['column_name'],
            );
        }
        return $columns;
    }

    /**
     * Метод формирует php-класс для использования в коде проекта
     * @param string $className
     * @param DataBase $db
     */
    private static function generateModel(string $className, DataBase $db) {
        $classFileName = getcwd() . DIRECTORY_SEPARATOR .
            GlobalConst::ModelsDirectory. DIRECTORY_SEPARATOR .
            $className . '.php';
        if (!file_exists($classFileName)) {
            $classFileHandle = fopen($classFileName, 'wt');

            $partialClassFileName = $className . self::$PartialSuffix . '.php';
            fwrite($classFileHandle, '<?php');
            fwrite($classFileHandle, PHP_EOL);
            fwrite($classFileHandle, 'namespace ' . self::getRootNameSpace() . '\\Model;');
            fwrite($classFileHandle, PHP_EOL . PHP_EOL);
            fwrite($classFileHandle, 'use  ' . self::getRootNameSpace() . '\\GlobalConst;');
            fwrite($classFileHandle, PHP_EOL);
            fwrite($classFileHandle, 'use  ' . self::getRootNameSpace() . '\\DataBase;');
            fwrite($classFileHandle, PHP_EOL);
            fwrite($classFileHandle, 'use  ' . self::getRootNameSpace() . '\\Model\\' . self::$PartialSuffix . '\\' . ucfirst($className) . self::$PartialSuffix . ';');
            fwrite($classFileHandle, PHP_EOL . PHP_EOL);
            fwrite($classFileHandle, 'require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::PartialModelsDirectory . DIRECTORY_SEPARATOR . \'' . $partialClassFileName . '\';');
            fwrite($classFileHandle, PHP_EOL . PHP_EOL);
            fwrite($classFileHandle, '/**');
            fwrite($classFileHandle, PHP_EOL);
            fwrite($classFileHandle, '* В этом классе реализуются все особенности поведения и строения соответствующего типа');
            fwrite($classFileHandle, PHP_EOL);
            fwrite($classFileHandle, '*/');
            fwrite($classFileHandle, PHP_EOL . PHP_EOL);
            fwrite($classFileHandle, 'class ' . ucfirst($className) . ' extends ' . ucfirst($className) . 'Partial {');
            fwrite($classFileHandle, PHP_EOL . PHP_EOL);
            fwrite($classFileHandle, "\t" . 'public function __construct (array $fields = array()) {');
            fwrite($classFileHandle, PHP_EOL);
            fwrite($classFileHandle, "\t\t" . 'parent::__construct($fields, DataBase::camelCaseToUnderscore(__CLASS__));');
            fwrite($classFileHandle, PHP_EOL);
            fwrite($classFileHandle, "\t" . '}');
            fwrite($classFileHandle, PHP_EOL);
            fwrite($classFileHandle, '}');
            fwrite($classFileHandle, PHP_EOL);
            fclose($classFileHandle);

        }
        else {
            $classFileHandle = fopen($classFileName, 'r+t');
            if (gettype($classFileHandle) === gettype(true)) {
                $view = new View();
                $view->trace(array(
                    'message' => 'Не удалось открыть файл ' . $classFileName
                ));
                $view->error(ErrorCode::PROGRAMMER_ERROR);
            }
            $lines = [];
            while (!feof($classFileHandle)) {
                $line = fgets($classFileHandle);
                if (preg_match('/class\s+' . ucfirst($className) . '/', $line)) {
                    $lines[] = 'class ' . ucfirst($className) . ' extends ' . ucfirst($className) . self::$PartialSuffix . ' {' . PHP_EOL;
                }
                else {
                    $lines[] = $line;
                }
            }
            fclose($classFileHandle);
            file_put_contents($classFileName, $lines);
        }
    }

    private static function registerModel(string $className) {
        /*Регистрируем созданный класс*/
        $classRegisterHandle = fopen(getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ModelsRegistry, 'r+t');
        if (strpos(fgets($classRegisterHandle), '<?php') === false) {
            fwrite($classRegisterHandle, '<?php' . PHP_EOL);
        }
        /*Ищем информацию о том, что файл уже зарегистрирован*/
        $newline = 'require_once GlobalConst::ModelsDirectory . DIRECTORY_SEPARATOR . \'' . $className . '.php\';';
        while (trim($line = fgets($classRegisterHandle)) !== $newline) {
            if(feof($classRegisterHandle)) {
                fwrite($classRegisterHandle, $newline . PHP_EOL);
                break;
            }
        }
        fclose($classRegisterHandle);
    }

    private static function getRootNameSpace() : string
    {
        return explode('\\', __NAMESPACE__)[0];
    }
}