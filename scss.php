<?php
include_once ('../../../tribe.init.php');
$sass = new Sass();
$sass->setStyle(Sass::STYLE_COMPRESSED);
$sass->setEmbed(true);
$css = $sass->compileFile(THEME_PATH.'/scss/bootstrap.scss');
echo $css;
?>