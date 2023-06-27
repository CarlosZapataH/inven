Vue.use(VeeValidate);
VeeValidate.Validator.localize("es");

new Vue({
  el: "#app",
  data: {
    filters: {
      q: null,
      date_from: null,
      date_to: null,
    },
    guides: [],
    itemsPerPage: 10,
    currentPage: 1,
  },
  created() {
    this.getGuides();
  },
  mounted() {},
  computed: {},

  computed: {
    totalPages() {
      return Math.ceil(this.guides.length / this.itemsPerPage);
    },

    paginatedItems() {
      const startIndex = (this.currentPage - 1) * this.itemsPerPage;
      const endIndex = startIndex + this.itemsPerPage;
      return this.guides.slice(startIndex, endIndex);
    },
  },

  methods: {
    downloadFile(base64Data) {
      console.log(base64Data);
      // const link = document.createElement("a");
      // link.href = "data:application/octet-stream;base64," + base64Data;
      // link.download = "archivo.descargar"; // Cambia el nombre del archivo según tus necesidades
      // link.click();
    },

    listenBtnDownload(guideId) {
      //downloadGuide
      downloadGuide({ id: guideId })
        .then((response) => {
          this.downloadFile(response);
        })
        .catch((error) => {
          swal.fire({
            title: "",
            type: "error",
            text: "Lo sentimos, Inténtalo de nuevo más tarde. Disculpa las molestias.",
          });
        });
    },

    listenFilter() {
      this.filters.q = this.filters.q || null;
      this.getGuides();
    },

    getGuides() {
      listGuide(this.filters)
        .then((response) => {
          if (Array.isArray(response?.data)) {
            this.guides = response?.data;
          }
        })
        .catch((error) => {
          swal.fire({
            title: "",
            type: "error",
            text: "Lo sentimos, la lista de registros no está disponible en este momento. Inténtalo de nuevo más tarde. Disculpa las molestias.",
          });
        });
    },
    setCurrentPage(page) {
      if (page >= 1 && page <= this.totalPages) {
        this.currentPage = page;
      }
    },

    getStatusProperty(value) {
      const list = [
        {
          value: "Rechazado",
          class: "badge-danger",
        },
        {
          value: "Aceptado Con Obs.",
          class: "badge-warning",
        },
        {
          value: "Aceptado",
          class: "badge-success",
        },
      ];
      if (value) {
        return list.find((e) => e.value == value);
      }
      return { value, class: "" };
    },
  },
});
