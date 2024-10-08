Vue.use(VeeValidate);
VeeValidate.Validator.localize("es", {
  messages: {
    required: "Este campo es obligatorio",
    email: "El campo de correo electrónico no es válido",
    date: "El campo debe tener un formato válido",
    date_format: "El campo debe tener un formato válido",
    alpha_dash: "El campo solo debe contener letras, números o guiones",
    alpha_num: "El campo solo puede contener caracteres alfanuméricos",
    length: (_, args) => {
      const [min] = args;
      return `El campo debe tener ${min} caracteres.`;
    },
    max: (_, args) => {
      const [max] = args;
      return `El campo no debe ser mayor a ${max} caracteres.`;
    },
    min: (_, args) => {
      const [min] = args;
      return `El campo debe tener al menos ${min} caracteres.`;
    },
  },
});

VeeValidate.Validator.extend("minDate", {
  validate: (value) => {
    if (!value) {
      return true;
    }
    const currentDate = new Date();
    currentDate.setHours(0, 0, 0, 0);
    const selectedDate = new Date(value + " 00:00:00");
    selectedDate.setHours(0, 0, 0, 0);
    return selectedDate >= currentDate;
  },
  getMessage: () => {
    return "La fecha seleccionada no debe ser menor al día actual";
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

    newCompany: {
      flag_new_company: false,
      new_document_type_id: 2,
      new_document: "",
      new_company_name: "",
    },

    generalData: {
      name: null,
      at_FechaEmision: null,
      observations: "",
      motive: 4,
      description_transfer: null,
      new_description: null,
      indicator_service: null,
      ent_InformacionPesoBrutoGRR: {
        at_Peso: "",
        at_UnidadMedida: "KGM",
        at_Cantidad: "",
      },
    },

    en_InformacionTransporteGRR: {
      at_Modalidad: 1,
    },

    publicTransport: {
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
    establishmentIni: null,
    establishmentDes: null
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
  watch: {
    "generalData.motive"(newValue) {
      this.validateTransferType(newValue);
    },
    "newCompany.flag_new_company"() {
      this.$nextTick(async function () {
        this.$validator.reset();
      });
    },
  },
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
    dateIssued: function () {
      return this.generalData?.at_FechaEmision;
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
    transportDocumentRule() {
      const code = this.publicTransport?.at_TipoDocumentoIdentidad;
      return this.getRuleDocument(code);
    },
    senderDocumentRule() {
      const code = this.start_store?.document_type;
      return this.getRuleDocument(code, "id");
    },
    recipientDocumentRule() {
      const code = this.end_store?.document_type;
      return this.getRuleDocument(code, "id");
    },
    newRecipientDocumentRule() {
      const code = this.newCompany?.new_document_type_id;
      return this.getRuleDocument(code, "id");
    },
    providerDocumentRule() {
      const code = this.provider?.document_type_code;
      return this.getRuleDocument(code);
    },
    buyerDocumentRule() {
      const code = this.buyer?.document_type_code;
      return this.getRuleDocument(code);
    },
  },
  methods: {
    validateTransferType(typeCode) {
      let messageAlert = "";
      if (
        typeCode == "06" &&
        this.start_store?.company_id == this.end_store?.company_id
      ) {
        messageAlert =
          "Para el tipo de traslado 'Devolución', el remitente y el destinatario deben ser diferentes.";
      } else if (
        typeCode == "04" &&
        this.start_store?.company_id != this.end_store?.company_id
      ) {
        messageAlert = "El remitente y destinatario deben ser el mismo.";
      }

      if (messageAlert) {
        swal.fire({
          title: "",
          type: "warning",
          text: messageAlert,
        });
        this.$nextTick(() => {
          this.generalData.motive = null;
        });
      }
    },

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
        } else if (
          this.newCompany?.flag_new_company &&
          this.generalData?.motive == "04" &&
          (this.start_store?.commercial_name !=
            this.newCompany?.new_company_name ||
            this.start_store?.document != this.newCompany?.new_document)
        ) {
          isCompleted = false;
          alertMsm =
            "Para el motivo de traslado seleccionado, el destinatario debe ser igual al remitente.";
        }

        if (isCompleted) {
          this.sendGuide(isSend);
        } else {
          swal.fire({
            title: "",
            type: "info",
            text: alertMsm,
          });
        }
      });
    },

    getData() {
      let urlBase =
        "Modules/TransitMovement/Controllers/TransitMovementController.php";
      let action = "index";
      if (this.typeMov == "interno") {
        urlBase = "Modules/Movement/Controllers/MovementController.php";
        action = "index";
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
          this.setData();
        })
        .catch((error) => {
          console.error(error);
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

      if(start_store && start_store.establishment){
        this.establishmentIni = start_store.establishment;
      }

      if(end_store && end_store.establishment){
        this.establishmentDes = end_store.establishment;
      }

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

      let arrAssets = [];

      if (Array.isArray(detailsList)) {
        arrAssets = detailsList.reduce((acc, item) => {
          if (Array.isArray(item?.detail)) {
            acc.push(...item.detail);
          }
          return acc;
        }, []);
      }
      this.movementDetail = arrAssets;
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
        send: isSend,
        name: this.generalData?.name || null,
        motive_code: this.generalData?.motive,
        observations: this.generalData?.observations || null,
        total_witght: this.generalData?.total_witght,
        total_quantity: this.generalData?.total_quantity,
        transport_modality: this.en_InformacionTransporteGRR?.at_Modalidad,

        indicator_service: this.generalData?.indicator_service || null,

        start_store: {
          company_id: this.start_store.company_id,
        },
        end_store: {
          company_id: this.end_store?.company_id,
          email_principal: this.end_store?.email_principal,
          email_secondary: this.end_store?.email_secondary,
          alternative_address: this.end_store?.alternative_address || false,
          district_id: this.end_store.ubigeo || null,
          address: this.end_store.address || null,
        },
        detail,
        ...this.newCompany,
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
            start_date: this.publicTransport?.at_FechaInicio,
            document_type_code: this.publicTransport?.at_TipoDocumentoIdentidad,
            document: this.publicTransport?.at_NumeroDocumentoIdentidad,
            company_name: this.publicTransport?.at_RazonSocial,
            mtc_number: this.publicTransport?.at_NumeroMTC,
          },
        ];
      } else if (this.en_InformacionTransporteGRR?.at_Modalidad == 2) {
        data.transports = this.drivers;
        data.vehicles = this.vehicles;
      }

      const params = {
        id: this.idMov,
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
            window.location.href = "guia-lista.php";
          }, 5000);
        })
        .catch((error) => {
          this.apiErros = error?.response?.data?.errors || [];
          swal.fire({
            title: "",
            type: "error",
            text: "No hemos podido registrar la guía en este momento. Te recomendamos revisar las observaciones en la parte inferior para obtener más detalles sobre el problema",
          });
        })
        .finally(() => {
          this.loadingSave = false;
        });
    },
  },
});
