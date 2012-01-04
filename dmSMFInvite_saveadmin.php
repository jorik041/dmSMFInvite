<?php
if(!require_once('dmSMFInvite_config.php')){die('No se ha podido cargar la configuracion.');}
if(!$context['user']['is_admin']){die(dm_bodyhead('Prohibido','No puedes estar aquí.'));}
template_header();
?>
<style type="text/css">
	.dmsmall {font-size:10px; text-align:right;}
	.dmemt {min-width:50% !important; letter-spacing:1px;}
	textarea {resize:none !important;}
	.dmbutt {min-width:20%; cursor:pointer;}
	.dmexpand {min-width:40%;}
	tbody {margin-top:10px !important;}
	.dmlog {min-width:99%; min-height:100px;}
</style>
<?php
if(empty($_POST['token'])){die(dm_bodyhead('Error','Falta tu token!!!'));}

if(isset($_POST['value'])){
    $token = $_POST['token'];
    $value = $_POST['value'];
    if(isset($_POST['dm_limit_f'])){
        if($dm->dm_save_config('limit',$value,$token)){
            echo dm_bodyhead('Correcto','El limite ha sido cambiado a '.(int)$_POST['value'].'.');
            template_footer();
            die();
        } else {
            echo dm_bodyhead('Error',$dm->dm_error);
            template_footer();
        }
    }

    elseif(isset($_POST['blockform'])){
        if($dm->dm_save_config('blockgroup',$value,$token)){
            echo dm_bodyhead('Correcto','Ha bloqueado los grupos indicados.');
            template_footer();
            die();
        } else {
            echo dm_bodyhead('Error',$dm->dm_error);
            template_footer();
        }
    }
    
    elseif(isset($_POST['resetuser'])){
        if($dm->dm_save_config('reset_user',$value,$token)){
            echo dm_bodyhead('Correcto','Ha reseteado el limite');
            template_footer();
            die();
        } else {
            echo dm_bodyhead('Error',$dm->dm_error);
            template_footer();
        }
    }
    
    elseif(isset($_POST['updateinter'])){
        if($dm->dm_save_config('autreset',$value,$token)){
            echo dm_bodyhead('Correcto','Su configuracion ha sido guardada.');
            template_footer();
            die();
        } else {
            echo dm_bodyhead('Error',$dm->dm_error);
            template_footer();
        }
    }
    
    elseif(isset($_POST['newrank'])){
        if($dm->dm_save_config('newrank',$value,$token)){
            echo dm_bodyhead('Correcto','Ha asignado el nuevo rango.');
            template_footer();
            die();
        } else {
            echo dm_bodyhead('Error',$dm->dm_error);
            template_footer();
        }
    }
    
    else {
        echo dm_bodyhead('Error','Ningun elemento seleccionado.');
        template_footer();
        die();
    }
    
} else {
    echo dm_bodyhead('Error','No ha introducido todos los datos.');
    template_footer();
    die();
}
?>
