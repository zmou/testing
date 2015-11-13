<?php
$agio = app_config('agio');
$agio = empty($agio) || $agio > '100' || $agio < 0 ? '100' : $agio ;
?>