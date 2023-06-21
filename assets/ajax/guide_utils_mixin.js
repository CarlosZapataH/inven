var guideUtilsMixin = {
  data: function () {
    return {
      documentTypesX: [],
      modalities: [],
      motives: [],
    };
  },
  methods: {
    getUtil() {
      listUtil().then((response) => {
        this.documentTypesX = response?.data?.documentTypes || [];
        this.modalities = response?.data?.modalities || [];
        this.motives = response?.data?.motives || [];
      });
    },

    getCurrentDate() {
      let currentDate = new Date();

      let year = currentDate.getFullYear();
      let month = currentDate.getMonth() + 1;
      let day = currentDate.getDate();
      if (month < 10) {
        month = "0" + month;
      }
      if (day < 10) {
        day = "0" + day;
      }
      let formattedDate = year + "-" + month + "-" + day;
      return formattedDate;
    },
  },
  created: function () {
    this.getUtil();
  },
  computed: {
    submotives: function () {
      const motive = this.motives.find((e) => e?.code == 13);
      return motive?.submotives || [];
    },
  },
};
