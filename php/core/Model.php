<?php
namespace Astkon\Model;

//find . -exec chown demonius:demonius {} \; -exec chmod a+rw {} \;

use Astkon\DataBase;
use Astkon\DocComment;
use Astkon\ErrorCode;
use Astkon\GlobalConst;
use function Astkon\Lib\array_keys_underscore;
use Astkon\linq;
use Astkon\QueryConfig;
use Astkon\Traits\ModelUpdate;
use Astkon\View\View;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

abstract class Model  {
    use ModelUpdate;
    const ValidStateError = 0;
    const ValidStateOK = 1;
    const ValidStateUndefined = 2;
    const ForeignKeyPrefix = '$fk';
    protected const  fkPrefix = '$fk_';

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
     * Метод извлекает caption для указанного поля из DocComment
     * @param string $fieldName
     * @return string
     */
    protected static function getFieldAlias(string $fieldName) {
        $editedProperties = self::getModelPublicProperties();

        $editedProperties = array_filter($editedProperties, function(ReflectionProperty $property) use ($fieldName) {
            return $property->name === $fieldName;
        });

        /** @var ReflectionProperty $reflectionProperty */
        $reflectionProperty = array_shift($editedProperties);
        $caption = DocComment::getDocCommentItem($reflectionProperty, 'caption');
        return !!$caption ? $caption : $fieldName;
    }

    /**
     * Метод возвращает набор публичных нестатичных свойств класса Partial
     * @param bool $usePartialClass - позволяет выбирать только поля, имеющиеся в таблице БД, отфильтровывая вспомогательные поля, объявленные в полной модели
     * @return array
     * @throws ReflectionException
     */
    protected static function getModelPublicProperties(bool $usePartialClass = false) {
        $partialClassName = explode('\\', static::class);
        $partialClassName[] = $partialClassName[count($partialClassName) - 1] . 'Partial';
        $partialClassName[count($partialClassName) - 2] = 'Partial';
        $partialClassName = implode('\\', $partialClassName);

        $reflectionClass = new ReflectionClass($usePartialClass ? $partialClassName : static::class);

        $editedProperties = array_filter(
            $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC),
            function(ReflectionProperty $property ) use ($partialClassName){
                return !$property->isStatic();
            }
        );
        $fieldsInfo = static::$fieldsInfo;
        usort($editedProperties, function(ReflectionProperty $a, ReflectionProperty $b) use ($fieldsInfo) {
            if (array_key_exists($a->name, $fieldsInfo)) {
                $aColumnKey = strtoupper($fieldsInfo[$a->name]['column_key']);
                if ($aColumnKey === GlobalConst::MySqlPKVal) {
                    return -1;
                }
            }
            if (array_key_exists($b->name, $fieldsInfo)) {
                $bColumnKey = strtoupper($fieldsInfo[$b->name]['column_key']);
                if ($bColumnKey === GlobalConst::MySqlPKVal) {
                    return 1;
                }
            }
            $orderA = DocComment::getDocCommentItem($a, 'form_edit_order');
            $orderB = DocComment::getDocCommentItem($b, 'form_edit_order');
            $orderA = is_null($orderA) ? 0 : $orderA;
            $orderB = is_null($orderB) ? 0 : $orderB;
            return floatval($orderA) <=> floatval($orderB);
        });
        return $editedProperties;
    }

    public static function ModelPublicProperties(bool $usePartialClass = false) {
        return array_map(
            function(ReflectionProperty $reflectionProperty) {
                return $reflectionProperty->name;
            },
            static::getModelPublicProperties($usePartialClass)
        );
    }

    /**
     * Метод генерирует форму редактирования экземпляра модели
     * @param array $item
     * @param array $options
     */
    protected static function EditForm($item = array(), $options = array()) {

        $editedProperties = self::getModelPublicProperties();

        $isFormProcessed = is_array($options) && isset($options['validation']);

        $db = new DataBase();

        $queryConfig = new QueryConfig();

        // https://getbootstrap.com/docs/4.1/components/forms/
        $Model = static::class;
        $Entity = $item;

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
            require \Astkon\View\FORM_EDIT_FIELDS_TEMPLATES . DIRECTORY_SEPARATOR . 'common_message.php';
        }
        $baseRequirePath = \Astkon\View\FORM_EDIT_FIELDS_TEMPLATES . DIRECTORY_SEPARATOR;
        foreach ($editedProperties as $property) {
            $propName = $property->name;
            $docCommentParams = DocComment::extractDocCommentParams($property);
            if (
                array_key_exists('noeditable', $docCommentParams) ||
                array_key_exists('autocalc', $docCommentParams)

            ) {
                continue;
            }
            /** @var string $caption - используется во вьюхах*/
            $caption = isset($docCommentParams['caption']) ? $docCommentParams['caption'] : $propName;
            /** @var mixed $value - используется во вьюхах*/
            $value = isset($item[$propName]) ? $item[$propName] : null;

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
            $fieldInfo = isset(static::$fieldsInfo[$propName]) ? static::$fieldsInfo[$propName] : null;

            if ($fieldInfo && $fieldInfo['column_key'] === GlobalConst::MySqlPKVal) {
                require_once $baseRequirePath . 'primary_key.php';
            }
            else  if ($fieldInfo && isset($fieldInfo['foreign_key'])){
                $ForeignKeyParams = $fieldInfo['foreign_key'];

                $model = $ForeignKeyParams['model'];

                $refModel = explode('\\', __CLASS__);
                $refModel[count($refModel) - 1] = DataBase::underscoreToCamelCase($model);

                $queryConfig->Condition = $ForeignKeyParams['field'] . ' = :' . $ForeignKeyParams['field'];
                $queryConfig->RequiredFields = call_user_func(implode('\\', $refModel) . '::getReferenceDisplayedKeys');
                $queryConfig->Substitution = array($ForeignKeyParams['field'] => intval($value));

                $refItem = $db->$model->getFirstRow($queryConfig);

                $displayValue = is_array($refItem) ? implode(' ', $refItem) : '';
                $dictionaryAction = '';
                if (isset($docCommentParams['foreign_key_action'])) {
                    $dictionaryAction = $docCommentParams['foreign_key_action'];
                }
                require $baseRequirePath . 'foreign_key.php';
            }
            else {
                $dataType = is_null($fieldInfo) ? DocComment::getDocCommentItem($property, 'data_type') : $fieldInfo['data_type'];
                switch ($dataType) {
                    case 'bit':
                        require $baseRequirePath . 'boolean.php';
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
                        require $baseRequirePath. 'input.php';
                        break;
                    case 'char':
                    case 'varchar':
                    case 'nvarchar':
                    case 'string':
                        $isPassword = DocComment::getDocCommentItem($property, 'password');

                        $inputType = $isPassword === 'true' ? 'password' : 'text';
                        require $baseRequirePath . 'input.php';
                        break;
                    case 'text':
                    case 'tinytext':
                    case 'mediumtext':
                    case 'longtext':
                        require $baseRequirePath . 'textarea.php';
                        break;
                    case 'json':
                        require $baseRequirePath . 'json.php';
                        break;
                }
            }
        }
            require $baseRequirePath . 'submit.php';
        echo '</form>';
        //https://getbootstrap.com/docs/4.1/components/forms/#validation   - проверка формы
    }

    /**
     * Метод возвращает список полей модели, которые должны использоваться для преобразования
     * справочного идентификатора в понятное пользователю значение
     * @return array
     */
    protected static function getReferenceDisplayedKeys() : array {
        $editedProperties = self::getModelPublicProperties();
        $editedProperties = array_map(function (ReflectionProperty $reflectionProperty){
            $docParams = DocComment::extractDocCommentParams($reflectionProperty);
            if (!array_key_exists('foreign_key_display_value', $docParams)) {
                return null;
            }
            return array(
                'key' => $reflectionProperty->name,
                'order' => intval($docParams['foreign_key_display_value'])
            );
        }, $editedProperties);
        $editedProperties = array_filter(
            $editedProperties,
            function($item) {
                return !is_null($item);
            }
        );
        usort(
            $editedProperties,
            function($a, $b){ return $a['order'] <=> $b['order']; }
        );
        $editedProperties = array_map(
            function($prop){ return $prop['key']; },
            $editedProperties
        );
        if (count($editedProperties) === 0) {
            $editedProperties = [static::PrimaryColumnName];
        }
        return $editedProperties;
    }

    /**
     * Метод возвращает список полей, значение которых будет выводиться вместо справочного поля
     * @return array
     */
    protected static function ReferenceDisplayedKeys() : array {
        return static::getReferenceDisplayedKeys();
    }

    /**
     * Метод расшифровывает значения справочных полей, заменяя идентификаторы понятными значениями
     * @param array $listItems - ключи в under_score стиле
     */
    public static function decodeForeignKeys(array &$listItems) {
        /*Функционал реализуется через join'ы и данная функция пока применения не имеет*/
        return;
        $fieldsInfo = array_filter(static::$fieldsInfo, function($fieldInfo){ return isset($fieldInfo['foreign_key']);});
        if (count($listItems) && count($fieldsInfo)) {
            $db = new DataBase();
            foreach ($fieldsInfo as $fieldInfo) {
                $columnName = $fieldInfo['column_name'];
                if (!isset($fieldInfo['foreign_key'])) {
                    continue;
                }
                $fk = $fieldInfo['foreign_key'];
                if (
                    !isset($fk['display_mode']) ||
                    empty($fk['display_mode'])
                ) {
                    /*Если режим отображения не задан, остается идентификатор*/
                    continue;
                }

                $model = $fk['model'];
                $refModel = explode('\\', __CLASS__);
                $refModel[count($refModel) - 1] = DataBase::underscoreToCamelCase($model);
                $refModel = implode('\\', $refModel);

                $listId = array_map(
                    function($item) use ($columnName){
                        /*
                         * Если поле не было запрошено, то $fieldInfo может быть не представлен в $item вообще
                         */
                        return array_key_exists($columnName, $item) ? $item[$columnName] : null;
                    },
                    $listItems
                );
                $listId = array_filter(
                    $listId,
                    function($searchId) {
                        return !is_null($searchId);
                    }
                );

                /*Избавляемся от повторяющихся ключей*/
                $listId = array_flip(array_flip($listId));

                if (count($listId) < 1) {
                    continue;
                }

                switch ($fk['display_mode']) {
                    case 'decode_id_to_string':
                        $required_keys = array_map(
                            function($key){ return DataBase::camelCaseToUnderscore($key);},
                            $refModel::getReferenceDisplayedKeys()
                        );
                        if (count($required_keys) === 1 && $required_keys[0] === $refModel::PrimaryColumnKey) {
                            break;
                        }
                        $fk['displayed_keys'] = $required_keys;

                        /*Т.к. полей всегда лишь несколько, то нет смысла искать через ключи - разница быстройдествия будет несущественной
                        или даже не в пользу такого метода*/
//                        if (!array_key_exists($refModel::PrimaryColumnKey, array_flip($required_keys))) {
//                            $required_keys[] = $refModel::PrimaryColumnKey;
//                        }
                        if (!in_array($refModel::PrimaryColumnKey, $required_keys)) {
                            $required_keys[] = $refModel::PrimaryColumnKey;
                        }

                        $queryConfig = new QueryConfig(
                            $refModel::PrimaryColumnKey .  ' in (' . implode(',', $listId) . ')',
                            $required_keys,
                            null,
                            null,
                            count($listItems)
                        );

                        $rows = $refModel::getRows(
                            $db,
                            $queryConfig,
                            true
                        );

                        $displayed_keys = $fk['displayed_keys'];

                        $rows = (new linq($rows))
                            ->toAssoc(
                                function($row) use ($refModel) { return $row[$refModel::PrimaryColumnKey];},
                                function($row) use ($displayed_keys) {
                                    return implode(
                                        ' ',
                                        array_map(
                                            function($key) use ($row) { return is_null($row[$key]) ? '' : $row[$key];},
                                            $displayed_keys
                                        )
                                    );

                                }
                            )->getData();

                        foreach ($listItems as &$item) {
                            if (is_null($item[$columnName]))  {
                                continue;
                            }
                            $id = $item[$columnName];
                            if (array_key_exists($id, $rows)) {
                                $item[static::fkPrefix . $columnName] = $rows[$id];
                            }
                        }

                        break;
                    case 'join_model':
                        $required_keys = array_map(
                            function($jc){
                                return $jc['key'];
                            },
                            $fk['joined_columns']
                        );
                        if (count($required_keys) < 1) {
                            break;
                        }
                        if (count($required_keys) === 1 && $required_keys[0] === $refModel::PrimaryColumnKey) {
                            break;
                        }

                        /*Т.к. полей всегда лишь несколько, то нет смысла искать через ключи - разница быстройдествия будет несущественной
                        или даже не в пользу такого метода*/
//                        if (!array_key_exists($refModel::PrimaryColumnKey, array_flip($required_keys))) {
//                            $required_keys[] = $refModel::PrimaryColumnKey;
//                        }
                        if (!in_array($refModel::PrimaryColumnKey, $required_keys)) {
                            $required_keys[] = $refModel::PrimaryColumnKey;
                        }

                        $queryConfig = new QueryConfig(
                            $refModel::PrimaryColumnKey .  ' in (' . implode(',', $listId) . ')',
                            $required_keys,
                            null,
                            null,
                            count($listItems)
                        );

                        $rows = $refModel::getRows(
                            $db,
                            $queryConfig,
                            2
                        );

                        $rows = (new linq($rows))
                            ->toAssoc(
                                function($row) use ($refModel) { return $row[$refModel::PrimaryColumnKey];}
                            )->getData();
                        $joinedColumns = (new linq($fk['joined_columns']))
                            ->toAssoc(
                                function($jc){ return $jc['key'];},
                                function($jc){ return isset($jc['alias']) ? $jc['alias'] : $jc['key'];}
                            )->getData();
                        foreach ($listItems as &$item) {
                            if (is_null($item[$columnName]))  {
                                continue;
                            }
                            $id = $item[$columnName];
                            if (array_key_exists($id, $rows)) {
                                $row = $rows[$id];
                                foreach ($joinedColumns as $joinedColumnName => $joinedColumnAlias) {
                                    $item[$joinedColumnAlias] = $row[$joinedColumnName];
                                    $joinedColumnName = static::fkPrefix . $joinedColumnName;
                                    if (array_key_exists($joinedColumnName, $row)) {
                                        $item[static::fkPrefix . $joinedColumnAlias] = $row[$joinedColumnName];

                                    }
                                }
                            }
                        }
                        break;
                }

            }
        }
    }

    /**
     * Метод сохраняет данные в БД.
     * В случае успеха возвращает true В случае ошибки возвращает массив с информацией об ошибке
     * @return array|bool
     */
    public function Save() {
        $editedProperties = self::getModelPublicProperties(true);
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
    protected static function SaveInstance(array $values) {
        $PKName = static::PrimaryColumnName;
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

        foreach ($fieldsInfo as $fieldName => $fieldParams) {
            $reflectionProperty = new ReflectionProperty(static::class, $fieldName);
            $fieldsInfo[$fieldName]['DocCommentParams'] = DocComment::extractDocCommentParams($reflectionProperty);
        }

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
            if (isset($fieldsInfo[$fieldKey]['DocCommentParams']['save_wrapper'])) {
                $query .= ' `' . $fieldsInfo[$fieldKey]['column_name'] . '` = ' . $fieldsInfo[$fieldKey]['DocCommentParams']['save_wrapper'] . '(:' . $fieldKey . '),';
            }
        else {

                $query .= ' `' . $fieldsInfo[$fieldKey]['column_name'] . '` = :' . $fieldKey . ',';
            }
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

    protected static function getCount(
        ?DataBase $db = null,
        ?QueryConfig $queryConfig = null,
        int $deepDecodeForeignKeys = 0
    ) : int {
        $db = $db ?? (new DataBase());
        static::prepareQuery($db, $deepDecodeForeignKeys);
        return $db->getCount(
            $queryConfig
        );
    }

    protected static function getRows(
        ?DataBase $db = null,
        ?QueryConfig $queryConfig = null,
        int $deepDecodeForeignKeys = 0
    ) : array {
        $db = $db ?? (new DataBase());
        static::prepareQuery($db, $deepDecodeForeignKeys);
        return $db->getRows(
            $queryConfig
        );
    }

    private static function prepareQuery(
        DataBase $db,
        int $deepDecodeForeignKeys = 0
    ) : void {
        $model = static::DataTable;
        $db->$model;
        if ($deepDecodeForeignKeys) {
            $fkFields = (new linq(static::$fieldsInfo))
                ->where(function($fieldInfo){
                    return isset($fieldInfo['foreign_key']);
                })
                ->getData();
            $index = 0;
            while ($index < count($fkFields)) {
                $fieldInfo = $fkFields[$index++];
                $joinedFields = null;
                if (isset($fieldInfo['foreign_key']['joined_columns'])) {
                    $joinedFields = (new linq($fieldInfo['foreign_key']['joined_columns']))
                        ->toAssoc(
                            function ($joinedColumn) { return $joinedColumn['key'];},
                            function ($joinedColumn) { return array_key_exists('alias', $joinedColumn) ? $joinedColumn['alias'] : $joinedColumn['key'];}
                        )
                        ->getData();
                }
                $db->joinForeignKey($fieldInfo['column_name'], $joinedFields, $deepDecodeForeignKeys);
                $refModel = explode('\\', static::class);
                $refModel[count($refModel) - 1] = DataBase::underscoreToCamelCase($fieldInfo['foreign_key']['model']);
                $refModel = implode('\\', $refModel);
                $refModelFKFields = (new linq($refModel::$fieldsInfo))
                    ->where(function($fieldInfo){
                        return isset($fieldInfo['foreign_key']);
                    })
                    ->getData();
                $refModelFKFields = (new linq($refModelFKFields))
                    ->where(function($fieldInfo) use ($joinedFields) {
                        return $joinedFields ? array_key_exists($fieldInfo['column_name'], $joinedFields) : true;
                    })
                    ->getData();
                (new linq($refModelFKFields))
                    ->for_each(function($fieldInfo) use (&$fkFields){
                        $fkFields[] = $fieldInfo;
                    });
            }
        }
    }

    protected static function getFirstRow(
        $db = null,
        ?QueryConfig $queryConfig = null,
        int $deepDecodeForeignKeys = 0
    ) : ?array {
        $queryConfig = $queryConfig ?? new QueryConfig();
        $queryConfig->Limit = 1;
        $rows = static::getRows(
            $db,
            $queryConfig,
            $deepDecodeForeignKeys
        );
        return count($rows) ? $rows[0] : null;
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
                    /*Строки разработчик должен сам приводить в json*/
                    if (!is_string($v)) {
                        if (!is_null($v)) {
                            $v = json_encode($v);
                        }
                    }
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
            $calcRule = DocComment::getDocCommentItem($reflectionProperty, 'autocalc');
            $result[$reflectionProperty->name] = $calcRule;
        }
        return $result;
    }

    const DocCommentKeys = array(
        'caption'                   => ' Псевдоним, выводимый на страницы для пользователя вместо реального имени свойства экзепмляра модели',
        'autocalc'                  => 'Обозначает, что значение свойства автоматически рассчитывается согласно указанному выражению. Такое свойство не отображается в форме редактирования',
        'database_column_name'      => 'наименование колонки в таблице БД, отвечающей за хранение значения свойства модели',
        'foreign_key_display_value' => 'Это значение показывается в ссылочных полях вместо идентификатора. Если несколько полей используются для отображения значения, их порядок сортируется значением этого параметра',
        'foreign_key_action'        => 'ссылка на action для справочника',
        'form_edit_order'           => 'порядок вывода в форме редактирования',
        'list_view_order'           => 'порядок вывода в списках',
        'nodisplay'                 => 'Обозначает, что данное свойство не отображается в представлениях списков',
        'noeditable'                => 'Обозначает, что данное свойство не отображается в форме редактирования',
        'save_wrapper'              => 'Функция sql, используемая для сохранения значения в БД, например password или md5 (наименование без скобок)',
    );

    /**
     * Функция возвращает конфигурацию для табличного представления экземпляров модели
     * @param null $excludeFields - список полей в CamelCase, которые не нужно выводить на страницу
     * @return array
     */
    protected static function getConfigForListView($excludeFields = null) {
        $excludeFields = is_array($excludeFields) ? array_flip($excludeFields) : array();

        $config = array_map(
            function(ReflectionProperty $prop) {
                $result = DocComment::extractDocCommentParams($prop);
//                Следующие ключи определяем принудительно независимо от настроек модели
                $result['key'] = $prop->name;
                if (array_key_exists($prop->name, static::$fieldsInfo)) {
                    $result['primary_key'] = static::$fieldsInfo[$prop->name]['column_key'] === GlobalConst::MySqlPKVal;
                    $result['foreign_key'] = isset(static::$fieldsInfo[$prop->name]['foreign_key']) ? static::$fieldsInfo[$prop->name]['foreign_key'] : null;
                    $result['data_type'] = static::$fieldsInfo[$prop->name]['data_type'];
                }
                else {
                    $result['primary_key'] = false;
                    $result['foreign_key'] = null;
                    $result['data_type'] = isset($result['var']) ? $result['var'] : 'string';
                }

                return $result;
            },
            array_filter(
                static::getModelPublicProperties(),
                function(ReflectionProperty $prop) use ($excludeFields) {
                    return !array_key_exists($prop->name, $excludeFields);
                })
        );
        $checkIsJoined = function (array $item) : bool {
            return isset($item['foreign_key']);
        };

        $joinedModels = array();
        foreach ($config as $configItem) {
            if (!$checkIsJoined($configItem)) {
                continue;
            }
            $joinedModels[] = $configItem;
        }

        while(count($joinedModels)) {
            $joinedField = array_pop($joinedModels);
            $joinedModel = $joinedField['foreign_key'];

            $refModel = explode('\\', static::class);
            $refModel[count($refModel) - 1] = DataBase::underscoreToCamelCase($joinedModel['model']);
            $refModel = implode('\\', $refModel);

            $refModelConfig = (new linq($refModel::getConfigForListView()))
                ->where(function($refModelFieldConfig){
                    return !$refModelFieldConfig['primary_key'];
                })
                ->toAssoc(function($refModelFieldConfig){ return $refModelFieldConfig['key'];})
                ->getData();
            $joinedColumns = isset($joinedModel['joined_columns']) ?
                $joinedModel['joined_columns'] :
                array_map(
                    function($refModelConfigItem) use ($joinedModel) {
                        return array(
                            'model' => $joinedModel['model'],
                            'key' => $refModelConfigItem['key'],
                        );
                    },
                    array_values($refModelConfig)
                );

            foreach ($joinedColumns as $joinedField) {
                $fieldName = DataBase::underscoreToCamelCase($joinedField['key']);
                if (!array_key_exists($fieldName, $refModelConfig)) {
                    $view = new View();
                    $view->trace = 'Внешний ключ модели ' . static::class . ' ссылается на несуществующее поле ' . $fieldName . ' модели ' . $refModel;
                    $view->error(ErrorCode::PROGRAMMER_ERROR);
                }
                $refModelFieldConfig = $refModelConfig[$fieldName];
                if ($checkIsJoined($refModelFieldConfig)) {
                    $joinedModels[] = $refModelFieldConfig;
                }
                else {
                    if (isset($joinedField['list_view_order'])) {
                        $refModelFieldConfig['list_view_order'] = $joinedField['list_view_order'];
                    }
                    if (isset($joinedField['alias'])) {
                        $refModelFieldConfig['$orig_key'] = $refModelFieldConfig['key'];
                        $refModelFieldConfig['key'] = DataBase::underscoreToCamelCase($joinedField['alias']);
                    }
                    $config[] = $refModelFieldConfig;
                }
            }
        }

        return static::Sort($config);
    }

    /**
     * Метод возвращает наименование модели без Namespace и в camelCase стиле
     * @return string
     */
    public static function Name() : string{
        return DataBase::underscoreToCamelCase(static::DataTable);
    }

    /**
     * Метод возвращает пустой образец сущности из БД
     * @param array|null $substitution - возможные значения для подстановки в пустую модель в under_score стиле
     * @return array
     */
    protected static function EmptyEntity(?array $substitution) : array {
        $entity = array();
        foreach (static::$fieldsInfo as $fieldInfo) {
            $entity[$fieldInfo['column_name']] = null;
        }
        $entity[static::PrimaryColumnKey] = 0;
        if (is_array($substitution)) {
            foreach ($substitution as $key => $value) {
                $entity[$key] = $value;
            }
        }
        return $entity;
    }

    public static function Sort(array $config) {
        usort($config, function(array $a, array $b) {
            if (
                array_key_exists('primary_key', $a) &&
                $a['primary_key']
            ) {
                return -1;
            }
            if (
                array_key_exists('primary_key', $b) &&
                $b['primary_key']
            ) {
                return 1;
            }
            $orderA = isset($a['list_view_order']) ? $a['list_view_order'] : 0;
            $orderB = isset($b['list_view_order']) ? $b['list_view_order'] : 0;

            return floatval($orderA) <=> floatval($orderB);
        });

        return $config;
    }

    /**
     * @param array         $substitution
     * @param DataBase|null $db
     * @param bool|null     $return
     * @return array|bool|null
     */
    protected static function Create(array $substitution, ?DataBase $db, ?bool $return = false) {
        $db = $db ?? new DataBase();
        $query = 'INSERT INTO `' . static::DataTable . '` SET ';
        $substitution = array_keys_underscore($substitution);
        $query .= implode(
            ', ',
            array_map(
                function($key){
                    return '`' . $key . '` = :' . $key;
                },
                array_keys($substitution)
            )
        );
        if ($db->query($query, $substitution) === false) {
            return false;
        }
        if ($return) {
            return static::getFirstRow(
                $db,
                new QueryConfig('`' . static::PrimaryColumnKey . '` = ' . $db->LastInsertId())
            );
        }
        return true;
    }

    protected static function GetByPrimaryKey(int $pk, ?DataBase $db = null) {
        $db = $db ?? new DataBase();
        $model = static::DataTable;
        return $db->$model->getFirstRow(new QueryConfig('`' . static::PrimaryColumnKey . '` = ' . $pk));
    }

    protected static function Update(array $substitution, ?DataBase $db = null, ?bool $return = false) {
        $db = $db ?? new DataBase();
        $query = 'UPDATE `' . static::DataTable . '` SET ';
        $substitution = array_keys_underscore($substitution);
        if (!isset($substitution[static::PrimaryColumnKey])) {
            $view = new View();
            $view->trace = nl2br(
                'Файл ' . __FILE__ . PHP_EOL .
                'Класс ' . __CLASS__ . PHP_EOL .
                'Метод ' . __METHOD__ . PHP_EOL .
                'Строка ' . __LINE__ . PHP_EOL .
                'Для обновления записи не указан первичный ключ'
            );
            $view->error(ErrorCode::PROGRAMMER_ERROR);

        }
        $pkValue = $substitution[static::PrimaryColumnKey];
        $query .= implode(
            ', ',
            array_map(
                function($key){
                    return '`' . $key . '` = :' . $key;
                },
                array_filter(
                    array_keys($substitution),
                    function($key){ return $key !== static::PrimaryColumnKey;}
                )
            )
        );

        $query .= ' WHERE `' . static::PrimaryColumnKey . '` = :' . static::PrimaryColumnKey;
        if ($db->query($query, $substitution) === false) {
            return false;
        }
        if ($return) {
            $queryConfig = new QueryConfig(
                '`' . static::PrimaryColumnKey . '` = :' . static::PrimaryColumnKey,
                null,
                array(static::PrimaryColumnKey => $pkValue)
            );
            return static::getFirstRow(
                $db,
                $queryConfig
            );
        }
        return true;
    }

    protected static function Delete($listId, ?DataBase $db) {
        $db = $db ?? new DataBase();
        if (is_numeric($listId)) {
            $db->query('delete from `' . static::DataTable .
                '` where `' . static::PrimaryColumnKey . '` = :' . static::PrimaryColumnKey .
                ' limit 1',
                array(static::PrimaryColumnKey => $listId)
            );
        }
        else if (is_array($listId)) {
            $db->query('delete from `' . static::DataTable .
                '` where `' . static::PrimaryColumnKey . '` in (' . implode(',', $listId) .
                ') limit ' . count($listId)
            );
        }
    }

}