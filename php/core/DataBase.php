<?php
namespace Astkon;
use Astkon\View\View;
use DateTime;
use Exception;
use PDO;
use PDOException;
use PDOStatement;

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
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
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
     * @param string $query       - строка запроса к выполнению
     * @param array $substitution - значения для подстановки в запрос на место placeholder'ов. Ключи в CamelCase
     * @param string $mode        - метод формирования списка значений
     * @return array|false        - возвращает false в случае возникновения ошибок. Информацию об ошибке можно получить через $db->QueryInfo
     */
    public function query(
        string $query,
        ?array $substitution = array(),
        string $mode = 'assoc'
    ) {
        $this->setInitState();
        $data = array();
        if ($query) {
            /*Получаем результат запроса из БД*/
            $query = trim($query);
            $queryType = self::getQueryType($query);
            if($queryType === self::QueryTypeDenied) {
                $this->lastQueryState = array(
                    '@error' => true,
                    'errors' => array(
                        array(
                            'errorType' => 'PHP',
                            'errorCode' => 0,
                            'errorMessage' => 'Запрещенная команда',
                        )
                    ),
                );
                return false;
            }
            else {
                $result = $this->_prepareQueryCommand($query, $queryType, $substitution);
                if (is_array($result)) {
                    if ($this->PDO->inTransaction()) {
                        $this->PDO->rollback();
                    }
                    $this->lastQueryState = $result;
                    return false;
                }

                $data = self::_fetchResult($result, $queryType, $mode);
            }
        }
        return $data;
    }

    /**
     * @param null|PDOStatement$result
     * @param int $queryType
     * @param string $mode
     * @return array
     */
    protected static function _fetchResult($result, int $queryType, string $mode) : array {
        $data = [];
        if ($queryType === self::QueryTypeSelect && $result) {
            while ($row = $result->fetch($mode === 'assoc' ? PDO::FETCH_ASSOC : PDO::FETCH_NUM)) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public function inTransaction() {
        return $this->PDO->inTransaction();
    }

    /**
     * Метод начинает транзакцию.
     */
    public function beginTransaction() {
        $this->setInitState();
        $this->PDO->beginTransaction();
    }

    public function commit() {
        $this->finishTransaction('commit');
    }

    public function rollback() {
        $this->finishTransaction('rollback');
    }

    private function finishTransaction(string $finishMethod) {
        $this->setInitState();
        if ($this->PDO->inTransaction()) {
            try {
                $this->PDO->$finishMethod();
            }
            catch (PDOException $PDOException) {
                $this->lastQueryState = array(
                    '@error' => true,
                    'errors' => array(
                        array(
                            'errorType' => 'PDO',
                            'errorCode' => $PDOException->getCode(),
                            'errorMessage' => $PDOException->getMessage(),
                            'errorInfo' => $PDOException->errorInfo
                        ),
                    ),
                );
                return false;
            }
            return true;
        }

        $errInfo = array(
            '@error' => true,
            'errors' => array(
                array(
                    'errorType' => 'PDO',
                    'errorCode' => null,
                    'errorMessage' => 'Нет активной транзакции',
                    'errorInfo' => null
                ),
            ),
        );
        $view = new View();
        $view->trace = $errInfo;
        $view->error(ErrorCode::PROGRAMMER_ERROR);
        die();
    }

    /**
     * Cтандартная функция выполнения одного запроса вне транзакций
     * @param PDOStatement $stmt
     * @param int $queryType
     * @param array|null $substitution
     * @return array|PDOStatement
     */
    protected function _execQueryCommand (PDOStatement $stmt, int $queryType, $substitution){
        try {

            if ($queryType === self::QueryTypeInsert) {
                if ($this->PDO->inTransaction()) {
                    $stmt->execute();
                    $this->lastInsertId = $this->PDO->lastInsertId();
                }
                else {
                    $this->PDO->beginTransaction();
                    $stmt->execute();
                    $this->lastInsertId = $this->PDO->lastInsertId();
                    $this->PDO->commit();
                }
            }
            else {
                $stmt->execute();
            }
        }
        catch (PDOException $PDOException) {
            $errInfo = array(
                '@error' => true,
                'errors' => array(
                    array(
                        'errorType' => 'PDO',
                        'errorCode' => $PDOException->getCode(),
                        'errorMessage' => $PDOException->getMessage(),
                        'errorInfo' => $PDOException->errorInfo
                    ),
                ),
            );
            try {
                if ($this->PDO->inTransaction()) {
                    $this->PDO->rollback();
                }
            }
            catch (PDOException $PDOExceptionRollback) {
                $errInfo['errors'][] = array(
                    'errorType' => 'PDO',
                    'errorCode' => $PDOExceptionRollback->getCode(),
                    'errorMessage' => $PDOExceptionRollback->getMessage(),
                );
            }

            $errInfo = self::errorMessageParser($errInfo, array_keys($substitution));

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
     * @param array|null $substitution - значения для подстановки. Ключи в CamelCase
     * @param int $queryType
     * @param string $query
     * @return bool|PDOStatement|array
     */
    protected function _prepareQueryCommand($query, int $queryType, $substitution = null) {
        $query = trim($query);
        /** @var \PDOStatement $stmt */
        try {
            $stmt = $this->PDO->prepare($query);
        }
        catch (PDOException $PDOException) {
            switch ($PDOException->getCode()) {
                case '42S22':
                    if ($this->PDO->inTransaction()) {
                        $this->PDO->rollback();
                    }
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
                        'errors' => array(
                            array(
                                'errorType' => 'PDO',
                                'errorCode' => $PDOException->getCode(),
                                'errorMessage' => $PDOException->getMessage(),
                            ),
                        ),
                    );
            }
        }
        catch (\Exception $exception) {
            return array(
                '@error' => true,
                'errors' => array(
                    array(
                        'errorType' => 'PHP',
                        'errorCode' => $exception->getCode(),
                        'errorMessage' => $exception->getMessage(),
                    ),
                ),
            );
        }
        return $this->_bindParamsQueryCommand($stmt, $queryType, $substitution);
    }

    /**
     * @param PDOStatement $stmt
     * @param int $queryType
     * @param null|array $substitution
     * @return array
     */
    protected function _bindParamsQueryCommand(PDOStatement $stmt, int $queryType, $substitution = null) {
        // Альтернатива PDO - https://habr.com/post/141127/
        if (is_array($substitution)) {
            try {
                foreach ($substitution as $k => $v) {
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
                    else if (
                        is_string($v) ||
                        (is_numeric($v) && !is_nan($v))
                    ) {
                        $paramType = PDO::PARAM_STR;
                    }
                    else if ($v instanceof DateTime) {
                        $v = $v->format('Y-m-d H:i:s');
                        $paramType = PDO::PARAM_STR;
                    }
                    if ($paramType !== false) {
                        (function($v) use ($stmt, $k, $paramType) {
                            $stmt->bindParam(':' . $k, $v, $paramType);

                        })($v);
                    }
                }
            } catch (PDOException $PDOException) {
                return array(
                    '@error' => true,
                    'errors' => array(
                        array(
                            'errorType' => 'PDO',
                            'errorCode' => $PDOException->getCode(),
                            'errorMessage' => $PDOException->getMessage(),
                        ),
                    ),
                );
            }
        }
        return $this->_execQueryCommand($stmt, $queryType, $substitution);
    }

    /**
     * Метод возвращает статус последнего запроса
     * true - запрос выполнен без ошибок
     * false - при выполнении были обнаружены ошибки
     * @return bool
     */
    public function QueryState() {
        return $this->lastQueryState === 0;
    }

    /**
     * Метод возвращает информацию о результатах последнего запроса
     * 0 - если запрос успешен
     * array ( ... ) - в случае ошибок массив с информацией об ошибках
     * @return array|int
     */
    public function QueryInfo() {
        return is_array($this->lastQueryState) ? $this->lastQueryState : array();
    }

    public function LastInsertId() {
        return $this->lastInsertId;
    }

    /**
     * Пока позволены только 4 типа запросов, не влияющих на структуру БД
     * @param string $query
     * @return int
     */
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
     * Метод обрабатывает ошибки, возникшие при работе с PDO (подготовкой и отправкой запроса)
     * @param array $errorInfo
     * @param array $fieldNames - наименования полей в CamelCase
     * @return array
     */
    protected static function errorMessageParser(array $errorInfo, array $fieldNames) {
        $programmerErrorProcessor = function(array $errInfo) use ($errorInfo) {
            $backtrace = (new linq(debug_backtrace(2, 6)))
                ->select(function($item){
                    unset($item['file']);
                    return $item;
                })
                ->getData();
            $view = new View();
            $view->trace = array(
                'errorCode' => $errInfo['errorCode'],
                'errorMessage' => $errInfo['errorMessage'],
                'errorInfo' => $errInfo['errorInfo'],
                'class' => __CLASS__,
                'line' => __LINE__,
                'backtrace' => $backtrace,
                'errors' => $errorInfo
            );
            $view->error(ErrorCode::PROGRAMMER_ERROR);
            die();
        };
        foreach ($errorInfo['errors'] as &$errInfo) {
            $expectedErrorColumn = [];
            switch ($errInfo['errorCode']) {
                case '42S02':
                    if(is_array($errInfo['errorInfo']) && count($errInfo['errorInfo']) > 1) {
                        switch ($errInfo['errorInfo'][1]) {
                            case 1146:
                                $programmerErrorProcessor($errInfo);
                                break;
                        }
                    }
                    break;
                case 'HY093':
                    $programmerErrorProcessor($errInfo);
                    die();
                case 'HY000':
                    if ($errInfo['errorMessage']) {

                        /*
                         * Попробуем получить колонку из сообщения об ошибке
                        */

                        foreach ($fieldNames as $fieldName) {
                            $underscoreFieldName = self::camelCaseToUnderscore($fieldName);
                            if (
                                mb_strpos($errInfo['errorMessage'], "'" . $underscoreFieldName . "'") !== false ||
                                mb_strpos($errInfo['errorMessage'], '"' . $underscoreFieldName . '"') !== false
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
                    $errMessage = '';
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
                        $underscoreFieldName = self::camelCaseToUnderscore($fieldName);
                        if (mb_strpos($errMessage, $keyPrefix . $underscoreFieldName . $keySuffix) !== false) {
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
                            $underscoreFieldName = self::camelCaseToUnderscore($fieldName);
                            if (
                                mb_strpos($errInfo['errorMessage'], "'" . $underscoreFieldName . "'") !== false ||
                                mb_strpos($errInfo['errorMessage'], '"' . $underscoreFieldName . '"') !== false
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
        }

        return $errorInfo;

    }


    /**
     * @param null|string $condition
     * @param null|array  $requiredFields
     * @param null|int    $offset
     * @param null|int    $limit
     * @return string
     */
    public function getQueryString(
        $condition = null, //строка
        $requiredFields = null, //массив наименований колонок для выборки,
        $offset = null,
        $limit = null
    ) : string {
        return $this->_getQueryString($condition, $requiredFields, $offset, $limit);
    }

    /**
     * @param null|string $condition
     * @param null|array  $requiredFields
     * @param null|int    $offset
     * @param null|int    $limit
     * @return string
     */
    private function _getQueryString (
        ?string $condition = null, //строка
        ?array $requiredFields = null, //массив наименований колонок для выборки,
        ?int $offset = null,
        ?int $limit = null
    ) : string {
        /*Определим список полей, которые требуется извлечь*/
        $selectedColumns = $this->currentObject['fields'];

        /*Выбираем список моделей для join'a*/
        $joinedColumns = array_filter(
            $selectedColumns,
            function($selectedColumn){
                return array_key_exists('_joinedForeignKey', $selectedColumn);
            }
        );

        if (is_array($requiredFields) && count($requiredFields)) {
            $dictRequiredFields = array_flip(
                array_map(
                    function($fieldName){
                        return DataBase::camelCaseToUnderscore($fieldName);
                    },
                    $requiredFields
                )
            );
            $selectedColumns = array_filter(
                $selectedColumns,
                function($v, $k) use ($dictRequiredFields) {
                    return array_key_exists($k, $dictRequiredFields);
                },
                ARRAY_FILTER_USE_BOTH
            );
        }

        $query = 'SELECT ';
        $query .= implode(
            ', ',
            array_map(
                function($selectedColumn){
                    $querySubstring = '`' . $selectedColumn['table_name'] . '`.`' . $selectedColumn['column_name'] . '`';
                    if (array_key_exists('_alias', $selectedColumn)) {
                        $querySubstring .= ' AS `' . $selectedColumn['_alias'] . '`';
                    }
                    return $querySubstring;
                },
                $selectedColumns
            )
        );
        $query .= ' FROM `' . $this->currentObject['name'] . '` ';
        if (count($joinedColumns)) {
            $joinSubqueries = array();
            foreach ($joinedColumns as $joinedColumn) {
                $subquery = 'LEFT JOIN `' . $joinedColumn['ref_table_name'] . '` ON `'
                    . $joinedColumn['table_name'] . '`.`' . $joinedColumn['column_name'] . '` = `'
                    . $joinedColumn['ref_table_name'] . '`.`' . $joinedColumn['ref_column_name'] . '`';

                if (!array_key_exists($joinedColumn['ref_table_name'], $joinSubqueries)) {
                    /*
                     * Если вдруг где-то встретится повторный join таблицы, не будем это считать за ошибку, а просто проигнорируем его,
                     * поскольку невозможно предсказать ожидаемую сложность и вложенность запросов
                    */
                    $joinSubqueries[$joinedColumn['ref_table_name']] = $subquery;
                }
            }
            $query .= implode(' ', $joinSubqueries);
        }
        $query .= ' ';

        /*Запросим строки и сразу произведем типизацию*/
        return $query . ($condition ? 'where ' . $condition : '') . ' ' .
            (!is_null($limit) ? 'limit ' . $limit : '') . ' ' .
            (!is_null($offset) ? 'offset ' . $offset : '');

    }

    /**
     * Подключение внешнего источника данных
     *
     * @param string     $columnName   - наименование колонки в текущей модели, к которой следует подключить foreign_key
     * @param array|null $joinedFields - массив подключаемых из дополнительной модели полей в формате column_name => column_alias
     * @param int        $recurciveDeep - глубина рекурсивного join'а
     */
    public function joinForeignKey(string $columnName, ?array $joinedFields = null, int $recurciveDeep = 0) {
        if (!$this->currentObject) {
            $backtrace = (new linq(debug_backtrace(2, 8)))
                ->select(function($item){
                    unset($item['file']);
                    return $item;
                })
                ->getData();
            $view = new View();
            $view->trace = array(
                'class'     => __CLASS__,
                'line'      => __LINE__,
                'message'   => 'Не установлен объект для извелечения из БД',
                'backtrace' => $backtrace,
            );
            $view->error(ErrorCode::PROGRAMMER_ERROR);
            die();
        }
        $columnName = self::camelCaseToUnderscore($columnName);
        if (!array_key_exists($columnName, $this->currentObject['fields'])) {
            $backtrace = (new linq(debug_backtrace(2, 8)))
                ->select(function($item){
                    unset($item['file']);
                    return $item;
                })
                ->getData();
            $view = new View();
            $view->trace = array(
                'class'                 => __CLASS__,
                'line'                  => __LINE__,
                'message'               => 'Колонка "' . $columnName . '" отстуствует в текущем объекте для извлечения',
                'CurrentObject[fields]' => $this->currentObject['fields'],
                'backtrace'             => $backtrace,
            );
            $view->error(ErrorCode::PROGRAMMER_ERROR);
            die();
        }
        $columnParams = &$this->currentObject['fields'][$columnName];

        $fkColumns = array();

        if (!is_null($columnParams['ref_table_name'])) {
            $fkColumns[] = array(
                'recurciveDeep' => $recurciveDeep,
                'columnParams'  => $columnParams,
            );
        }

        $index = 0;
        while ($index < count($fkColumns)) {
            $fkColumn = $fkColumns[$index++];
            $deep = $fkColumn['recurciveDeep'];
            if ($deep < 1) {
                continue;
            }
            $fkColumn = $fkColumn['columnParams'];

            $refTableFields = $this->getTableSchema($fkColumn['ref_table_name'])['fields'];
            /*
             * Первичный ключ уже имеется в той таблице, к которой присоединяем
            */
            $refTableFields = array_filter(
                $refTableFields,
                function($v){
                    return !$v['_primary_key'];
                }
            );
            foreach ($refTableFields as $refTableColumnKey => $refTableColumnParams) {
                if (array_key_exists($refTableColumnKey, $this->currentObject['fields'])) {
                    $existsColumn = $this->currentObject['fields'][$refTableColumnKey];
                    if (
                        $existsColumn['table_name'] === $refTableColumnParams['table_name'] &&
                        $existsColumn['column_name'] === $refTableColumnParams['column_name']
                    ) {
                        /*Повторно одну и ту же колонку одной и той же таблицы не добавляем*/
                        continue;
                    }
                    /*
                     * Теоретически несколько таблиц могут иметь колонку с одинаковым наименованием.
                     * Псевдоним из наименования таблицы и наименования колонки даст уникальное значение.
                     * Префикс $fk_ даст понимание, что данный псевдоним сформирован автоматически
                     */
                    $refTableColumnKey = '$fk_' . $fkColumn['ref_table_name'] . '_' . $refTableColumnKey;
                    $refTableColumnParams['_alias'] = $refTableColumnKey;
                }
                $this->currentObject['fields'][$refTableColumnKey] = $refTableColumnParams;
                if (!is_null($refTableColumnParams['ref_table_name'])) {
                    $fkColumns[] = array(
                        'recurciveDeep' => $deep - 1,
                        'columnParams'  => &$this->currentObject['fields'][$refTableColumnKey],
                    );
                }
            }
            $key = isset($fkColumn['_alias']) ? $fkColumn['_alias'] : $fkColumn['column_name'];
            $this->currentObject['fields'][$key]['_joinedForeignKey'] = true;
        }

        if (is_array($joinedFields) && count($joinedFields)) {
            /*
             * !!!!!Вопрос - фильтровать по alias'ам или по реальным наименованиям?
             * Теоретически несколько таблиц могут иметь колонку с одинаковым наименованием.
             * В этом случае не будет возможности обратиться к конкретной колонке для ее исключения.
             * Поэтому правильнее фильтровать по псевдонимам
            */
            $tableName = $this->currentObject['name'];
            $this->currentObject['fields'] = (new linq($this->currentObject['fields'], 'assoc'))
                ->select(function($field, $key) use ($tableName, $joinedFields) {
                    if ($field['table_name'] === $tableName) {
                        return $field;
                    }
                    if (array_key_exists($key, $joinedFields)) {
                        $field['_alias'] = $joinedFields[$key];
                        return  $field;
                    }
                    return null;
                })
                ->where(function($field){ return ! is_null($field);})
                ->toAssoc(function($field, $key){
                    return isset($field['_alias']) && $field['_alias'] !== $key ? $field['_alias'] : $key;
                })
                ->getData();
        }
    }

    /**
     * @param null|string $condition      - условие для выборки
     * @param null|array  $requiredFields - требуемые для выборки поля
     * @param array       $substitution   - значения для подставновки в запрос вместо placeholder'ов
     * @param null|int    $offset
     * @param null|int    $limit
     * @return array
     */
    public function getRows(
        $condition = null, //строка
        $requiredFields = null, //массив наименований колонок для выборки
        $substitution = array(),
        $offset = null,
        $limit = null
            ) : array {
        $this->setInitState();
        if (!$this->currentObject) {
            throw new Exception('Не установлен объект для извелечения из БД');
        }
        /*Отберем список колонок, которым надо преобразовать тип из строкового*/
        $columns = $this->currentObject['fields'];
        /*Запросим строки и сразу произведем типизацию*/
        $query = $this->_getQueryString($condition, $requiredFields, $offset, $limit);
        $rows = (new linq($this->query($query, $substitution)))
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
//        if (!$this->currentObject) {
//            throw new Exception('Не установлен объект для удаления из БД');
//        }
//        if ($entity === null || gettype($entity) !== gettype($entity) || count($entity) < 1) {
//            throw new Exception('Нет информации для удаления из БД');
//        }
//        /*Проверим наличие первичного ключа в таблице - без него удаление этим методом невозможно*/
//        if (($pk = (new linq($this->currentObject['fields'], 'assoc'))
//        ->first(function($column){ return $column['_primary_key'];})) === null) {
//            throw new Exception('Данная таблица не имеет первичного ключа. Поэтому удаление данным методом невозможно');
//        }
//        if (!array_key_exists($pk['column_name'], $entity)) {
//            throw new Exception('Полученный объект не содержит информации о первичном ключе. Удаление невозможно.');
//        }
//        /*Создаем запрос для удаления записи*/
//        $query = 'DELETE FROM ' . $this->currentObject['name'] . ' WHERE  ' . $pk['column_name'] . '=' . $entity[$pk['column_name']] . ' LIMIT 1';
//        $smtp = $this->_execQueryCommand($query, self::QueryTypeDelete);
//        return $smtp->errorCode();
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
     * @param string $requiredFields
     * @param array  $substitution
     * @param null   $offset
     * @return array|null
     * @throws Exception
     */
    public function getFirstRow(
        $condition = null, //строка
        $requiredFields = null, //массив строк
        $substitution = array(),
        $offset = null
            ) {
        $this->setInitState();
        $rows = $this->getRows($condition, $requiredFields, $substitution, $offset, 1);
        return $rows != null  && count($rows) > 0 ? $rows[0] : null;
    }

    private function setInitState() {
        $this->lastInsertId = null;
        $this->lastQueryState = 0;
    }

    /**
     * Метод позволяет выбрать из БД конкретную запись по ее Id
     * Возвращает Entity - ассоциативный массив с типизированными значениями
     * @param int $IdEntity
     * @return array|null
     * @throws Exception
     */
//    public function getEntity(
//            $IdEntity//Идентификатор записи
//            ) {
//        if (!$this->currentObject) {
//            throw new Exception('Не установлен объект для извелечения из БД');
//        }
//        /*Найдем ключевую колонку*/
//        $primaryKey = (new linq($this->currentObject['fields'], 'assoc'))->first(function($col){ return $col['_primary_key'] === true;});
//        $rows = array();
//        if ($primaryKey) {
//            /*Отберем список колонок, которым надо преобразовать тип из строкового*/
//            $columns = $this->currentObject['fields'];
//            /*Запросим строки и сразу произведем типизацию*/
//            $rows = (new linq($this->query('select * from ' . $this->currentObject['name'] . ' WHERE ' . $primaryKey['column_name'] . '=' . $IdEntity)))
//                ->where(function($row){ return count($row) > 0;})
//                ->for_each(function(&$row) use ($columns){
//                    self::_convertValue($row, $this->currentObject['fields']);
//                })->getData();
//        }
//        return count($rows) > 0 ? $rows[0] : null;
//    }

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
                case 'tinyint':
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
//                case 'tinyint':
//                    $entity[$k] = $v === TRUE || (gettype($v) === gettype('aaa') && strtolower($v) === 'true') || (int)$v === 1 ? 1 : 0;
                    break;
                case 'bit':
                    $entity[$k] = $v === '1' || $v === true || $v === 1;
                    break;
                case 'json':
                    /*MySql возвращает все значения в строковом виде, поэтому здесь не проверяем is_string($v)*/
                    if ($v !== null && trim($v) !== '') {
                        $entity[$k] = json_decode($v, true);
                    }
                    else {
                        $entity[$k] = null;
                    }
                    break;
                case 'datetime':
                case 'date':
                    $entity[$k] = is_null($v) ? $v : new DateTime($v);
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
            $tableParams = $list[0];
        }
        /*Получим необходимые характеристики, чтобы по ним построить выборку*/
        $this->currentObject = $this->getTableSchema($tableParams['table_name']);
        return $this;
    }

    private function getTableSchema(string $tableName) {
        return array(
            'name' => $tableName,
            'fields' => (new linq($this->getClassColumns($tableName))
            )->where(function($line){
                return count($line) > 0;
            })->select(function($line){
                /*Делаем полученную инфомрацию более удобной для использования - подгоняем типы значений, добавляем новые флаги*/
                $key = 'max_length';
                $line[$key] !== null && ($line[$key] = (int)$line[$key]);
                $key = 'num_prec';
                $line[$key] !== null && ($line[$key] = (int)$line[$key]);
                $key = 'num_scale';
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
    }

    public function getClassColumns(string $className) {
        $columns = $this->query('SELECT '
            . '`isc`.`table_name`, '
            . '`isc`.`column_name`, '
            . '`isc`.`data_type`, '
            . '`isc`.`character_maximum_length` AS `max_length`, '
            . '`isc`.`numeric_precision` AS `num_prec`, '
            . '`isc`.`numeric_scale` AS `num_scale`, '
            . '`isc`.`datetime_precision` AS `dtime_prec`, '
            . '`isc`.`character_set_name` AS `char_set`, '
            . '`isc`.`column_key`, '
            . '`isc`.`is_nullable`, '
            . '`isc`.`privileges`, '
            . '`iskcu`.`referenced_table_name` as `ref_table_name`, '
            . '`iskcu`.`referenced_column_name` as `ref_column_name` '
            . ' FROM `information_schema`.`columns` AS `isc` '
            . ' LEFT JOIN `information_schema`.`key_column_usage` AS `iskcu` '
            . ' ON `isc`.`table_schema` = `iskcu`.`table_schema` '
            . ' AND `isc`.`table_name` = `iskcu`.`table_name` '
            . ' AND `isc`.`column_name` = `iskcu`.`column_name` '
            . ' where `isc`.`table_name`=\'' . self::camelCaseToUnderscore($className) . '\' and `isc`.`table_schema`=\'' . $this->dbname . '\'');

        return array_filter($columns, function($line){ return count($line) > 0 ;});
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
        return ucfirst($camelCase);
    }

    /**
     * Переключает используемую базу данных
     * @param string $dbName
     * @return bool
     */
    public function UseDataBase($dbName) : bool {
        $this->setInitState();
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
