<?php
if(!require_once('dmSMFInvite_config.php')){die('No se ha podido cargar la configuracion.');}
if($context['user']['is_guest'] OR $dm->dm_check_if_blocked($context['user']['id'])){
   template_header();
   echo dm_bodyhead('Prohibido','Solo los usuarios registrados/admitidos pueden invitar.');
   template_footer();
   die();
}
template_header();

$dm->dm_aut_reset();
$dm->dm_check_user($context['user']['id']);
$token = $dm->dm_generate_token();
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
<script languaje='JavaScript'>
	function vaciarCampos(){
		document.getElementById('email').value = '';
		document.getElementById('tpersonal').value = '';
	}
</script>


<table class="table_list">
	<tbody class="header" id="category_1">
		<tr>
			<td colspan="4">
				<div class="cat_bar">
					<h3 class="catbg">Enviar invitación</h3>
				</div>
			</td>
		</tr>
	</tbody>
	<tbody class="content" id="category_1_boards">
		<tr id="board_1" class="windowbg2">
			<td class="info" valign="top">
				<a class="subject">Enviar invitación.</a>
				<p>
                                    <?php
                                     $id = $context['user']['id'];
                                     $get_limit = $dm->dm_get_invite_left($id);
                                     $max_limit = $dm->dm_config['ilimit'];
                                     $show = $max_limit - $get_limit;
                                     echo 'Actualmente tienes <b>'.$show.'</b>
                                         invitación/es restante/s de '.$max_limit;
                                     ?>
                                </p>
			</td>
			<td class="lastpost"><form action="dmSMFInvite_invite.php" method="POST">
					<label><strong>E-mail del invitado *</strong></label><br />
					<input class="dmemt" id="email" type="text" autocomplete="off" name="dm_email" value="ejemplo@ejemplo.com" />
					<br /><br />
					<label><strong>Texto personal</strong></label><br />
					<textarea id="tpersonal" name="dm_text" rows="5" cols="70">Hola, acepta mi invitación!</textarea><br /><br />
                                        <input type="hidden" value="<?php echo $token; ?>" name="token" />
					<input class="dmbutt" type="submit" name="send_invitation" value="Enviar" />
					<input class="dmbutt" type="reset" onclick='vaciarCampos()' value="Borrar campos" />
                            </form>
			</td>
					
		</tr>
			</tbody>
			<tbody class="divider"><tr><td colspan="4"></td></tr></tbody>
		</table>
<?php
if(!$context['user']['is_admin']){die($term_message.template_footer());}
?>
<table class="table_list">
	<tbody class="header" id="category_1">
		<tr>
			<td colspan="4">
				<div class="cat_bar">
					<h3 class="catbg">Administracion</h3>
				</div>
			</td>
		</tr>
	</tbody>

	<tbody class="content" id="category_1_boards">
		<tr id="board_1" class="windowbg2">
			<td class="info" valign="top">
				<a class="subject">Limite de invitaciones.</a>
				<p>Aquí puedes determinar el limite de invitaciones que tendrán los usuarios.</p>
			</td>
			<td class="lastpost dmexpand">
					<label><strong>Limite</strong></label><br />
                                        <form action="dmSMFInvite_saveadmin.php" method="POST">
					<input class="dmemt" type="text" value="<?php echo $max_limit ?>" autocomplete="off" name="value"/><br /><br />
                                        <input type="hidden" value="<?php echo $token; ?>" name="token" />
					<input class="dmbutt" name="dm_limit_f" type="submit" value="Guardar configuración" />
                                        </form>
			</td>
					
		</tr>
	</tbody>

	<tbody class="divider"><tr><td colspan="4"></td></tr></tbody>

	<tbody class="content" id="category_1_boards">
		<tr id="board_1" class="windowbg2">
			<td class="info" valign="top">
				<a class="subject">Bloquear grupos de usuarios.</a>
				<p>Aquí puedes bloquear grupos de usuarios que no serán capaces de enviar invitaciónes.</p>
			</td>
			<td class="lastpost dmexpand">
					<label><strong>Ver grupos</strong></label><br />
					<select class="dmemt">
						<?php
                                                $dar = $dm->dm_get_user_groups();
                                                foreach($dar as $group){
                                                    echo '<option>';
                                                    echo 'ID: '.$group['id'];
                                                    echo ' Nombre: '.$group['name'];
                                                    echo '</option>';
                                                }
                                                ?>
					</select><br /><br />
                                        <form action="dmSMFInvite_saveadmin.php" method="POST">
					<label><strong>ID de grupos bloqueados, separados por coma (,)</strong></label><br />
					<input class="dmemt" value="<?php echo $dm->dm_config['blockgroup']; ?>" type="text" autocomplete="off" name="value"/><br /><br />
                                        <input type="hidden" value="<?php echo $token; ?>" name="token" />
					<input class="dmbutt" name="blockform" type="submit" value="Guardar configuración" />
                                        </form>
			</td>
					
		</tr>
	</tbody>

	<tbody class="divider"><tr><td colspan="4"></td></tr></tbody>

	<tbody class="content" id="category_1_boards">
		<tr id="board_1" class="windowbg2">
			<td class="info" valign="top">
				<a class="subject">Resetear limitación.</a>
				<p>Aquí puedes resetear la limitación que tiene un determinado usuario o de todos.</p>
			</td>
			<td class="lastpost dmexpand"><form action="dmSMFInvite_saveadmin.php" method="POST">
					<label><strong>Seleccionar usuario:</strong></label><br />
					<select name="value" class="dmemt">
						<?php
                                                $rim = $dm->dm_get_users();
                                                foreach($rim as $user_s){
                                                    echo '<option value="'.$user_s['user_id'].'">';
                                                    echo htmlentities($user_s['name'],ENT_QUOTES);
                                                    echo ' ('.$user_s['invites_left'].'/'.$max_limit.')';
                                                    echo '</option>';
                                                }
                                                ?>
                                            <option value="dm_all">-----Todos-----</option>
					</select><br /><br />
                                        <input type="hidden" value="<?php echo $token; ?>" name="token" />
					<input class="dmbutt" type="submit" name="resetuser" value="Resetear" /></form>
			</td>
					
		</tr>
	</tbody>
	
	<tbody class="divider"><tr><td colspan="4"></td></tr></tbody>

	<tbody class="content" id="category_1_boards">
		<tr id="board_1" class="windowbg2">
			<td class="info" valign="top">
				<a class="subject">Resetear limitación automáticamente.</a>
				<p>Aquí puedes elegir cuando resetear (o no hacerlo) los limites.</p>
			</td>
			<td class="lastpost dmexpand">
                                        <form action="dmSMFInvite_saveadmin.php" method="POST">
					<label><strong>Resetear automáticamente cada x dias. (0 = nunca)</strong></label><br />
					<input class="dmemt" type="text" value="<?php echo $dm->dm_config['update']; ?>" autocomplete="off" name="value"/><br /><br />
                                        <input type="hidden" value="<?php echo $token; ?>" name="token" />
					<input class="dmbutt" type="submit" name="updateinter" value="Guardar configuración" />
                                        </form>
			</td>
					
		</tr>
	</tbody>

	<tbody class="divider"><tr><td colspan="4"></td></tr></tbody>

	<tbody class="content" id="category_1_boards">
		<tr id="board_1" class="windowbg2">
			<td class="info" valign="top">
				<a class="subject">Rango de nuevos usuarios.</a>
				<p>Aquí puedes elegir el rango que obtendrán los nuevos usuarios,</p>
				<p>cuando completen su registro.</p>
			</td>
			<td class="lastpost dmexpand">
                            <form action="dmSMFInvite_saveadmin.php" method="POST">
					<label><strong>Rango</strong></label><br />
					<select name="value">
						<?php
                                                foreach($dar as $group){
                                                    echo '<option value="'.$group['id'].'">';
                                                    echo $group['name'];
                                                    echo '</option>';
                                                }
                                                ?>
                                            
					</select><br /><br />
                                        <input type="hidden" value="<?php echo $token; ?>" name="token" />
					<input class="dmbutt" name="newrank" type="submit" value="Guardar configuración" />
                            </form>
			</td>
					
		</tr>
	</tbody>

	<tbody class="divider"><tr><td colspan="4"></td></tr></tbody>
</table>

<table class="table_list">
	<tbody class="header" id="category_1">
		<tr>
			<td colspan="4">
				<div class="cat_bar">
					<h3 class="catbg">Log</h3>
				</div>
			</td>
		</tr>
	</tbody>
	<tbody class="content" id="category_1_boards">
		<tr id="board_1" class="windowbg2">
			<td class="lastpost">
				<textarea class="dmlog"><?php echo $dm->dm_public_log('read',0,0);?></textarea>
			</td>
					
		</tr>
			</tbody>
			<tbody class="divider"><tr><td colspan="4"></td></tr></tbody>
		</table>
		<?php echo $term_message; ?>

<?php template_footer();?>
