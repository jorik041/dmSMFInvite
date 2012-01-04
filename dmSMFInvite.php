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
 * #nombre: dmSMFInvite.php
 * #version: 0.1 beta
 * #contiene: Functiones Principales
 * ---------------------------
 */

class dmSMFInvite {
    /**
     *
     * @var data $db_handle
     * @desc Almacena la conexion para la BD.
     */
    private $db_handle = NULL;
    
    /**
     * @var string $dm_error;
     * @desc Almacena un error, para su devuelta.
     */
    public $dm_error = NULL;
     
    /**
     * @var array $dm_config
     * @desc Almacena la configuracion del sistema.
     */
    public $dm_config = NULL;

    /**
     * @name dm_log
     * @desc Intenta escribir en un archivo los errores del Script.
     * @param int $grade 1|2|3|none
     * @param string $what  String. Que causo el problema.
     * @return bool(true)
     * @example dmSMFInvite::dm_log(2,'Fallo al iniciar algo..');
     */
    public static function dm_log($grade, $what) {
        $handle = @fopen('dmSMFInvite_fLog.log','a+');
        if($grade==3){$stars = '###';}
        elseif($grade==2){$stars = '##';}
        elseif($grade==1){$stars = '#';}
        else {$stars = 'N/A';}
        $time = date('H:m:s d/m/Y');
        @fwrite($handle,'['.$stars.'] ['.$time.'] ---- '.$what."\n");
        @fclose($handle);
        return true;
    }

    /**
     * @name dm_die
     * @desc Intenta loggear algo y si se necesita, terminar la execucion.
     * @param string $type String. fatal | error | info
     * @param string $what String. Que causo el problema..
     * @return bool(true)
     * @example dmSMFInvite::dm_die('info','Testeando....');
     */
    private static function dm_die($type, $what) {
        switch ($type) {
            case 'fatal':
                dmSMFInvite::dm_log(3, $what);
                die('<div class="dmerror"><strong>!!!ME MUERO!!!.!!! 
                    Razon: ' . $what .' </strong></div>');
                break;
            case 'error':
                dmSMFInvite::dm_log(2, $what);
                echo '<div class="dmerror"><strong>--ERROR: ' . $what .
                        '</strong></div>';
                break;
            case 'info':
                dmSMFInvite::dm_log(1, $what);
        }
        return true;
    }
    
    /**
     * @name dm_obtain_config
     * @desc Obtiene la configuracion y la almacena en una variable.
     * @return bool true
     * @example $this->dm_obtain_config();
     */
    public function dm_obtain_config(){
        $res=$this->dm_runq("SELECT what,value FROM dm_config");
        while($row = mysql_fetch_array($res)){
            $dm_config[$row['what']] = $row['value'];
        }
        $this->dm_config = $dm_config;
        return true;
    }
    
    /**
     * @name dm_connect_db
     * @desc Intenta conectarse a la base de datos.
     * @param string $db_user Usuario para la BD.
     * @param string $db_pswd Contraseña para la BD.
     * @param string $db_host Servidor de la BD.
     * @param string $db_name Nombre de la BD.
     * @return bool true
     * @example $this->dm_connect_db($user,$password,'localhost','smf_forum');
     */
    public function dm_connect_db($db_user,$db_pswd,$db_host,$db_name){
        $handle = @mysql_connect($db_host,$db_user,$db_pswd) or
                dmSMFInvite::dm_die('fatal','No se puede connectar a la BD.');
        @mysql_select_db($db_name,$handle) or 
                dmSMFInvite::dm_die('fatal',mysql_error($handle));
        $this->db_handle = $handle;
        return true;
    }
    
    /**
     * @name dm_runq
     * @desc Intenta ejecutar una consulta y devuelve el resultado.
     * @param string $query Query.
     * @return mixed data|false
     * @example $this->dm_runq('SELECT * FROM users');
     */
    public function dm_runq($query){
        $result =  mysql_query($query,$this->db_handle);
        if($result){
            return $result;
        } else {
            dmSMFInvite::dm_die('error',mysql_error($this->db_handle));
            return false;
        }
    }
    
    /**
     * @name dm_close_db
     * @desc Intenta cerrar la conexion de la BD y limpiar la variable.
     * @return bool true
     * @example $this->dm_close_db();
     */
    public function dm_close_db(){
        mysql_close($this->db_handle) or
                dmSMFInvite::dm_die('error',mysql_error($this->db_handle));
        $this->db_handle = NULL;
        return true;
    }
    
    /**
     * @name dm_check_install
     * @desc Comprueba si el script corre por primera vez.
     * @return bool true|false
     * @example $this->dm_check_install();
     */
    public function dm_check_install(){
        if($this->db_handle == NULL){
            dmSMFInvite::dm_die('fatal','SIN BASE DE DATOS.');
            return false;
        } else{
            $dm_tables = array('dm_config','dm_users','dm_invites');
            $i = 0;
            $result = $this->dm_runq('SHOW TABLES');
            while($row = mysql_fetch_array($result)){
                if(in_array($row[0],$dm_tables)){
                    $i++;
                }
            }
            if($i==count($dm_tables)){
                return true;
            } else {
                return false;
            }
            
        }
    }
    
    
    /**
     * @name dm_get_invite_limit
     * @desc Devuelve el limite de invitaciones.
     * @return mixed int|false
     * @example $this->dm_get_invite_limit();
     */
    public function dm_get_invite_limit(){
        return $this->dm_config['ilimit'];
    }
    
    /**
     * @name dm_get_user_invite_left()
     * @desc Devuelve el limite de invitaciones de un usuario..
     * @param int $userid User ID for SMF.
     * @return mixed int|false
     * @example $this->dm_get_invite_left($userid);
     */
    public function dm_get_invite_left($user_id){
        $user_id = (int)$user_id;
        $res = $this->dm_runq("SELECT invites_left FROM dm_users WHERE
            user_id=".$user_id);
        $row = mysql_fetch_array($res);
        return $row['invites_left'];
    }
    
    /**
     * @name dm_get_user_groups
     * @desc Obtine el nombre y el ID de los grupos de SMF.
     * @return array
     * @example $this->dm_get_user_groups();
     */
    public function dm_get_user_groups(){
        $res = $this->dm_runq("SELECT id_group,group_name FROM ".
            dm_db_prefix."membergroups");
        while($row = mysql_fetch_array($res)){
            $dm_user_groups[] = array('id'=>$row['id_group'],
                'name'=>$row['group_name']);
        }
        return $dm_user_groups;
    }
    
    /**
     * @name dm_get_block_groups
     * @desc Devuelve los grupos bloqueados.
     * @return array
     * @example $this->dm_get_block_groups();
     */
    public function dm_get_block_groups(){
        return $this->dm_config['blockgroup'];
    }
    
    /**
     * @name dm_get_users
     * @desc Devuelve el ID de los usuarios, sus nombres y su limite.
     * @return array
     * @example $this->dm_get_users();
     */
    public function dm_get_users(){
        $res = $this->dm_runq("SELECT smf.real_name,dm.user_id,dm.invites_left
            FROM ".dm_db_prefix."members as smf, dm_users as dm WHERE 
                dm.user_id=smf.id_member");
        while ($row = mysql_fetch_array($res)){
            $dm_users[] = array('user_id'=>$row['user_id'],
                'name'=>$row['real_name'],'invites_left'=>$row['invites_left']);
        }
        return $dm_users;
    }
    
    /**
     * @name dm_reset_user_limit
     * @desc Pone 0 el limite de un usuario especifico.
     * @param int $user_id ID del usuario.
     * @return bool true
     * @example $this->dm_reset_user_limit(1); 
     */
    public function dm_reset_user_limit($user_id){
        $user_id = (int)$user_id;
        $res = $this->dm_runq("UPDATE dm_users SET invites_left='0' WHERE
            user_id='".$user_id."'");
        return true;
    }
    
    /**
     * @name dm_reset_limit
     * @desc Pone en 0 el limite de todos los usuarios.
     * @return bool true
     * @example $this->dm_reset_limit();
     */
    public function dm_reset_limit(){
        $res = $this->dm_runq("UPDATE dm_users SET invites_left='0'");
        return true;
    }
    
    /**
     * @name dm_check_if_blocked
     * @desc Comprueba si el usuario esta en el grupo bloqueado.
     * @param int $user_id ID del usuario.
     * @return bool true|false
     * @example $this->dm_check_if_blocked($user_id);
     */
    public function dm_check_if_blocked($user_id){
        $user_id = (int)$user_id;
        $blocked_groups = explode(',',$this->dm_get_block_groups());
        $res = $this->dm_runq("SELECT id_group FROM ".dm_db_prefix."members
            WHERE id_member= '".$user_id."'");
        $row = mysql_fetch_array($res);
        $user_group = $row['id_group'];
        foreach($blocked_groups as $bg){
            if($bg == $user_group){return true; break;}
        }
        return false;
    }
    
    /**
     * @name dm_public_log
     * @desc Escribe en un log publico que podran ver los administradores.
     * @param string $do write|read.
     * @param string $what sys|none.
     * @param string $value value.
     * @return mixed string|true 
     * @example $this->dm_public_log('write','sys','Testing');
     * @example $this->dm_public_log('read',0,0);
     */
    public function dm_public_log($do,$what,$value){
        if($do=='write'){
            $sys = '[Normal]';
            $handle = @fopen('log.log','a+');
            if($what==='sys'){$sys = '[Sistema]';}
            $time = date('H:m:s d/m/Y');
            @fwrite($handle,$sys.' ['.$time.'] ---- '.$value."\n");
            @fclose($handle);
            return true;  
        } else {
            $filename = "log.log";
            $handle = @fopen($filename, "r");
            $contents = @fread($handle, filesize($filename));
            @fclose($handle);
            $cnts = explode("\n",$contents);
            $cnts = array_reverse($cnts);
            $contents = implode("\n",$cnts);
            return $contents;
        }
    }
        
        /**
         * @name dm_aut_reset
         * @desc Determina si es necesario resetear los limites de los usuarios.
         * @return bool true|false
         * @example $this->dm_aut_reset();
         */
        public function dm_aut_reset (){
            $update = (int)$this->dm_config['update'];
            if($update < 1){return false;}else{
                $last_update = (int)$this->dm_config['last_update'];
                $actual_time = time();
                $passed_time = $last_update + ($update * 24 * 60 * 60);
                if($actual_time > $passed_time){
                    $this->dm_public_log('write','sys','La limitacion ha sido reseteada automaticamente.');
                    $this->dm_reset_limit();
                    $this->dm_runq("UPDATE dm_config SET value=".$actual_time."
                        WHERE what='last_update'");
                    return true;
                } else {return false;}
            }
        }
        
        /**
         * @name dm_check_user
         * @desc Comprueba si el usuario esta en la tabla de dm_users y si no
         * lo agrega.
         * @param int $user_id ID del usuario.
         * @return bool true
         * @example $this->dm_check_user($user_id);
         */
        public function dm_check_user($user_id){
            $user_id = (int)$user_id;
            $res = $this->dm_runq("SELECT COUNT(user_id) as rows_in FROM 
                dm_users WHERE user_id='".$user_id."'");
            $row = mysql_fetch_array($res);
            $count = $row['rows_in'];
            if($count > 0){return true;} else {
                $this->dm_runq("INSERT INTO dm_users (user_id,invites_left)
                    VALUES (".$user_id.",0)");
                return true;
            }
        }
        
        /**
         * @name dm_generate_token
         * @desc Genera una clave unica para evitar XSFR.
         * @return string
         * @example $this->dm_generate_token();
         */
        public function dm_generate_token(){
            @session_start();
            $token = md5(mt_rand(111,9999));
            $_SESSION['dm_token'] = $token;
            return $token;
        }
        
        /**
         * @name dm_check_token
         * @desc Comprueba si el token es valido
         * @param md5 $token
         * @return bool true|false
         * @example $this->dm_check_token($token);
         */
        public function dm_check_token($token){
            @session_start();
            $stoken = $_SESSION['dm_token'];
            if($stoken == $token){
                return true;
            } else {
                return false;
            }
        }
        
        /**
         * @name dm_clear_sql
         * @desc Limpia un string para ser guardado en la base de datos.
         * @param string $str
         * @return string string
         * @example $this->dm_clear_sql("'or=0'");
         */
        private function dm_clear_sql($str){
            return @utf8_encode(mysql_real_escape_string($str,$this->db_handle));
        }
        
        /**
         * @name dm_save_config
         * @desc Comprueba y almacena la configuracion
         * @param string $what
         * @param mixed $value int|str
         * @param md5 $token
         * @return true|false
         * @example $this->dm_save_config('limit',30,$token);
         */
        public function dm_save_config($what,$value,$token){
           if($this->dm_check_token($token)==false){
                $this->dm_error = 'Token no valido.';
                return false;
            }
            switch($what){
                case 'limit':
                    $value = (int)$value;
                    $this->dm_runq("UPDATE dm_config SET value='".$value."'
                        WHERE what='ilimit'");
                    return true;
                    break;
                case 'blockgroup':
                    $value = $this->dm_clear_block($value);
                    if(strlen($value) <= 0){$value = 0;}
                    $this->dm_runq("UPDATE dm_config SET value='".$value."'
                        WHERE what='blockgroup'");
                    return true;
                    break;
                case 'reset_user':
                    if($value === 'dm_all'){
                        $this->dm_reset_limit(); 
                        return true; 
                        break;
                    }
                    $value=(int)$value;
                    if($value <= 0){
                        $this->dm_error = 'No has selecionado ningun usuario.';
                        return false;
                        break;
                    }
                    $this->dm_runq("UPDATE dm_users SET invites_left='0'
                        WHERE user_id='".$value."'");
                    return true;
                    break;
                case 'autreset':
                    $value = (int)$value;
                    if($value <= 0) {$value = 0;}
                    $this->dm_runq("UPDATE dm_config SET value='".$value."'
                        WHERE what='update'");
                    return true;
                    break;
                case 'newrank':
                    $value = (int)$value;
                    if($value <= 0){
                        $this->dm_error = 'Ese rango no existe!';
                        return false;
                        break;
                    }
                    $this->dm_runq("UPDATE dm_config SET value='".$value."'
                        WHERE what='newrank'");
                    return true;
                    break;
                case 'purgereset':
                    $this->dm_runq("TRUNCATE TABLE dm_invites");
                    $this->dm_runq("TRUNCATE TABLE dm_users");
                    return true;
                    break;
            }
            $this->dm_error = 'Ninguna opcion selecionada.';
            return false;
        }
        
        /** 
         * @name dm_clear_block
         * @desc Deja solo las comas (,) y los numeros y elimina cualquier
         * numero o coma duplicado.
         * @param str $str
         * @return str
         * @example $this->dm_clear_block('1,2,asda,3dsadas,@"·,'); 
         */
        private function dm_clear_block($str){
            $str=preg_replace(array('/[^\d,]/','/(?<=,),+/','/^,+/','/,+$/'),
                    '',$str);
            $str = explode(',',$str);
            foreach($str as $strr){
                $strr = (int)$strr.',';
            }
            $str = array_unique($str);
            $str = implode(',',$str);
            return $str;
        }
        
        /**
         * @name dm_check_mail()
         * @desc Comprueba que el email sea valido
         * @param str $email
         * @return bool true|false
         * @example $this->dm_check_mail('example@domain.com');
         */
        private function dm_check_mail($mail){
            if (preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/',$mail)) {
                return true;} else {return false;}
        }
        
        /**
         * @name dm_send_mail
         * @desc Intenta enviar un mail y devuelve true o false.
         * @param str $mail
         * @param str $input
         * @param str $user
         * @return bool true|false
         * @example $this->dm_send_mail('example@dddd.com','Hello','dmonkey');
         */
        private function dm_send_mail($mail,$input,$user,$hash){
            $cabeceras  = 'MIME-Version: 1.0' . "\r\n";
            $cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $cabeceras .= 'From: '.dm_mail_from . "\r\n";
            $cabeceras .= 'Reply-To: '.dm_mail_reply . "\r\n";
            $dmailb = dm_mail_body;
            $dmailt = dm_mail_title;
            $dmailb = @str_replace('{dm_up}',$user,$dmailb);
            $dmailb = @str_replace('{dm_upm}',$input,$dmailb);
            $dmailb = @str_replace('{hash}',$hash,$dmailb);
            if(mail($mail,$dmailt,$dmailb,$cabeceras)){
                return true;} else {return false;}
        }
        
        /**
         * @name dm_if_invited
         * @desc Comprueba si ha sido invitado antes.
         * @param str $mail
         * @return bool true|false
         * @example $this->dm_if_invited('lala@lala.com')
         */
        private function dm_if_invited($mail){
            @session_start();
            if(isset($_COOKIE['dm_force_invite'])){
                if($_COOKIE['dm_force_invite']=='true'){
                    @setcookie("dm_force_invite",'false', time()-3600);
                    return false;
                }
            }
            $res = $this->dm_runq("SELECT COUNT(id) as cnt 
                FROM dm_invites WHERE email='".$mail."'");
            $row = mysql_fetch_array($res);
            if($row['cnt'] > 0){return true;} else {return false;}
        }
        
        /**
         * @name dm_alredy_member
         * @desc Comprueba el invitado ya es miembro.
         * @param str $mail
         * @return bool true|false
         */
        private function dm_alredy_member($mail){
            $res = $this->dm_runq("SELECT COUNT(id_member) as cnt FROM
                ".dm_db_prefix."members WHERE email_address='".$mail."'");
            $row = mysql_fetch_array($res);
            if($row['cnt'] > 0){return true;} else {return false;}
        }
      
        /**
         * @name dm_send_invite
         * @desc Comprueba que todo sea valido y envia la invitacion.
         * @param md5 $token
         * @param str $mail
         * @param str $input
         * @param str $user
         * @param int $user_id
         * @return mixed true|false|alredy
         * @example $this->dm_send_invite($token,'lala@laa.com','me',1); 
         */
        public function dm_send_invite($token,$mail,$input,$user,$user_id){
            if(!$this->dm_check_token($token)){
                $this->dm_error = 'Token, no valido';
                return false;
            }
            $user_id = (int)$user_id;
            $input = @utf8_encode(htmlentities($input,ENT_QUOTES));
            $user = @utf8_encode(htmlentities($user,ENT_QUOTES));
            if($this->dm_get_invite_left($user_id) >= $this->dm_get_invite_limit()){
                $this->dm_error = 'No puedes enviar mas invitaciones.';
                return false;}
            if($this->dm_check_if_blocked($user_id)){
                $this->dm_error = 'Estas bloqueado. No puedes enviar invitaciones.';
                return false;}
            if(!$this->dm_check_mail($mail)){
                $this->dm_error = 'El email, no es valido.';
                return false;}
            
            $hash = md5($mail.'dmsmf'.mt_rand(11,99));
            $user = $this->dm_clear_sql($user);
            $mail = $this->dm_clear_sql($mail);
            if($this->dm_if_invited($mail)){
                $this->dm_error = 'Este usuario ya ha sido invitado.';
                $this->dm_error .= ' El correo podria estar marcado como SPAM.';
                $this->dm_error .= '<br /> Si lo deseas, puedes reenviar ';
                $this->dm_error .= 'la invitación, haciendo click ';
                $this->dm_error .= '<a href="?force" title="Force"><u>AQUÍ</u></a>';
                return false;
            }
            if($this->dm_alredy_member($mail)){
                $this->dm_error = 'Este usuario ya es miembro.';
                return false;
            }
            if($this->dm_send_mail($mail,$input,$user,$hash)){
                $this->dm_runq("UPDATE dm_users SET invites_left = 
                    invites_left + 1 WHERE user_id= '".$user_id."'");
                $this->dm_runq("INSERT INTO dm_invites
                    (email,token,invite_by) VALUES 
                    ('".$mail."','".$hash."','".$user."')");
                $this->dm_public_log('write','none','El usuario '.$user_id.' ha invitado a '.$mail);
                return true;
            } else {
                $this->dm_error = 'Fallo en el envio de Email.';
                return false;
            }
        }
        
        /**
         * @name dm_verify_invite
         * @desc Comprueba la invitacion y da al usuario en rango.
         * @param md5 $token
         * @param md5 $hash
         * @return bool true|false
         * @example $this->dm_verify_infite($token,$hash);
         */
        public function dm_verify_invite($token,$hash,$user_id,$user){
            if($this->dm_check_token($token)){
                $user_id = (int)$user_id;
                $hash = $this->dm_clear_sql($hash);
                $res = $this->dm_runq("SELECT token,COUNT(id) as cnt FROM dm_invites 
                    WHERE token= '".$hash."'");
                $row = mysql_fetch_array($res);
                if($row['cnt'] <= 0){
                    $this->dm_error = 'Codigo no valido.';
                    return false;
                } else {
                   $this->dm_runq("UPDATE ".dm_db_prefix."members SET 
                       id_group='".$this->dm_config['newrank']."'
                       WHERE id_member='".$user_id."'");
                   $res = $this->dm_runq("SELECT invite_by FROM dm_invites WHERE
                       token= '".$hash."'");
                   $row = mysql_fetch_array($res);
                   $user = htmlentities($user,ENT_QUOTES);
                   $this->dm_public_log('write','none','El miembro '.$user.' , invitado por '.$row['invite_by'].' ha sido confirmado.');
                   $this->dm_runq("DELETE FROM dm_invites WHERE token='".$hash."'");
                   return true;
                }
            } else {$this->dm_error = 'Token no valido.'; return false;}
        }

}

?>
