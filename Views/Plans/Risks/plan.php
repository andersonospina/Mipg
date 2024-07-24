<?php
/** @var  $bootstrap */
/** @var  $model */
/** @var  $oid */

$bootstrap = service("bootstrap");
$plan = $model->get_Plan($oid);
$back = "/mipg/plans/view/{$oid}";

$code = "";

$card = $bootstrap->get_Card("card-view-service", array(
    "title" => sprintf("Riesgos del Plan de acción %s", $plan['order']),
    "header-back" => $back,
    "footer-continue" => $back,
    "content" => $code,
));
echo($card);
?>