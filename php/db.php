<?php
namespace Astkon;
use Astkon\View\View;
use DateTime;
use \PDO as PDO;
use \PDOException as PDOException;
use \Exception as Exception;
use PDOStatement;
use ReflectionProperty;

//echo getcwd() . PHP_EOL;
//echo __DIR__ . PHP_EOL;
//die();
/*
 * Данный класс выполняет работу с базой данных
 */
# https://andreyex.ru/bazy-dannyx/baza-dannyx-mysql/12-osnovnyx-primerov-komandy-insert-v-mysql/


class DataBase {
    const QueryTypeInsert = 0;
    const QueryTypeUpdate = 1;
    const QueryTypeDelete = 2;
    const QueryTypeSelect = 3;
    const QueryTypeDenied = 4;

    /**
     * Текущее подключение к БД
     * @var PDOStatement
     */
    private $PDO;
    /**
     * Имя используемой базы данных
     * @var string
     */
    private $dbname;
    /**
     * Наименование текущего объекта, который следует загрузить из БД и
     * характеристики его полей.
     * @var string
     */
    private $currentObject;
    /**
     * Статус последнего запроса в БД
     * 0 - успешно
     * иначе инфорация об ошибке
     * @var array|int
     */
    protected $lastQueryState = 0;
    /**
     * Идентификатор последней вставленной записи
     * @var null
     */
    protected $lastInsertId = null;

    /**
     * DataBase constructor.
     * @param string $host
     * @param string $dbName
     * @param string $login
     * @param string $pass
     * @throws Exception
     */
    function __construct(
        $host = GlobalConst::Host,
        $dbName = GlobalConst::DbName,
        $login = GlobalConst::HostUser,
        $pass = GlobalConst::HostPass
    ) {
        $this->connect($host, $dbName, $login, $pass);
    }

    /**
     * Метод непосредственно выполняет соединение с требуемой БД
     * @param string $host
     * @param string $dbName
     * @param string $login
     * @param string $pass
     * @throws Exception
     */
    private function connect(
            /*все параметры - строки*/
            $host, $dbName, $login, $pass
            ) {
        $host = $host == '' ? 'localhost' : $host;
        $login = $login == '' ? 'root' : $login;
        $pass = $pass == '' ? '' : $pass;//null, 0 тоже превращаем в пустую строку
        try {
            $this->PDO = new PDO(
                "mysql:host=$host;dbname=$dbName",
                $login,
                $pass,
                array(
//                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
//                    PDO::ATTR_EMULATE_PREPARES => true, //Не ставить принудительно в false - может сильно глючить!!!
                    PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                )
            );
        }
        catch (PDOException $ex) {
            throw new Exception('Не удалось подключиться к БД с текущими параметрами авторизации');
        }

        /*После подключения к БД запомним ее имя*/
        $this->dbname = $dbName;
    }

    /**
     * @param string $query - строка запроса к выполнению
     * @param array $values - значения для подстановки в запрос на место placeholder'ов. Ключи в CamelCase
     * @param string $mode - метод формирования списка значений
     * @return array|false
     */
    public function query(
            $query,
            $values = array(),
            $mode = 'assoc'
    ) {
        $data = array();
        if ($query) {
            /*Получаем результат запроса из БД*/
            $query = trim($query);
            $this->lastQueryState = 0;
            $queryType = self::getQueryType($query);
            if($queryType === self::QueryTypeDenied) {
                $this->lastQueryState = array(
                    '@error' => true,
                    'errorType' => 'PHP',
                    'errorCode' => 0,
                    'errorMessage' => 'Запрещенная команда',
                );;
                return false;
            }
            $result = $this->_execQueryCommand($query, $queryType, $values);
            if (is_array($result)) {
                $this->lastQueryState = $result;
                return false;
            }
            if ($queryType === self::QueryTypeSelect) {
                while ($row = $result->fetch($mode === 'assoc' ? PDO::FETCH_ASSOC : PDO::FETCH_NUM)) {
                    $data[] = $row;
                }
            }
        }
        return $data;
    }

    public function QueryState() {
        return $this->lastQueryState === 0;
    }

    public function QueryInfo() {
        return is_array($this->lastQueryState) ? $this->lastQueryState : array();
    }

    public function LastInsertId() {
        return $this->lastInsertId;
    }

    protected static function getQueryType(string $query) : int {
        $queryType = strtolower(mb_substr($query, 0, mb_strpos($query, ' ')));
        switch ($queryType) {
            case 'select':
                return self::QueryTypeSelect;
            case 'insert':
                return self::QueryTypeInsert;
            case 'update':
                return self::QueryTypeUpdate;
            case 'delete':
                return self::QueryTypeDelete;
            default:
                return self::QueryTypeDenied;

        }
    }

    public function setPDOAttribute(int $attribute, mixed $value) {
        $this->PDO->setAttribute($attribute, $value);
    }

    /**
     * @param array $values - значения для подстановки. Ключи в CamelCase
     * @param int $queryType
     * @param string $query
     * @return bool|PDOStatement|array
     */
    protected function _execQueryCommand($query, int $queryType, $values = null) {
        $query = trim($query);


        /** @var \PDOStatement $stmt */
        try {
            $stmt = $this->PDO->prepare($query);
        }
        catch (\PDOException $PDOException) {
            switch ($PDOException->getCode()) {
                case '42S22':
                    $view = new View();
                    $view->trace = array(
                        'errorCode' => $PDOException->getCode(),
                        'errorMessage' => $PDOException->getMessage(),
                        'errorInfo' => $PDOException->errorInfo,
                    );
                    $view->error(ErrorCode::PROGRAMMER_ERROR);
                    die();
                default:
                    return array(
                        '@error' => true,
                        'errorType' => 'PDO',
                        'errorCode' => $PDOException->getCode(),
                        'errorMessage' => $PDOException->getMessage(),
                    );
            }
        }
        catch (\Exception $exception) {
            return array(
                '@error' => true,
                'errorType' => 'PHP',
                'errorCode' => $exception->getCode(),
                'errorMessage' => $exception->getMessage(),
            );
        }
        // Альтернатива PDO - https://habr.com/post/141127/
        if (is_array($values)) {
            try {
                foreach ($values as $k => $v) {
                    $paramType = false;
                    if (is_int($v)) {
                        $paramType = PDO::PARAM_INT;
                    }
                    else if (is_bool($v)) {
                        $paramType = PDO::PARAM_BOOL;
                    }
                    else if (is_null($v)) {
                        $paramType = PDO::PARAM_NULL;
                    }
                    else if(is_array($v)) {
                        $paramType = PDO::PARAM_STR;
                        $v = json_encode($v);
                    }
                    else if (is_string($v)) {
                        $paramType = PDO::PARAM_STR;
                    }
                    if ($paramType !== false) {
                        (function($v) use ($stmt, $k, $paramType) {
                            $stmt->bindParam(':' . $k, $v, $paramType);

                        })($v);
                    }
                }
            } catch (\PDOException $PDOException) {
                return array(
                    '@error' => true,
                    'errorType' => 'PDO',
                    'errorCode' => $PDOException->getCode(),
                    'errorMessage' => $PDOException->getMessage(),
                );
            }
        }

        try {

            if ($queryType === self::QueryTypeInsert) {
                $this->PDO->beginTransaction();
                $stmt->execute();
                $this->lastInsertId = $this->PDO->lastInsertId();
                $this->PDO->commit();
//                echo '<pre>';
//                $stmt->debugDumpParams();
//                die();
            }
            else {
                $stmt->execute();
            }
        }
        catch (\PDOException $PDOException) {
            try {
                if ($this->PDO->inTransaction()) {
                    $this->PDO->rollback();
                }
            }
            catch (\PDOException $PDOException) {
                return array(
                    '@error' => true,
                    'errorType' => 'PDO',
                    'errorCode' => $PDOException->getCode(),
                    'errorMessage' => $PDOException->getMessage(),
                );
            }
            $errInfo = array(
                '@error' => true,
                'errorType' => 'PDO',
                'errorCode' => $PDOException->getCode(),
                'errorMessage' => $PDOException->getMessage(),
                'errorInfo' => $PDOException->errorInfo
            );

            $errInfo = self::errorMessageParser($errInfo, array_keys($values));

            return $errInfo;
        }
        catch(\Exception $exception) {
            return array(
                '@error' => true,
                'errorType' => 'PHP',
                'errorCode' => $exception->getCode(),
                'errorMessage' => $exception->getMessage(),
            );
        }
        return $stmt;
    }

    /**
     * Метод обрабатывает ошибки, возникшие при работе с PDO (подготовкой и отправкой запроса)
     * @param array $errInfo
     * @param array $fieldNames - наименования полей в CamelCase
     * @return array
     */
    protected static function errorMessageParser(array $errInfo, array $fieldNames) {
        $expectedErrorColumn = [];
        switch ($errInfo['errorCode']) {
            case 'HY093':
                $view = new View();
                $view->trace = array(
                    'errorCode' => $errInfo['errorCode'],
                    'errorMessage' => $errInfo['errorMessage'],
                    'errorInfo' => $errInfo['errorInfo'],
                    'class' => __CLASS__,
                    'line' => __LINE__,
                );
                $view->error(ErrorCode::PROGRAMMER_ERROR);
                die();
            case 'HY000':
                if ($errInfo['errorMessage']) {

                    /*
                     * Попробуем получить колонку из сообщения об ошибке
                    */

                    foreach ($fieldNames as $fieldName) {
                        $_field_name = self::camelCaseToUnderscore($fieldName);
                        if (
                            mb_strpos($errInfo['errorMessage'], "'" . $_field_name . "'") !== false ||
                            mb_strpos($errInfo['errorMessage'], '"' . $_field_name . '"') !== false
                        ) {
                            $expectedErrorColumn[] = $fieldName;
                        }
                    }
                }
                if (count($expectedErrorColumn) > 0) {
                    $errInfo['expected_error_column_name'] = implode(',', $expectedErrorColumn);
                    $errInfo['err_code_explain'] = 'Недопустимое значение';
                }
                break;
            case '23000':
                //1048 - not null ; 1062 - unique
                $keySuffix = '';
                $keyPrefix = '';
                $errCodeExplain = 'Ошибка при сохранении значения';
                if ($errInfo['errorMessage']) {
                    $errMessage = strtolower($errInfo['errorMessage']);
                    if(is_array($errInfo['errorInfo']) && count($errInfo['errorInfo']) > 1) {
                        switch ($errInfo['errorInfo'][1]) {
                            case 1062:
                                $keySuffix = '_unique';
                                $errCodeExplain = 'Значение поля должно быть уникальным';
                                break;
                            case 1048:
                                $errCodeExplain = 'Значение поля не может быть пустым';
                                break;
                            case 1452:
                                $keySuffix = $keyPrefix = '`';
                                $errCodeExplain = 'Необходимо выбрать значение из справочника';
                                break;
                        }
                    }
                }
                foreach ($fieldNames as $fieldName) {
                    $_field_name = self::camelCaseToUnderscore($fieldName);
                    if (mb_strpos($errMessage, $keyPrefix . $_field_name . $keySuffix) !== false) {
                        $expectedErrorColumn[] = $fieldName;
                    }
                }
                if (count($expectedErrorColumn) > 0) {
                    $errInfo['expected_error_column_name'] = implode(',', $expectedErrorColumn);
                    $errInfo['err_code_explain'] = $errCodeExplain;
                }
                break;
            default:
                if ($errInfo['errorMessage']) {
                    foreach ($fieldNames as $fieldName) {
                        $_field_name = self::camelCaseToUnderscore($fieldName);
                        if (
                            mb_strpos($errInfo['errorMessage'], "'" . $_field_name . "'") !== false ||
                            mb_strpos($errInfo['errorMessage'], '"' . $_field_name . '"') !== false
                        ) {
                            $expectedErrorColumn[] = $fieldName;
                        }
                    }
                }
                if (count($expectedErrorColumn) > 0) {
                    $errInfo['expected_error_column_name'] = implode(',', $expectedErrorColumn);
                    $errInfo['err_code_explain'] = 'Непредвиденная ошибка';
                }
                break;
        }

        return $errInfo;

    }


    /**
     * @param null|string $condition
     * @param null|array $required_fields
     * @param null|int $offset
     * @param null|int $limit
     * @return string
     */
    public function getQueryString(
        $condition = null, //строка
        $required_fields = null, //массив наименований колонок для выборки,
        $offset = null,
        $limit = null
    ) : string {
        return $this->_getQueryString($condition, $required_fields, $offset, $limit);
    }

    /**
     * @param null|string $condition
     * @param null|array $required_fields
     * @param null|int $offset
     * @param null|int $limit
     * @return string
     */
    private function _getQueryString (
        $condition = null, //строка
        $required_fields = null, //массив наименований колонок для выборки,
        $offset = null,
        $limit = null
    ) : string {
        /*Определим список полей, которые требуется извлечь*/
        (
            !$required_fields ||
            (gettype($required_fields) !== gettype(array())) ||
            count($required_fields) < 1
        ) &&
        $required_fields = array('*');//По умолчанию извлекаются все поля
        $required_fields = array_map(function($fieldName){ return DataBase::camelCaseToUnderscore($fieldName);}, $required_fields);

        /*Запросим строки и сразу произведем типизацию*/
        return 'select ' .
            implode(', ', $required_fields) .
            ' from ' . $this->currentObject['name'] . ' ' .
            ($condition ? 'where ' . $condition : '') . ' ' .
            (!is_null($limit) ? 'limit ' . $limit : '') . ' ' .
            (!is_null($offset) ? 'offset ' . $offset : '');
    }

    /**
     * @param null|string $condition - условие для выборки
     * @param null|array $required_fields - требуемые для выборки поля
     * @param array $values - значения для подставновки в запрос вместо placeholder'ов
     * @param null|int $offset
     * @param null|int $limit
     * @return array
     */
    public function getRows(
            $condition = null, //строка
            $required_fields = null, //массив наименований колонок для выборки
            $values = array(),
            $offset = null,
            $limit = null
            ) : array {
        if (!$this->currentObject) {
            throw new Exception('Не установлен объект для извелечения из БД');
        }
        /*Отберем список колонок, которым надо преобразовать тип из строкового*/
        $columns = $this->currentObject['fields'];
        /*Запросим строки и сразу произведем типизацию*/
        $query = $this->_getQueryString($condition, $required_fields, $offset, $limit);
        $rows = (new linq($this->query($query, $values)))
            ->where(function($row){ return count($row) > 0;})
            ->for_each(function(&$row) use ($columns){
                self::_convertValue($row, $this->currentObject['fields']);
            })->getData();
        return $rows;
    }

    /**
     * @param string $condition
     * @param array $values
     * @return array|null
     */
//    public function getObjects($condition, $values = array()) {
//        if (!$this->currentObject) {
//            throw new Exception('Не установлен объект для извелечения из БД');
//        }
//        $className = $this->getObjClassName();
//        $entityName = $this->currentObject['name'];
//        return (new linq($this->getRows($condition, $values)))
//            ->select(function($row) use ($className, $entityName){
//                return new $className($row, $entityName);
//            })->getData();
//    }

    /**
     * @return string
     */
//    private function getObjClassName(){
//        return class_exists($this->currentObject['name']) ? $this->currentObject['name'] : 'StandPrototype';
//    }

    /**
     * @param array $entity
     * @return bool|PDOStatement
     * @throws Exception
     */
    public function Delete(
            array $entity
            ) {
        if (!$this->currentObject) {
            throw new Exception('Не установлен объект для удаления из БД');
        }
        if ($entity === null || gettype($entity) !== gettype($entity) || count($entity) < 1) {
            throw new Exception('Нет информации для удаления из БД');
        }
        /*Проверим наличие первичного ключа в таблице - без него удаление этим методом невозможно*/
        if (($pk = (new linq($this->currentObject['fields'], 'assoc'))
        ->first(function($column){ return $column['_primary_key'];})) === null) {
            throw new Exception('Данная таблица не имеет первичного ключа. Поэтому удаление данным методом невозможно');
        }
        if (!array_key_exists($pk['column_name'], $entity)) {
            throw new Exception('Полученный объект не содержит информации о первичном ключе. Удаление невозможно.');
        }
        /*Создаем запрос для удаления записи*/
        $query = 'DELETE FROM ' . $this->currentObject['name'] . ' WHERE  ' . $pk['column_name'] . '=' . $entity[$pk['column_name']] . ' LIMIT 1';
        $smtp = $this->_execQueryCommand($query, self::QueryTypeDelete);
        return $smtp->errorCode();
    }

    /**
     * @param array $entity
     * @return array|null
     * @throws Exception
     */
    public function Insert(
            array $entity
            ) {
//        if (!$this->currentObject) {
//            throw new Exception('Не установлен объект для вставки в БД');
//        }
//        if ($entity === null || gettype($entity) !== gettype($entity) || count($entity) < 1) {
//            throw new Exception('Нет информации для вставки в БД');
//        }
//        $values = array();
//        /*Создаем запрос для вставки записи*/
//        $query = 'INSERT INTO ' . $this->currentObject['name'] . ' (' .
//                join(
//                    ',',
//                    (new linq($this->currentObject['fields'], 'assoc'))
//                    ->where(function($column){ return $column['_primary_key'] ? false : true;})
//                    ->select(function($column){return '`' . $column['column_name'] . '`';})
//                    ->getData()
//                )
//                .') VALUES(' .
//                join(
//                    ',',
//                    (new linq($this->currentObject['fields'], 'assoc'))
//                        ->where(function($column) {return $column['_primary_key'] ? false : true;})
//                        ->select(function($column) use ($entity, &$values) {
//                            $colKey = $column['column_name'];
//                            $values[$colKey] = self::_getValueForQuery(
//                                array_key_exists($colKey, $entity) ? $entity[$colKey] : NULL,
//                                $column
//                            );
//                            return ':' . $colKey;
//                        })
//                    ->getData()
//                )
//                .')';
//
//        $this->_execQueryCommand($query, self::QueryTypeInsert, $values);
//        /*После вставки попробуем вернуть вставленную запись, если данная таблица имеет первичный ключ*/
//        $result = null;
//        if ((new linq($this->currentObject['fields'], 'assoc'))
//        ->first(function($column){ return $column['_primary_key'];})) {
//            $command = 'SELECT last_insert_id() as `liid`';
//            $lastId = $this->query($command)[0]['liid'];
//            $table_name = $this->currentObject['name'];
//            $result = $this->$table_name->getEntity($lastId);
//        }
//        return $result;
    }
    
    public function entityPKColumn() {
//        if (!$this->currentObject) {
//            throw new Exception('Не установлен объект для извелечения из БД');
//        }
//        return (new linq($this->currentObject['fields'], 'assoc'))
//        ->first(function($column){ return $column['_primary_key'];});
    }

    /**
     * Метод обновляет запись в БД
     * @param array $entity
     * @return array|null
     * @throws Exception
     */
    public function Update(
            $entity
            ) {
//        if (!$this->currentObject) {
//            throw new Exception('Не установлен объект для извелечения из БД');
//        }
//        if ($entity === null || gettype($entity) !== gettype($entity) || count($entity) < 1) {
//            throw new Exception('Нет информации для вставки в БД');
//        }
//        /*Проверим наличие первичного ключа в таблице - без него обновление этим методом невозможно*/
//        if (($pk = (new linq($this->currentObject['fields'], 'assoc'))
//        ->first(function($column){ return $column['_primary_key'];})) === null) {
//            throw new Exception('Данная таблица не имеет первичного ключа. Поэтому обновление данным методом невозможно');
//        }
//        if (!array_key_exists($pk['column_name'], $entity)) {
//            throw new Exception('Полученный объект не содержит информации о первичном ключе. Обновление невозможно.');
//        }
//        /*Составляем запрос на обновление*/
//        $columns = $this->currentObject['fields'];
//        $values = array();
//        $query = 'UPDATE ' . $this->currentObject['name'] . ' SET ' .
//            join(
//                ',',
//                (new linq($entity, 'assoc'))
//                ->where(function($v, $k) use ($pk) {
//                    return $k !== $pk['column_name'];
//                })
//                ->select(function($v, $k) use ($columns, $values) {
//                    $values[$k] = self::_getValueForQuery($v, $columns[$k]);
//                    return '`' . $k . '`= :' . $k;
//                })
//                ->getData()
//            ).
//            ' WHERE ' . $pk['column_name'] . '=' . $entity[$pk['column_name']];
//        $this->_execQueryCommand($query, self::QueryTypeUpdate, $values);
//        /*Вернем обновленную информацию*/
//        $table_name = $this->currentObject['name'];
//        return $this->$table_name->getEntity($entity[$pk['column_name']]);
    }

    /**
     * Подготавливает значение к использованию в SQL-запросе
     * @param string $v
     * @param array $column
     * @return DateTime|int|mixed|string
     * @throws Exception
     */
    protected static function _getValueForQuery($v, &$column) {
//        if ($v === null) {
//            return 'null';
//        }
//        switch ($column['data_type']) {
////            case 'int':
////            case 'year':
////            case 'bigint':
////            case 'mediumint':
////            case 'smallint':
////            case 'decimal':
////            case 'dec':
////            case 'double':
////            case 'float':
////            case 'real':
////            case 'tinyint':
////                /*
////                 * Защита от ввода недопустимых значений,
////                 * которые потенциально опасны для БД
////                 */
////                if (!is_numeric($v)) {
////                    $v = 0;
////                }
////                return $v;
////            case 'char':
////            case 'varchar':
////            case 'nvarchar':
////            case 'text':
////            case 'tinytext':
////            case 'mediumtext':
////                /*В строковых значениях необходимо экранировать кавычки и обратные слеши*/
////                return $v != null ? '\'' . str_replace('\'', '\'\'', str_replace('\\', '\\\\', (string)$v)) . '\'' : 'null';
////            case 'tinyint(1)':
////                return $v === TRUE || (gettype($v) === gettype('aaa') && strtolower($v) === 'true') || (int)$v === 1 ? 1 : 0;
//            case 'bit':
//                return 'b\'' . (gettype($v) === gettype(true) ?
//                    ($v ? '1' : '0') :
//                    (($v = strtolower($v)) && ($v === 'true' || $v === '1') ? '1' : '0')) . '\'';
//            case 'json':
//                if (gettype($v) === gettype('') ) {
//                    /*Это строка, которую надо распарсить перед сохранением*/
//                    if ($v !== '') {
//                        $v = json_decode($v);
//                    }
//                }
//                else {
//                    /*Это готовый объект для сохранения*/
//                        $v = $v;
//                }
//                return $v;
//            case 'datetime':
//                $formatString = 'Y-m-d H:i:s';
//                if (gettype($v) === gettype('') ) {
//                    if ($v !== '') {
//
//                        $v = new DateTime($v);
//                        $v = '\'' . date($formatString, $v->getTimestamp()) . '\'';
//                    }
//                }
//                elseif (gettype($v) === gettype(array())) {
//                    if (array_key_exists('date', $v)) {
//                        $v = new DateTime($v['date']);
//                        $v = '\'' . date($formatString, $v->getTimestamp()) . '\'';
//                    }
//                    else {
//                        $v = 'null';
//                    }
//                }
//                else {
//                    try {
//                        $v = '\'' . date($formatString, $v->getTimestamp()) . '\'';
//                    } catch (Exception $ex) {
//                        $v = 'null';
//                    }
//                }
//                return $v;
//            case 'date':
//                $formatString = 'Y-m-d';
//                if (gettype($v) === gettype('') ) {
//                    if ($v !== '') {
//                        $v = new DateTime($v);
//                        $v = '\'' . date($formatString, $v->getTimestamp()) . '\'';
//                    }
//                }
//                elseif (gettype($v) === gettype(array())) {
//                    if (array_key_exists('date', $v)) {
//                        $v = new DateTime($v['date']);
//                        $v = '\'' . date($formatString, $v->getTimestamp()) . '\'';
//                    }
//                    else {
//                        $v = 'null';
//                    }
//                }
//                else {
//
//                    try {
//                        $v = '\'' . date($formatString, $v->getTimestamp()) . '\'';
//                    } catch (Exception $ex) {
//                        $v = 'null';
//                    }
//                }
//                return $v;
//            default: return $v;
//        }
    }

    /**
     * @param string $condition
     * @param string $required_fields
     * @param array $values
     * @param null $offset
     * @return array|null
     * @throws Exception
     */
    public function getFirstRow(
            $condition = null, //строка
            $required_fields = null, //массив строк
            $values = array(),
            $offset = null
            ) {
        $rows = $this->getRows($condition, $required_fields, $values, $offset, 1);
        return $rows != null  && count($rows) > 0 ? $rows[0] : null;
    }

    /**
     * Метод позволяет выбрать из БД конкретную запись по ее Id
     * Возвращает Entity - ассоциативный массив с типизированными значениями
     * @param int $IdEntity
     * @return array|null
     * @throws Exception
     */
    public function getEntity(            
            $IdEntity//Идентификатор записи
            ) {
        if (!$this->currentObject) {
            throw new Exception('Не установлен объект для извелечения из БД');
        }
        /*Найдем ключевую колонку*/
        $primaryKey = (new linq($this->currentObject['fields'], 'assoc'))->first(function($col){ return $col['_primary_key'] === true;});
        $rows = array();
        if ($primaryKey) {
            /*Отберем список колонок, которым надо преобразовать тип из строкового*/
            $columns = $this->currentObject['fields'];
            /*Запросим строки и сразу произведем типизацию*/
            $rows = (new linq($this->query('select * from ' . $this->currentObject['name'] . ' WHERE ' . $primaryKey['column_name'] . '=' . $IdEntity)))
                ->where(function($row){ return count($row) > 0;})
                ->for_each(function(&$row) use ($columns){
                    self::_convertValue($row, $this->currentObject['fields']);
                })->getData();
        }
        return count($rows) > 0 ? $rows[0] : null;
    }

    /**
     * Возвращает экземпляр класса
     * @param int $IdObject
     * @return mixed
     * @throws Exception
     */
//    public function getObject(
//            $IdObject
//            ) {
//        if (!$this->currentObject) {
//            throw new Exception('Не установлен объект для извелечения из БД');
//        }
//
//        $className = $this->getObjClassName();
//
//        return new $className($this->getEntity($IdObject), $this->currentObject['name']);
//    }

    /**
     * @param null $values
     * @return array|null
     * @throws Exception
     */
//    public function getEmptyEntity(
//            $values = null
//            ) {
//        if (!$this->currentObject) {
//            throw new Exception('Не установлен объект для извелечения из БД');
//        }
//        $values = gettype($values) === gettype(array()) ? $values : array();
//        $entity = (new linq($this->currentObject['fields'], 'assoc'))
//            ->toAssoc(
//                function($column){ return $column['column_name'];},
//                function($column) use ($values) {
//                    return array_key_exists($column['column_name'], $values) ?
//                        $values[$column['column_name']] : null;
//                }
//            )->getData();
//        self::_convertValue($entity, $this->currentObject['fields']);
//        return $entity;
//    }

    /**
     * Конвертирует значения в Entity в соответствии с типом полей
     * @param array $entity
     * @param array $columns
     * @throws Exception
     */
    protected static function _convertValue(&$entity, &$columns) {
        foreach ($entity as $k => &$v) {
            switch ($columns[$k]['data_type']) {
                case 'int':
                case 'year':
                case 'bigint':
                case 'mediumint':
                case 'smallint':
                    $entity[$k] = (int)$v;
                    break;
                case 'decimal':
                case 'dec':
                case 'double':
                case 'float':
                case 'real':
                    $entity[$k] = (float)$v;
                    break;
                case 'char':
                case 'varchar':
                case 'nvarchar':
                case 'text':
                case 'tinytext':
                case 'mediumtext':
                    $entity[$k] = $v . '';
                    break;
                case 'tinyint':
                    $entity[$k] = $v === TRUE || (gettype($v) === gettype('aaa') && strtolower($v) === 'true') || (int)$v === 1 ? 1 : 0;
                    break;
                case 'bit':
                    $entity[$k] = $v === '1' || $v === true || $v === 1;
                    break;
                case 'json':
                    if ($v !== null && trim($v) !== '') {
                        $entity[$k] = json_decode($v);
                    }
                    else {
                        $entity[$k] = null;
                    }
                    break;
                case 'datetime':
                case 'date':
                    $entity[$k] = new DateTime($v);
                    break;
            }
        }
    }

    /**
     * Метод получает характеристики запрашиваемого класса из БД
     * @param string $name
     * @return $this
     * @throws Exception
     */
    public function __get(string $name) {
        /*Проверим, есть ли такая таблица в БД*/
        $list = null;
        if (count($list = $this->query('select table_name from information_schema.tables where table_schema=\'' 
                . $this->dbname . '\' AND LOWER(table_name)=\'' . strtolower($name) . '\'')) < 1) {
            throw new Exception('Неизвестный тип объекта.');
        }
        else {
            $table_params = $list[0];
        }
        /*Получим необходимые характеристики, чтобы по ним построить выборку*/
        $this->currentObject = array(
            'name' => $table_params['table_name'],
            'fields' => (new linq($this->getClassColumns($table_params['table_name']))
                )->where(function($line){
                    return count($line) > 0;
                })->select(function($line){
                    $key = 'max_length';
                    $line[$key] !== null && ($line[$key] = (int)$line[$key]);
                    $key = 'num_prec';
                    $line[$key] !== null && ($line[$key] = (int)$line[$key]);
                    $key = 'column_key';
                    $line['_primary_key'] = $line[$key] !== null && strtolower($line[$key]) === 'pri';
                    $key = 'is_nullable';
                    $line[$key] = $line[$key] !== '1' ? false : true;
                    return $line;
                })->toAssoc(function($column){
                    return $column['column_name'];
                })->getData()
                    
        );
        return $this;
    }

    private function getClassColumns(string $className) {
        $columns = $this->query('select '
            . 'table_name, '
            . 'column_name, '
            . 'data_type, '
            . 'character_maximum_length as max_length, '
            . 'numeric_precision as num_prec, '
            . 'datetime_precision as dtime_prec, '
            . 'character_set_name as char_set, '
            . 'column_key, '
            . 'is_nullable, '
            . 'privileges '
            . ' from information_schema.columns where table_name=\'' . self::camelCaseToUnderscore($className) . '\' and table_schema=\'' . $this->dbname . '\'');

        return array_filter($columns, function($line){ return count($line) > 0 ;});
    }

    private function setColumnsForeignKeys(array $columns, string $className) {
        $references = $this->query('select '
            . '`table_name`, '
            . '`column_name`, '
            . '`referenced_table_name`, '
            . '`referenced_column_name`  '
            . ' from `information_schema`.`key_column_usage` where table_schema = \'' . GlobalConst::DbName .
            '\' AND table_name=\'' . self::camelCaseToUnderscore($className) . '\' AND `referenced_column_name` IS NOT NULL'
        );
        foreach ($references as $reference) {
            $columns[DataBase::underscoreToCamelCase($reference['column_name'])]['foreign_key'] = array(
                'model' => $reference['referenced_table_name'],
                'field' => $reference['referenced_column_name'],
            );
        }
        return $columns;
    }

    private function setColumnsExtLinks(array $columns, string $className) {
        $extLinks = $this->query('select '
            . '`table_name`, '
            . '`column_name`, '
            . '`referenced_table_name`, '
            . '`referenced_column_name`  '
            . ' from `information_schema`.`key_column_usage` where table_schema = \'' . GlobalConst::DbName .
            '\' AND referenced_table_name=\'' . self::camelCaseToUnderscore($className) . '\''
        );
        foreach ($extLinks as $extLink) {
            $columns[DataBase::underscoreToCamelCase($extLink['referenced_column_name'])]['external_link'] = array(
                'model' => $extLink['table_name'],
                'field' => $extLink['column_name'],
            );
        }
        return $columns;
    }

    private function setPDOParamType(array $columns) {
        foreach ($columns as $columnData) {
            $dataType = $columnData['data_type'];
            if (($pos = strpos($dataType, '(')) !== false) {
                $dataType = substr($dataType, 0, $pos);
            }

        }
        return $columns;
    }

    private static function getRootNameSpace() : string
    {
        return explode('\\', __NAMESPACE__)[0];
    }

    /**
     * Метод генерирует Partial-класс указанной сущности из БД. Используется для разработки
     * @param string $className Имя таблицы из БД в CamelCase, для которой необходимо сгенерировать базовый класс для использования в PHP
     */
    public function generateClass(string $className) {
        $className = self::underscoreToCamelCase($className);

        $tableName = self::camelCaseToUnderscore($className);

        $this->generatePartialModel($className, $tableName);

        $this->generateModel($className);

        $this->registerModel($className);
    }

    private function getPartialModelFieldsDocBlock(string $className) {
        $reflectClass = new \ReflectionClass(self::getRootNameSpace() . '\\Model\\Partial\\' . $className . 'Partial');
        $fieldsDocs = array();
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
        fwrite($fileHandler, ' * Допускается вручную только расширять doc-блок публичный полей класса. ');
        fwrite($fileHandler,  PHP_EOL);
        fwrite($fileHandler, ' * При этом разделы @var и @database_column_name будут автоматически перезаписываться.');
        fwrite($fileHandler, ' */');
        fwrite($fileHandler,  PHP_EOL);

    }

    /**
     * Метод генерирует базовую модель сущности БД
     * @param string $className
     * @param string $tableName
     */
    private function generatePartialModel(string $className, string $tableName) {
        $partialModelFileName = getcwd() . DIRECTORY_SEPARATOR .
            GlobalConst::PartialClassDirectory . DIRECTORY_SEPARATOR .
            $className . 'Partial.php';
        if (file_exists($partialModelFileName)) {
            $fieldsDoc = $this->getPartialModelFieldsDocBlock($className);
        }
        $partialHandle = fopen(
            $partialModelFileName,
            'wt'
        );
        fwrite($partialHandle, '<?php ');
        fwrite($partialHandle,  PHP_EOL);
        fwrite($partialHandle,  PHP_EOL);
        static::PartialModelInstruction($partialHandle);
        fwrite($partialHandle,  PHP_EOL);
        fwrite($partialHandle,  PHP_EOL);
        fwrite($partialHandle, 'namespace ' . self::getRootNameSpace() . '\\Model\\Partial;' . PHP_EOL);
        fwrite($partialHandle,  PHP_EOL);
        fwrite($partialHandle, 'use ' . self::getRootNameSpace() . '\\Model\\Model;' . PHP_EOL);
        fwrite($partialHandle,  PHP_EOL);
        fwrite($partialHandle, 'abstract class ' . ucfirst($className) . 'Partial extends Model {' . PHP_EOL);
        fwrite($partialHandle, "\t" . 'const DataTable = \'' . $tableName . '\';' . PHP_EOL);

        $columns = $this->getClassColumns(self::camelCaseToUnderscore($className));

        $primaryColumn = (new linq($columns))->first(function($column){ return $column['column_key'] === GlobalConst::MySqlPKVal;});
        fwrite($partialHandle, "\t" . 'const PrimaryColumnName = ' . (
            $primaryColumn ?
                '\'' .  DataBase::underscoreToCamelCase($primaryColumn['column_name']) . '\'' :
                'null'
            ) . ';' . PHP_EOL);


        $columns = (new linq($columns))->toAssoc(function($column){ return DataBase::underscoreToCamelCase($column['column_name']);})->getData();

        $columns = $this->setColumnsForeignKeys($columns, $className);

        $columns = $this->setColumnsExtLinks($columns, $className);

        fwrite($partialHandle, '/** @var array */' . PHP_EOL);
        fwrite($partialHandle, 'protected static $fieldsInfo = ' . var_export($columns, true) . ';' . PHP_EOL);

        $columns = array_values($columns);

        uasort($columns, function($a, $b){ return $a['column_name'] <=> $b['column_name'];});

        array_walk($columns, function($line) use ($partialHandle, $fieldsDoc){
            $_column_name = $line['column_name'];
            $columnName = self::underscoreToCamelCase($_column_name);
            if (isset($fieldsDoc[$columnName])) {
//                echo __LINE__;
//                echo '<br />';
//                var_dump($fieldsDoc[$columnName]);
//                echo '<hr />';

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

                fwrite($partialHandle, "\t" . '/**' . PHP_EOL);
                foreach ($fieldsDoc[$columnName]['doc'] as $line){
                    if (mb_strpos($line, '@var') === 0) {
                        $line = '@var ' . $var;
                    }
                    else if (mb_strpos($line, '@database_column_name') === 0) {
                        $line = '@database_column_name ' . $databaseColumnName;
                    }
                    fwrite($partialHandle, "\t" . '* ' . $line . PHP_EOL);
                };
                fwrite($partialHandle, "\t" . '*/' . PHP_EOL);
            }
            else {
                fwrite($partialHandle, "\t" . '/**' . PHP_EOL);
                fwrite($partialHandle, "\t" . '* @database_column_name ' . $_column_name . PHP_EOL);
                fwrite($partialHandle, "\t" . '* @alias' . PHP_EOL);
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

            fwrite($partialHandle, "\t" . 'public $' . $columnName . ';' . PHP_EOL . PHP_EOL);
        });

        fwrite($partialHandle, '}' . PHP_EOL);
        fclose($partialHandle);
    }

    /**
     * Метод формирует php-класс для использования в коде проекта
     * @param string $className
     */
    private function generateModel(string $className) {
        $classFileName = getcwd() . DIRECTORY_SEPARATOR .
            GlobalConst::ClassDirectory. DIRECTORY_SEPARATOR .
            $className . '.php';
        if (!file_exists($classFileName)) {
            $classFileHandle = fopen($classFileName, 'wt');

            $partialClassRelativePath = DIRECTORY_SEPARATOR . GlobalConst::PartialClassDirectory . DIRECTORY_SEPARATOR .
                $className . 'Partial.php';
            fwrite($classFileHandle, '<?php');
            fwrite($classFileHandle, PHP_EOL);
            fwrite($classFileHandle, 'namespace ' . self::getRootNameSpace() . '\\Model;');
            fwrite($classFileHandle, PHP_EOL . PHP_EOL);
            fwrite($classFileHandle, 'require_once getcwd() . \'/' . $partialClassRelativePath . '\';');
            fwrite($classFileHandle, PHP_EOL . PHP_EOL);
            fwrite($classFileHandle, 'use  ' . self::getRootNameSpace() . '\\DataBase;');
            fwrite($classFileHandle, PHP_EOL . PHP_EOL);
            fwrite($classFileHandle, 'use  ' . self::getRootNameSpace() . '\\Model\\Partial\\' . ucfirst($className) . 'Partial;');
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
            $lines = [];
            while (!feof($classFileHandle)) {
                $line = fgets($classFileHandle);
                if (preg_match('/class\s+' . ucfirst($className) . '/', $line)) {
                    $lines[] = 'class ' . ucfirst($className) . ' extends ' . ucfirst($className) . 'Partial {' . PHP_EOL;
                }
                else {
                    $lines[] = $line;
                }
            }
            fclose($classFileHandle);
            file_put_contents($classFileName, $lines);
        }
    }

    private function registerModel(string $className) {
        /*Регистрируем созданный класс*/
        $classRelativePath = DIRECTORY_SEPARATOR . GlobalConst::ClassDirectory . DIRECTORY_SEPARATOR . $className . '.php';
        $classRegisterHandle = fopen(getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ClassRegistry, 'r+t');
        if (strpos(fgets($classRegisterHandle), '<?php') === false) {
            fwrite($classRegisterHandle, '<?php' . PHP_EOL);
        }
        /*Ищем информацию о том, что файл уже зарегистрирован*/
        $newline = 'require_once getcwd() . \'' . $classRelativePath . '\';';
        while (trim($line = fgets($classRegisterHandle)) !== $newline) {
            if(feof($classRegisterHandle)) {
                fwrite($classRegisterHandle, $newline . PHP_EOL);
                break;
            }
        }
        fclose($classRegisterHandle);
    }

    /**
     * Метод формирует из CamelCase under_score
     * @param string $camelCase
     * @return string
     */
    public static function camelCaseToUnderscore(string $camelCase) : string {
        $underscore = preg_replace_callback('/[A-Z]/', function ($matches) { return  '_' . strtolower($matches[0]);}, $camelCase);
        return $underscore[0] === '_' ? substr($underscore, 1) : $underscore;
    }

    /**
     * Метод формирует из under_score CamelCase
     * @param string $underscore
     * @return string
     */
    public static function underscoreToCamelCase(string $underscore) {
        $camelCase = preg_replace_callback('/(_[a-z])/', function ($matches) { return strtoupper($matches[1][1]);}, $underscore);
        return strtoupper($camelCase[0]) . substr($camelCase, 1);
    }

    /**
     * Переключает используемую базу данных
     * @param string $dbName
     * @return bool
     */
    public function UseDataBase($dbName) : bool {
        $res = (new linq($this->query('SHOW DATABASES')))
            ->select(function($row){ return $row['Database'];})
            ->first(function(string $existsDatabase) use ($dbName) {
                if (strtolower($existsDatabase !== strtolower($dbName))) {
                    return false;
                }
                $this->dbname = $existsDatabase;
                return true;
            });
        if (!$res) {
            throw new \Exception('Database \'' . $dbName . '\' does not exists!');
        }
        else {
            $this->query('use `' . $this->dbname . '`');
        }
    }
}
