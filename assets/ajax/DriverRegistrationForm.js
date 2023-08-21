Vue.component("DriverRegistrationForm", {
  mixins: [guideUtilsMixin],
  props: {
    value: {
      type: [Array],
      required: false,
    },
    dateIssued: {
      type: [String],
      required: false,
    },
  },
  data() {
    return {
      en_ConductorGRR: {
        document_type_code: 1,
        document: "",
        license: "",
        name: "",
        last_name: "",
        start_date: "",
      },
    };
  },
  template: `
  <div class="card">
    <div class="card-header"> Información del Conductor(es)
    </div>
    <div class="card-body">
        <form @submit.prevent="addDriver" novalidate>
            <div class="row">
                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        <label for="cdt_at_Nombres">Nombres</label>
                        <input v-model="en_ConductorGRR.name" v-validate="'required'" name="driver_name" type="text" class="form-control" id="cdt_at_Nombres">
                        <span class="text-danger">{{ errors.first('driver_name') }}</span>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        <label for="cdt_at_Apellidos">Apellidos</label>
                        <input v-model="en_ConductorGRR.last_name" v-validate="'required'" name="driver_lastname" type="text" class="form-control" id="cdt_at_Apellidos">
                        <span class="text-danger">{{ errors.first('driver_lastname') }}</span>
                    </div>
                </div>

                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        <label for="cdt_at_TipoDocumentoIdentidad">Tipo de Documento</label>
                        <select v-model="en_ConductorGRR.document_type_code" v-validate="'required'" name="driver_document_type" class="form-control" id="cdt_at_TipoDocumentoIdentidad">
                            <option v-for="document in documentTypes" :key="document.id + '-documentCode'" :value="document.code">{{ document.description }}</option>
                        </select>
                        <span class="text-danger">{{ errors.first('driver_document_type') }}</span>
                    </div>
                </div>

                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        <label for="cdt_at_NumeroDocumentoIdentidad">Número de Documento</label>
                        <input v-model="en_ConductorGRR.document" v-validate="transportDocumentRule" name="driver_document" type="text" class="form-control" id="cdt_at_NumeroDocumentoIdentidad">
                        <span class="text-danger">{{ errors.first('driver_document') }}</span>
                    </div>
                </div>

                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        <label for="cdt_at_Licencia">Licencia:</label>
                        <input v-model="en_ConductorGRR.license" v-validate="'required|alpha_dash|min:9|max:10|uniqueLicense'" name="driver_license" type="text" class="form-control" id="cdt_at_Licencia">
                        <span class="text-danger">{{ errors.first('driver_license') }}</span>
                    </div>
                </div>

                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        <label for="cdt_at_date">Fecha de inicio o entrega:</label>
                        <input v-model="en_ConductorGRR.start_date" v-validate="'required|minDate'" :min="dateIssued" name="driver_startdate" type="date" class="form-control" id="cdt_at_date">
                        <span class="text-danger">{{ errors.first('driver_startdate') }}</span>
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
                    <th scope="col">Número de Documento</th>
                    <th scope="col">Licencia</th>
                    <th scope="col">Fecha</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(driver, index) in drivers" :key="index + '-driver'">
                    <td>{{ index + 1 }}</td>
                    <td>{{ driver.name }}</td>
                    <td>{{ driver.last_name }}</td>
                    <td>{{ driver.document }}</td>
                    <td>{{ driver.license }}</td>
                    <td>{{ driver.start_date }}</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm" @click="removeDriver(index)">Eliminar</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
    `,
  created() {
    VeeValidate.Validator.extend("uniqueDocument", {
      validate: (value) => {
        return !this.isRepetitive("document", value);
      },
      getMessage: () => {
        return `El número de documento ya ha sido registrado.`;
      },
    });
    VeeValidate.Validator.extend("uniqueLicense", {
      validate: (value) => {
        return !this.isRepetitive("license", value);
      },
      getMessage: () => {
        return `El número de licencia ya ha sido registrado.`;
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
  },
  mounted() {},
  computed: {
    drivers: {
      get() {
        return this.value || false;
      },
      set(value) {
        this.$emit("input", value);
      },
    },
    transportDocumentRule() {
      const code = this.en_ConductorGRR?.document_type_code;
      const rule = this.getRuleDocument(code);
      return { ...rule, uniqueDocument: true };
    },
  },
  methods: {
    addDriver() {
      if (this.drivers.length < 3) {
        this.$validator.validateAll().then((result) => {
          if (result) {
            const license = (this.en_ConductorGRR?.license || "").toUpperCase();
            this.drivers.push({ ...this.en_ConductorGRR, license });
            this.cleanform();
          }
        });
      } else {
        swal.fire({
          title: "Registro de Conductores",
          type: "info",
          text: "Recuerda que solo puedes registrar un máximo de tres conductores. ¡Gracias por tu comprensión!",
        });
      }
    },

    removeDriver(index) {
      this.drivers.splice(index, 1);
    },

    isRepetitive(property, value) {
      if (property) {
        return this.drivers.some(
          (driver) =>
            (driver[property] || "")?.toUpperCase() ===
            (value || "")?.toUpperCase()
        );
      }
      return false;
    },

    cleanform() {
      this.$validator.reset();

      this.en_ConductorGRR = {
        document_type_code: 1,
        document: "",
        license: "",
        name: "",
        last_name: "",
        start_date: "",
      };
    },
  },
});
