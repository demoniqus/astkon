<?php
namespace Astkon\Model;

abstract class Model  {
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