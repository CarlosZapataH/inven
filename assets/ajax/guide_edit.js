Vue.use(VeeValidate);
VeeValidate.Validator.localize("es", {
  messages: {
    required: "Este campo es obligatorio",
    email: "El campo de correo electrónico no es válido",
  },
});

console.log(this);
const globalObject = this;

VeeValidate.Validator.extend("validationRuc", {
  validate: (value) => /^[A-Za-z0-9]{11}$/.test(value),
  message:
    "El campo debe ser alfanumérico y tener una longitud de 11 caracteres.",
});

new Vue({
  el: "#app",
  data: {
    ent_RemitenteGRR: {
      at_NumeroDocumentoIdentidad: "",
      at_RazonSocial: "",
      at_NombreComercial: "",
    },

    ent_DestinatarioGRR: {
      at_TipoDocumentoIdentidad: 6,
      at_NumeroDocumentoIdentidad: null,
      at_RazonSocial: "",
      at_CorreoPrincipal: "",
      aa_CorreoSecundario: "",
    },

    ent_DatosGeneralesGRR: {
      at_FechaEmision: "",
      at_Serie: "", //T004
      at_Numero: "", //445
      at_Observacion: "",
      at_HoraEmision: "",
      at_CodigoMotivo: 4,
      ent_InformacionPesoBrutoGRR: {
        at_Peso: "",
        at_UnidadMedida: "KGM",
        at_Cantidad: "",
      },
    },

    en_InformacionTransporteGRR: {
      at_Modalidad: 1,
      at_FechaInicio: "",
    },

    ent_PuntoPartidaGRR: {
      at_Ubigeo: "",
      at_DireccionCompleta: "",
      at_CodigoEstablecimiento: "",
      at_NumeroDocumentoIdentidad: "",
    },

    ent_PuntoLlegadaGRR: {
      at_Ubigeo: "",
      at_DireccionCompleta: "",
      at_CodigoEstablecimiento: "",
      at_NumeroDocumentoIdentidad: "",
    },

    ent_TransportePublicoGRR: {
      at_TipoDocumentoIdentidad: 6,
      at_NumeroDocumentoIdentidad: "",
      at_RazonSocial: "",
      at_NumeroMTC: "",
    },

    en_ConductorGRR: {
      at_TipoDocumentoIdentidad: null,
      at_NumeroDocumentoIdentidad: null,
      at_Licencia: "",
      at_Nombres: "",
      at_Apellidos: "",
    },
    en_VehiculoGRR: {
      aa_NumeroPlaca: "",
    },

    drivers: [],
    vehicles: [],

    movement: null,
    movementDetail: [],
    documentTypes: [],
  },
  // validations: {
  //   ent_RemitenteGRR: {
  //     at_NumeroDocumentoIdentidad: {
  //       required: true,
  //       regex: /^[0-9]{11}$/, // Validación de RUC con 11 dígitos numéricos
  //     },
  //   },
  // },
  created() {
    this.getData();
    this.getDocumentType();
  },
  mounted() {},
  computed: {
    idMov: function () {
      const urlParams = new URLSearchParams(window.location.search);
      const queryValue = urlParams.get("idMovimiento");
      return queryValue;
    },
  },
  methods: {
    addDriver() {
      if (this.drivers.length < 3) {
        this.drivers.push({ ...this.en_ConductorGRR });
      }
    },

    removeDriver(index) {
      this.drivers.splice(index, 1);
    },

    addVehicles() {
      if (this.vehicles.length < 3) {
        this.vehicles.push({ ...this.en_VehiculoGRR });
      }
    },

    removeVehicle(index) {
      this.vehicles.splice(index, 1);
    },

    submitForm() {
      this.$refs.ppUbigeoSelects.$validator.validate();
      this.$refs.plUbigeoSelects.$validator.validate();
      this.$validator.validateAll().then((result) => {
        if (result) {
          this.sendGuide();
        } else {
          swal.fire({
            title: "",
            type: "info",
            text: "Completar formulario correctamente",
            showConfirmButton: false,
            timer: 2000,
          });
          console.log(this.$validator.errors.items);
        }
      });
    },

    getData() {
      const urlBase = "Modules/";
      axios
        .get(
          urlBase + "TransitMovement/Controllers/TransitMovementController.php",
          {
            params: {
              action: "getTransitMovement",
              id: this.idMov,
            },
          }
        )
        .then((response) => {
          this.movement = response?.data?.data || {};
          this.movementDetail = response?.data?.data?.detalle || [];
          this.setData();
        })
        .catch((error) => {
          console.error(error);
        });
    },

    getDocumentType() {
      const params = { action: "index" };
      listDocumentType(params).then((response) => {
        this.documentTypes = response?.data || [];
      });
    },

    setData() {
      const movement = { ...this.movement };
      console.log(movement);

      const remitente = movement?.almacen_partida?.company || {};
      this.ent_RemitenteGRR = {
        at_NumeroDocumentoIdentidad: remitente?.document,
        at_RazonSocial: remitente?.name,
        at_NombreComercial: remitente?.commercial_name,
      };

      const destinatario = movement?.almacen_destino?.company || {};
      this.ent_DestinatarioGRR = {
        at_TipoDocumentoIdentidad: destinatario.document_type_code || 6,
        at_NumeroDocumentoIdentidad: destinatario.document,
        at_RazonSocial: destinatario.name,
        at_CorreoPrincipal: "",
        aa_CorreoSecundario: "",
      };

      const puntoPartida = movement?.almacen_partida || {};
      this.ent_PuntoPartidaGRR = {
        at_Ubigeo: puntoPartida?.district?.code || "",
        at_DireccionCompleta: puntoPartida?.direccion_alm,
        at_CodigoEstablecimiento: null,
        at_NumeroDocumentoIdentidad: null,
      };

      const puntoLlegada = movement?.almacen_destino;
      this.ent_PuntoLlegadaGRR = {
        at_Ubigeo: puntoLlegada?.district?.code || "",
        at_DireccionCompleta: puntoLlegada?.direccion_alm,
        at_CodigoEstablecimiento: null,
        at_NumeroDocumentoIdentidad: null,
      };

      // ent_DatosGeneralesGRR: {
      //   at_FechaEmision: "2023-01-17",
      //   at_Serie: "", //T004
      //   at_Numero: 0, //445
      //   at_Observacion: "",
      //   at_HoraEmision: "16:50:00",

      this.ent_DatosGeneralesGRR.at_Observacion = movement?.observ_mov;
    },

    sendGuide() {
      const data = {
        // ent_RemitenteGRR
        almacen_partida: {
          document: this.ent_RemitenteGRR?.at_NumeroDocumentoIdentidad,
          name: this.ent_RemitenteGRR?.at_RazonSocial,
          commercial_name: this.ent_RemitenteGRR?.at_NombreComercial,
          // ent_PuntoPartidaGRR
          ubigeo: this.ent_PuntoPartidaGRR?.at_Ubigeo,
          address: this.ent_PuntoPartidaGRR?.at_DireccionCompleta,
        },
        // ent_DestinatarioGRR
        almacen_destino: {
          document_type_code:
            this.ent_DestinatarioGRR?.at_TipoDocumentoIdentidad,
          document: this.ent_DestinatarioGRR?.at_NumeroDocumentoIdentidad,
          name: this.ent_DestinatarioGRR?.at_RazonSocial,
          email_principal: this.ent_DestinatarioGRR?.at_CorreoPrincipal,
          email_secondary: this.ent_DestinatarioGRR?.aa_CorreoSecundario,
          // ent_PuntoPartidaGRR
          ubigeo: this.ent_PuntoLlegadaGRR?.at_Ubigeo,
          address: this.ent_PuntoLlegadaGRR?.at_DireccionCompleta,
        },
        // ent_DatosGeneralesGRR
        fecha_emision: this.ent_DatosGeneralesGRR?.at_FechaEmision,
        serie: this.ent_DatosGeneralesGRR?.at_Serie,
        numero: this.ent_DatosGeneralesGRR?.at_Numero,
        observacion: this.ent_DatosGeneralesGRR?.at_Observacion,
        hora_emision: this.ent_DatosGeneralesGRR?.at_HoraEmision,
        // ent_InformacionTrasladoGRR
        // // ent_InformacionPesoBrutoGRR
        peso: this.ent_DatosGeneralesGRR?.ent_InformacionPesoBrutoGRR?.at_Peso,
        transporte: {
          modalidad: this.en_InformacionTransporteGRR?.at_Modalidad,
          fecha_inicio: this.en_InformacionTransporteGRR?.at_FechaInicio,
          tipo_documento:
            this.ent_TransportePublicoGRR?.at_TipoDocumentoIdentidad,
          documento: this.ent_TransportePublicoGRR?.at_NumeroDocumentoIdentidad,
          razon_social: this.ent_TransportePublicoGRR?.at_RazonSocial,
          numero_mtc: this.ent_TransportePublicoGRR?.at_NumeroMTC,
        },
      };
      const params = { id: this.idMov };
      createGuide(data, params)
        .then((response) => {
          console.log(response);
          swal.fire({
            title: "",
            type: "success",
            text: "¡El formulario se ha guardado correctamente!",
            showConfirmButton: false,
            timer: 2000,
          });
        })
        .catch((error) => {
          console.log(error);
          swal.fire({
            title: "",
            type: "error",
            text: "No se pudo procesar la solicitud de guardado debido a errores en el formulario. Por favor, revisa la información ingresada.",
            showConfirmButton: false,
            timer: 2000,
          });
        });
    },
  },
});
