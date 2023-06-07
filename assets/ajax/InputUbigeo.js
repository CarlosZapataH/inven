Vue.component("input-ubigeo", {
  props: {
    value: {
      type: [String, Number],
      required: false,
    },
    initialName: {
      type: [String],
      required: false,
    },
  },
  data() {
    return {
      selectedDepartamento: "",
      selectedProvincia: "",
      selectedDistrito: "",
      departamentos: [],
      ubigeoProvincias: [],
      ubigeoDistritos: [],
    };
  },
  template: `
      <div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Departamento
              <span class="text-danger font-weight-bold">*</span>
            </label>
            <div class="col-sm-9">
                <select v-model="selectedDepartamento" @change="onChangeDepartamento" :name="initialName + '_Departamento'" v-validate="'required'" class="form-control">
                    <option v-for="item in departamentos" :value="item.inei">{{ item.departamento }}</option>
                </select>
                <span class="text-danger">{{ errors.first(initialName + '_Departamento') }}</span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Provincia
              <span class="text-danger font-weight-bold">*</span>
            </label>
            <div class="col-sm-9">
                <select v-model="selectedProvincia" @change="onChangeProvincia" :name="initialName + '_Provincia'" v-validate="'required'" class="form-control">
                    <option v-for="item in provincias" :value="item.inei">{{ item.provincia }}</option>
                </select>
                <span class="text-danger">{{ errors.first(initialName + '_Provincia') }}</span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Distrito
              <span class="text-danger font-weight-bold">*</span>
            </label>
            <div class="col-sm-9">
                <select v-model="selectedDistrito" @change="onChangeDistrito" :name="initialName + '_Distrito'" v-validate="'required'" class="form-control">
                    <option v-for="item in distritos" :value="item.inei">{{ item.distrito }}</option>
                </select>
                <span class="text-danger">{{ errors.first(initialName + '_Distrito') }}</span>
            </div>
        </div>
      </div>
    `,
  created() {
    this.loadDepartamento();
    this.loadProvincia();
    this.loadDistrito();
    if (this.ubigeo) {
      const ubigeo = this.ubigeo?.toString();
      this.selectedDepartamento = ubigeo.substring(0, 2) + "0000";
      this.selectedProvincia = ubigeo.substring(0, 4) + "00";
      this.selectedDistrito = ubigeo;
    }
  },
  mounted() {
    // this.$validator.localize("es", {
    //   // Configura los mensajes de error en el idioma deseado
    // });
    // this.$validator.attach(this.initialName + "_Provincia", "required");
  },
  computed: {
    ubigeo: {
      get() {
        return this.value || false;
      },
      set(value) {
        this.$emit("input", value);
      },
    },
    provincias() {
      let provincias = [];
      const codigoDepartamento = this.selectedDepartamento.substring(0, 2);
      if (this.selectedDepartamento) {
        provincias = this.ubigeoProvincias.filter(function (e) {
          return codigoDepartamento == (e?.inei || "").substring(0, 2);
        });
      }
      return provincias;
    },
    distritos() {
      let distritos = [];
      const codigoProvincia = this.selectedProvincia.substring(0, 4);
      if (this.selectedProvincia) {
        distritos = this.ubigeoDistritos.filter(function (e) {
          return codigoProvincia == (e?.inei || "").substring(0, 4);
        });
      }
      return distritos;
    },
  },
  methods: {
    loadDepartamento() {
      fetch("../assets/json/ubigeo_departamento.json")
        .then((response) => response.json())
        .then((data) => {
          this.departamentos = data;
        })
        .catch((error) => {
          console.error("Error al cargar el archivo JSON:", error);
        });
    },

    loadProvincia() {
      fetch("../assets/json/ubigeo_provincia.json")
        .then((response) => response.json())
        .then((data) => {
          this.ubigeoProvincias = data;
        })
        .catch((error) => {
          console.error("Error al cargar el archivo JSON:", error);
        });
    },

    loadDistrito() {
      fetch("../assets/json/ubigeo_distrito.json")
        .then((response) => response.json())
        .then((data) => {
          this.ubigeoDistritos = data;
        })
        .catch((error) => {
          console.error("Error al cargar el archivo JSON:", error);
        });
    },

    onChangeDepartamento() {
      this.selectedProvincia = "";
      this.selectedDistrito = "";
    },

    onChangeProvincia() {
      this.selectedDistrito = "";
    },

    onChangeDistrito() {
      this.ubigeo = this.selectedDistrito;
    },
  },
});
