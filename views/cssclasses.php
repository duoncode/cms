<?php
$ft = $prefix . $field->fieldType;
$vt = $prefix . strtolower($field->valueType);
$cs = $prefix . 'colspan-' . $field->colspan;
$rs = $prefix . 'rowspan-' . $field->rowspan;
?><?= "$ft $vt $cs $rs" ?>
