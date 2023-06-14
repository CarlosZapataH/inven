<?php
interface ICommonRepository {
    public function find();
    public function findBy($field, $value);
    public function findConcat($field1, $field2, $character, $value);
    public function store($data);
    public function update($id, $data);
    public function updateBy($field, $value, $data);
    public function delete($id);
    public function deleteBy($field, $value);
    public function query($query);
}