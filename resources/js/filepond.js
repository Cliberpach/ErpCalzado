import * as FilePond from "filepond";
import FilePondPluginImagePreview from "filepond-plugin-image-preview";
import FilePondPluginFileValidateSize from "filepond-plugin-file-validate-size";
import es_ES from "filepond/locale/es-es.js";

import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type";

FilePond.registerPlugin(
    FilePondPluginImagePreview,
    FilePondPluginFileValidateSize,
    FilePondPluginFileValidateType
);

FilePond.setOptions(es_ES);

window.FilePond = FilePond;
