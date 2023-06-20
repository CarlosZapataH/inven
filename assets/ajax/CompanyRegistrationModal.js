Vue.component("CompanyRegistrationModal", {
  props: {
    value: {
      type: [String, Number],
      required: false,
    },
    documentTypes: {
      type: [Array],
      required: false,
    },
  },
  data() {
    return {
      company: {
        name: "",
        commercial_name: "",
        document: "",
        document_type_id: 4,
      },
      apiErros: [],
      isLoading: false,
    };
  },
  template: `
  <div>
    <button type="button" class="btn btn-primary ml-4" data-toggle="modal" data-target="#myModal">
        Registrar compañia
    </button>

    <div class="modal" id="myModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar compañia</h5>
                    <button type="button" class="close" @click="closeModal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="registerCompany" novalidate>
                        <div class="form-group">
                            <label class="col-form-label">Tipo de documento
                                <span class="text-danger font-weight-bold">*</span>
                            </label>
                            <div class="">
                                <select v-model="company.document_type_id" name="DES_TipoDocumento"
                                    v-validate="'required'" class="form-control">
                                    <option v-for="document in documentTypes" :key="document.id + '-DESdocumentCode'"
                                        :value="document.id">
                                        {{ document.description }}
                                    </option>
                                </select>
                                <span class="text-danger">{{ errors.first('DES_TipoDocumento') }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">Número de Documento
                                <span class="text-danger font-weight-bold">*</span>
                            </label>
                            <div class="">
                                <input v-model="company.document" name="DES_Numero_Documento_Identidad"
                                    v-validate="'required'" type="text" class="form-control" />
                                <span class="text-danger">{{ errors.first('DES_Numero_Documento_Identidad') }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">Razón social
                                <span class="text-danger font-weight-bold">*</span>
                            </label>
                            <div class="">
                                <input v-model="company.name" name="DES_Razon_Social" v-validate="'required'"
                                    type="text" class="form-control" />
                                <span class="text-danger">{{ errors.first('DES_Razon_Social') }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="at_NombreComercial" class="col-form-label">Nombre comercial</label>
                            <div class="">
                                <input v-model="company.commercial_name" name="RM_Nombre_Comercial" type="text"
                                    class="form-control" id="at_NombreComercial" />
                                <span class="text-danger">{{ errors.first('RM_Nombre_Comercial') }}</span>
                            </div>
                        </div>
                        <div v-for="(group, groupIndex) in apiErros" :key="groupIndex + '-errGroup'">
                            <div v-for="(msm, msmIndex) in group" :key="msmIndex + '-errMsm'" class="alert alert-warning" role="alert">
                                {{ msm }}
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="closeModal" :disabled="isLoading">
                        Cerrar
                    </button>
                    <button type="button" class="btn btn-primary" @click="registerCompany" :disabled="isLoading">Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>
    `,
  created() {},
  mounted() {},
  computed: {
    companyId: {
      get() {
        return this.value || false;
      },
      set(value) {
        this.$emit("input", value);
      },
    },
  },
  methods: {
    openModal() {
      $("#myModal").modal("show");
    },

    closeModal() {
      $("#myModal").modal("hide");
    },

    registerCompany() {
      this.$validator.validateAll().then((result) => {
        if (result) this.sendApi();
      });
    },

    sendApi() {
      this.apiErros = [];
      this.isLoading = true;
      createCompany(this.company)
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
          this.$emit("company-registered", response?.data);
          this.companyId = response?.data?.id;
          this.cleanform();
          this.closeModal();
        })
        .catch((error) => {
          console.log(error?.response?.data);
          this.apiErros = error?.response?.data?.errors || [];
        })
        .finally(() => {
          this.isLoading = false;
        });
    },

    cleanform() {
      this.$validator.reset();
      this.company = {
        name: "",
        commercial_name: "",
        document: "",
        document_type_id: 4,
      };
    },
  },
});
