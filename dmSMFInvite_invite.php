<?php

if(!require_once('dmSMFInvite_config.php')){die('No se ha podido cargar la configuracion.');}
if($context['user']['is_guest']){die(dm_bodyhead('Prohibido','Solo los usuarios registrados pueden invitar.'));}
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
if(isset($_GET['force'])){
    @setcookie("dm_force_invite",'true', time()+3600);
    echo dm_bodyhead('Forzado','Ahora, vuelve a enviar la invitacion. 
        Esta vez, NO comprobaremos si ya ha sido invitado.');
    template_footer();
    die();
}

if(isset($_POST['send_invitation'])){
    if(!empty($_POST['dm_email']) && !empty($_POST['token'])){
        $mail = $_POST['dm_email'];
        $token = $_POST['token'];
        if(isset($_POST['dm_text'])){$input = $_POST['dm_text'];} 
        else {$input = 'No hay mensaje.';}
        $user = $context['user']['username'];
        $userid = $context['user']['id'];
        if($dm->dm_send_invite($token,$mail,$input,$user,$userid)){
            die(dm_bodyhead('Invitación enviada.',
                    'Su invitación ha sido enviada a '.htmlentities($mail,ENT_QUOTES)));
        } else {
            die(dm_bodyhead('Error',$dm->dm_error));
        }
    } else {
        die(dm_bodyhead('Error','Falta el email o el token!!!.'));
    }
} else { die(dm_bodyhead('Error','Eres un gran juanker o.O'));}
template_footer();
?>
