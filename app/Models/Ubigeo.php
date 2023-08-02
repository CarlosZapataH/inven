<?php
class Ubigeo {
    const TABLE_NAME = 'ubigeo';

    private $id_ubigeo;
    private $nombre_ubigeo;
    private $codigo_ubigeo;
    private $etiqueta_ubigeo;
    private $buscador_ubigeo;
    private $numero_hijos_ubigeo;
    private $nivel_ubigeo;
    private $id_padre_ubigeo;
    private $codigo_inei;

    public function __construct(
        $id_ubigeo,
        $nombre_ubigeo,
        $codigo_ubigeo,
        $etiqueta_ubigeo,
        $buscador_ubigeo,
        $numero_hijos_ubigeo,
        $nivel_ubigeo,
        $id_padre_ubigeo,
        $codigo_inei
    ) {
        $this->id_ubigeo = $id_ubigeo;
        $this->nombre_ubigeo = $nombre_ubigeo;
        $this->codigo_ubigeo = $codigo_ubigeo;
        $this->etiqueta_ubigeo = $etiqueta_ubigeo;
        $this->buscador_ubigeo = $buscador_ubigeo;
        $this->numero_hijos_ubigeo = $numero_hijos_ubigeo;
        $this->nivel_ubigeo = $nivel_ubigeo;
        $this->id_padre_ubigeo = $id_padre_ubigeo;
        $this->codigo_inei = $codigo_inei;
    }

    // Getters y setters

    public function getId() {
        return $this->id;
    }

    public function getEntity(){
        return [
            "id_ubigeo" => $this->id_ubigeo,
            "nombre_ubigeo" => $this->nombre_ubigeo,
            "codigo_ubigeo" => $this->codigo_ubigeo,
            "etiqueta_ubigeo" => $this->etiqueta_ubigeo,
            "buscador_ubigeo" => $this->buscador_ubigeo,
            "numero_hijos_ubigeo" => $this->numero_hijos_ubigeo,
            "nivel_ubigeo" => $this->nivel_ubigeo,
            "id_padre_ubigeo" => $this->id_padre_ubigeo,
            "codigo_inei" => $this->codigo_inei
        ];
    }
}