<html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>dmSMFInvites - Instalador</title>
    </head>
    <body>
<?php
if(isset($_GET['step'])){
    $step = $_GET['step'];
    switch($step){
        case '1':
            echo 'Procediendo a llamar a <b>dmSMFInvite_config.php</b><br />';
            if(!require_once('dmSMFInvite_config.php')){die('No se ha podido cargar la configuracion.');}
            echo 'Correcto. Para el siguiente paso, haz click <a href="?step=2"><u>aquí</u>.';
            break;
            
        case '2':
            if(!require_once('dmSMFInvite_config.php')){die('No se ha podido cargar la configuracion.');}
            if(!$context['user']['is_admin']){die('Solo un usuario autentificado como administrador puede continuar.');}
            echo 'Procediendo a llamar a <b>dmSMFInvite.php</b><br />';
            if(!require_once('dmSMFInvite.php')){die('No se ha podido cargar el archivo.');}
            echo '-> Correcto.<br />';
            echo 'Procediendo a establecer conexion con base de datos...<br />';
            $dm = New dmSMFInvite();
            if(!$dm->dm_connect_db($db_user,$db_passwd,$db_server,$db_name)){
                die('No se ha podido establecer conexion con la base de datos.');
            }
            echo '-> Correcto.<br />';
            echo 'Procediendo a popular base de datos...<br />';
            $dm->dm_runq("CREATE TABLE IF NOT EXISTS dm_config (id int(11) NOT NULL AUTO_INCREMENT, what varchar(60) NOT NULL, value varchar(300) NOT NULL,PRIMARY KEY (id)) DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;");
            echo '---> Tabla: dm_config <b>creada</b><br />';
            $dm->dm_runq("INSERT INTO dm_config (id, what, value) VALUES (1, 'ilimit', '3'), (2, 'blockgroup', '999'), (3, 'update', '0'), (4, 'last_update', '0'), (5, 'newrank', '0');");
            echo '-----> Tabla: dm_config <b>populada</b><br />';
            $dm->dm_runq("CREATE TABLE IF NOT EXISTS dm_invites (id int(11) NOT NULL AUTO_INCREMENT, email varchar(150) NOT NULL, token varchar(32) NOT NULL, invite_by varchar(60) NOT NULL, PRIMARY KEY (id) ) DEFAULT CHARSET=utf8 ;");
            echo '---> Tabla: dm_invites <b>creada</b><br />';
            $dm->dm_runq("CREATE TABLE IF NOT EXISTS dm_users (id int(11) NOT NULL AUTO_INCREMENT,user_id int(11) NOT NULL, invites_left int(11) NOT NULL,PRIMARY KEY (id) ) DEFAULT CHARSET=utf8 ;");
            echo '---> Tabla: dm_users <b>creada</b><br />';
            echo '<br /><b>TODO CORRECTO.</b>';
            echo ' Haz click <a href="?step=3"><u>aquí</u></a> para continuar.';
            break;
        case '3':
            if(!require_once('dmSMFInvite_config.php')){die('No se ha podido cargar la configuracion.');}
            if(!$context['user']['is_admin']){die('Solo un usuario autentificado como administrador puede continuar.');}
            $dm = New dmSMFInvite();
            if(!$dm->dm_connect_db($db_user,$db_passwd,$db_server,$db_name)){
                die('No se ha podido establecer conexion con la base de datos.');
            }
            echo 'Comprobando instalacion...<br />';
            if(!$dm->dm_check_install()){
                die('Error en la instalacion. Por favor, contacta con el creador (drvy.net).');
            } else {
                echo '-> Base de datos y tabla correctos.<br />';
            }
            echo 'Intentado borrar <b>dmSMFInstall.php</b>...<br />';
            if(!unlink('dmSMFInstall.php')){
                die('-> <b>Error</b>, debe eliminar este archivo manualmente. 
                    Asegurse de que dmSMFInvite.php tenga los permisos necesarios (lectura y escritura).<br />
                    <b>TODO CORRECTO. Asistente finalizado.</b>');
            } else {
                die('---> Correcto. <br /> <b>TODO CORRECTO. Asistente finalizado.</b>');
            }
            
    }
} else {
    die('Para iniciar el asistente, primero<b> CONFIGURE su dmSMFInvite_config.php.</b><br />
        Una vez hecho eso, haga click <a href="?step=1">aquí</a> para continuar.');
}
?>
    </body>
</html>