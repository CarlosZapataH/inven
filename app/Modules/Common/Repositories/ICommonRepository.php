<?php
interface ICommonRepository {
    public function find();
    public function findAllBy($field, $value);
    public function findBy($field, $value);
    public function findByNumber($serie, $number);
    public function store($data);
    public function update($id, $data);
    public function updateBy($field, $value, $data);
    public function delete($id);
    public function deleteBy($field, $value);
    public function query($query);
}