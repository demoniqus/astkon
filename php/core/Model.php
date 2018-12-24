<?php
namespace Astkon\Model;

//find . -exec chown demonius:demonius {} \; -exec chmod a+rw {} \;

use Astkon\DataBase;
use Astkon\ErrorCode;
use Astkon\GlobalConst;
use Astkon\View\View;
use ReflectionClass;
use ReflectionProperty;

abstract class Model  {
    const ValidStateError = 0;
    const ValidStateOK = 1;
    const ValidStateUndefined = 2;
    /**
     * Системная функция для переноса изменений структуры БД в код.
     */
    public static function UpdateModelPhpCode() {
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
            if (preg_match('/Partial$/', $className)) {
                $className = substr($className, 0, strlen($className) - strlen('Partial'));
            }
            $db->generateClass($className);
        }
    }

    public static function PKName() {
        if (!self::checkIsClassOfModel()) {
            $view = new View();
            $view->trace = nl2br(
                'Файл ' . __FILE__ . PHP_EOL .
                'Класс ' . __CLASS__ . PHP_EOL .
                'Метод ' . __METHOD__ . PHP_EOL .
                'Строка ' . __LINE__ . PHP_EOL .
                'Метод может быть вызван только из класса модели, а не ее родительских классов'
            );
            $view->error(ErrorCode::PROGRAMMER_ERROR);
            die();
        }
        $PK = array_filter(static::$fieldsInfo, function($item){
            return strtoupper($item['column_key']) === GlobalConst::MySqlPKVal;
        });
        $PK = array_keys($PK);
        return array_pop($PK);
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
            $view = new View();
            $view->trace = nl2br(
                'Файл ' . __FILE__ . PHP_EOL .
                'Класс ' . __CLASS__ . PHP_EOL .
                'Метод ' . __METHOD__ . PHP_EOL .
                'Строка ' . __LINE__ . PHP_EOL .
                'Метод может быть вызван только из класса модели, а не ее родительских классов'
            );
            $view->error(ErrorCode::PROGRAMMER_ERROR);
            die();
        }
        $editedProperties = self::getModelPublicProperties();

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
    protected static function getModelPublicProperties() {
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
            $aColumnKey = strtoupper($fieldsInfo[$a->name]['column_key']);
            if ($aColumnKey === GlobalConst::MySqlPKVal) {
                return -1;
            }
            $bColumnKey = strtoupper($fieldsInfo[$b->name]['column_key']);
            if ($bColumnKey === GlobalConst::MySqlPKVal) {
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
            $segments = explode(' ', $item, 2);
            if (count($segments) < 2) {
                $segments[] = null;
            }
            list($key, $value) = $segments;
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
            $view = new View();
            $view->trace = nl2br(
                'Файл ' . __FILE__ . PHP_EOL .
                'Класс ' . __CLASS__ . PHP_EOL .
                'Метод ' . __METHOD__ . PHP_EOL .
                'Строка ' . __LINE__ . PHP_EOL .
                'Метод может быть вызван только из класса модели, а не ее родительских классов'
            );
            $view->error(ErrorCode::PROGRAMMER_ERROR);
            die();
        }

        $editedProperties = self::getModelPublicProperties();

        $isFormProcessed = is_array($options) && isset($options['validation']);

        // https://getbootstrap.com/docs/4.1/components/forms/

        echo '<form action="' . (isset($options['formAction']) ? $options['formAction'] : '') . '" method="post" enctype="multipart/form-data">';
        if ($isFormProcessed && isset($options['validation']['message'])) {
            $validationFormClass = 'alert-secondary';
            if (isset($options['validation']['state'])) {
                switch ($options['validation']['state']) {
                    case Model::ValidStateOK:
                        $validationFormClass = 'alert-success';
                        break;
                    case Model::ValidStateError:
                        $validationFormClass = 'alert-danger';
                        break;
                }
            }
            $validationFormMessage = $options['validation']['message'];
            require getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . '_form_edit_fields' . DIRECTORY_SEPARATOR . 'common_message.php';
        }

        foreach ($editedProperties as $property) {
            $propName = $property->name;
            $docCommentParams = self::extractDocCommentParams($property);
            if (
                array_key_exists('@noeditable', $docCommentParams) ||
                array_key_exists('@autocalc', $docCommentParams)

            ) {
                continue;
            }
            /** @var string $alias - используется во вьюхах*/
            $alias = isset($docCommentParams['@alias']) ? $docCommentParams['@alias'] : $propName;
            /** @var mixed $value - используется во вьюхах*/
            $value = $item[$propName];

            $validMessage = null;
            $validState = self::ValidStateUndefined;
            if ($isFormProcessed) {
                if (isset($options['validation']['fields'][$propName])) {
                    $validState = $options['validation']['fields'][$propName]['state'];
                    $validMessage = isset($options['validation']['fields'][$propName]['message']) ?
                        trim($options['validation']['fields'][$propName]['message']) :
                        null;
                }
            }

            /** @var array $fieldInfo */
            $fieldInfo = static::$fieldsInfo[$property->name];
            if ($fieldInfo['column_key'] === GlobalConst::MySqlPKVal) {
                require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . '_form_edit_fields' . DIRECTORY_SEPARATOR . 'primary_key.php';
            }
            else  if (isset($fieldInfo['foreign_key'])){
                $fk = $fieldInfo['foreign_key'];
                $model = $fk['model'];
                $refModel = explode('\\', __CLASS__);
                $refModel[count($refModel) - 1] = DataBase::underscoreToCamelCase($model);
                $refItem = (new DataBase())->$model->getFirstRow(
                    $fk['field'] . ' = :' . $fk['field'],
                    call_user_func(implode('\\', $refModel) . '::getReferenceDisplayedKeys'),
                    array($fk['field'] => intval($value))
                );
                $displayValue = is_array($refItem) ? implode(' ', $refItem) : '';
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
        //https://getbootstrap.com/docs/4.1/components/forms/#validation   - проверка формы
    }

    public static function getReferenceDisplayedKeys() : array {
        $editedProperties = self::getModelPublicProperties();
        $editedProperties = array_map(function (ReflectionProperty $reflectionProperty){
            $docParams = static::extractDocCommentParams($reflectionProperty);
            if (!array_key_exists('@foreign_key_display_value', $docParams)) {
                return null;
            }
            return array(
                'key' => $reflectionProperty->name,
                'order' => intval($docParams['@foreign_key_display_value'])
            );
        }, $editedProperties);
        $editedProperties = array_filter($editedProperties, function($item) {return !is_null($item);});
        usort($editedProperties, function($a, $b){ return $a['order'] <=> $b['order']; });
        $editedProperties = array_map(function($prop){ return $prop['key']; }, $editedProperties);
        if (count($editedProperties) === 0) {
            $editedProperties = static::PKName();
        }
        return $editedProperties;
    }

    /**
     * Метод сохраняет данные в БД.
     * В случае успеха возвращает true В случае ошибки возвращает массив с информацией об ошибке
     * @return array|bool
     */
    public function Save() {
        $editedProperties = self::getModelPublicProperties();
        $values = array();
        foreach ($editedProperties as $referenceProperty) {
            $propName = $referenceProperty->name;
            $values[$propName] = $this->$propName;
        }
        $result = self::SaveInstance($values);
        if (array_key_exists('@error', $result)) {
            return $result;
        }

        foreach ($result as $fieldKey => $fieldVal) {
            $this->$fieldKey = $fieldVal;
        }
        return true;
    }

    /**
     * Метод сохраняет данные в БД.
     * Возвращает массив - либо поля сохраненного объекта, либо информаццию об ошибке (при наличии ключа @error => true)
     * @param array $values
     * @return array
     */
    public static function SaveInstance(array $values) {
        if (!self::checkIsClassOfModel()) {
            $view = new View();
            $view->trace = nl2br(
                'Файл ' . __FILE__ . PHP_EOL .
                'Класс ' . __CLASS__ . PHP_EOL .
                'Метод ' . __METHOD__ . PHP_EOL .
                'Строка ' . __LINE__ . PHP_EOL .
                'Метод может быть вызван только из класса модели, а не ее родительских классов'
            );
            $view->error(ErrorCode::PROGRAMMER_ERROR);
            die();
        }
        $primaryFieldInfo = array_filter(
            static::$fieldsInfo,
            function($fieldInfo){
                return strtoupper($fieldInfo['column_key']) === GlobalConst::MySqlPKVal;
            }
        );
        $PKName = array_keys($primaryFieldInfo)[0];
        if (!array_key_exists($PKName, $values)) {
            $view = new View();
            $view->trace = nl2br(
                'Файл ' . __FILE__ . PHP_EOL .
                'Класс ' . __CLASS__ . PHP_EOL .
                'Метод ' . __METHOD__ . PHP_EOL .
                'Строка ' . __LINE__ . PHP_EOL .
                'Не передано значение первичного ключа для обновления записи в БД'
            );
            $view->error(ErrorCode::PROGRAMMER_ERROR);
            die();
        }
        if (!$values[$PKName]) {
            $values[$PKName] = 0;
        }
        $PKVal = $values[$PKName];
        if (intval($PKVal) != $PKVal) {
            throw new \Exception('Недопустимое значение первичного ключа');
        }
        $PKVal = intval($PKVal);
        if ($PKVal < 0) {
            throw new \Exception('Недопустимое значение первичного ключа');
        }
        $fieldsInfo = static::$fieldsInfo;
        $query = $PKVal === 0 ? ' INSERT INTO `' . static::DataTable . '` SET ' : 'UPDATE `' . static::DataTable . '` SET ';
        $values = array_filter(
            $values,
            function($v, $k) use ($PKName, $fieldsInfo ){
                return $k !== $PKName && array_key_exists($k, $fieldsInfo);
            },
            ARRAY_FILTER_USE_BOTH
        );
        $values = static::typifyValues($fieldsInfo, $values);

        $autoCalcFields = static::getAutoCalcFields(array_keys($values));
        /*Здесь потом будем рассчитывать автоматические значения*/

        foreach ($values as $fieldKey => $fieldValue) {
            $query .= ' `' . $fieldsInfo[$fieldKey]['column_name'] . '` = :' . $fieldKey . ',';
        }

        $query = mb_substr($query, 0, mb_strlen($query) - 1);

        if ($PKVal > 0) {
            $query .= ' WHERE `' . $fieldsInfo[$PKName]['column_name'] . '` = :' . $PKName . '';
            $values[$PKName] = $PKVal;
        }

        $db = new DataBase();
        $res = $db->query($query, $values);
        if ($res === false) {
            return $db->QueryInfo();
        }
        else {
            return $db->query('SELECT * FROM `' . static::DataTable . '` WHERE `' . $fieldsInfo[$PKName]['column_name'] . '` = ' . $db->LastInsertId())[0];
        }
    }

    protected static function typifyValues(array $fieldsInfo, array $values) {
        foreach ($values as $k => &$v) {
            if (is_null($v)) {
                continue;
            }
            switch (strtolower($fieldsInfo[$k]['data_type'])) {
                case 'int':
                case 'year':
                case 'bigint':
                case 'mediumint':
                case 'smallint':
                case 'tinyint':
                    if (is_string($v) && trim($v) === ''/* && array_key_exists(strtoupper($fieldsInfo[$k]['is_nullable']), array('YES' => true, 'TRUE' => true))*/) {
                        $v = null;
                    }
                    else {
                        $v = (int)$v;
                    }
                    break;
                case 'decimal':
                case 'dec':
                case 'double':
                case 'float':
                case 'real':
                    if (is_string($v) && trim($v) ===''/* && array_key_exists(strtoupper($fieldsInfo[$k]['is_nullable']), array('YES' => true, 'TRUE' => true))*/) {
                        $v = null;
                    }
                    else {
                        $v = (float)$v;
                    }
                    break;
                case 'char':
                case 'varchar':
                case 'nvarchar':
                case 'text':
                case 'tinytext':
                case 'mediumtext':
                case 'longtext':
                    $v = $v . '';
                    break;
                case 'bit':
                    if (is_string($v) && trim($v) === ''/* && array_key_exists(strtoupper($fieldsInfo[$k]['is_nullable']), array('YES' => true, 'TRUE' => true))*/) {
                        $v = null;
                    }
                    else {
                        $v = $v === '1' || strtolower($v) === 'true' || $v === 1 || strtolower($v) === 'on';
                    }
                    break;
                case 'json':
//                    if (is_sting($v) && trim($v) !== '') {
//                        $v = json_decode($v);
//                    }
//                    else {
//                        $v = null;
//                    }
                    break;
                case 'datetime':
                case 'date':
                    $v = $v . '';
                    break;
            }
        }
        return $values;
    }

    /**
     * Метод формирует набор свойств с автоматическим рассчетом
     * @param array $keys - список наименвоаний свойсвт, среди которых надо искать рассчитываемые автоматически
     * @return array
     */
    protected static function getAutoCalcFields(array $keys) {
        $autoCalcFields = array_map(function($fieldKey){
            return new ReflectionProperty(static::class, $fieldKey);
        }, $keys);
        $autoCalcFields = array_filter($autoCalcFields, function(ReflectionProperty $reflectionProperty){
            return mb_strpos($reflectionProperty->getDocComment(), '@autocalc') !== false;
        });
        $result = array();
        foreach ($autoCalcFields as $reflectionProperty) {
            $calcRule = self::extractDocCommentItem($reflectionProperty, 'autocalc');
            $result[$reflectionProperty->name] = self::trimDocCommentKey($calcRule);
        }
        return $result;
    }

    const DocCommentKeys = array(
        'alias' => ' Псевдоним, выводимый на страницы для пользователя вместо реального имени свойства экзепмляра модели',
        'autocalc' => 'Обозначает, что значение свойства автоматически рассчитывается согласно указанному выражению. Такое свойство не отображается в форме редактирования',
        'database_column_name' => 'наименование колонки в таблице БД, отвечающей за хранение значения свойства модели',
        'foreign_key_display_value' => 'Это значение показывается в ссылочных полях вместо идентификатора. Если несколько полей используются для отображения значения, их порядок сортируется значением этого параметра',
        'foreign_key_action' => 'ссылка на action для справочника',
        'noeditable' => 'Обозначает, что данное свойство не отображается в форме редактирования',
    );

    public static function getConfigForListView() {
        return array_map(function(ReflectionProperty $prop){
            return array(
                'key' => $prop->name,
                'alias' => self::getFieldAlias($prop->name),
                'primary_key' => static::$fieldsInfo[$prop->name]['column_key'] === GlobalConst::MySqlPKVal,
                'foreign_key' => isset(static::$fieldsInfo[$prop->name]['foreign_key']) ? static::$fieldsInfo[$prop->name]['foreign_key'] : null
            );
        }, self::getModelPublicProperties());
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
    public function __construct(/*array $fields, string $entityName*/) {
//        $this->fields = $fields;
//        $this->entityName = $entityName;
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