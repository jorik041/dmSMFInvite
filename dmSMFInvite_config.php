<?php
/**
 * @name dmSMFInvitationSistem.
 * 
 * @author: Dragomir Valentinov Yourdanov (drvymonkey)
 * 
 * @link: www.drvy.net | www.drvymonkey.com
 * 
 * @email: bad.stupid.monkey@gmail.com
 * 
 * @license: Attribution-NonCommercial-ShareAlike 3.0 Unported (CC BY-NC-SA 3.0)
 * 
 * @desc: Sistema de invitaciones (separado) para SMF.
 * 
 * @version: 0.1 beta
 * 
 */
/*
 * ----> This file <----------
 * #nombre: dmSMFInvite_config.php
 * #version: 0.1 beta
 * #contiene: Configuracion y carga del sistema.
 * ---------------------------
 */

###########################################################
/* Configuracion General  */

//Para poder desinstalar este script, debe introducir una contraseña mayor de 4 chars.
$dm_remove_pwd = ''; 

$dm_allow_bots = 'no'; // Permitir bots ? yes / no
//
// Link hacia donde seran redirigidos los bots
$dm_redirect_bots = 'http://localhost/smf'; 

###########################################################
/* Ruta de los archivos SSI.php y Settings.php del foro Smf */

$dm_smf_SSI_file = '../SSI.php'; // Archivo SSI.php
$dm_smf_config_file = '../Settings.php'; // Archivo Settings.php

###########################################################
/* Enlaces. RECUERDA que siempre deben llevar http:// delante. */

 // Registro de SMF
$dm_link_register = 'http://localhost/smf/index.php?action=register';

// Link para la verificacion del usuario. Siempre debe acabar en verify.php
$dm_link_verify = 'http://localhost/smf/invitaciones/verify.php'; 

###########################################################
/* 
 * Configuracion para el envio de correo. Utiliza:
 * {link} -- Para el link del registro de nuevo usuario.
 * {dm_link} -- Para el link que verificara el usuario.
 * {dm_up} -- Para el nombre del usuario que lo ha invitado.
 * {dm_upm} -- Para mostrar el mensaje personal en caso de
 * que haya alguno.
 * {hash} -- Para la clave unica mediante la cual se podra
 * verificar el usuario
 */

$dm_mail_title = 'Invitacion Comunidad'; // Titulo del correo.

/* Texto del mensaje. */
$dm_mail_body = '<p>Hola, el usuario {dm_up}, te ha invitado a nuestra comunidad.';
$dm_mail_body .= '<br /> Si deseas unirte, visita nuestro ';
$dm_mail_body .= '<u><a href="{link}" title="Registro">registro de usuarios</a></u>.';
$dm_mail_body .= '<br /><br />Una vez completado, visita nuestro ';
$dm_mail_body .= '<u><a href="{dm_link}{hash}" title="Verificador">vericador de usuarios</a></u>,';
$dm_mail_body .= ' usuarios para asignarte el nuevo rango automaticamente.</p>';
$dm_mail_body .= '<br />Mensaje personal de {dm_up}<hr />{dm_upm}<hr />';
$dm_mail_body .= '<br /><b>Links</b><br><b>Registro</b>: {link} <br />';
$dm_mail_body .= '<b>Verificacion</b>: {dm_link}{hash} <br />';
$dm_mail_body .= '<b>Hash</b>: {hash} <br />';
$dm_mail_body .= '<hr /> Un saludo de nuestra comunidad.';

$dm_header_from = 'example@example.com'; // De donde proviene el correo.
$dm_header_reply = 'example@example.com'; // A quien responder.

###########################################################
/* NO TOCAR DESDE AQUI / DO NOT EDIT FROM HERE */
###########################################################
if($dm_allow_bots=='no'){
    if(@stristr($_SERVER['HTTP_USER_AGENT'],'bot'))
        {die(header("Location: ".$dm_redirect_bots));}
}
function dm_safe_include($file){
    if(!include_once($file)){
        die('ERROR GRAVE, No se ha podido incluir el archivo: '.$file.'
            <br /> Compruebe la configuracion y si el error persiste,
            Haga una nueva instalación.');
    }
    return true;
}

dm_safe_include($dm_smf_SSI_file);
dm_safe_include($dm_smf_config_file);

dm_safe_include('dmSMFInvite.php');

$dm_mail_body = @str_replace('{link}',$dm_link_register,$dm_mail_body);
$dm_mail_body = @str_replace('{dm_link}',$dm_link_verify.'?hash=',$dm_mail_body);
define('dm_mail_title',$dm_mail_title);
define('dm_mail_body',$dm_mail_body);
define('dm_mail_from',$dm_header_from);
define('dm_mail_reply',$dm_header_reply);
$term_message = '<div class="dmsmall" align="right">developed by: <a href="http://drvy.net" 
    target="_blank">@drvymonkey</a></div>';

###########################################################
###########################################################
/* 
 * En caso de que desees que se use configuracion personal
 * para el accesso a la base de datos, elimina los # de las
 * variables y pon tu configuracion. 
 */

#$db_user = 'user'; // Usuario de la base de datos.
#$db_passwd = 'password'; // Contraseña de la base de datos.
#$db_server = 'localhost'; // Servidor de la base de datos.
#$db_name = 'smf_forum'; // Nombre de la base de datos de smf.
#$db_prefix = 'smf_'; // Prefix que itliza SMF (smf_) para las tablas.

###########################################################
/* NO TOCAR DESDE AQUI / DO NOT EDIT FROM HERE */
###########################################################
define('dm_db_prefix',$db_prefix);

function dm_bodyhead($title,$context){
    return '<table class="table_list"><tbody class="header" id="category_1"><tr>
        <td colspan="4"><div class="cat_bar"><h3 class="catbg">'.$title.'</h3>
	</div></td></tr></tbody><tbody class="content" id="category_1_boards">
        <tr id="board_1" class="windowbg2"><td class="info" valign="top">
        <p>'.$context.'</p> 
        </td></tr></table><div class="dmsmall" align="right">developed by: 
        <a href="http://drvy.net" target="_blank">@drvymonkey</a></div>';
}
if(!file_exists('dmSMFInstall.php')){
   $dm = New dmSMFInvite();
    $dm->dm_connect_db($db_user,$db_passwd,$db_server,$db_name);
    if(!$dm->dm_check_install()){
        die('Error en la instalacion. Por favor, reinstale esta applicacion.');
    }
    $dm->dm_obtain_config(); 
}
?>
