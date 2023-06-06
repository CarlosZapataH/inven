<?php
include('header.php');
require_once '../assets/util/Session.php';
require_once '../controller/ControlSesion.php';
require_once '../model/AlmacenModel.php';
require_once '../model/GerenciaModel.php';
require_once '../model/ServicioModel.php';
require_once '../model/PerfilModel.php';
$obj_serv = new ServicioModel();
$obj_alm = new AlmacenModel();
$lstSev = $obj_serv->lst_servicios_Asignados_Activos_xIDUS($user['id_us']);

$nameAlmacen = "Control de EPPS ";
if (sizeof($lstSev) == 1) {
    $lstAlmacenes = $obj_alm->lst_Almacenes_Asignados_xUsuario($lstSev[0]['id_su']);
    if (is_array($lstAlmacenes)) {
        if (sizeof($lstAlmacenes) == 1) {
            $nameAlmacen = "Control de EPPS : " . $lstAlmacenes[0]['titulo_alm'];
        }
    }
}
$obj_pf = new PerfilModel();
$dtllePerfil = $obj_pf->detalle_Perfil_xID($user['perfil']);


/**
 * start code
 */
$idMovimiento = $_REQUEST['idMovimiento'];
require_once '../model/MovimientoModel.php';
$obj_mov = new MovimientoModel();
$movement = $obj_mov->detalle_MovimientoTransito_xID($idMovimiento);
$movement_detail = $obj_mov->lista_MovimientoTransitoDetalle_xIdMovimiento($idMovimiento);

//detalle_MovimientoTransito_xID($id)
//lista_MovimientoTransitoDetalle_xIdMovimiento($id)

/**
 * end code
 */
?>
<!-- <link rel="stylesheet" href="https://unpkg.com/vee-validate/dist/style.css"> -->
<style>
    .img {
        margin: 10px auto;
        border-radius: 5px;
        border: 1px solid #ccc;
        padding: 13px;
        width: 30%;
        background-size: 100% 100%;
    }

    .imgFinger {
        width: 97%;
    }

    .ct2 {
        margin: 10px auto;
        border-radius: 5px;
        border: 1px solid #ccc;
        padding: 5px 7px;
        width: 270px;
        height: 30px;
        background-size: 100% 100%;
    }

    .dataUser {
        margin: 10px auto;
        border-radius: 5px;
        border: 1px solid #ccc;
        padding: 5px 7px;
        width: 270px;
        height: 58px;
        background-size: 100% 100%;
    }
</style>

<input type="hidden" id="movement" value='<?= json_encode($movement) ?>'>
<input type="hidden" id="movement_detail" value='<?= json_encode($movement_detail) ?>'>

<input type="hidden" id="acc_edit" value="<?= $dtllePerfil['editar_perfil'] ?>">
<input type="hidden" id="acc_del" value="<?= $dtllePerfil['eliminar_perfil'] ?>">
<input type="hidden" id="acc_import" value="<?= $dtllePerfil['importar_perfil'] ?>">

<input type="hidden" id="inv_new" value="<?= $dtllePerfil['nuevo_perfil'] ?>">
<input type="hidden" id="inv_trans" value="<?= $dtllePerfil['transferir_perfil'] ?>">
<input type="hidden" id="inv_reti" value="<?= $dtllePerfil['retirar_perfil'] ?>">
<input type="hidden" id="inv_devol" value="<?= $dtllePerfil['devolver_perfil'] ?>">
<div class="container-fluid" id="divHead">
    <div id="app">
        <div class="page-title pl-0 pr-0 pb-10">
            <h4 class="mb-0">
                <!-- <span id="txtAlmacen"><?= $nameAlmacen ?></span> -->
                <span>Formulario GRE</span>
            </h4>
            <ol class="breadcrumb mb-0 pl-0 pt-1 pb-0">
                <li class="breadcrumb-item text-muted">Formulario para generar guía de remisión electrónica (GRE)</li>
            </ol>
        </div>

        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">

                <form @submit.prevent="submitForm">
                    <div class="card">
                        <div class="card-header">
                            Datos del Remitente
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label for="at_NumeroDocumentoIdentidad" class="col-sm-3 col-form-label">RUC</label>
                                <div class="col-sm-9">
                                    <input v-model="ent_RemitenteGRR.at_NumeroDocumentoIdentidad" v-validate="'required|validationRuc'" name="Numero_Documento_Identidad" type="text" v-bind:class="'form-control ' + (errors.first('Numero_Documento_Identidad') ? 'is-invalid' : '')" id="at_NumeroDocumentoIdentidad">
                                    <span class="text-danger">{{ errors.first('Numero_Documento_Identidad') }}</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="rm_at_RazonSocial" class="col-sm-3 col-form-label">Razón social</label>
                                <div class="col-sm-9">
                                    <input v-model="ent_RemitenteGRR.at_RazonSocial" name="RM_Razon_Social" type="text" class="form-control" id="rm_at_RazonSocial">
                                    <span class="text-danger">{{ errors.first('RM_Razon_Social') }}</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="at_NombreComercial" class="col-sm-3 col-form-label">Nombre comercial</label>
                                <div class="col-sm-9">
                                    <input v-model="ent_RemitenteGRR.at_NombreComercial" type="text" class="form-control" id="at_NombreComercial">
                                </div>
                            </div>
                            <button type="submit">Validar</button>
                        </div>
                    </div>
                </form>

                <div class="card">
                    <div class="card-header">
                        Datos del Destinatario
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="des_at_NumeroDocumentoIdentidad" class="col-sm-3 col-form-label">RUC</label>
                            <div class="col-sm-9">
                                <input v-model="ent_DestinatarioGRR.at_NumeroDocumentoIdentidad" type="text" class="form-control" id="des_at_NumeroDocumentoIdentidad">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="at_RazonSocial" class="col-sm-3 col-form-label">Razón social</label>
                            <div class="col-sm-9">
                                <input v-model="ent_DestinatarioGRR.at_RazonSocial" type="text" class="form-control" id="at_RazonSocial">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="des_at_CorreoPrincipal" class="col-sm-3 col-form-label">Correo Principal</label>
                            <div class="col-sm-9">
                                <input v-model="ent_DestinatarioGRR.at_CorreoPrincipal" type="text" class="form-control" id="des_at_CorreoPrincipal">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="des_aa_CorreoSecundario" class="col-sm-3 col-form-label">Correo Secundario</label>
                            <div class="col-sm-9">
                                <input v-model="ent_DestinatarioGRR.aa_CorreoSecundario" type="text" class="form-control" id="des_aa_CorreoSecundario">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        Tipo y Motivo de Traslado
                    </div>
                    <div class="card-body">

                        <div class="form-group row">
                            <label for="at_CodigoMotivo" class="col-sm-3 col-form-label">Tipo de Traslado</label>
                            <div class="col-sm-9">
                                <select v-model="ent_DatosGeneralesGRR.at_CodigoMotivo" id="at_CodigoMotivo" class="form-control">
                                    <option :value="4" selected>Traslado entre establecimientos de la misma empresa</option>
                                    <option :value="6">Devolución</option>
                                    <option :value="13">Otros</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row" v-if="ent_DatosGeneralesGRR.at_CodigoMotivo == 13">
                            <label for="at_DescripcionMotivo" class="col-sm-3 col-form-label">Motivo de Traslado</label>
                            <div class="col-sm-9">
                                <input v-model="ent_DatosGeneralesGRR.at_DescripcionMotivo" type="text" class="form-control" id="at_DescripcionMotivo">
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card">
                    <div class="card-header"> Datos del Transporte </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="at_FechaInicio" class="col-sm-4 col-form-label">Fecha de inicio o entrega</label>
                            <div class="col-sm-8">
                                <input v-model="en_InformacionTransporteGRR.at_FechaInicio" type="date" class="form-control" id="at_FechaInicio">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="at_Modalidad" class="col-sm-4 col-form-label">Tipo Transporte</label>
                            <div class="col-sm-8">
                                <select v-model="en_InformacionTransporteGRR.at_Modalidad" id="at_Modalidad" class="form-control">
                                    <option :value="1">Transporte público</option>
                                    <option :value="2">Transporte privado</option>
                                </select>
                            </div>
                        </div>


                        <div class="card" v-if="en_InformacionTransporteGRR.at_Modalidad == 2">
                            <div class="card-header"> Información del Conductor(es)
                            </div>
                            <div class="card-body">
                                <form @submit.prevent="addDriver">
                                    <div class="row">
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label for="cdt_at_Nombres">Nombres</label>
                                                <input v-model="en_ConductorGRR.at_Nombres" type="text" class="form-control" id="cdt_at_Nombres">
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label for="cdt_at_Apellidos">Apellidos</label>
                                                <input v-model="en_ConductorGRR.at_Apellidos" type="text" class="form-control" id="cdt_at_Apellidos">
                                            </div>
                                        </div>

                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label for="cdt_at_TipoDocumentoIdentidad">Tipo de Documento</label>
                                                <select v-model="en_ConductorGRR.at_TipoDocumentoIdentidad" class="form-control" id="cdt_at_TipoDocumentoIdentidad">
                                                    <option v-for="document in documentTypes" :key="document.codigo + '-documentCode'" :value="document.codigo">{{ document.descripcion }}</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label for="cdt_at_NumeroDocumentoIdentidad">Número de Documento</label>
                                                <input v-model="en_ConductorGRR.at_NumeroDocumentoIdentidad" type="text" class="form-control" id="cdt_at_NumeroDocumentoIdentidad">
                                            </div>
                                        </div>

                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label for="cdt_at_Licencia">Licencia:</label>
                                                <input v-model="en_ConductorGRR.at_Licencia" type="text" class="form-control" id="cdt_at_Licencia">
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Registrar</button>
                                </form>
                                <hr>
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Nombres</th>
                                            <th scope="col">Apellidos</th>
                                            <th scope="col">Tipo de Documento</th>
                                            <th scope="col">Número de Documento</th>
                                            <th scope="col">Licencia</th>
                                            <th scope="col"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(driver, index) in drivers" :key="index + '-driver'">
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ driver.at_Nombres }}</td>
                                            <td>{{ driver.at_Apellidos }}</td>
                                            <td>{{ driver.at_TipoDocumentoIdentidad }}</td>
                                            <td>{{ driver.at_NumeroDocumentoIdentidad }}</td>
                                            <td>{{ driver.at_Licencia }}</td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm" @click="removeDriver(index)">Eliminar</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card" v-if="en_InformacionTransporteGRR.at_Modalidad == 2">
                            <div class="card-header"> Información del vehículo(s) </div>
                            <div class="card-body">
                                <form @submit.prevent="addVehicles">
                                    <div class="row">
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label for="vhl_aa_NumeroPlaca">Nombres</label>
                                                <input v-model="en_VehiculoGRR.aa_NumeroPlaca" type="text" class="form-control" id="vhl_aa_NumeroPlaca">
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Registrar</button>
                                </form>
                                <hr>
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Placa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(vehicle, index) in vehicles" :key="index + '-vehicle'">
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ vehicle.aa_NumeroPlaca }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger" @click="removeVehicle(index)">Eliminar</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card" v-if="en_InformacionTransporteGRR.at_Modalidad == 1">
                            <div class="card-header"> Información del transporte público </div>
                            <div class="card-body">
                                <form @submit.prevent="addDriver">
                                    <div class="row">
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label for="tp_at_TipoDocumentoIdentidad">Tipo de Documento</label>
                                                <select v-model="ent_TransportePublicoGRR.at_TipoDocumentoIdentidad" class="form-control" id="tp_at_TipoDocumentoIdentidad">
                                                    <option v-for="document in documentTypes" :key="document.codigo + '-tp-documentCode'" :value="document.codigo">{{ document.descripcion }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label for="tp_at_NumeroDocumentoIdentidad">Número de Documento</label>
                                                <input v-model="ent_TransportePublicoGRR.at_NumeroDocumentoIdentidad" type="text" class="form-control" id="tp_at_NumeroDocumentoIdentidad">
                                            </div>
                                        </div>

                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label for="tp_at_RazonSocial">Razón Social</label>
                                                <input v-model="ent_TransportePublicoGRR.at_RazonSocial" type="text" class="form-control" id="tp_at_RazonSocial">
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label for="tp_at_NumeroMTC">Número MTC</label>
                                                <input v-model="ent_TransportePublicoGRR.at_NumeroMTC" type="text" class="form-control" id="tp_at_NumeroMTC">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card">
                    <div class="card-header">
                        Ubicaciones
                    </div>
                    <div class="card-body">
                        <h6>Punto de Partida</h6>
                        <br>
                        <div class="form-group row">
                            <label for="pp_at_Ubigeo" class="col-sm-3 col-form-label">Ubigeo</label>
                            <div class="col-sm-9">
                                <input v-model="ent_PuntoPartidaGRR.at_Ubigeo" type="text" class="form-control" id="pp_at_Ubigeo" disabled>
                            </div>
                        </div>
                        <input-ubigeo v-model="ent_PuntoPartidaGRR.at_Ubigeo"></input-ubigeo>
                        <div class="form-group row">
                            <label for="pp_at_DireccionCompleta" class="col-sm-3 col-form-label">Direccion Completa</label>
                            <div class="col-sm-9">
                                <input v-model="ent_PuntoPartidaGRR.at_DireccionCompleta" type="text" class="form-control" id="pp_at_DireccionCompleta">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="pp_at_CodigoEstablecimiento" class="col-sm-3 col-form-label">Codigo Establecimiento</label>
                            <div class="col-sm-9">
                                <input v-model="ent_PuntoPartidaGRR.at_CodigoEstablecimiento" type="text" class="form-control" id="pp_at_CodigoEstablecimiento">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="pp_at_NumeroDocumentoIdentidad" class="col-sm-3 col-form-label">Numero Documento Identidad</label>
                            <div class="col-sm-9">
                                <input v-model="ent_PuntoPartidaGRR.at_NumeroDocumentoIdentidad" type="text" class="form-control" id="pp_at_NumeroDocumentoIdentidad">
                            </div>
                        </div>

                        <hr>

                        <h6 class="mb-4">Punto de Llegada</h6>
                        <br>
                        <div class="form-group row">
                            <label for="pl_at_Ubigeo" class="col-sm-3 col-form-label">Ubigeo</label>
                            <div class="col-sm-9">
                                <input v-model="ent_PuntoLlegadaGRR.at_Ubigeo" type="text" class="form-control" id="pl_at_Ubigeo" disabled>
                            </div>
                        </div>
                        <input-ubigeo v-model="ent_PuntoLlegadaGRR.at_Ubigeo"></input-ubigeo>
                        <div class="form-group row">
                            <label for="pl_at_DireccionCompleta" class="col-sm-3 col-form-label">Direccion Completa</label>
                            <div class="col-sm-9">
                                <input v-model="ent_PuntoLlegadaGRR.at_DireccionCompleta" type="text" class="form-control" id="pl_at_DireccionCompleta">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="pl_at_CodigoEstablecimiento" class="col-sm-3 col-form-label">Codigo Establecimiento</label>
                            <div class="col-sm-9">
                                <input v-model="ent_PuntoLlegadaGRR.at_CodigoEstablecimiento" type="text" class="form-control" id="pl_at_CodigoEstablecimiento">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="pl_at_NumeroDocumentoIdentidad" class="col-sm-3 col-form-label">Numero Documento Identidad</label>
                            <div class="col-sm-9">
                                <input v-model="ent_PuntoLlegadaGRR.at_NumeroDocumentoIdentidad" type="text" class="form-control" id="pl_at_NumeroDocumentoIdentidad">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        Bienes
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table datatable-responsive-row-control">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Código</th>
                                        <th>Descripción</th>
                                        <th>Unid.Med.</th>
                                        <th>Nro.Parte/Serie</th>
                                        <th>Cantidad</th>
                                        <th>kg</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in movementDetail" :key="index + '-movementDetail'">
                                        <td>{{index + 1}}</td>
                                        <td>{{item.cod_mde}}</td>
                                        <td>{{item.des_mde}}</td>
                                        <td></td>
                                        <td>{{item.nparte_mde}}</td>
                                        <td>{{item.cant_mde}}</td>
                                        <td><input type="text" v-model="item.kg_mde"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-end">
                    <div class="col-12 col-md-8 col-xl-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="dg_at_UnidadMedida" class="col-12 col-md-6 col-xl-4 col-form-label">Unidad de Medida</label>
                                    <div class="col-12 col-md-6 col-xl-8">
                                        <input v-model="ent_DatosGeneralesGRR.ent_InformacionPesoBrutoGRR.at_UnidadMedida" id="dg_at_UnidadMedida" type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="dg_at_Peso" class="col-12 col-md-6 col-xl-4 col-form-label">Peso</label>
                                    <div class="col-12 col-md-6 col-xl-8">
                                        <input v-model="ent_DatosGeneralesGRR.ent_InformacionPesoBrutoGRR.at_Peso" id="dg_at_Peso" type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="dg_at_Cantidad" class="col-12 col-md-6 col-xl-4 col-form-label">Cantidad</label>
                                    <div class="col-12 col-md-6 col-xl-8">
                                        <input v-model="ent_DatosGeneralesGRR.ent_InformacionPesoBrutoGRR.at_Cantidad" id="dg_at_Cantidad" type="text" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-body shadow mb-40">
            <h5 class="card-title font-weight-bold">Datos Generales</h5>
            <form id="form_Change_Password">

                <select name="IdAlmacen" id="IdAlmacen" class="form-control selectClass" data-placeholder="Seleccione motivo de ">
                    <option></option>
                    <?php
                    foreach ($lstAlmacenes as $almacen) { ?>
                        <option value="<?= $almacen['id_alm'] ?>" data-vista="<?= $almacen['vista_alm'] ?>"><?= $almacen['titulo_alm'] ?></option>
                    <?php
                    }
                    ?>
                </select>

                <input type="hidden" name="us_pss" id="us_pss" value="<?= $user['id_us'] ?>">
                <div class="form-group row">
                    <label for="id_alm_des" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                        Almacén Destino
                    </label>
                    <div class="col-xl-6 col-lg-6 col-md-8 col-sm-12">
                        <input v-model="movement.id_alm_des" type="text" class="form-control input-md text-left" id="id_alm_des" name="id_alm_des" maxlength="45" autocomplete="off" disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="motivotransfer_mov" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                        Motivo
                    </label>
                    <div class="col-xl-6 col-lg-6 col-md-8 col-sm-12">
                        <input v-model="movement.motivotransfer_mov" type="text" class="form-control input-md text-left" id="motivotransfer_mov" name="motivotransfer_mov" maxlength="45" autocomplete="off" disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="recibido_mov" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                        Atención a
                    </label>
                    <div class="col-xl-6 col-lg-6 col-md-8 col-sm-12">
                        <input v-model="movement.recibido_mov" type="text" class="form-control input-md text-left" id="recibido_mov" name="recibido_mov" maxlength="45" autocomplete="off" disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="autorizado_mov" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                        Autorizado por
                    </label>
                    <div class="col-xl-6 col-lg-6 col-md-8 col-sm-12">
                        <input v-model="movement.autorizado_mov" type="text" class="form-control input-md text-left" id="autorizado_mov" name="autorizado_mov" maxlength="45" autocomplete="off" disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="observ_mov" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                        Observaciones
                    </label>
                    <div class="col-xl-6 col-lg-6 col-md-8 col-sm-12">
                        <input v-model="movement.observ_mov" type="text" class="form-control input-md text-left" id="observ_mov" name="observ_mov" maxlength="45" autocomplete="off" disabled>
                    </div>
                </div>
                <hr>

                <h5 class="card-title font-weight-bold">Datos Transito</h5>
                <div class="form-group row">
                    <label for="fechaguia_mov" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                        Fecha Guía
                    </label>
                    <div class="col-xl-6 col-lg-6 col-md-8 col-sm-12">
                        <input v-model="movement.fechaguia_mov" type="text" class="form-control input-md text-left" id="fechaguia_mov" name="fechaguia_mov" maxlength="45" autocomplete="off" disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="nroguia_mov" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                        Número de Guía
                    </label>
                    <div class="col-xl-6 col-lg-6 col-md-8 col-sm-12">
                        <input v-model="movement.nroguia_mov" type="text" class="form-control input-md text-left" id="nroguia_mov" name="nroguia_mov" maxlength="45" autocomplete="off" disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="timellegada_mov" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                        Tiempo llegada estimada (Días)
                    </label>
                    <div class="col-xl-6 col-lg-6 col-md-8 col-sm-12">
                        <input v-model="movement.timellegada_mov" type="text" class="form-control input-md text-left" id="timellegada_mov" name="timellegada_mov" maxlength="45" autocomplete="off" disabled>
                    </div>
                </div>
            </form>
        </div>


    </div>
    <input type="hidden" id="count_servicio" value="<?= sizeof($lstSev) ?>">
    <!-- <?php
            if (sizeof($lstSev) == 1) { ?>
        <input type="hidden" id="IdServicioUsuario" value="<?= $lstSev[0]['id_su'] ?>">
        <?php
                $lstAlmacenes = $obj_alm->lst_Almacenes_Asignados_xUsuario($lstSev[0]['id_su']); ?>
        <input type="hidden" id="count_almacen" value="<?= sizeof($lstAlmacenes) ?>">
        <?php
                if (is_array($lstAlmacenes)) {
                    if (sizeof($lstAlmacenes) == 1) { ?>
                <input type="hidden" id="IdAlmacen" value="<?= $lstAlmacenes[0]['id_alm'] ?>">
                <input type="hidden" id="VistaAlmacen" value="<?= $lstAlmacenes[0]['vista_alm'] ?>">
            <?php
                    } else { ?>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-10">
                        <input type="hidden" id="VistaAlmacen" value="0">
                        <select name="IdAlmacen" id="IdAlmacen" class="form-control selectClass" data-placeholder="Seleccione...">
                            <option></option>
                            <?php
                            foreach ($lstAlmacenes as $almacen) { ?>
                                <option value="<?= $almacen['id_alm'] ?>" data-vista="<?= $almacen['vista_alm'] ?>"><?= $almacen['titulo_alm'] ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
        <?php
                    }
                }
            } else {
                $obj_ge = new GerenciaModel();
                $lstGerencias = $obj_ge->lst_Gerencia_Activas(); ?>
        <input type="hidden" id="count_almacen" value="0">
        <div class="row">
            <div class="col-lg-7 col-md-6 col-sm-12 col-xs-12 mb-10">
                <select id="IdServicioUsuario" class="form-control input-md selectSearch" data-placeholder="Servicio">
                    <option></option>
                    <?php
                    if (!is_null($lstGerencias)) {
                        $obj_serv_b = new ServicioModel();
                        foreach ($lstGerencias as $gerencia) {
                            $lstServiciosUS = $obj_serv_b->lst_Servicio_xGerencia_Usuario($gerencia['id_ge'], $user['id_us']);
                            if (!is_null($lstServiciosUS)) { ?>
                                <optgroup label="<?= $gerencia['des_ge'] ?>">
                                    <?php foreach ($lstServiciosUS as $servicio) { ?>
                                        <option value="<?= $servicio['id_su'] ?>">
                                            <?= $servicio['des_serv'] ?>
                                        </option>
                                    <?php } ?>
                                </optgroup>
                    <?php }
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-lg-5 col-md-6 col-sm-12 col-xs-12 mb-10">
                <select id="IdAlmacen" class="form-control selectClass" data-placeholder="Almacen..." disabled>
                    <option></option>
                </select>
            </div>
        </div>
    <?php
            }
    ?>
</div>
<div class="container" id="divSearching"></div>
<div class="container-fluid pt-30" id="divResponse"></div> -->


    <div class="scrollToTop"><i class="fa fa-arrow-up"></i></div>
    <?php
    include('footer.php');
    ?>
    <!-- Formatter -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/formatter.js/0.1.5/jquery.formatter.min.js"></script>
    <!-- html2pdf -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
    <!-- Bootstrap  Table -->
    <link href="https://unpkg.com/bootstrap-table@1.18.1/dist/bootstrap-table.min.css" rel="stylesheet">
    <script src="https://unpkg.com/bootstrap-table@1.18.1/dist/bootstrap-table.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.18.1/dist/bootstrap-table-locale-all.min.js"></script>
    <!--Timer-->

    <!--toastr-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css<?= $version ?>" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
    <script src="../assets/ajax/despacho.js<?= $version ?>"></script>

    <!-- vue -->
    <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
    <!--<script src="https://cdn.jsdelivr.net/npm/@vee-validate/rules@4.4.7/dist/vee-validate-rules.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/vee-validate@<3.0.0/dist/vee-validate.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vee-validate@2/dist/locale/es.js"></script>
    <script src="../assets/ajax/InputUbigeo.js<?= $version ?>"></script>
    <script src="../assets/ajax/movement_detail.js<?= $version ?>"></script>