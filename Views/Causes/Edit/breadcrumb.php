<?php

/**
 * █ ---------------------------------------------------------------------------------------------------------------------
 * █ ░FRAMEWORK                                  2024-04-15 15:29:38
 * █ ░█▀▀█ █▀▀█ █▀▀▄ █▀▀ ░█─░█ ─▀─ █▀▀▀ █▀▀▀ █▀▀ [App\Modules\Plans\Views\Causes\Creator\deny.php]
 * █ ░█─── █──█ █──█ █▀▀ ░█▀▀█ ▀█▀ █─▀█ █─▀█ ▀▀█ Copyright 2023 - CloudEngine S.A.S., Inc. <admin@cgine.com>
 * █ ░█▄▄█ ▀▀▀▀ ▀▀▀─ ▀▀▀ ░█─░█ ▀▀▀ ▀▀▀▀ ▀▀▀▀ ▀▀▀ Para obtener información completa sobre derechos de autor y licencia,
 * █                                             consulte la LICENCIA archivo que se distribuyó con este código fuente.
 * █ ---------------------------------------------------------------------------------------------------------------------
 * █ EL SOFTWARE SE PROPORCIONA -TAL CUAL-, SIN GARANTÍA DE NINGÚN TIPO, EXPRESA O
 * █ IMPLÍCITA, INCLUYENDO PERO NO LIMITADO A LAS GARANTÍAS DE COMERCIABILIDAD,
 * █ APTITUD PARA UN PROPÓSITO PARTICULAR Y NO INFRACCIÓN. EN NINGÚN CASO SERÁ
 * █ LOS AUTORES O TITULARES DE LOS DERECHOS DE AUTOR SERÁN RESPONSABLES DE CUALQUIER
 * █ RECLAMO, DAÑOS U OTROS RESPONSABILIDAD, YA SEA EN UNA ACCIÓN DE CONTRATO,
 * █ AGRAVIO O DE OTRO MODO, QUE SURJA DESDE, FUERA O EN RELACIÓN CON EL SOFTWARE
 * █ O EL USO U OTROS NEGOCIACIONES EN EL SOFTWARE.
 * █ ---------------------------------------------------------------------------------------------------------------------
 * █ @Author Jose Alexis Correa Valencia <jalexiscv@gmail.com>
 * █ @link https://www.codehiggs.com
 * █ @Version 1.5.0 @since PHP 7, PHP 8
 * █ ---------------------------------------------------------------------------------------------------------------------
 * █ Datos recibidos desde el controlador - @ModuleController
 * █ ---------------------------------------------------------------------------------------------------------------------
 * █ @var object $parent Trasferido desde el controlador
 * █ @var object $authentication Trasferido desde el controlador
 * █ @var object $request Trasferido desde el controlador
 * █ @var object $dates Trasferido desde el controlador
 * █ @var string $component Trasferido desde el controlador
 * █ @var string $view Trasferido desde el controlador
 * █ @var string $oid Trasferido desde el controlador
 * █ @var string $views Trasferido desde el controlador
 * █ @var string $prefix Trasferido desde el controlador
 * █ @var array $data Trasferido desde el controlador
 * █ @var object $model Modelo de datos utilizado en la vista y trasferido desde el index
 * █ ---------------------------------------------------------------------------------------------------------------------
 **/
$bootstrap = service("bootstrap");
//[models]--------------------------------------------------------------------------------------------------------------
$mdimensions = model('App\Modules\Mipg\Models\Mipg_Dimensions');
$mpolitics = model('App\Modules\Mipg\Models\Mipg_Politics');
$mdiagnostics = model('App\Modules\Mipg\Models\Mipg_Diagnostics');
$mcomponents = model('App\Modules\Mipg\Models\Mipg_Components');
$mcategories = model('App\Modules\Mipg\Models\Mipg_Categories');
$mactivities = model('App\Modules\Mipg\Models\Mipg_Activities');
$mplans = model('App\Modules\Mipg\Models\Mipg_Plans');
$mcauses = model('App\Modules\Mipg\Models\Mipg_Causes');
// $oid Recibe  "Plan"
$cause = $mcauses->get_Cause($oid);
$plan = $mplans->get_Plan($cause['plan']);
$activity = $mactivities->get_Activity($plan['activity']);
$category = $mcategories->get_Category($activity['category']);
$component = $mcomponents->get_Component($category['component']);
$diagnostic = $mdiagnostics->get_Diagnostic($component['diagnostic']);
$politic = $mpolitics->get_Politic($diagnostic['politic']);
$dimension = $mdimensions->get_Dimension($politic['dimension']);

$name_category = $strings->get_Striptags($category["name"]);
$name_component = $strings->get_Striptags($component["name"]);
$name_diagnostic = $strings->get_Striptags($diagnostic["name"]);
$name_politic = $strings->get_Striptags($politic["name"]);
$name_dimension = $strings->get_Striptags($dimension["name"]);

$menu = array(
    array("href" => "/mipg/", "text" => "MiPG", "class" => false),
    array("href" => "/mipg/politics/home/{$dimension['dimension']}", "text" => "Dimensión", "class" => false),
    array("href" => "/mipg/diagnostics/home/{$politic['politic']}", "text" => "Política", "class" => false),
    array("href" => "/mipg/components/home/{$diagnostic['diagnostic']}", "text" => "Diagnóstico", "class" => false),
    array("href" => "/mipg/categories/home/{$category['category']}", "text" => "Componente", "class" => false),
    array("href" => "/mipg/activities/home/$oid", "text" => "Categoría", "class" => true),
    array("href" => "/mipg/plans/home/$oid", "text" => "Actividad", "class" => true),
);
echo($bootstrap->get_Breadcrumb($menu));

?>