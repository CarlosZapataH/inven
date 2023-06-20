<?php
class Setting {
    const TABLE_NAME = 'settings';

    private $id;
    private $name;
    private $description;
    private $group;
    private $value;

    public function __construct(
        $id,
        $name,
        $description,
        $group,
        $value
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->group = $group;
        $this->value = $value;
    }

    // Getters y setters

    public function getId() {
        return $this->id;
    }

    public function getEntity(){
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "group" => $this->group,
            "value" => $this->value
        ];
    }
}