//{{path}}/DocumentType/Controllers/DocumentTypeController.php?action=index
const urlBase = "Modules";

function listDocumentType(params) {
  return axios
    .get(urlBase + "/DocumentType/Controllers/DocumentTypeController.php", {
      params,
    })
    .then((response) => {
      if (response.status === 200) {
        return response.data;
      } else {
        throw new Error("Error al obtener datos");
      }
    })
    .catch((error) => {
      console.error("Error al obtener datos:", error);
      throw error;
    });
}

function createGuide(data, params) {
  const url =
    urlBase + "/TransferGuide/Controllers/TransferGuideController.php";
  return axios
    .post(url, data, {
      params,
    })
    .then((response) => response.data)
    .catch((error) => {
      console.error("Error en la solicitud POST:", error);
      throw error;
    });
}

function listGuide(params = {}) {
  const url =
    urlBase + "/TransferGuide/Controllers/TransferGuideController.php";
  params.action = "index";
  return axios
    .get(url, { params })
    .then((response) => response?.data)
    .catch((error) => {
      console.error("Error en la solicitud:", error);
      throw error;
    });
}

function readGuide(params = {}) {
  const url =
    urlBase + "/TransferGuide/Controllers/TransferGuideController.php";
  params.action = "show";
  return axios
    .get(url, { params })
    .then((response) => response?.data)
    .catch((error) => {
      console.error("Error en la solicitud:", error);
      throw error;
    });
}

function listCompany(params = {}) {
  const url = urlBase + "/Company/Controllers/CompanyController.php";
  params.action = "index";
  return axios
    .get(url, { params })
    .then((response) => response?.data)
    .catch((error) => {
      console.error("Error en la solicitud:", error);
      throw error;
    });
}

function listUtil(params = {}) {
  const url = urlBase + "/Util/Controllers/UtilController.php";
  params.action = "index";
  return axios
    .get(url, { params })
    .then((response) => response?.data)
    .catch((error) => {
      console.error("Error en la solicitud:", error);
      throw error;
    });
}

function createCompany(data, params) {
  const url = urlBase + "/Company/Controllers/CompanyController.php";
  return axios
    .post(url, data, {
      params: {
        action: "store",
        ...params,
      },
    })
    .then((response) => response.data)
    .catch((error) => {
      console.error("Error en la solicitud POST:", error);
      throw error;
    });
}

function downloadGuide(data, params) {
  const url = urlBase + "/TransferGuide/Controllers/TransferGuideController.php";
  return axios
    .post(url, data, {
      params: {
        action: "download",
        ...params,
      },
    })
    .then((response) => response.data)
    .catch((error) => {
      console.error("Error en la solicitud POST:", error);
      throw error;
    });
}