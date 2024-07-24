<?php

use App\Libraries\Bootstrap;

/*
* -----------------------------------------------------------------------------
* [Requests]
* -----------------------------------------------------------------------------
*/
$request = service("request");
$dimension = $request->getGet("dimension");
$politic = $request->getGet("politic");
$diagnostic = $request->getGet("diagnostic");
$component = $request->getGet("component");

$b = new Bootstrap();

if (!empty($dimension) && !empty($politic) && !empty($diagnostic) && !empty($component)) {
    $mcomponents = model('App\Modules\Mipg\Models\Mipg_Components');
    $mcategories = model("\App\Modules\Mipg\Models\Mipg_Categories");

    $component = $mcomponents->where("component", $component)->first();
    $categories = $mcategories->where("component", $component["component"])->findAll();
    // Texts
    $component_name = urldecode($component["name"]);

    $html = "";
    $html .= "<table class=\"table table-bordered border-gray\">";
    $html .= "<tr>";
    $html .= "<th class=\"text-center \" style=\"width:36px;\">#</th>";
    $html .= "<th class=\"text-start\">Categorias</th>";
    $html .= "<th class=\"text-center\" style=\"width:90px;\">Puntajes</th>";
    $html .= "<th class=\"text-center\" style=\"width:100px;\">Estado</th>";
    $html .= "<th class=\"text-center\" style=\"width:32px;\"></th>";
    $html .= "<tr>";
    foreach ($categories as $category) {
        $order = $category["order"];
        $score = $mcategories->get_Score($category["category"]);
        $name = urldecode($category["name"]);
        $html .= "<tr id=\"category-{$category['category']}\">";
        $html .= "<td class=\"text-center px-2-1 bg-transparent\">{$order}</td>";
        $html .= "<td class=\"text-start px-2-1 bg-transparent\">{$name}</td>";
        $html .= "<td class=\"text-right px-2 bg-transparent\">{$score}</td>";
        $html .= "<td class=\"text-center px-2 bg-transparent\">{$b->get_Progress("d-{$category["category"]}",array("now"=>$score,"min"=>0, "max"=>100))}</td>";
        $html .= "<td class=\"text-center px-2-1 bg-transparent\"><a href=\"/mipg/control/home/" . lpk() . "?dimension={$dimension}&politic={$politic}&diagnostic={$diagnostic}&component={$component["component"]}&category={$category["category"]}\"><i class=\"fa fa-eye\"></i></a></td>";
        $html .= "</tr>";
    }

    $html .= "</table>";
    echo($html);
}
?>