var guideUtilsMixin = {
  data: function () {
    return {
      documentTypes: [],
      modalities: [],
      motives: [],
      unit_measure: [],
      indicatorsServices: [],
    };
  },
  computed: {
    submotives: function () {
      const motive = this.motives.find((e) => e?.code == 13);
      return motive?.submotives || [];
    },

    lengthDocument() {
      const lengthDocument = [
        { code: 1, length: 8, id: 2 },
        { code: 4, length: 12, id: 3 },
        { code: 6, length: 11, id: 4 },
        { code: 7, length: 12, id: 5 },
      ];

      const updatedLengthDocument = lengthDocument.map((e) => {
        const document = this.documentTypes.find((doc) => doc?.code == e.code);
        const id = document ? document.id : null;
        return { ...e, id };
      });

      return updatedLengthDocument;
    },
  },
  methods: {
    getUtil() {
      listUtil().then((response) => {
        this.documentTypes = response?.data?.documentTypes || [];
        this.modalities = response?.data?.modalities || [];
        this.motives = response?.data?.motives || [];
        this.unit_measure = response?.data?.unit_measure?.all || [];
        this.indicatorsServices = response?.data?.indicatorsServices || [];
      });
    },

    getLengthByDocument(documentCode, by = "code") {
      const found = this.lengthDocument.find((e) => e[by] == documentCode);
      return found ? found.length : null;
    },

    getRuleDocument(code = "", by = "code") {
      let rule = { required: true, alpha_dash: true };
      const length = this.getLengthByDocument(code, by);
      if (length) rule.length = length;
      return rule;
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
};
