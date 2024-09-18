<?php
class Profile {
    const TABLE_NAME = 'perfil';

    private $id_perfil;
    private $titulo_perfil;
    private $create_guide;
    private $edit_guide;
    private $delete_guide;
    private $show_guide;
    private $revert_guide;
    private $des_perfil;
    private $admin_guide;
    private $condicion_perfil;

    public function __construct(
        $id_perfil,
        $titulo_perfil,
        $create_guide,
        $edit_guide,
        $delete_guide,
        $show_guide,
        $revert_guide,
        $des_perfil,
        $admin_guide,
        $condicion_perfil
    ) {
        $this->id_perfil = $id_perfil;
        $this->titulo_perfil = $titulo_perfil;
        $this->create_guide = $create_guide;
        $this->edit_guide = $edit_guide;
        $this->delete_guide = $delete_guide;
        $this->show_guide = $show_guide;
        $this->revert_guide = $revert_guide;
        $this->des_perfil = $des_perfil;
        $this->admin_guide = $admin_guide;
        $this->condicion_perfil = $condicion_perfil;
    }

    // Getters y setters

    public function getId() {
        return $this->id_perfil;
    }

    public function getEntity(){
        return [
            'id' => $this->id_perfil,
            'titulo_perfil' => $this->titulo_perfil,
            'create_guide' => $this->create_guide,
            'edit_guide' => $this->edit_guide,
            'delete_guide' => $this->delete_guide,
            'show_guide' => $this->show_guide,
            'revert_guide' => $this->revert_guide,
            'des_perfil' => $this->des_perfil,
            'admin_guide' => $this->admin_guide,
            'condicion_perfil' => $this->condicion_perfil
        ];
    }
}