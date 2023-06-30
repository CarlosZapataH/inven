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
    downloadFile(base64Data, code) {
      const link = document.createElement("a");
      link.href = base64Data;
      link.download = "GRE_" + code;
      link.click();
    },

    listenBtnDownload(guide) {
      downloadGuide({ id: guide?.id })
        .then((response) => {
          if (response?.data?.file)
            this.downloadFile(
              response?.data?.file,
              guide?.serie + "-" + guide?.number
            );
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

    getGuideStatus(guide) {
      updateGuideStatus({ id: guide?.id })
        .then((response) => {
          console.log(response?.data?.at_MensajeResultado);
          swal.fire({
            title: "",
            type: response?.data?.at_MensajeResultado ? "info" : "success",
            text:
              response?.data?.at_MensajeResultado ||
              "Los registros se han actualizado correctamente.",
          });
          this.listenFilter();
        })
        .catch((error) => {
          swal.fire({
            title: "",
            type: "error",
            text: "Lo sentimos, Inténtalo de nuevo más tarde. Disculpa las molestias.",
          });
        });
    },
  },
});
