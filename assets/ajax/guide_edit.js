Vue.use(VeeValidate);
VeeValidate.Validator.localize("es", {
  messages: {
    required: "Este campo es obligatorio",
    email: "El campo de correo electrónico no es válido",
    date: "El campo debe tener un formato válido",
    date_format: "El campo debe tener un formato válido",
  },
});

VeeValidate.Validator.extend("validationRuc", {
  validate: (value) => /^[A-Za-z0-9]{11}$/.test(value),
  message:
    "El campo debe ser alfanumérico y tener una longitud de 11 caracteres.",
});

new Vue({
  el: "#app",
  mixins: [guideUtilsMixin],
  data: {
    start_store: {},
    end_store: {},

    generalData: {
      name: null,
      at_FechaEmision: null,
      observations: "",
      motive: 4,
      description_transfer: null,
      new_description: null,
      unit_measure: null,
      total_witght: null,
      total_quantity: null,
    },

    en_InformacionTransporteGRR: {
      at_Modalidad: 1,
    },

    ent_TransportePublicoGRR: {
      at_FechaInicio: "",
      at_TipoDocumentoIdentidad: 6,
      at_NumeroDocumentoIdentidad: "",
      at_RazonSocial: "",
      at_NumeroMTC: "",
    },

    provider: {
      document_type_code: 6,
      document: "",
      name: "",
    },

    buyer: {
      document_type_code: 6,
      document: "",
      name: "",
    },

    drivers: [],
    vehicles: [],
    movement: null,
    movementDetail: [],
    apiErros: [],
    companies: [],
    loadingSave: false,
    toggleProvider: false,
    togglebuyer: false,
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
    this.getCompany();
    this.generalData.at_FechaEmision = this.getCurrentDate();
  },
  mounted() {},
  computed: {
    idGuide: function () {
      const urlParams = new URLSearchParams(window.location.search);
      const queryValue = urlParams.get("idMovimiento");
      return queryValue;
    },
    typeMov: function () {
      const urlParams = new URLSearchParams(window.location.search);
      const queryValue = urlParams.get("tipo");
      return queryValue;
    },
    dateIssued: function () {
      const date = this.generalData?.at_FechaEmision;
      return this.addDay(date);
    },
    recipientHasCompany() {
      return !!this.movement?.end_store?.company?.id;
    },
    recipientHasUbigeo() {
      return !!this.movement?.end_store?.district?.id;
    },
    senderHasCompany() {
      return !!this.movement?.start_store?.company?.id;
    },
    senderHasUbigeo() {
      return !!this.movement?.start_store?.district?.id;
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
      readGuide({ id: this.idGuide }).then((response) => {
        this.movement = response?.data || {};
        console.log(this.movement);
        this.setData();
      });
    },

    getCompany() {
      listCompany().then((response) => {
        this.companies = response?.data || [];
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

    addDay(date, days = 1) {
      if (date) {
        let fecha = new Date(date);
        fecha.setDate(fecha.getDate() + days);
        return fecha.toISOString().slice(0, 10);
      }
      return date;
    },

    setData() {
      const movement = { ...this.movement };
      const detailsList = movement?.data || {};
      const start_store = movement?.start_store || {};
      const end_store = movement?.end_store || {};
      const transports = movement?.transports || null;

      this.start_store = {
        name: start_store?.company?.name,
        commercial_name: start_store?.company?.commercial_name,
        address: start_store?.address,
        ubigeo: start_store?.district?.code,
        company_id: start_store?.company?.id,
        document: start_store?.company?.document,
        document_type: start_store?.company?.document_type_id,
      };

      this.end_store = {
        name: end_store?.company?.name,
        address: end_store?.address,
        email_principal: end_store?.email_principal,
        email_secondary: end_store?.email_secondary,
        ubigeo: end_store?.district?.code,
        company_id: end_store?.company?.id,
        document: end_store?.company?.document,
        document_type: end_store?.company?.document_type_id,
      };

      this.generalData = {
        ...this.generalData,
        name: movement?.name || null,
        observations: movement.observations,
        motive: movement?.motive_code,
        total_witght: movement?.total_witght || null,
        unit_measure: movement?.unit_measure || "KGM",
        total_quantity: movement?.total_quantity || null,
      };

      if (this.isMotiveInSubmotives(movement?.motive_description)) {
        this.generalData.description_transfer = movement?.motive_description;
      } else {
        this.generalData.description_transfer = "NEW";
        this.generalData.new_description = movement?.motive_description || null;
      }

      this.provider = movement?.provider || this.provider;
      this.buyer = movement?.buyer || this.buyer;
      if (movement?.provider) this.toggleProvider = true;
      if (movement?.buyer) this.togglebuyer = true;

      if (Array.isArray(detailsList)) {
        arrAssets = detailsList.reduce((acc, item) => {
          if (Array.isArray(item?.detail)) {
            acc.push(...item.detail);
          }
          return acc;
        }, []);
      }
      this.movementDetail = movement?.details || [];

      this.en_InformacionTransporteGRR = {
        at_Modalidad: movement.transport_modality,
      };

      if (
        Array.isArray(transports) &&
        transports.length > 0 &&
        movement.transport_modality == 1
      ) {
        this.ent_TransportePublicoGRR = {
          at_FechaInicio: transports[0].start_date,
          at_TipoDocumentoIdentidad: transports[0].document_type_code,
          at_NumeroDocumentoIdentidad: transports[0].document,
          at_RazonSocial: transports[0].company_name,
          at_NumeroMTC: transports[0].mtc_number,
        };
      } else if (
        Array.isArray(transports) &&
        transports.length > 0 &&
        movement.transport_modality == 2
      ) {
        this.drivers = movement?.transports || [];
        this.vehicles = movement?.vehicles || [];
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
      const detail = this.movementDetail.map((item) => {
        return { ...item, unit_measure_sunat: item?.unit_measure };
      });
      const data = {
        id: this.idGuide,
        send: isSend,
        name: this.generalData?.name || null,
        motive_code: this.generalData?.motive,
        observations: this.generalData?.observations || null,
        total_witght: this.generalData?.total_witght,
        total_quantity: this.generalData?.total_quantity,
        transport_modality: this.en_InformacionTransporteGRR?.at_Modalidad,

        start_store: {
          company_id: this.start_store.company_id,
        },
        end_store: {
          company_id: this.end_store?.company_id,
          email_principal: this.end_store?.email_principal,
          email_secondary: this.end_store?.email_secondary,
        },
        detail,
      };

      if (
        this.generalData.motive == 13 &&
        this.generalData.description_transfer == "NEW"
      ) {
        data.motive_description = this.generalData.new_description;
      } else if (this.generalData.motive == 13) {
        data.motive_description = this.generalData.description_transfer;
      }

      if (this.generalData.motive == 13 && this.provider?.document) {
        data.provider = this.provider;
      }

      if (this.generalData.motive == 13 && this.buyer?.document) {
        data.buyer = this.buyer;
      }

      if (this.en_InformacionTransporteGRR?.at_Modalidad == 1) {
        data.transports = [
          {
            start_date: this.ent_TransportePublicoGRR?.at_FechaInicio,
            document_type_code:
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

      const params = {
        action: "store",
      };
      this.apiErros = [];
      this.loadingSave = true;
      createGuide(data, params)
        .then((response) => {
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
            //window.location.href = "guia-lista.php";
            location.reload();
          }, 5000);
        })
        .catch((error) => {
          console.log(error?.response?.data);
          this.apiErros = error?.response?.data?.errors || [];
          swal.fire({
            title: "",
            type: "error",
            text: "No se pudo procesar la solicitud de guardado debido a errores en el formulario. Por favor, revisa la información ingresada.",
          });
        })
        .finally(() => {
          this.loadingSave = false;
        });
    },
  },
});
