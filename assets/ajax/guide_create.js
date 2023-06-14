Vue.use(VeeValidate);
VeeValidate.Validator.localize("es", {
  messages: {
    required: "Este campo es obligatorio",
    email: "El campo de correo electrónico no es válido",
  },
});

VeeValidate.Validator.extend("validationRuc", {
  validate: (value) => /^[A-Za-z0-9]{11}$/.test(value),
  message:
    "El campo debe ser alfanumérico y tener una longitud de 11 caracteres.",
});

new Vue({
  el: "#app",
  data: {
    ent_RemitenteGRR: {
      at_TipoDocumentoIdentidad: 4,
      at_NumeroDocumentoIdentidad: "",
      at_RazonSocial: "",
      at_NombreComercial: "",
    },

    ent_DestinatarioGRR: {
      at_TipoDocumentoIdentidad: 4,
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

    drivers: [],
    vehicles: [],

    movement: null,
    movementDetail: [],
    documentTypes: [],
    apiErros: [],
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
    typeMov: function () {
      const urlParams = new URLSearchParams(window.location.search);
      const queryValue = urlParams.get("tipo");
      return queryValue;
    },
  },
  methods: {
    submitForm(isSend) {
      this.$refs.ppUbigeoSelects.$validator.validate();
      this.$refs.plUbigeoSelects.$validator.validate();
      this.$validator.validateAll().then((result) => {
        let alertMsm = true;
        let isCompleted = true;
        if (result == false) {
          isCompleted = false;
          alertMsm = "Completar formulario correctamente";
        } else if (
          this.en_InformacionTransporteGRR.at_Modalidad == 2 &&
          this.drivers.length == 0
        ) {
          isCompleted = false;
          alertMsm = "Agregar conductores al registro.";
        } else if (
          this.en_InformacionTransporteGRR.at_Modalidad == 2 &&
          this.vehicles.length == 0
        ) {
          isCompleted = false;
          alertMsm = "Agregar vehículos al registro.";
        }

        if (isCompleted) {
          this.sendGuide(isSend);
        } else {
          swal.fire({
            title: "",
            type: "info",
            text: alertMsm,
            showConfirmButton: false,
            timer: 3000,
          });
        }
      });
    },

    getData() {
      let urlBase =
        "Modules/TransitMovement/Controllers/TransitMovementController.php";
      let action = "getTransitMovement";
      if (this.typeMov == "interno") {
        urlBase = "Modules/Movement/Controllers/MovementController.php";
        action = "getMovement";
      }
      axios
        .get(urlBase, {
          params: {
            action: action,
            id: this.idMov,
          },
        })
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

    convertTimeFormat(fullTime) {
      if (fullTime) {
        const [hour, minutes] = fullTime.split(":");
        const date = new Date();
        date.setHours(hour);
        date.setMinutes(minutes);
        const convertedTime = date.toLocaleTimeString([], {
          hour: "2-digit",
          minute: "2-digit",
        });
        return convertedTime;
      }
      return fullTime;
    },

    setData() {
      const movement = { ...this.movement };
      console.log(movement);

      const remitente = movement?.almacen_partida?.company || {};
      const destinatario = movement?.almacen_destino?.company || {};
      const puntoPartida = movement?.almacen_partida || {};
      const puntoLlegada = movement?.almacen_destino;
      const transports = movement?.transports;

      this.ent_RemitenteGRR = {
        ...this.ent_RemitenteGRR,
        document: remitente?.document,
        document_type: remitente?.document_type_code || null,
        name: remitente?.name,
        commercial_name: remitente?.commercial_name,
      };

      this.ent_DestinatarioGRR = {
        at_TipoDocumentoIdentidad: destinatario?.document_type_code || null,
        at_NumeroDocumentoIdentidad: destinatario.document,
        at_RazonSocial: destinatario.name,
        at_CorreoPrincipal: puntoLlegada?.email_principal || null,
        aa_CorreoSecundario: puntoLlegada?.email_secondary || null,
      };

      this.ent_PuntoPartidaGRR = {
        at_Ubigeo: puntoPartida?.district?.code || "",
        at_DireccionCompleta: puntoPartida?.direccion_alm,
        at_CodigoEstablecimiento: null,
        at_NumeroDocumentoIdentidad: null,
      };

      this.ent_PuntoLlegadaGRR = {
        at_Ubigeo: puntoLlegada?.district?.code || "",
        at_DireccionCompleta: puntoLlegada?.direccion_alm,
        at_CodigoEstablecimiento: null,
        at_NumeroDocumentoIdentidad: null,
      };

      this.ent_DatosGeneralesGRR = {
        ...this.ent_DatosGeneralesGRR,
        at_FechaEmision: movement?.fecha_emision || null,
        at_Serie: movement?.serie || null, //T004
        at_Numero: movement?.numero || null, //445
        at_Observacion: movement?.observacion || null,
        at_HoraEmision: this.convertTimeFormat(movement?.hora_emision) || null,
        at_CodigoMotivo: 4,
        ent_InformacionPesoBrutoGRR: {
          at_Peso: movement?.peso || null,
          at_UnidadMedida: "KGM",
          at_Cantidad: "",
        },
      };

      if (
        Array.isArray(transports) &&
        transports.length > 0 &&
        transports[0].modality == 1
      ) {
        this.en_InformacionTransporteGRR = {
          at_Modalidad: transports[0].modality,
          at_FechaInicio: transports[0].start_date,
        };
        this.ent_TransportePublicoGRR = {
          at_TipoDocumentoIdentidad: transports[0].document_type,
          at_NumeroDocumentoIdentidad: transports[0].document,
          at_RazonSocial: transports[0].company_name,
          at_NumeroMTC: transports[0].mtc_number,
        };
      } else if (
        Array.isArray(transports) &&
        transports.length > 0 &&
        transports[0].modality == 2
      ) {
        this.en_InformacionTransporteGRR = {
          at_Modalidad: transports[0].modality,
          at_FechaInicio: transports[0].start_date,
        };
        this.drivers = movement?.transports || [];
      }
    },

    convertToExtendedFormat(time) {
      const parts = time.split(":");
      if (parts.length === 2) {
        const [hours, minutes] = parts;
        return `${hours}:${minutes}:00`;
      }
      return time; // Si ya tiene el formato "00:00:00", retornarlo sin cambios
    },

    getdocumentCodebyId(id) {
      const document = this.documentTypes.find((e) => e?.id == id);
      return document ? document.code : undefined;
    },

    getdocumentIdbyCode(code) {
      const document = this.documentTypes.find((e) => e?.code == code);
      return document ? document.id : undefined;
    },

    sendGuide(isSend) {
      const data = {
        // ent_RemitenteGRR
        send: isSend,
        almacen_partida: {
          document_type_id: this.ent_RemitenteGRR?.document_type,
          document: this.ent_RemitenteGRR?.document,
          name: this.ent_RemitenteGRR?.name,
          commercial_name: this.ent_RemitenteGRR?.commercial_name,
          // ent_PuntoPartidaGRR
          ubigeo: this.ent_PuntoPartidaGRR?.at_Ubigeo,
          address: this.ent_PuntoPartidaGRR?.at_DireccionCompleta,
        },
        // ent_DestinatarioGRR
        almacen_destino: {
          document_type_id: this.ent_DestinatarioGRR?.at_TipoDocumentoIdentidad,
          document_type_code: this.getdocumentIdbyCode(
            this.ent_DestinatarioGRR?.at_TipoDocumentoIdentidad
          ),
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
        hora_emision: this.convertToExtendedFormat(
          this.ent_DatosGeneralesGRR?.at_HoraEmision
        ),
        // ent_InformacionTrasladoGRR
        // // ent_InformacionPesoBrutoGRR
        peso: this.ent_DatosGeneralesGRR?.ent_InformacionPesoBrutoGRR?.at_Peso,
        cantidad:
          this.ent_DatosGeneralesGRR?.ent_InformacionPesoBrutoGRR?.at_Cantidad,
        modalidad_transporte: this.en_InformacionTransporteGRR?.at_Modalidad,
      };

      if (this.en_InformacionTransporteGRR?.at_Modalidad == 1) {
        data.transports = [
          {
            start_date: this.en_InformacionTransporteGRR?.at_FechaInicio,
            document_type:
              this.ent_TransportePublicoGRR?.at_TipoDocumentoIdentidad,
            document:
              this.ent_TransportePublicoGRR?.at_NumeroDocumentoIdentidad,
            company_name: this.ent_TransportePublicoGRR?.at_RazonSocial,
            mtc_number: this.ent_TransportePublicoGRR?.at_NumeroMTC,
          },
        ];
      } else if (this.en_InformacionTransporteGRR?.at_Modalidad == 2) {
        data.transports = this.drivers;
        data.vehicles = this.vehicles;
      }

      const params = { id: this.idMov };
      this.apiErros = [];
      createGuide(data, params)
        .then((response) => {
          if (response?.success === true) {
            swal.fire({
              title: "",
              type: "success",
              text:
                response?.message ||
                "¡El formulario se ha guardado correctamente!",
              showConfirmButton: false,
              timer: 5000,
            });
            setTimeout(() => {
              window.location.href = "guia-lista.php";
            }, 5000);
          } else if (response?.success === false) {
            this.apiErros = response?.errors;
            swal.fire({
              title: "",
              type: "info",
              text: "Lo siento, pero los datos que has proporcionado no son válidos. Por favor, verifica la información e inténtalo nuevamente.",
            });
          } else {
            swal.fire({
              title: "",
              type: "error",
              text: "¡Ups! Parece que hay un problema con la API en este momento. Por favor, intenta nuevamente más tarde. Gracias por tu paciencia.",
            });
          }

          console.log(response);
        })
        .catch((error) => {
          console.log(error?.response?.data);
          this.apiErros = error?.response?.data?.errors || [];
          swal.fire({
            title: "",
            type: "error",
            text: "No se pudo procesar la solicitud de guardado debido a errores en el formulario. Por favor, revisa la información ingresada.",
          });
        });
    },
  },
});
