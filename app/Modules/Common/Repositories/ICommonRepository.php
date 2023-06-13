<?php
interface ICommonRepository {
    public function find();
    public function findBy($field, $value);
    public function store($data);
    public function update($id, $data);
    public function delete($id);
    public function deleteBy($field, $value);
    public function query($query);
}