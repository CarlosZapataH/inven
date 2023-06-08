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
      params: {
        action: "storeTransitMovementGuide",
        ...params,
      },
    })
    .then((response) => response.data)
    .catch((error) => {
      console.error("Error en la solicitud POST:", error);
      throw error;
    });
}
