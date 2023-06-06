<?php
require_once '../../../../ds/AccesoDB.php';
require_once 'ICommonRepository.php';

abstract class CommonRepository implements ICommonRepository{
    protected $connection;
    private $modelClass;

    public function __construct($modelClass) {
        $pdo = AccesoDB::getPDO();
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
        $this->modelClass = $modelClass;
        $this->tableName = $this->getTableName();
        $this->connection = $pdo;
    }

    public function find() {
        // Implementación para obtener todos los registros de la tabla
        $query = "SELECT * FROM {$this->tableName}";
        $stm = $this->connection->prepare($query);
        $stm->execute();
        $result = $stm->fetchAll(PDO::FETCH_ASSOC);

        $models = [];
        foreach ($result as $row) {
            $model = $this->mapRowToModel($row);
            $models[] = $model;
        }

        return $models;
    }

    public function findBy($field, $value) {
        // Implementación para obtener un registro por su ID
        $query = "SELECT * FROM {$this->tableName} WHERE ".$field." = :value";
        $stm = $this->connection->prepare($query);
        $stm->bindParam(":value",$value);
        $stm->execute();
        $result = $stm->fetch(PDO::FETCH_ASSOC);

        if($result){
            return $this->mapRowToModel($result);
        }
        return null;
    }

    public function query($query) {
        // Implementación para obtener todos los registros de la tabla
        $stm = $this->connection->prepare($query);
        $stm->execute();
        $result = $stm->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    public function create($data) {
        // Implementación para crear un nuevo registro
        $query = "INSERT INTO {$this->tableName} (nombre, document) VALUES (:nombre, :document)";
    }

    public function update($id, $data) {
        // Implementación para actualizar un registro existente
        $query = "UPDATE {$this->tableName} SET nombre = :nombre, document = :document WHERE id = :id";
    }

    public function delete($id) {
        // Implementación para eliminar un registro existente
        $query = "DELETE FROM {$this->tableName} WHERE id = :id";
    }

    private function getTableName() {
        return $this->modelClass::TABLE_NAME;
    }

    private function mapRowToModel($row) {
        $reflection = new ReflectionClass($this->modelClass);
        $constructor = $reflection->getConstructor();
        $constructorParams = $constructor->getParameters();
    
        $args = [];
        foreach ($constructorParams as $param) {
            $paramName = $param->getName();
            $args[] = $row[$paramName] ?? null;
        }
        $instance = $reflection->newInstanceArgs((array) $args);
        return $instance->getEntity();
    }
}