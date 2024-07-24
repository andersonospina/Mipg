<?php
/*
 * **
 *  ** █ ---------------------------------------------------------------------------------------------------------------------
 *  ** █ ░FRAMEWORK                                  2023-12-01 23:19:27
 *  ** █ ░█▀▀█ █▀▀█ █▀▀▄ █▀▀ ░█─░█ ─▀─ █▀▀▀ █▀▀▀ █▀▀ [App\Modules\Account\Views\Processes\Creator\deny.php]
 *  ** █ ░█─── █──█ █──█ █▀▀ ░█▀▀█ ▀█▀ █─▀█ █─▀█ ▀▀█ Copyright 2023 - CloudEngine S.A.S., Inc. <admin@cgine.com>
 *  ** █ ░█▄▄█ ▀▀▀▀ ▀▀▀─ ▀▀▀ ░█─░█ ▀▀▀ ▀▀▀▀ ▀▀▀▀ ▀▀▀ Para obtener información completa sobre derechos de autor y licencia,
 *  ** █                                             consulte la LICENCIA archivo que se distribuyó con este código fuente.
 *  ** █ ---------------------------------------------------------------------------------------------------------------------
 *  ** █ EL SOFTWARE SE PROPORCIONA -TAL CUAL-, SIN GARANTÍA DE NINGÚN TIPO, EXPRESA O
 *  ** █ IMPLÍCITA, INCLUYENDO PERO NO LIMITADO A LAS GARANTÍAS DE COMERCIABILIDAD,
 *  ** █ APTITUD PARA UN PROPÓSITO PARTICULAR Y NO INFRACCIÓN. EN NINGÚN CASO SERÁ
 *  ** █ LOS AUTORES O TITULARES DE LOS DERECHOS DE AUTOR SERÁN RESPONSABLES DE CUALQUIER
 *  ** █ RECLAMO, DAÑOS U OTROS RESPONSABILIDAD, YA SEA EN UNA ACCIÓN DE CONTRATO,
 *  ** █ AGRAVIO O DE OTRO MODO, QUE SURJA DESDE, FUERA O EN RELACIÓN CON EL SOFTWARE
 *  ** █ O EL USO U OTROS NEGOCIACIONES EN EL SOFTWARE.
 *  ** █ ---------------------------------------------------------------------------------------------------------------------
 *  ** █ @Author Jose Alexis Correa Valencia <jalexiscv@gmail.com>
 *  ** █ @link https://www.codehiggs.com
 *  ** █ @Version 1.5.0 @since PHP 7, PHP 8
 *  ** █ ---------------------------------------------------------------------------------------------------------------------
 *  ** █ Datos recibidos desde el controlador - @ModuleController
 *  ** █ ---------------------------------------------------------------------------------------------------------------------
 *  ** █ @authentication, @request, @dates, @parent, @component, @view, @oid, @views, @prefix
 *  ** █ ---------------------------------------------------------------------------------------------------------------------
 *  **
 */

/* @$oid Recibe el "Requerimiento" */
$bootstrap = service('bootstrap');
$strings = service('strings');
//[models]--------------------------------------------------------------------------------------------------------------
$mdimensions = model('App\Modules\Mipg\Models\Mipg_Dimensions');
$mpolitics = model('App\Modules\Mipg\Models\Mipg_Politics');
$mdiagnostics = model('App\Modules\Mipg\Models\Mipg_Diagnostics');
$mcomponents = model('App\Modules\Mipg\Models\Mipg_Components');
$mcategories = model('App\Modules\Mipg\Models\Mipg_Categories');
$mactivities = model('App\Modules\Mipg\Models\Mipg_Activities');

// $oid Recibe la "Diagnostic"
$diagnostic = $mdiagnostics->get_Diagnostic($oid);
if (!$diagnostic) {
    $component = $mcomponents->get_Component($oid);
    $diagnostic = $mdiagnostics->get_Diagnostic($component['diagnostic']);
}
$politic = $mpolitics->get_Politic($diagnostic['politic']);
$dimension = $mdimensions->get_Dimension($politic['dimension']);

$name_diagnostic = $strings->get_Striptags($diagnostic["name"]);
$name_politic = $strings->get_Striptags($politic["name"]);
$name_dimension = $strings->get_Striptags($dimension["name"]);

$links['dimensions'] = $bootstrap->get_A("link-$oid", array('href' => "/mipg/dimensions/home/" . lpk(), 'content' => lang('App.Dimensions')));
$links['dimension'] = $bootstrap->get_A("link-$oid", array('href' => "/mipg/politics/home/{$dimension['dimension']}", 'content' => $name_dimension));
$links['politic'] = $bootstrap->get_A("link-$oid", array('href' => "/mipg/diagnostics/home/{$politic['politic']}", 'content' => $name_politic));
$links['diagnostic'] = $bootstrap->get_A("link-$oid", array('href' => "/mipg/components/home/$oid", 'content' => $name_diagnostic));

$ndimension = $bootstrap->get_TreeNode($links['dimension'], "Dimensión");
$npolitic = $bootstrap->get_TreeNode($links['politic'], "Política");
$ndiagnostic = $bootstrap->get_TreeNode($links['diagnostic'], "Diagnóstico");
//$ncomponent = $bootstrap->get_TreeNode($links['component'], "Componente");

$tree = $bootstrap->get_Tree($ndimension);
$ndimension->addChild($npolitic);
$npolitic->addChild($ndiagnostic);
//$ndiagnostic->addChild($ncomponent);

$render = $tree->render();

/**
 * $root = $bootstrap->get_TreeNode($links['dimensions']);
 * $ndimension = $bootstrap->get_TreeNode($links['dimension']);
 * $npolitic = $bootstrap->get_TreeNode($links['politic']);
 * $ndiagnostic = $bootstrap->get_TreeNode($links['diagnostic']);
 * $ncomponents = $bootstrap->get_TreeNode(lang('App.Components'));
 *
 * $tree = $bootstrap->get_Tree($root);
 * $root->addChild($ndimension);
 * $ndimension->addChild($npolitic);
 * $npolitic->addChild($ndiagnostic);
 * $ndiagnostic->addChild($ncomponents);
 * $render = $tree->render();
 * **/

//[widget-score]--------------------------------------------------------------------------------------------------------
$widget_score = $bootstrap->get_Score(array(
    "title" => lang('App.Diagnostic'),
    "value" => $mdiagnostics->get_Score($oid),
    "description" => "Valoración promedio",
));
echo($widget_score);
//[widget]--------------------------------------------------------------------------------------------------------------
$widget = $bootstrap->get_Card("card-view-service", array(
    "title" => "Ruta MiPG",
    "content" => $render,
    "body-class" => "py-0 px-0",
));
echo($widget);
?>