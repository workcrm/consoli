<?php 
###########
## Módulo para Preferências do Usuário
## Criado: 19/04/2006 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
// Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";
?>

<script language="JavaScript">
function oculta(id) 
{
	ID = document.getElementById(id);
	ID.style.display = "none";
}

function wdSubmitUsuarioPreferenciaAltera() 
{
	
	var Form;
	var urlSenha = "";
	var mudaSenha = 1;
	
	Form = document.frmUsuarioPreferenciaAltera;
 
	if (Form.edtNovaSenha.value.length > 0) 
	{
		
		if (Form.edtSenhaAtual.value.length == 0) 
		{
			
			alert('É necessário informar a senha atual !');
			Form.edtSenhaAtual.focus();
			return false;
		}
		
		if (Form.edtNovaSenha.value != Form.edtNovaSenhaRepete.value) 
		{
			
			alert('As novas senhas informadas não conferem !');
		 	Form.edtNovaSenha.focus();
			return false;
		
		}
		
		urlSenha = "&edtSenhaAtual=" + Form.edtSenhaAtual.value + "&edtNovaSenha=" + Form.edtNovaSenha.value;
	
	}

	if (Form.edtNovaSenhaRepete.value.length > 0) 
	{
		
		if (Form.edtSenhaAtual.value.length == 0) 
		{
			
			alert('É necessário informar a senha atual !');
			Form.edtSenhaAtual.focus();
			return false;
		
		}	
		
		if (Form.edtNovaSenha.value != Form.edtNovaSenhaRepete.value) 
		{
			
			alert('As novas senhas informadas não conferem !');
		 	Form.edtNovaSenha.focus();		  
		 	return false;
		
		}
		
		urlSenha = "&edtSenhaAtual=" + Form.edtSenhaAtual.value + "&edtNovaSenha=" + Form.edtNovaSenha.value;
	
	}

	if (Form.edtSenhaAtual.value.length > 0) 
	{
		
		if (Form.edtNovaSenha.value.length == 0) 
		{
			
			alert('É necessário informar a nova senha !');
			Form.edtNovaSenha.focus();		  
			return false;
		
		}
		
		if (Form.edtNovaSenha.value != Form.edtNovaSenhaRepete.value) 
		{
			
			alert('As novas senhas informadas não conferem !');
		 	Form.edtNovaSenha.focus();		  
		 	return false;
		
		}
	
		urlSenha = "&edtSenhaAtual=" + Form.edtSenhaAtual.value + "&edtNovaSenha=" + Form.edtNovaSenha.value;
	}

	//Verifica se não deve-se alterar as senhas
	if (Form.edtSenhaAtual.value.length == 0 && Form.edtNovaSenha.value.length == 0 && Form.edtNovaSenhaRepete.value.length == 0) 
	{
	 	
		alert('Você não especificou uma nova senha.\nNenhuma mudança em sua senha será feita !');
		mudaSenha = 0;
	
	}
	     		
	var urlCadastro;
	urlCadastro = "UsuarioPreferencia.php?UsuarioId=" + Form.UsuarioId.value + "&FlagAlterar=1" + urlSenha + "&edtHash=" + Form.edtHash.value + "&edtMudaSenha=" + mudaSenha;

	wdCarregarFormulario(urlCadastro,'conteudo');
	return true;

}
</script>

<form name="frmUsuarioPreferenciaAltera" action="#">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
	<tr>
		<td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Preferências do Usuário</span></td>
				</tr>
				<tr>
					<td colspan="5">
						<img src="image/bt_espacohoriz.gif" width="100%" height="12">
					</td>
				</tr>
			</table>

			<table id="2" width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%" class="text">

						<?php
						
							//Verifica se a página está abrindo vindo de uma postagem
							if($_GET["FlagAlterar"])
							{						
						
								//Monta as variáveis com os valores vindos do formulário		
								$id = $_GET["UsuarioId"];
								$edtSenhaAtual = md5($_GET["edtSenhaAtual"]);
								$edtNovaSenha = md5($_GET["edtNovaSenha"]);
								$edtHash = $_GET["edtHash"];
								$edtMudaSenha = $_GET["edtMudaSenha"];						

								//Se estiver marcado para alterar a senha
								if ($edtMudaSenha == 1) 
								{
							
									//Se a senha atual for igual ao hash da senha armazenada no banco
									if ($edtSenhaAtual == $edtHash) 
									{
								
										//Monta e executa a query que atualiza a base local do usuário
										$sql = mysql_query("UPDATE usuarios SET 
															senha = '$edtNovaSenha'
															WHERE usuario_id = $id");
																	 
										//Exibe a mensagem de inclusão com sucesso
										echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Preferências configuradas com sucesso !  Atenção: Sua senha foi alterada !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500); wdCarregarFormulario('ModuloCalendario.php','calendario','1')</script>";
					    
									} 
									
									else 
									
									{
							
										//Exibe a mensagem que as senhas não conferenm
										echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>A Senha Atual informada não confere !  Atenção: Nenhuma alteração foi efetuada !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 4500)</script>";
									
									}

        
								//Caso não vai alterar a senha 
								} 
							}

							//**** RECEBENDO OS VALORES PARA A ALTERAÇAO DO REGISTRO
							//Pega o Id do usuário das diretivas
							$UsuarioId = $usuarioId;
							//Monta a SQL para buscar os dados do usuário do banco
							$sql = "SELECT * FROM usuarios WHERE usuario_id = $UsuarioId";
							//Executa a query
							$resultado = mysql_query($sql);
							//Monta o array com os dados
							$campos = mysql_fetch_array($resultado);

							//Recupera a senha atual do usuário na variável
							$senhaAtual = $campos["senha"];

							//Monta o switch com os dados do usuário		
							switch ($campos[ativo]) 
							{
					
								case 0: $desc_ativo = "Cadastro Inativo"; break;
								case 1: $desc_ativo = "Cadastro Ativo"; break;
							
							} 
							
							//Monta o switch com o nivel de acesso
							switch ($campos[nivel_acesso]) 
							{
							
								case 1: $nivel = "Somente Agenda";	break;
								case 2: $nivel = "Operacional";	break;       	
								case 3: $nivel = "Gerencial";	break;
								case 4: $nivel = "Administrador";	break;						  
		  	
							}
							
							//Monta o switch com o plano contratado		
							switch ($campos[plano_acesso]) 
							{
								case 0: $desc_plano = "Professional"; break;
								case 3: $desc_plano = "Corporate"; break;
							}
							
						?>

						<table cellspacing="0" cellpadding="0" width="520" border="0">
							<tr>
								<td style="PADDING-BOTTOM: 2px">
									<input name="UsuarioId" type="hidden" value="<?php echo $usuarioId ?>" />
									<input name="edtHash" type="hidden" value="<?php echo $senhaAtual ?>" />
									<input name="Alterar" type="button" class="button" id="Alterar" title="Salva as preferências do usuário" value="Salvar Prefer&ecirc;ncias" onclick="wdSubmitUsuarioPreferenciaAltera()" />            	
								</td>
							</tr>
						</table>
           
						<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="20">
									<table cellspacing="0" cellpadding="0" width="100%" border="0">
										<tr>
											<td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe suas prefer&ecirc;ncias para utiliza&ccedil;&atilde;o do work | eventos e clique em [Salvar Prefer&ecirc;ncias] </td>
										</tr>
									</table>             
								</td>
							</tr>
							<tr>
								<td class="dataLabel" width="18%">
									<span class="dataLabel">Login:</span>
								</td>
								<td colspan="3" class="tabDetailViewDF"><strong><?php echo $campos[login] ?></strong></td>           
							</tr>
							<tr>
								<td class="dataLabel" width="18%">
									<span class="dataLabel">Nome:</span>
								</td>
								<td width="31%" class="tabDetailViewDF"><strong><?php echo $campos[nome] ?></strong></td>
								<td width="20%" class="dataLabel">Sobrenome:</td>
								<td class="tabDetailViewDF"><strong><?php echo $campos[sobrenome] ?></strong></td>
							</tr>		   	
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<br/>
						<span class="TituloModulo">Alteração de Senha:</span>
						<br />
						<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td class="dataLabel" width="18%">
									<span class="dataLabel">Senha Atual:</span>
								</td>
								<td colspan="3" class="tabDetailViewDF">
									<input name="edtSenhaAtual" type="password" class="requerido" id="edtSenhaAtual" style="width: 170" maxlength="20">
								</td>
							</tr>		   	 
							<tr>
								<td class="dataLabel" width="18%">
									<span class="dataLabel">Nova Senha:</span>
								</td>
								<td width="31%" class="tabDetailViewDF">
									<input name="edtNovaSenha" type="password" class="requerido" id="edtNovaSenha" style="width: 170" maxlength="20">
								</td>
								<td width="20%" class="dataLabel">Repita a nova senha:</td>
								<td class="tabDetailViewDF">
									<input name="edtNovaSenhaRepete" type="password" class="requerido" id="edtNovaSenhaRepete" style="width: 170" maxlength="20">					 
								</td>
							</tr>
						</table>       
					</td>
				</tr>
			</table>  	 
</form>
</tr>
</table>
