<?php
if(!require_once('dmSMFInvite_config.php')){die('No se ha podido cargar la configuracion.');}
if(!$context['user']['is_admin']){die('Solo el administrador puede acededer aquí.');}
if(strlen($dm_remove_pwd)<4){die('Debes establecer una contraseña en el dmSMFInvite_config.');}

if(isset($_GET['token'])){
    if($dm->dm_check_token($_GET['token'])){
        $dm->dm_runq('DROP TABLE dm_config,dm_invites,dm_users');
        echo '<b>Desinstalado</b>';
    } else {
        die('Token No valido.');
    }  
} else {
    $token = $dm->dm_generate_token();
    die('<b>¿Estas seguro de que deseas desinstalar esta applicacion ?</b>
       <br /> Si continuas, las tablas de la base de datos seran automaticamente,
        eliminadas junto con todo su contenido. <br /><br />
        Si quieres continuar, haz click <a href="?token='.$token.'">aqui</a>');
}
?>
