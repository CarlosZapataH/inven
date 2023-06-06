<?php
interface ICommonRepository {
    public function find();
    public function findBy($field, $value);
    public function create($data);
    public function update($id, $data);
    public function delete($id);
    public function query($query);
}