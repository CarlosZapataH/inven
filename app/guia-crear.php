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
// $idMovimiento = $_REQUEST['idMovimiento'];
// require_once '../model/MovimientoModel.php';
// $obj_mov = new MovimientoModel();
// $movement = $obj_mov->detalle_MovimientoTransito_xID($idMovimiento);
// $movement_detail = $obj_mov->lista_MovimientoTransitoDetalle_xIdMovimiento($idMovimiento);

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
        <div>
            <div class="row justify-content-center">

                <div class="col-12 col-lg-10" v-if="movement">
                    <div class="card" id="section_origin">
                        <div class="card-header">
                            Datos del Remitente
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Tipo de documento
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <select v-model="start_store.document_type" name="RM_TipoDocumento" v-validate="'required'" class="form-control">
                                        <option v-for="document in documentTypes" :key="document.id + '-RMdocumentCode'" :value="document.id">{{ document.description }}</option>
                                    </select>
                                    <span class="text-danger">{{ errors.first('DES_TipoDocumento') }}</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="at_NumeroDocumentoIdentidad" class="col-sm-3 col-form-label">Documento
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input v-model="start_store.document" name="RM_Numero_Documento_Identidad" v-validate="'required|alpha_dash|length:11'" type="text" class="form-control" id="at_NumeroDocumentoIdentidad">
                                    <span class="text-danger">{{ errors.first('RM_Numero_Documento_Identidad') }}</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="rm_at_RazonSocial" class="col-sm-3 col-form-label">Razón social
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input v-model="start_store.name" name="RM_Razon_Social" v-validate="'required'" type="text" class="form-control" id="rm_at_RazonSocial">
                                    <span class="text-danger">{{ errors.first('RM_Razon_Social') }}</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="at_NombreComercial" class="col-sm-3 col-form-label">Nombre comercial</label>
                                <div class="col-sm-9">
                                    <input v-model="start_store.commercial_name" name="RM_Nombre_Comercial" type="text" class="form-control" id="at_NombreComercial">
                                    <span class="text-danger">{{ errors.first('RM_Nombre_Comercial') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card" id="section_detination">
                        <div class="card-header">
                            Datos del Destinatario
                        </div>
                        <div class="card-body">
                            <div class="form-group row" v-if="!recipientHasCompany">
                                <label class="col-sm-3 col-form-label">Compañia
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <div class="d-flex">
                                        <select v-model="end_store.company_id" name="DES_company" v-validate="'required'" class="form-control">
                                            <option v-for="company in companies" :key="company.id + '-DESdocumentCode'" :value="company.id">{{ company.name + ' - ' + company.document }}</option>
                                        </select>
                                        <company-registration-modal v-model="end_store.company_id" :document-types="documentTypes" @company-registered="getCompany" />
                                    </div>

                                    <span class="text-danger">{{ errors.first('DES_company') }}</span>
                                </div>
                            </div>
                            <div class="form-group row" v-if="recipientHasCompany">
                                <label class="col-sm-3 col-form-label">Tipo de documento
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <select v-model="end_store.document_type" name="DES_TipoDocumento" v-validate="'required'" class="form-control" disabled>
                                        <option v-for="document in documentTypes" :key="document.id + '-DESdocumentCode'" :value="document.id">{{ document.description }}</option>
                                    </select>
                                    <span class="text-danger">{{ errors.first('DES_TipoDocumento') }}</span>
                                </div>
                            </div>
                            <div class="form-group row" v-if="recipientHasCompany">
                                <label class="col-sm-3 col-form-label">Número de Documento
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input v-model="end_store.document" name="DES_Numero_Documento_Identidad" v-validate="'required'" type="text" class="form-control" disabled>
                                    <span class="text-danger">{{ errors.first('DES_Numero_Documento_Identidad') }}</span>
                                </div>
                            </div>
                            <div class="form-group row" v-if="recipientHasCompany">
                                <label class="col-sm-3 col-form-label">Razón social
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input v-model="end_store.name" name="DES_Razon_Social" v-validate="'required'" type="text" class="form-control" disabled>
                                    <span class="text-danger">{{ errors.first('DES_Razon_Social') }}</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Correo Principal
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input v-model="end_store.email_principal" name="DES_Correo_Principal" v-validate="'required|email'" type="email" class="form-control">
                                    <span class="text-danger">{{ errors.first('DES_Correo_Principal') }}</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Correo Secundario
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input v-model="end_store.email_secondary" name="DES_Correo_Secundario" v-validate="'required|email'" type="email" class="form-control">
                                    <span class="text-danger">{{ errors.first('DES_Correo_Secundario') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card" id="section_general">
                        <div class="card-header">
                            Datos Generales - Tipo y Motivo de Traslado
                        </div>
                        <div class="card-body">

                            <div class="form-group row">
                                <label for="at_CodigoMotivo" class="col-sm-3 col-form-label">Tipo de Traslado
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <select v-model="generalData.motive" v-validate="'required'" name="at_CodigoMotivo" id="at_CodigoMotivo" class="form-control">
                                        <option v-for="(motive, index) in motives" :key="index + '-motive'" :value="motive.code">{{ motive.description }}</option>
                                        <!-- <option :value="4" selected>Traslado entre establecimientos de la misma empresa</option>
                                        <option :value="6">Devolución</option>
                                        <option :value="13">Otros</option> -->
                                    </select>
                                    <span class="text-danger">{{ errors.first('at_CodigoMotivo') }}</span>
                                </div>
                            </div>
                            <div class="form-group row" v-if="generalData.motive == 13">
                                <label class="col-sm-3 col-form-label">Descripción del Motivo
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <select v-model="generalData.description_transfer" name="description" v-validate="'required'" class="form-control">
                                                <option v-for="(submotive, index) in submotives" :key="index + '-submotives'" :value="submotive.value">{{ submotive.description }}</option>
                                                <option value="NEW">Escribir nueva descripción</option>
                                            </select>
                                            <span class="text-danger">{{ errors.first('description') }}</span>
                                        </div>
                                        <div class="col-sm-6" v-if="generalData.description_transfer == 'NEW'">
                                            <input v-model="generalData.new_description" name="new_motive" v-validate="'required'" type="text" class="form-control" placeholder="">
                                            <span class="text-danger">{{ errors.first('new_motive') }}</span>
                                        </div>
                                    </div>
                                    <!-- <input v-model="generalData.at_DescripcionMotivo" type="text" class="form-control" id="at_DescripcionMotivo"> -->

                                </div>
                            </div>

                            <!-- <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Serie - Número
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-2">
                                    <input v-model="generalData.at_Serie" name="DG_Serie" v-validate="'required'" type="text" class="form-control" placeholder="">
                                    <span class="text-danger">{{ errors.first('DG_Serie') }}</span>
                                </div>
                                <span>-</span>
                                <div class="col-sm-3">
                                    <input v-model="generalData.at_Numero" name="DG_Numero" v-validate="'required'" type="text" class="form-control" placeholder="">
                                    <span class="text-danger">{{ errors.first('DG_Numero') }}</span>
                                </div>
                            </div> -->

                            <!-- <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Fecha de Emisión
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input v-model="generalData.at_FechaEmision" name="DG_Fecha_Emision" v-validate="'required'" type="date" class="form-control" disabled>
                                    <span class="text-danger">{{ errors.first('DG_Fecha_Emision') }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Hora de Emisión
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input v-model="generalData.at_HoraEmision" name="DG_Hora_Emision" v-validate="'required'" type="time" class="form-control" disabled>
                                    <span class="text-danger">{{ errors.first('DG_Hora_Emision') }}</span>
                                </div>
                            </div> -->

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Observaciones</label>
                                <div class="col-sm-9">
                                    <input v-model="generalData.at_Observacion" name="DG_Observacion" type="text" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card" v-if="generalData.motive == 13" id="section_supplier">
                        <div class="card-header">
                            Datos del Proveedor
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Tipo de documento
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <select v-model="supplier.document_type" name="supplier_document_type" v-validate="'required'" class="form-control">
                                        <option v-for="document in documentTypes" :key="document.id + '-RMdocumentCode'" :value="document.code">{{ document.description }}</option>
                                    </select>
                                    <span class="text-danger">{{ errors.first('supplier_document_type') }}</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="supplier_document" class="col-sm-3 col-form-label">Documento
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input v-model="supplier.document" name="supplier_document" v-validate="'required|alpha_dash'" type="text" class="form-control" id="supplier_document">
                                    <span class="text-danger">{{ errors.first('supplier_document') }}</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="supplier_name" class="col-sm-3 col-form-label">Razón social
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input v-model="supplier.name" name="supplier_name" v-validate="'required'" type="text" class="form-control" id="supplier_name">
                                    <span class="text-danger">{{ errors.first('supplier_name') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card" v-if="generalData.motive == 13" id="section_buyer">
                        <div class="card-header">
                            Datos del Comprador
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Tipo de documento
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <select v-model="buyer.document_type" name="buyer_document_type" v-validate="'required'" class="form-control">
                                        <option v-for="document in documentTypes" :key="document.id + '-RMdocumentCode'" :value="document.code">{{ document.description }}</option>
                                    </select>
                                    <span class="text-danger">{{ errors.first('buyer_document_type') }}</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="buyer_document" class="col-sm-3 col-form-label">Documento
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input v-model="buyer.document" name="buyer_document" v-validate="'required|alpha_dash|length:11'" type="text" class="form-control" id="buyer_document">
                                    <span class="text-danger">{{ errors.first('buyer_document') }}</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="buyer_name" class="col-sm-3 col-form-label">Razón social
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input v-model="buyer.name" name="buyer_name" v-validate="'required'" type="text" class="form-control" id="buyer_name">
                                    <span class="text-danger">{{ errors.first('buyer_name') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card" id="section_transports">
                        <div class="card-header"> Datos del Transporte </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Tipo Transporte
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-8">
                                    <select v-model="en_InformacionTransporteGRR.at_Modalidad" name="DT_Modalidad" v-validate="'required'" class="form-control">
                                        <option :value="1">Transporte público</option>
                                        <option :value="2">Transporte privado</option>
                                    </select>
                                    <span class="text-danger">{{ errors.first('DT_Modalidad') }}</span>
                                </div>
                            </div>

                            <driver-registration-form v-model="drivers" :document-types="documentTypes" :date-issued="dateIssued" v-if="en_InformacionTransporteGRR.at_Modalidad == 2"></driver-registration-form>

                            <vehicle-registration-form v-model="vehicles" v-if="en_InformacionTransporteGRR.at_Modalidad == 2"></vehicle-registration-form>

                            <!-- <div class="card" v-if="en_InformacionTransporteGRR.at_Modalidad == 2">
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
                            </div> -->
                            <div class="card" v-if="en_InformacionTransporteGRR.at_Modalidad == 1">
                                <div class="card-header"> Información del transporte público </div>
                                <div class="card-body">
                                    <form @submit.prevent="addDriver">
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Tipo de Documento
                                                        <span class="text-danger font-weight-bold">*</span>
                                                    </label>
                                                    <select v-model="ent_TransportePublicoGRR.at_TipoDocumentoIdentidad" name="TP_Tipo_Documento_Identidad" v-validate="'required'" class="form-control">
                                                        <option v-for="document in documentTypes" :key="document.id + '-tp-documentCode'" :value="document.code">{{ document.description }}</option>
                                                    </select>
                                                    <span class="text-danger">{{ errors.first('TP_Tipo_Documento_Identidad') }}</span>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Número de Documento
                                                        <span class="text-danger font-weight-bold">*</span>
                                                    </label>
                                                    <input v-model="ent_TransportePublicoGRR.at_NumeroDocumentoIdentidad" name="TP_Numero_Documento" v-validate="'required'" type="text" class="form-control">
                                                    <span class="text-danger">{{ errors.first('TP_Numero_Documento') }}</span>
                                                </div>
                                            </div>

                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Razón Social
                                                        <span class="text-danger font-weight-bold">*</span>
                                                    </label>
                                                    <input v-model="ent_TransportePublicoGRR.at_RazonSocial" name="TP_Razon_Social" v-validate="'required'" type="text" class="form-control" id="tp_at_RazonSocial">
                                                    <span class="text-danger">{{ errors.first('TP_Razon_Social') }}</span>
                                                </div>
                                            </div>

                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>
                                                        Fecha de inicio o entrega
                                                        <span class="text-danger font-weight-bold">*</span>
                                                    </label>
                                                    <input v-model="ent_TransportePublicoGRR.at_FechaInicio" name="TP_FechaInicio" v-validate="'required'" :min="dateIssued" type="date" class="form-control" id="tp_at_RazonSocial">
                                                    <span class="text-danger">{{ errors.first('TP_FechaInicio') }}</span>
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

                    <div class="card" id="section_ubication">
                        <div class="card-header">
                            Ubicaciones
                        </div>
                        <div class="card-body">
                            <h6>Punto de Partida</h6>
                            <br>
                            <input-ubigeo v-model="start_store.ubigeo" initial-name="PP" ref="ppUbigeoSelects"></input-ubigeo>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Ubigeo
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input v-model="start_store.ubigeo" name="PP_Ubigeo" v-validate="'required'" type="text" class="form-control" disabled>
                                    <span class="text-danger">{{ errors.first('PP_Ubigeo') }}</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Direccion Completa
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input v-model="start_store.address" name="PP_Direccion_Completa" v-validate="'required'" type="text" class="form-control">
                                    <span class="text-danger">{{ errors.first('PP_Direccion_Completa') }}</span>
                                </div>
                            </div>
                            <!-- <div class="form-group row">
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
                            </div> -->

                            <hr>

                            <h6 class="mb-4">Punto de Llegada</h6>
                            <br>
                            <input-ubigeo v-model="end_store.ubigeo" initial-name="PL" ref="plUbigeoSelects"></input-ubigeo>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Ubigeo
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input v-model="end_store.ubigeo" name="PL_Ubigeo" v-validate="'required'" type="text" class="form-control" disabled>
                                    <span class="text-danger">{{ errors.first('PL_Ubigeo') }}</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Direccion Completa
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input v-model="end_store.address" name="PL_Direccion_Completa" v-validate="'required'" type="text" class="form-control">
                                    <span class="text-danger">{{ errors.first('PL_Direccion_Completa') }}</span>
                                </div>
                            </div>
                            <!-- <div class="form-group row">
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
                            </div> -->
                        </div>
                    </div>

                    <div class="card" id="section_states">
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
                                            <th>Cantidad</th>
                                            <th>Descripción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in movementDetail" :key="index + '-movementDetail'">
                                            <td>{{index + 1}}</td>
                                            <td>{{item.code}}</td>
                                            <td>{{item.des_mde}}</td>
                                            <td>{{item.unit_measure}}</td>
                                            <td>{{item.quantity}}</td>
                                            <td>
                                                <textarea v-model="item.additional_description" class="form-control" rows="1"></textarea>
                                                <!-- <input  type="text" > -->
                                            </td>
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
                                            <input v-model="generalData.ent_InformacionPesoBrutoGRR.at_UnidadMedida" id="dg_at_UnidadMedida" type="text" class="form-control" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="dg_at_Peso" class="col-12 col-md-6 col-xl-4 col-form-label">Peso
                                            <span class="text-danger font-weight-bold">*</span>
                                        </label>
                                        <div class="col-12 col-md-6 col-xl-8">
                                            <input v-model="generalData.total_witght" name="PB_Peso" v-validate="'required'" id="dg_at_Peso" type="text" class="form-control">
                                            <span class="text-danger">{{ errors.first('PB_Peso') }}</span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="dg_at_Cantidad" class="col-12 col-md-6 col-xl-4 col-form-label">Cantidad
                                            <span class="text-danger font-weight-bold">*</span>
                                        </label>
                                        <div class="col-12 col-md-6 col-xl-8">
                                            <input v-model="generalData.total_quantity" name="PB_Cantidad" v-validate="'required'" id="dg_at_Cantidad" type="text" class="form-control">
                                            <span class="text-danger">{{ errors.first('PB_Cantidad') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div v-for="(group, groupIndex) in apiErros" :key="groupIndex + '-errGroup'">
                                <div v-if="Array.isArray(group)">
                                    <div v-for="(msm, msmIndex) in group" :key="msmIndex + '-errMsm'" class="alert alert-warning" role="alert">
                                        {{ msm }}
                                    </div>
                                </div>
                                <div class="alert alert-warning" role="alert" v-else>
                                    {{group}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-end">
                        <div class="col-auto">
                            <div class="mb-10">
                                <button type="button" class="btn btn-primary" @click="submitForm(false)" :disabled="loadingSave">Guardar Guía</button>
                                <button type="button" class="btn btn-success" @click="submitForm(true)" :disabled="loadingSave">Guardar y Enviar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- <div class="card card-body shadow mb-40">
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
        </div> -->


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
    <script src="https://cdn.jsdelivr.net/npm/vee-validate@2.2.15/dist/vee-validate.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/vee-validate@<3.0.0/dist/vee-validate.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/vee-validate@2/dist/locale/es.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script src="../assets/ajax/apis.js<?= $version ?>"></script>
    <script src="../assets/ajax/InputUbigeo.js<?= $version ?>"></script>
    <script src="../assets/ajax/DriverRegistrationForm.js<?= $version ?>"></script>
    <script src="../assets/ajax/VehicleRegistrationForm.js<?= $version ?>"></script>
    <script src="../assets/ajax/CompanyRegistrationModal.js<?= $version ?>"></script>
    <script src="../assets/ajax/guide_utils_mixin.js<?= $version ?>"></script>
    <script src="../assets/ajax/guide_create.js<?= $version ?>"></script>