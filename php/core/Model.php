<?php
namespace Astkon\Model;

//find . -exec chown demonius:demonius {} \; -exec chmod a+rw {} \;

use Astkon\DataBase;
use Astkon\GlobalConst;
use Astkon\View\View;
use ReflectionClass;
use ReflectionProperty;

abstract class Model  {
    /**
     * Системная функция для переноса изменений структуры БД в код.
     */
    public static function UpdateModel() {
        $db = new DataBase();
        if (static::class === self::class) {
            $tables = $db->query('select `table_name` from `information_schema`.`tables` where `table_schema`=\'' . GlobalConst::DbName . '\'');
            foreach ($tables as $table) {
                $tableName = DataBase::underscoreToCamelCase($table['table_name']);
                $db->generateClass($tableName);
            }
            die();
        }
        else {
            $className = explode('\\', static::class);
            $className = $className[count($className) - 1];
            if (preg_match('Partial$', $className)) {
                $className = substr($className, 0, strlen($className) - strlen('Partial'));
            }
            $db->generateClass($className);
        }
    }

    /**
     * Метод проверяет, что метод, вызвавший проверку checkIsClassOfModel, вызван не из Model или Partial классов, а
     * из полноценной модели
     * @return bool
     */
    protected static function checkIsClassOfModel() {
        if (static::class === self::class) {
            return false;
        }
        $className = explode('\\', static::class);
        $className = $className[count($className) - 1];
        if (preg_match('/Partial$/i', $className)) {
            return false;
        }
        return true;
    }

    /**
     * Метод извлекает alias для указанного поля из DocComment
     * @param string $fieldName
     * @return string
     */
    public static function getFieldAlias(string $fieldName) {
        if (!self::checkIsClassOfModel()) {
            //Ошибка
        }
        $editedProperties = self::getEditedProperties();

        $editedProperties = array_filter($editedProperties, function(ReflectionProperty $property) use ($fieldName) {
            return $property->name === $fieldName;
        });

        /** @var ReflectionProperty $reflectionProperty */
        $reflectionProperty = array_shift($editedProperties);
        $alias = self::extractDocCommentItem($reflectionProperty, 'alias');
        $alias = self::trimDocCommentKey($alias);
        return !!$alias ? $alias : $fieldName;
    }

    /**
     * Метод возвращает набор публичных нестатичных свойств класса Partial
     * @return array
     */
    protected static function getEditedProperties() {
        $partialClassName = explode('\\', static::class);
        $partialClassName[] = $partialClassName[count($partialClassName) - 1] . 'Partial';
        $partialClassName[count($partialClassName) - 2] = 'Partial';
        $partialClassName = implode('\\', $partialClassName);

        $partialParentClass = new ReflectionClass($partialClassName);

        $editedProperties = array_filter(
            $partialParentClass->getProperties(ReflectionProperty::IS_PUBLIC),
            function(ReflectionProperty $property ) use ($partialClassName){
                if ($property->isStatic()) {
                    return false;
                }

                return $partialClassName === $property->class;
            }
        );
        $fieldsInfo = static::$fieldsInfo;
        usort($editedProperties, function(ReflectionProperty $a, ReflectionProperty $b) use ($fieldsInfo) {
            $aColumnKey = strtolower($fieldsInfo[$a->name]['column_key']);
            if ($aColumnKey === 'pri') {
                return -1;
            }
            $bColumnKey = strtolower($fieldsInfo[$b->name]['column_key']);
            if ($bColumnKey === 'pri') {
                return 1;
            }
            return 0;
        });
        return $editedProperties;
    }

    /**
     * Метод извлекаект из DocComment элемент по @key. Предполагается, что такой элемент однострочный без переносов строк.
     * @param ReflectionProperty $reflectionProperty
     * @param string $itemName
     * @return string|null
     */
    protected static function extractDocCommentItem(ReflectionProperty $reflectionProperty, string $itemName) : string{
        $items = array_filter(
            explode(PHP_EOL, $reflectionProperty->getDocComment()),
            function($line) use ($itemName) { return mb_strpos($line, '@' . $itemName) > 0; }
        );
        return count($items) > 0 ? array_shift($items) : null;
    }

    /**
     * Метод извлекает значение из DocComment-строки и отрезает @key и начальные *
     * @param string $docCommentLine
     * @return string
     */
    protected static function trimDocCommentKey(string $docCommentLine) {
        $docCommentLine = trim($docCommentLine);
        while (mb_substr($docCommentLine, 0, 1) === '*') {
            $docCommentLine = trim(mb_substr($docCommentLine, 1));
        }
        if (mb_substr($docCommentLine, 0, 1) === '@') {
            $docCommentLine = trim($docCommentLine);
            if (mb_strpos($docCommentLine, ' ') === false) {
                /*У ключа нет значения*/
                $docCommentLine = null;
            }
            else {
                $docCommentLine = trim(mb_substr($docCommentLine, mb_strpos($docCommentLine, ' ')));
            }
        }
        return $docCommentLine;
    }

    /**
     * Извлекает из DocComment все @параметры и возвращает из них массив (с @ключами)
     * @param ReflectionProperty $reflectionProperty
     * @return array
     */
    protected static function extractDocCommentParams(ReflectionProperty $reflectionProperty) {
        $_items = array_filter(
            explode(PHP_EOL, $reflectionProperty->getDocComment()),
            function($line){
                return mb_strpos($line, '@') !== false;
            }
        );
        $_items = array_map(
            function($line){
                while (mb_substr($line = trim($line), 0, 1) === '*') {
                    $line = mb_substr($line, 1);
                }
                return $line;
            },
            $_items
        );
        $_items = array_filter(
            $_items,
            function($line){
                return !!preg_match('/^@[a-z_]/i', $line);
            }
        );

        $items = array();
        array_walk($_items, function($item) use (&$items) {
            list($key, $value) = explode(' ', $item, 2);
            $items[$key] = $value;
        });
        return $items;
    }

    /**
     * Метод генерирует форму редактирования экземпляра модели
     * @param array $item
     * @param array $options
     */
    public static function EditForm($item = array(), $options = array()) {
        if (!self::checkIsClassOfModel()) {
            //Ошибка
            (new View())->error(1);//Ошибка программиста
            die();
        }

        $editedProperties = self::getEditedProperties();

        // https://getbootstrap.com/docs/4.1/components/forms/

        echo '<form action="' . (isset($options['formAction']) ? $options['formAction'] : '') . '" method="post" enctype="multipart/form-data">';
        foreach ($editedProperties as $property) {
            $propName = $property->name;
            $_prop_name = DataBase::camelCaseToUnderscore($propName);
            $docCommentParams = self::extractDocCommentParams($property);
            if (array_key_exists('@noeditable', $docCommentParams)) {
                continue;
            }
            /** @var string $alias - используется во вьюхах*/
            $alias = isset($docCommentParams['@alias']) ? $docCommentParams['@alias'] : $propName;
            /** @var mixed $value - используется во вьюхах*/
            $value = $item[$_prop_name];
            /** @var array $fieldInfo */
            $fieldInfo = static::$fieldsInfo[$property->name];
            if ($fieldInfo['column_key'] === 'PRI') {
                require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . '_form_edit_fields' . DIRECTORY_SEPARATOR . 'primary_key.php';
            }
            else  if (isset($fieldInfo['foreign_key'])){
                require getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . '_form_edit_fields' . DIRECTORY_SEPARATOR . 'foreign_key.php';

            }
            else {

                switch ($fieldInfo['data_type']) {
                    case 'bit':
                        require getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . '_form_edit_fields' . DIRECTORY_SEPARATOR . 'boolean.php';
                        break;
                    case 'int':
                    case 'tinyint':
                    case 'smallint':
                    case 'mediumint':
                    case 'bigint':
                    case 'float':
                    case 'double':
                    case 'decimal':
                        $inputType = 'number';
                        require getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . '_form_edit_fields' . DIRECTORY_SEPARATOR . 'input.php';
                        break;
                    case 'char':
                    case 'varchar':
                    case 'nvarchar':
                        $inputType = 'text';
                        require getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . '_form_edit_fields' . DIRECTORY_SEPARATOR . 'input.php';
                        break;
                    case 'text':
                    case 'tinytext':
                    case 'mediumtext':
                    case 'longtext':
                        require getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . '_form_edit_fields' . DIRECTORY_SEPARATOR . 'textarea.php';
                        break;
                    case 'json':
                        require getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . '_form_edit_fields' . DIRECTORY_SEPARATOR . 'json.php';
                        break;
                }
            }
        }
            require getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . '_form_edit_fields' . DIRECTORY_SEPARATOR . 'submit.php';
        echo '</form>';
    }


    protected $fields = array();
    /**
     * @var string
     */
    protected $entityName = null;
    /**
     * @var \PDOStatement
     */
    protected $db = null;
    protected $extLinks = null;

    public static $regexp = array(
        'safeSQLValue' => '/[\'\\`*\\/\\\\]+/i'
    );
    /**
     * Список полей, значения которых в БД хранятся в виде base64-кодированной строки
     * @var array
     */
    protected $base64Keys = array();

    /*
     * Якоря - если существует хоть одна внешняя связь с объектом типа, указанного
     * в данном массиве, то текущий объект не может быть удален.
     * Типы указываются в нижнем регистре.
     * Это используется для того, чтобы сохранять целостность данных: например,
     * если с лотом связаны финансовые данные, то такой лот нельзя удалять из БД.
     */
    protected $anchors = array(
        /*'table_name' => 'table_name'*/
    );

    /**
     * @param array $fields
     * @param string $entityName
     * @return StandPrototype|mixed
     */
    public static function CreateInstance(array $fields, string $entityName) {
        if (class_exists($entityName)) {
            return new $entityName($fields, $entityName);
        }
        else {
            return new StandPrototype($fields, $entityName);
        }

    }

    protected static function clearValueForSQL($val) {
        return preg_replace(self::$regexp['safeSQLValue'], '', $val);
    }

    /**
     * StandPrototype constructor.
     * @param array $fields - массив полей нового объекта
     * @param string $entityName - наименование объекта (наименование таблицы БД, реализующей объект). Регистр имеет значение
     */
    public function __construct(array $fields, string $entityName) {
        $this->fields = $fields;
        $this->entityName = $entityName;
    }

    /**
     * Метод проверяет, нет ли связанных объектов, из-за которых удаление текущего объкта невозможно
     * @param \PDOStatement|null $db
     * @return bool
     */
    public function CanDelete(\PDOStatement $db = null) {
        /*
         * Чтобы не создавать множество новых экземпляров подключения к БД,
         * будем по возможности передавать одно подключение всем связанным объектам -
         * это поможет сэкономить время
         */
        $this->_setDBConnection($db);
        $_self = $this;
        /*Получим все внешние объекты, связанные с текущим, если данная операция еще не была произведена*/
        $this->extLinks === null && ($this->extLinks = $this->getExternalLinks($_self->db));
        return (new linq($this->extLinks))->first(function($extLink) use ( $_self) {
            return array_key_exists(strtolower($extLink->ObjType()), $_self->anchors) || $extLink->CanDelete($_self->db) === FALSE;
        }) !== null ? FALSE : TRUE;
    }

    /**
     * @param null|\PDOStatement $db
     * @return bool
     */
    public function Delete(\PDOStatement $db = null) {
        /*
         * Чтобы не создавать множество новых экземпляров подключения к БД,
         * будем по возможности передавать одно подключение всем связанным объектам -
         * это поможет сэкономить время
         */
        $this->_setDBConnection($db);
        if ($this->CanDelete() !== false) {
            $entityName = $this->entityName;
            $this->db->$entityName->Delete($this->fields);
            $_self = $this;
            (new linq($this->extLinks))->for_each(function($extLink) use ($_self){
                $extLink->Delete($_self->db);
            });
            return true;
        }
        return false;
    }

    /**
     * @param null|\PDOStatement $db
     */
    protected function _setDBConnection(\PDOStatement $db = null) {
        !$this->db && ($this->db = ($db ? $db : new DataBase(
            GlobalVars::$host,
            GlobalVars::$dbName,
            GlobalVars::$hostUser,
            GlobalVars::$hostPass
        )));

    }

    /**
     * @return bool
     */
    public function Insert() {
        $this->_setDBConnection();

        if (($fields = $this->_beforeInsert($this->fields)) !== false) {
            $entName = $this->entityName;
            $this->fields = $this->_base64Decode($this->db->$entName->Insert($this->_base64Encode($fields)));
            return true;
        }
        return false;

    }

    /**
     * Метод позволяет в зависимости от конкретного типа
     * обработать поля или остановить дальнейшее выполнение
     * процедуры, если обнаружены существенные ошибки в данных
     * @param array $fields
     * @return mixed
     */
    protected function _beforeInsert(array $fields) {
        return $fields;
    }

    /**
     * @return bool
     */
    public function Update() {
        $this->_setDBConnection();
        if (($fields = $this->_beforeUpdate($this->fields)) !== false) {
            $entName = $this->entityName;
            $this->fields = $this->_base64Decode($this->db->$entName->Update($this->_base64Encode($fields)));
            return true;
        }
        return false;
    }

    /**
     * Метод позволяет в зависимости от конкретного типа
     * обработать поля или остановить дальнейшее выполнение
     * процедуры, если обнаружены существенные ошибки в данных
     * @param array $fields
     * @return mixed
     */
    protected function _beforeUpdate(array $fields) {
        return $fields;
    }

    /**
     * @return string
     */
    public function ObjType () {
        return $this->entityName;
    }

    /**
     * @return array
     */
    public function getFields() {
        return $this->fields;
    }

    /**
     * @param null|\PDOStatement $db
     * @return array
     */
    public function getExternalLinks(\PDOStatement $db = null) {
        $_self = $this;
        $res = array();
        !$this->db && ($this->db = ($db ? $db : new DataBase(
            GlobalVars::$host,
            GlobalVars::$dbName,
            GlobalVars::$hostUser,
            GlobalVars::$hostPass
        )));
        $query = 'SELECT `REFERENCED_COLUMN_NAME`,`TABLE_NAME`,`COLUMN_NAME` FROM '
            . 'INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE `REFERENCED_TABLE_SCHEMA`=\'' . GlobalVars::$dbName . '\' '
            . 'and `REFERENCED_TABLE_NAME`=\'' . $this->entityName . '\'';
        (new linq($this->db->query($query)))
            ->where(function($row){ return $row !== null && count($row) > 0; })
            ->for_each(function($row) use ($_self, &$res){
                $extLinkType = $row['TABLE_NAME'];
                $key = $row['REFERENCED_COLUMN_NAME'];
                $extLinks = $_self->db->$extLinkType->getObjects('`' . $row['COLUMN_NAME'] . '`=' . $_self->$key);
                $i = 0;
                $c = count($extLinks);
                while($i < $c) {
                    $res[] = $extLinks[$i++];
                }
            });
        return $res;
    }

    /**
     * @param array $fields
     * @return array
     */
    protected function _base64Encode (array $fields) {
        foreach ($this->base64Keys as $key) {
            if (array_key_exists($key, $fields)) {
                $fields[$key] && ($fields[$key] = base64_encode($fields[$key]));
            }
        }
        return $fields;
    }

    /**
     * @param $fields
     * @return mixed
     */
    protected function _base64Decode ($fields) {
        foreach ($this->base64Keys as $key) {
            if (array_key_exists($key, $fields)) {
                $fields[$key] && ($fields[$key] = base64_decode($fields[$key]));
            }
        }
        return $fields;
    }

    public function Base64EncodeFields() {
        $this->fields = $this->_base64Encode($this->fields);
    }

    public function Base64DecodeFields() {
        $this->fields = $this->_base64Decode($this->fields);
    }

    public function __get($name) {
        return array_key_exists($name, $this->fields) ? $this->fields[$name] : null;
    }
    public function __set($name, $value) {
        $this->fields[$name] = $value;
    }



}