<?php
if(!require_once('dmSMFInvite_config.php')){die('No se ha podido cargar la configuracion.');}
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
        .dmsmalln {font-size:10px;}
</style>
<?php
if($context['user']['is_guest']){
    echo dm_bodyhead('Solo registrados.',
            '<h2>Solo usuarios registrados pueden acceder aqui.</h2>
                Si te han enviado una invitacion, primero registrate y luego
                vuelve aquí con tu codigo.');
    template_footer();
    die();
}

if(isset($_POST['conf_reg'])){
    if(!empty($_POST['value']) && !empty($_POST['token'])){
        $hash = $_POST['value'];
        $token = $_POST['token'];
        $user = $context['user']['username'];
        $userid = $context['user']['id'];
        
        if($dm->dm_verify_invite($token,$hash,$userid,$user)){
            echo dm_bodyhead('Correcto','Ya has sido confirmado y tu nuevo
                rango ha sido establecido.');
            template_footer();
            die();
        } else {
            echo dm_bodyhead('Error',$dm->dm_error);
            template_footer();
            die();
        }
    } else {
        echo dm_bodyhead('Error','Token y/o codigo no valido.');
        template_footer();
        die();
    }
}

if(!empty($_GET['hash'])){$valuer = htmlentities($_GET['hash'],ENT_QUOTES);}
else {$valuer = '';}
$tokens = $dm->dm_generate_token();
$return_html = '<h1>Verificar codigo</h1>
    <form action="" method="POST">
    <label>Codigo </label>
    <input type="text" class="dmbutt" value="'.$valuer.'" name="value" /> 
    <input type="hidden" value="'.$tokens.'" name="token" />
    <input type="submit" value="Confirmar" name="conf_reg" /></form>';

echo dm_bodyhead('Verificar',$return_html);
template_footer();
die();
$dm->dm_close_db();
?>