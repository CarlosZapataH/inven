var guideUtilsMixin = {
  data: function () {
    return {
      documentTypes: [],
      modalities: [],
      motives: [],
    };
  },
  methods: {
    getUtil() {
      listUtil().then((response) => {
        this.documentTypes = response?.data?.documentTypes || [];
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

    getCurrentTime() {
      var date = new Date();
      var hours = date.getHours();
      var minutes = date.getMinutes();
      var seconds = date.getSeconds();

      hours = hours < 10 ? "0" + hours : hours;
      minutes = minutes < 10 ? "0" + minutes : minutes;
      seconds = seconds < 10 ? "0" + seconds : seconds;

      var formattedTime = hours + ":" + minutes + ":" + seconds;

      return formattedTime;
    },

    isMotiveInSubmotives(submotive) {
      return this.submotives.some((obj) => obj?.value == submotive);
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
