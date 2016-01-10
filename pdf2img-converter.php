<?php
/*
Plugin Name: PDF2IMG Converter
Plugin URI: https://github.com/fedorenko-dmitriy/pdf2img-converter
Description: This plugin allow convert uploaded pdf file in runtime to img file.
This plugin use Imagick php extension.
Version: 1.0
Author: Dmytro Fedorenko
*/

define('P2J_DIR', plugin_dir_path(__FILE__));
define('P2J_URL', plugin_dir_url(__FILE__));

function p2j_load(){
    if(is_admin()) require_once(P2J_DIR.'includes/options.php');

    require_once(P2J_DIR.'includes/core.php');
}
p2j_load();

