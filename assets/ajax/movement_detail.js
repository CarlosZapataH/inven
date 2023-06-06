Vue.use(VeeValidate);
VeeValidate.Validator.localize("es");

VeeValidate.Validator.extend("validationRuc", {
  validate: (value) => /^[A-Za-z0-9]{11}$/.test(value),
  message:
    "El campo debe ser alfanumérico y tener una longitud de 11 caracteres.",
});

new Vue({
  el: "#app",
  data: {
    ent_RemitenteGRR: {
      at_NumeroDocumentoIdentidad: 20357259976,
      at_RazonSocial: "CONFIPETROL ANDINA S.A.",
      at_NombreComercial: "--",
    },

    ent_DestinatarioGRR: {
      at_TipoDocumentoIdentidad: 6,
      at_NumeroDocumentoIdentidad: 20112811096,
      at_RazonSocial: "TCI TRANSPORTE CONFIDENCIAL DE INFORMACION SA",
      at_CorreoPrincipal: "jsotomayor@tci.net.pe",
      aa_CorreoSecundario: "jhermosilla@tci.net.pe",
    },

    ent_DatosGeneralesGRR: {
      at_FechaEmision: "2023-01-17",
      at_Serie: "T004",
      at_Numero: 445,
      at_Observacion: "Pruebas de GRE",
      at_HoraEmision: "16:50:00",
      at_CodigoMotivo: 4,
      ent_InformacionPesoBrutoGRR: {
        at_Peso: 2,
        at_UnidadMedida: "KGM",
        at_Cantidad: 1,
      },
    },

    en_InformacionTransporteGRR: {
      at_Modalidad: 2,
      at_FechaInicio: "2022-09-20",
    },

    ent_PuntoPartidaGRR: {
      at_Ubigeo: 150101,
      at_DireccionCompleta: "AV. MIGUEL SEMINARIO 315",
      at_CodigoEstablecimiento: 0,
      at_NumeroDocumentoIdentidad: 20112811096,
    },

    ent_PuntoLlegadaGRR: {
      at_Ubigeo: 150102,
      at_DireccionCompleta: "AV. LAS BEGOÑAS 1223",
      at_CodigoEstablecimiento: 3,
      at_NumeroDocumentoIdentidad: 20112811096,
    },

    ent_TransportePublicoGRR: {
      at_TipoDocumentoIdentidad: 6,
      at_NumeroDocumentoIdentidad: 10442201647,
      at_RazonSocial: "LAUR E.I.R.L.",
      at_NumeroMTC: "MTC012345678",
    },

    en_ConductorGRR: {
      at_TipoDocumentoIdentidad: 1,
      at_NumeroDocumentoIdentidad: 43530092,
      at_Licencia: "Q43530092",
      at_Nombres: "EDWARD",
      at_Apellidos: "LAZO",
    },
    en_VehiculoGRR: {
      aa_NumeroPlaca: "AGV9874",
    },

    drivers: [],
    vehicles: [],

    movement: {},
    movementDetail: [],
    message: "¡Hola, Vue.js 2!",
    documentTypes: [
      {
        codigo: "0",
        descripcion: "DOCUMENTO TRIBUTARIO NO DOMICILIADO SIN RUC",
      },
      { codigo: "1", descripcion: "DOCUMENTO NACIONAL DE IDENTIDAD" },
      { codigo: "4", descripcion: "CARNET DE EXTRANJERIA" },
      { codigo: "6", descripcion: "REGISTRO UNICO DE CONTRIBUYENTES" },
      { codigo: "7", descripcion: "PASAPORTE" },
      { codigo: "A", descripcion: "CEDULA DIPLOMATICA DE IDENTIDAD" },
      { codigo: "B", descripcion: "DOC.IDENT.PAIS.RESIDENCIA-NO.D" },
      {
        codigo: "C",
        descripcion: "Tax Identification Number - TIN – Doc Trib PP.NN",
      },
      {
        codigo: "D",
        descripcion: "Identification Number - IN – Doc Trib PP. JJ",
      },
      { codigo: "E", descripcion: "TAM- Tarjeta Andina de Migración" },
      { codigo: "F", descripcion: "Permiso Temporal de Permanencia - PTP" },
      { codigo: "G", descripcion: "Salvoconducto" },
    ],
  },
  validations: {
    ent_RemitenteGRR: {
      at_RazonSocial: {
        regex: /^[A-Za-z0-9\s]{1,250}$/,
      },
    },
  },
  created() {
    const input = document.getElementById("movement");
    this.movement = JSON.parse(input?.value);
    const input2 = document.getElementById("movement_detail");
    this.movementDetail = JSON.parse(input2?.value);
  },
  mounted() {},
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
      this.$validator.validateAll().then((result) => {
        if (result) {
          // Enviar el formulario si la validación es exitosa
          console.log("Formulario válido");
        } else {
          console.log("Formulario inválido");
          const errors = this.$validator.errors.items;
          console.log(errors);
        }
      });
    },
  },
});
