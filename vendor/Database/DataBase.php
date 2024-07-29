<?php

namespace Database;

use Database\Core\Entity\Where;
use Database\Core\Enums\DataBaseOperatorWhereType;
use Database\Core\Enums\DataBaseTable;
use Database\Core\Enums\DataBaseType;

class DataBase
{
    private \PDO $instance;
    private \PDOStatement $statement;
    private bool $prod;

    public function __construct(string $host, string $dbName, string $user, string $password, bool $prod = false)
    {
        $this->prod = $prod;
        $dsn = "mysql:host=" . $host . ";dbname=" . $dbName . ";charset=utf8";
        $this->instance = new \PDO($dsn, $user, $password);
        $this->instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->instance->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        $this->instance->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        $this->instance->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, false);
    }

    public function getDataBaseName(DataBaseType $type): string
    {
        return match($type) {
            DataBaseType::main => 'sql_tsd_main',
            DataBaseType::data => $this->prod ? 'sql_tsd_spb_lich' : 'sql_tsd_test',
            DataBaseType::mdm => 'sql_tsd_mdm',
            DataBaseType::data_debug => "sql_tsd_test",
            DataBaseType::data_prod => "sql_tsd_spb_lich"
        };
    }

    public function beginTransaction(): bool
    {
        return $this->instance->beginTransaction();
    }

    public function inTransaction(): bool
    {
        return $this->instance->inTransaction();
    }

    public function commit(): bool
    {
        if (!$this->inTransaction()) {
            return true;
        }

        return $this->instance->commit();
    }

    public function rollBack(): bool
    {
        if (!$this->inTransaction()) {
            return true;
        }

        return $this->instance->rollBack();
    }

    public function lastInsertId(): int
    {
        $lastInsertId = $this->instance->lastInsertId();
        if (!$lastInsertId) {
            throw new \Exception("Could not get the ID of the last inserted record");
        }

        return (int) $lastInsertId;
    }

    public function rowCount(): int
    {
        return $this->statement->rowCount();
    }

    public function execute(string $query, array $args = []): \PDOStatement
    {
        $statement = $this->instance->prepare($query);
        if ($statement === false) {
            throw new \PDOException($this->instance->errorInfo()[2]);
        }

        if ($statement->execute($args) === false) {
            throw new \PDOException($this->instance->errorInfo()[2]);
        }
        $this->statement = $statement;

        return $statement;
    }

    /**
     * Добавление нескольких записей в бд
     */
    public function insertMultiple(DataBaseType $type, DataBaseTable $table, array $values): void
    {
        if (empty($values)) return;

        $dataBaseName = $this->getDataBaseName($type);

        $params = [];

        $keys = array_keys($values[0]);

        // Название полей, которые нужно добавить
        $quotedArray = array_map(fn($item) => "`$item`", $keys);
        $fields = implode(",", $quotedArray);

        // Собираем одну строку с плейсхолдерами для значений, в виде (?, ...)
        $placeholder_params = substr(str_repeat('?,', count($keys)), 0, -1);
        $placeholder_list = '(' . $placeholder_params . ')';

        // Собираем плейсхолдеры на кол-во строк (?, ...), (?, ...)
        $valuesStr = substr(str_repeat($placeholder_list . ',', count($values)), 0, -1);

        foreach ($values as $value) {
            $params = array_merge($params, array_values($value));
        }

        $query = "INSERT INTO $dataBaseName.$table->value ($fields) VALUES $valuesStr";
        $this->execute($query, $params);
    }

    /**
     * Добавить или обновить строку
     */
    public function upsert(DataBaseType $type, DataBaseTable $table, array $values): void
    {
        if (empty($values)) return;

        $dataBaseName = $this->getDataBaseName($type);

        $params = [];

        $keys = array_keys($values[0]);

        // Название полей, которые нужно добавить
        $quotedArray = array_map(fn($item) => "`$item`", $keys);
        $fields = implode(",", $quotedArray);

        // Собираем одну строку с плейсхолдерами для значений, в виде (?, ...)
        $placeholder_params = substr(str_repeat('?,', count($keys)), 0, -1);
        $placeholder_list = '(' . $placeholder_params . ')';

        // Собираем плейсхолдеры на кол-во строк (?, ...), (?, ...)
        $valuesStr = substr(str_repeat($placeholder_list . ',', count($values)), 0, -1);

        foreach ($values as $value) {
            $params = array_merge($params, array_values($value));
        }

        // Параметры = значение, для обновления
        $updateStr = '';
        foreach ($keys as $key) {
            $updateStr .= "$key = VALUES($key),";
        }
        $updateStr = substr($updateStr, 0, -1);

        $query = "INSERT INTO $dataBaseName.$table->value ($fields) VALUES $valuesStr ON DUPLICATE KEY UPDATE $updateStr";
        $this->execute($query, $params);
    }

    public function update(
        DataBaseType  $type,
        DataBaseTable $table,
        array         $values,
        ?Where        $where = null): void
    {
        $dataBaseName = $this->getDataBaseName($type);

        $fields = '';

        $where_query = '';
        $params = [];

        if (!is_null($where)) {
            $condition = $where->release();

            $where_query = $condition->query;
            $params = $condition->params;
        }

        foreach (array_keys($values) as $key) {
            $fields.= $key.'=?,';
        }

        $params = array_merge(array_values($values), $params);
        $fields = substr($fields, 0, -1);

        $query = "UPDATE $dataBaseName.$table->value SET $fields$where_query";
        $this->execute($query, $params);
    }

    public function delete(DataBaseType $type, DataBaseTable $table, ?Where $where = null): void
    {
        $dataBaseName = $this->getDataBaseName($type);

        $where_query = '';
        $params = [];

        if (!is_null($where)) {
            $condition = $where->release();

            $where_query = $condition->query;
            $params = $condition->params;
        }

        $query = "DELETE FROM $dataBaseName.$table->value$where_query";
        $this->execute($query, $params);
    }

    /**
     * Добавление строки
     */
    public function insert(DataBaseType $type, DataBaseTable $table, array $values): void
    {
        $dataBaseName = $this->getDataBaseName($type);

        $keys = array_keys($values);
        $params = array_values($values);

        // Название полей, которые нужно добавить
        $quotedArray = array_map(fn($item) => "`$item`", $keys);
        $fields = implode(",", $quotedArray);

        // Собираем одну строку с плейсхолдерами для значений, в виде (?, ...)
        $placeholder_params = substr(str_repeat('?,', count($keys)), 0, -1);
        $placeholder_list = '(' . $placeholder_params . ')';

        $query = "INSERT INTO $dataBaseName.$table->value ($fields) VALUES $placeholder_list";
        $this->execute($query, $params);
    }

    public function count(
        DataBaseType  $type,
        DataBaseTable $table,
        ?Where        $where = null) : int
    {
        $dataBaseName = $this->getDataBaseName($type);

        $where_query = '';
        $params = [];

        if (!is_null($where)) {
            $condition = $where->release();

            $where_query = $condition->query;
            $params = $condition->params;
        }

        $query = "SELECT COUNT(*) FROM $dataBaseName.$table->value$where_query";
        $request = $this->execute($query, $params);

        $count = $request->fetch(\PDO::FETCH_COLUMN);
        return $count ?: 0;
    }

    public function updateRow(
        DataBaseType $type,
        DataBaseTable $table,
        array $values,
        int $row = 1): void
    {
        $dataBaseName = $this->getDataBaseName($type);

        $fields = '';

        foreach (array_keys($values) as $key) {
            $fields.= $key.'=?,';
        }

        $params = array_values($values);
        $params[] = $row;
        $fields = substr($fields, 0, -1);

        $query = "UPDATE $dataBaseName.$table->value SET $fields WHERE ?";
        $this->execute($query, $params);
    }

    public function updateGroup(
        DataBaseType $type,
        DataBaseTable $table,
        array $updateValues,
        string $groupField,
        array $groupValues,
        DataBaseOperatorWhereType $groupWhereType = DataBaseOperatorWhereType::in): void
    {
        $dataBaseName = $this->getDataBaseName($type);

        $updateArray = array_map(function($key){ return $key.'=?'; }, array_keys($updateValues));
        $updateString = join(",", $updateArray);
        $groupPlaceholderString = substr(str_repeat('?,', count($groupValues)), 0, -1);
        $params = array_values(array_merge(array_values($updateValues), $groupValues));

        $query = "UPDATE $dataBaseName.$table->value SET $updateString WHERE $groupField $groupWhereType->value ($groupPlaceholderString)";
        $this->execute($query, $params);
    }

    private function select(
        DataBaseType  $type,
        DataBaseTable $table,
        ?Where        $where = null,
        ?string       $group_field = null,
        array         $order_value = [],
        array         $select_fields = [],
        ?int          $limit = null,
        ?int          $offset = null) : \PDOStatement
    {
        $dataBaseName = $this->getDataBaseName($type);

        $where_query = '';
        $group = $group_field ? " GROUP BY $group_field" : "";
        $order = '';
        $limit = $limit ? " LIMIT $limit" : "";
        $offset = $offset ? " OFFSET $offset" : "";

        $params = [];

        if (!is_null($where)) {
            $condition = $where->release();

            $where_query = $condition->query;
            $params = $condition->params;
        }

        if (count($order_value) > 0) {
            $order = ' ORDER BY ';
            foreach ($order_value as $key => $value) {
                $order .= $key . ' ' . $value . ', ';
            }
            $order = rtrim($order, ', ');
        }

        $select = "*";
        if (count($select_fields) > 0) {
            $select = join(',', $select_fields);
        }

        $query = "SELECT $select FROM $dataBaseName.$table->value$where_query$group$order$limit$offset";
        return $this->execute($query, $params);
    }

    /**
     * @param bool $implicitCondition - указывает что условие не явное и подставляет LIMIT = 1
     */
    public function selectOne(
        DataBaseType  $type,
        DataBaseTable $table,
        ?Where        $where = null,
        array         $order_value = [],
        array         $select_fields = [],
        bool          $fetchColumn = false,
        bool          $implicitCondition = false): mixed
    {
        $request = $this->select(
            type: $type,
            table: $table,
            where: $where,
            order_value: $order_value,
            select_fields: $select_fields,
            limit: $implicitCondition ? 1 : null
        );

        return $request->fetch($fetchColumn ? \PDO::FETCH_COLUMN : \PDO::FETCH_DEFAULT);
    }

    public function selectRow(
        DataBaseType $type,
        DataBaseTable $table,
        int $row = 1,
        bool $fetchColumn = true): mixed
    {
        $dataBaseName = $this->getDataBaseName($type);

        $query = "SELECT * FROM $dataBaseName.$table->value WHERE ?";
        $requestData = $this->execute($query, [$row]);

        return $requestData->fetch($fetchColumn ? \PDO::FETCH_COLUMN : \PDO::FETCH_BOTH);
    }

    public function selectAll(
        DataBaseType  $type,
        DataBaseTable $table,
        ?Where        $where = null,
        ?string       $group_field = null,
        array         $order_value = [],
        array         $select_fields = [],
        ?int          $limit = null,
        ?int          $offset = null,
        bool          $fetchColumn = false) : array
    {
        $request = $this->select(
            type: $type,
            table: $table,
            where: $where,
            group_field: $group_field,
            order_value: $order_value,
            select_fields: $select_fields,
            limit: $limit,
            offset: $offset
        );

        return $request->fetchAll($fetchColumn ? \PDO::FETCH_COLUMN : \PDO::FETCH_DEFAULT);
    }
}