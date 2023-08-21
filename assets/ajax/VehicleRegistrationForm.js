Vue.component("VehicleRegistrationForm", {
  props: {
    value: {
      type: [Array],
      required: false,
    },
  },
  data() {
    return {
      en_VehiculoGRR: {
        plate: null,
      },
    };
  },
  template: `
    <div class="card">
        <div class="card-header"> Información del vehículo(s) </div>
        <div class="card-body">
            <form @submit.prevent="addVehicles" novalidate>
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="vhl_aa_NumeroPlaca">Placa</label>
                            <input v-model="en_VehiculoGRR.plate"  v-validate="'required|alpha_num|min:6|max:8|uniquePlate'" name="vehicle_placa" type="text" class="form-control" id="vhl_aa_NumeroPlaca">
                            <span class="text-danger">{{ errors.first('vehicle_placa') }}</span>
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
                        <td>{{ vehicle.plate }}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger" @click="removeVehicle(index)">Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    `,
  created() {
    VeeValidate.Validator.extend("uniquePlate", {
      validate: () => {
        return !this.isPlateRepetitive();
      },
      getMessage: () => {
        return `El número de placa ya ha sido registrado.`;
      },
    });
  },
  mounted() {
    // this.$validator.localize("es", {
    //   // Configura los mensajes de error en el idioma deseado
    // });
    // this.$validator.attach(this.initialName + "_Provincia", "required");
  },
  computed: {
    vehicles: {
      get() {
        return this.value || false;
      },
      set(value) {
        this.$emit("input", value);
      },
    },
  },
  methods: {
    addVehicles() {
      if (this.vehicles.length < 3) {
        this.$validator.validateAll().then((result) => {
          if (result) {
            const plate = (this.en_VehiculoGRR?.plate || "").toUpperCase();
            this.vehicles.push({ ...this.en_VehiculoGRR, plate });
            this.cleanform();
          }
        });
      } else {
        swal.fire({
          title: "Registro de Vehículos",
          type: "info",
          text: "Recuerda que solo puedes registrar un máximo de tres vehículos. ¡Gracias por tu comprensión!",
        });
      }
    },

    removeVehicle(index) {
      this.vehicles.splice(index, 1);
    },

    cleanform() {
      this.$validator.reset();
      this.en_VehiculoGRR = {
        plate: null,
      };
    },

    isPlateRepetitive() {
      return this.vehicles.some(
        (vehicle) =>
          (vehicle?.plate || "")?.toUpperCase() ===
          (this.en_VehiculoGRR?.plate || "")?.toUpperCase()
      );
    },
  },
});
