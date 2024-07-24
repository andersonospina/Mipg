<?php
$bootstrap = service('bootstrap');

$mplans = model('App\Modules\Mipg\Models\Mipg_Plans');
$mattachments = model('App\Modules\Storage\Models\Storage_Attachments');
$mactions = model('App\Modules\Mipg\Models\Mipg_Actions');

$action = $mactions->get_Action($oid);
$plan = $mplans->get_Plan($action["plan"]);

//[grid]----------------------------------------------------------------------------------------------------------------
$bgrid = $bootstrap->get_Grid();
$bgrid->set_Class("P-0 m-0");
$bgrid->set_Headers(array(
    array("content" => "#", "class" => "text-center text-nowrap align-middle"),
    array("content" => "Tipo", "class" => "text-center align-middle"),
    array("content" => "Archivo", "class" => "text-center align-middle"),
    array("content" => "Opciones", "class" => "text-center text-nowrap align-middle"),
));
$count = 0;
$files = $mattachments->where('object', $oid)->orderBy("created_at", "DESC")->findAll();
if (is_array($files)) {
    foreach ($files as $file) {
        $count++;

        if ($file["reference"] == "ACT-EVI-01") {
            $type = "Evidencia";
        } elseif ($file["reference"] == "ACT-EVI-99") {
            $type = "Otro (Fuera del listado)";
        } else {
            $type = "Otro";
        }
        $url = "https://cdn.cgine.com" . $file["file"];
        $url_delete = "#";
        $options = "";
        $cell_count = array("content" => $count, "class" => "text-center  align-middle",);
        $cell_type = array("content" => $type, "class" => "text-center  align-middle",);
        $cell_url = array("content" => "<a href=\"" . $url . "\" target=\"_blank\">" . $file["attachment"] . "</a>", "class" => "text-center  align-middle",);
        $cell_options = "<div class=\"btn-group w-auto\">";
        $cell_options .= "<a href=\"" . $url . "\" class=\"btn btn-sm btn-primary\" target=\"_blank\"><i class=\"fa-light fa-eye\"></i></a>";

        if ($plan['status'] == "APPROVED" || $plan['status'] == "NOTCOMPLETED") {
            $cell_options .= "<a href=\"" . $url_delete . "\" data-attachment=\"" . $file["attachment"] . "\" class=\"btn btn-sm btn-danger  btn-delete\"><i class=\"fa-light fa-trash\"></i></a>";
        }
        $cell_options .= "</div>";
        $bgrid->add_Row(array($cell_count, $cell_type, $cell_url, $cell_options));
    }
} else {
    //mensaje no hay mallas
}

$pstatus = "";
if ($plan['status'] == "COMPLETED") {
    $pstatus = "disabled";
}

//[/grid]---------------------------------------------------------------------------------------------------------------
$code = "<!-- [files] //-->\n";
$code .= "<div class=\"container m-0 p-0\">\n";
$code .= "\t\t<form id=\"uploadForm\" class=\"row w-100 p-0 m-0\">\n";
$code .= "\t\t\t\t<div class=\"col-md-3 p-2\">\n";
$code .= "\t\t\t\t\t\t<select class=\"form-select {$pstatus}\" id=\"fileType\" {$pstatus}>\n";
$code .= "\t\t\t\t\t\t\t\t<option value=\"ACT-EVI-01\">Evidencia</option>\n";
$code .= "\t\t\t\t\t\t\t\t<option value=\"ACT-EVI-99\">Otro (Fuera del listado)</option>\n";
$code .= "\t\t\t\t\t\t</select>\n";
$code .= "\t\t\t\t</div>\n";
$code .= "\t\t\t\t<div class=\"col-md-7 p-2\">\n";
$code .= "\t\t\t\t\t\t<input type=\"file\" class=\"form-control {$pstatus} \" id=\"attachment\" multiple=\"multiple\" {$pstatus}>\n";
$code .= "\t\t\t\t</div>\n";
$code .= "\t\t\t\t<div class=\"col-md-2 p-2\">\n";
$code .= "\t\t\t\t\t\t<button type=\"button\" onclick=\"uploadFiles()\" class=\"btn btn-primary w-100 {$pstatus}\">Cargar</button>\n";
$code .= "\t\t\t\t</div>\n";
$code .= "\t\t</form>\n";
$code .= "\t\t<div class=\"row w-100 p-0 m-0\">\n";
$code .= "\t\t\t\t<div class=\"col-12 p-2\">\n";
$code .= "\t\t\t\t\t\t<div id=\"grid-files\">\n";
$code .= "\t\t\t\t\t\t{$bgrid}\n";
$code .= "\t\t\t\t\t</div>\n";
$code .= "\t\t\t\t</div>\n";
$code .= "\t\t</div>\n";
$code .= "</div>\n";
$code .= "<!-- [/files] //-->\n";
echo($code);

?>
<script>

    <?php if($plan['status'] == "APPROVED" || $plan['status'] == "NOTCOMPLETED"){?>
    function uploadFiles() {
        const object = "<?php echo($oid);?>";
        const input = document.getElementById('attachment');
        const fileType = document.getElementById('fileType').value;
        const grid = document.getElementById('grid-files').value;
        const url = "/storage/uploader/single/sie/" + object + "/?time=" + new Date().getTime();
        const files = input.files;
        const formData = new FormData();
        for (let i = 0; i < files.length; i++) {
            formData.append('attachment' + i, files[i]);
        }
        formData.append('reference', fileType);
        const xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                const response = JSON.parse(xhr.responseText);
                refreshGridFiles();
                //console.log(response);
                //alert('Archivos cargados: ' + response.status)
            }
        };
        xhr.send(formData);
    }
    <?php }else{?>
    function uploadFiles() {
        alert("No puedes cargar archivos debido al estado del plan");
    }
    <?php }?>
    function refreshGridFiles() {
        const object = "<?php echo($oid);?>";
        const gridfiles = document.getElementById('grid-files');
        gridfiles.innerHTML = "";
        let url = "/storage/api/files/json/" + object + "/?time=" + new Date().getTime();
        const formData = new FormData();
        const xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                const json = JSON.parse(xhr.responseText);
                let files = json.files;
                let table = '<table class="table table-striped table-bordered table-hoverP-0 m-0">';
                table += '<tr>';
                table += '<th>#</th>';
                table += '<th>Tipo</th>';
                table += '<th>Archivo</th>';
                table += '<th>Opciones</th>';
                table += '</tr>';
                for (let i = 0; i < files.length; i++) {
                    let url = "#";
                    let options = "";
                    options = "<div class=\"btn-group w-auto\">";
                    options += '<a href="' + url + '" class="btn btn-sm btn-primary" target="_blank"><i class="fa-light fa-eye"></i></a>';
                    options += '<a href="#" onclick="deleteAttachment(\'' + files[i].attachment + '\')" class="btn btn-sm btn-danger btn-delete" data-attachment="' + files[i].attachment + '"><i class="fa-light fa-trash"></i></a>';
                    options += "</div>";
                    if (files[i].reference == "ACT-EVI-01") {
                        reference = "Evidencia";
                    } else if (files[i].reference == "ACT-EVI-99") {
                        reference = "Otro (Fuera del listado)";
                    }
                    table += '<tr>';
                    table += '<td>' + (i + 1) + '</td>';
                    table += '<td>' + reference + '</td>';
                    table += '<td class="text-center"><a href="' + files[i].file + '" target="_blank">' + files[i].attachment + '</a></td>';
                    table += '<td>' + options + '</td>';
                    table += '</tr>';
                }
                table += '</table>';
                gridfiles.innerHTML = table;
            }
        };
        xhr.send(formData);
    }
</script>
<!-- Asegúrate de que el código HTML de la tabla se mantenga como está, pero asegúrate de que los botones de borrar tengan la clase "btn-delete" -->

<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        // Selecciona todos los botones con la clase "btn-delete"
        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault(); // Previene la acción por defecto para permitir mostrar el diálogo de confirmación
                // Muestra el diálogo de confirmación
                const attachment = button.getAttribute('data-attachment');
                deleteAttachment(attachment);
            });
        });
    });

    function deleteAttachment(attachment) {
        const confirmation = confirm("¿Estás seguro de que deseas eliminar este archivo?");
        if (confirmation) {
            let url = "/storage/api/files/delete/" + attachment + "/?time=" + new Date().getTime();
            const formData = new FormData();
            formData.append('attachment', attachment)
            const xhr = new XMLHttpRequest();
            xhr.open('POST', url, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    console.log("Archivo eliminado"); // Mensaje de prueba, remplázalo por tu lógica de eliminación
                    refreshGridFiles();
                }
            }
            xhr.send(formData);
        }
    }
</script>