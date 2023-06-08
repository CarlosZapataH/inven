Vue.use(VeeValidate);
VeeValidate.Validator.localize("es");

new Vue({
  el: "#app",
  data: {
    filters: {},
  },
  created() {},
  mounted() {},
  computed: {},
  methods: {
    listenFilter() {
      console.log(this.filters);
    },
  },
});
