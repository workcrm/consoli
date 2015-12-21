<?php 
###########
## Módulo para alteraçao de Usuarios
## Criado: 09/05/2007 - Maycon Edinger
## Alterado: 29/07/2007 - Maycon Edinger 
## Alterações: 
## 29/07/2007 - Inserido o nível 5 de usuário
###########

if ($_GET["headers"] == 1) 
{
	
	//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
	header("Content-Type: text/html;  charset=ISO-8859-1",true);

}

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
// Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

?>

<script language="JavaScript">

//Função que alterna a visibilidade do painel especificado.
function oculta(id)
{
	
	ID = document.getElementById(id);
	ID.style.display = "none";

}

function wdSubmitUsuarioGerenciaAltera() 
{
	
	var Form;
	Form = document.frmUsuarioGerenciaAltera;
   
	if (Form.edtLogin.value.length == 0) 
	{
		
		alert('É necessário informar um login para o usuário !');
		Form.edtLogin.focus();		  
		return false;
	
	}	
   
	if (Form.edtNome.value.length == 0) {
	
		alert("É necessário informar o nome do usuário !");
		Form.edtNome.focus();		  
		return false;
	
	}
   
	return true;
	
}
</script>

<form name="frmUsuarioGerenciaAltera" action="sistema.php?ModuloNome=UsuarioGerenciaAltera" method="post" onsubmit="return wdSubmitUsuarioGerenciaAltera()">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
	<tr>
		<td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Alteração de Usuário </span></td>
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
							if($_POST["Alterar"]) 
							{
										
								//Recupera os valores vindo do formulário e atribui as variáveis
								$id = $_POST["Id"];			
								$edtStatus = $_POST["edtStatus"];
								$edtLogin = $_POST["edtLogin"];
								$edtNome = $_POST["edtNome"];
								$edtSobrenome = $_POST["edtSobrenome"];						

								$chkMenuCompromisso = $_POST["chkMenuCompromisso"];
								$chkMenuOrcamento = $_POST["chkMenuOrcamento"];
								$chkMenuEvento = $_POST["chkMenuEvento"];
								$chkMenuLocacao = $_POST["chkMenuLocacao"];
								$chkMenuCliente = $_POST["chkMenuCliente"];
								$chkMenuFornecedor = $_POST["chkMenuFornecedor"];
								$chkMenuColaborador = $_POST["chkMenuColaborador"];
								$chkMenuFinanceiro = $_POST["chkMenuFinanceiro"];
								$chkMenuRelatorio = $_POST["chkMenuRelatorio"];
								$chkMenuConfiguracao = $_POST["chkMenuConfiguracao"];

								$chkNovoCompromisso = $_POST["chkNovoCompromisso"];
								$chkNovoEvento = $_POST["chkNovoEvento"];
								$chkNovaLocacao = $_POST["chkNovaLocacao"];
								$chkNovoCliente = $_POST["chkNovoCliente"];
								$chkNovoFornecedor = $_POST["chkNovoFornecedor"];
								$chkNovoColaborador = $_POST["chkNovoColaborador"];
								$chkNovaContaPagar = $_POST["chkNovaContaPagar"];
								$chkNovaContaReceber = $_POST["chkNovaContaReceber"];
								$chkNovoChequeRecebido = $_POST["chkNovoChequeRecebido"];
								$chkNovoRecado = $_POST["chkNovoRecado"];
								$chkNovoLancamentoCaixa = $_POST["chkNovoLancamentoCaixa"];
								$chkNovoVale = $_POST["chkNovoVale"];							
														
								$chkCadCentroCustoConta = $_POST["chkCadCentroCustoConta"];
								$chkCadContaCaixa = $_POST["chkCadContaCaixa"];
								$chkCadBanco = $_POST["chkCadBanco"];
								$chkCadContaCorrente = $_POST["chkCadContaCorrente"];
								$chkCadCentroCustoProduto = $_POST["chkCadCentroCustoProduto"];
								$chkCadCentroCustoServico = $_POST["chkCadCentroCustoServico"];
								$chkCadProduto = $_POST["chkCadProduto"];
								$chkCadProdutoFotoVideo = $_POST["chkCadProdutoFotoVideo"];
								$chkCadMaterial = $_POST["chkCadMaterial"];
								$chkCadServico = $_POST["chkCadServico"];
								$chkCadBrinde = $_POST["chkCadBrinde"];
								$chkCadRepertorio = $_POST["chkCadRepertorio"];
								$chkCadTipoLocal = $_POST["chkCadTipoLocal"];
								$chkCadMusica = $_POST["chkCadMusica"];
								$chkCadFuncao = $_POST["chkCadFuncao"];
								$chkCadCidade = $_POST["chkCadCidade"];	
								$chkCadCurso = $_POST["chkCadCurso"];
								$chkCadTipoDoc = $_POST["chkCadTipoDoc"];

								$chkEventoAltera = $_POST["chkEventoAltera"];	
								$chkEventoExclui = $_POST["chkEventoExclui"];
								$chkEventoRelatorio = $_POST["chkEventoRelatorio"];
								$chkEventoFinanceiro = $_POST["chkEventoFinanceiro"];
								$chkEventoFotovideo = $_POST["chkEventoFotovideo"];

								$chkEventoDataExibe = $_POST["chkEventoDataExibe"];
								$chkEventoDataInclui = $_POST["chkEventoDataInclui"];
								$chkEventoDataAltera = $_POST["chkEventoDataAltera"];
								$chkEventoDataExclui = $_POST["chkEventoDataExclui"];

								$chkEventoParticipanteExibe = $_POST["chkEventoParticipanteExibe"];
								$chkEventoParticipanteInclui = $_POST["chkEventoParticipanteInclui"];
								$chkEventoParticipanteAltera = $_POST["chkEventoParticipanteAltera"];
								$chkEventoParticipanteExclui = $_POST["chkEventoParticipanteExclui"];

								$chkEventoEnderecoExibe = $_POST["chkEventoEnderecoExibe"];
								$chkEventoEnderecoInclui = $_POST["chkEventoEnderecoInclui"];
								$chkEventoEnderecoAltera = $_POST["chkEventoEnderecoAltera"];
								$chkEventoEnderecoExclui = $_POST["chkEventoEnderecoExclui"];

								$chkEventoProdutoExibe = $_POST["chkEventoProdutoExibe"];
								$chkEventoProdutoInclui = $_POST["chkEventoProdutoInclui"];
								$chkEventoProdutoAltera = $_POST["chkEventoProdutoAltera"];
								$chkEventoProdutoExclui = $_POST["chkEventoProdutoExclui"];

								$chkEventoServicoExibe = $_POST["chkEventoServicoExibe"];
								$chkEventoServicoInclui = $_POST["chkEventoServicoInclui"];
								$chkEventoServicoAltera = $_POST["chkEventoServicoAltera"];
								$chkEventoServicoExclui = $_POST["chkEventoServicoExclui"];

								$chkEventoTerceiroExibe = $_POST["chkEventoTerceiroExibe"];
								$chkEventoTerceiroInclui = $_POST["chkEventoTerceiroInclui"];
								$chkEventoTerceiroAltera = $_POST["chkEventoTerceiroAltera"];
								$chkEventoTerceiroExclui = $_POST["chkEventoTerceiroExclui"];

								$chkEventoTerceiroExibe = $_POST["chkEventoTerceiroExibe"];
								$chkEventoTerceiroInclui = $_POST["chkEventoTerceiroInclui"];
								$chkEventoTerceiroAltera = $_POST["chkEventoTerceiroAltera"];
								$chkEventoTerceiroExclui = $_POST["chkEventoTerceiroExclui"];

								$chkEventoBrindeExibe = $_POST["chkEventoBrindeExibe"];
								$chkEventoBrindeInclui = $_POST["chkEventoBrindeInclui"];
								$chkEventoBrindeAltera = $_POST["chkEventoBrindeAltera"];
								$chkEventoBrindeExclui = $_POST["chkEventoBrindeExclui"];

								$chkEventoRepertorioExibe = $_POST["chkEventoRepertorioExibe"];
								$chkEventoRepertorioInclui = $_POST["chkEventoRepertorioInclui"];
								$chkEventoRepertorioAltera = $_POST["chkEventoRepertorioAltera"];
								$chkEventoRepertorioExclui = $_POST["chkEventoRepertorioExclui"];

								$chkEventoFormandoExibe = $_POST["chkEventoFormandoExibe"];
								$chkEventoFormandoInclui = $_POST["chkEventoFormandoInclui"];
								$chkEventoFormandoAltera = $_POST["chkEventoFormandoAltera"];
								$chkEventoFormandoExclui = $_POST["chkEventoFormandoExclui"];

								$chkEventoFotovideoExibe = $_POST["chkEventoFotovideoExibe"];
								$chkEventoFotovideoInclui = $_POST["chkEventoFotovideoInclui"];
								$chkEventoFotovideoAltera = $_POST["chkEventoFotovideoAltera"];
								$chkEventoFotovideoExclui = $_POST["chkEventoFotovideoExclui"];

								$chkEventoDocumentoExibe = $_POST["chkEventoDocumentoExibe"];
								$chkEventoDocumentoInclui = $_POST["chkEventoDocumentoInclui"];
								$chkEventoDocumentoAltera = $_POST["chkEventoDocumentoAltera"];
								$chkEventoDocumentoExclui = $_POST["chkEventoDocumentoExclui"];

								$chkAtivaGerUsuario = $_POST["chkAtivaGerUsuario"];
								$chkAtivaPesquisa = $_POST["chkAtivaPesquisa"];
								$chkAtivaPreferencias = $_POST["chkAtivaPreferencias"];

								$chkRelFinanceiro = $_POST["chkRelFinanceiro"];
								$chkRelCadastros = $_POST["chkRelCadastros"];
								$chkRelEventos = $_POST["chkRelEventos"];
								$chkRelRh = $_POST["chkRelRh"];						

								//Monta e executa a query
								$sql = mysql_query("UPDATE usuarios SET 
													login = '$edtLogin',
													nome = '$edtNome',
													sobrenome = '$edtSobrenome',
													ativo = '$edtStatus',

													menu_compromisso = '$chkMenuCompromisso',
													menu_orcamento = '$chkMenuOrcamento',
													menu_evento = '$chkMenuEvento',
													menu_locacao = '$chkMenuLocacao',
													menu_cliente = '$chkMenuCliente',
													menu_fornecedor = '$chkMenuFornecedor',
													menu_colaborador = '$chkMenuColaborador',
													menu_financeiro = '$chkMenuFinanceiro',
													menu_relatorio = '$chkMenuRelatorio',
													menu_configuracao = '$chkMenuConfiguracao',

													novo_compromisso = '$chkNovoCompromisso',
													novo_evento = '$chkNovoEvento',
													nova_locacao = '$chkNovaLocacao',
													novo_cliente = '$chkNovoCliente',
													novo_fornecedor = '$chkNovoFornecedor',
													novo_colaborador = '$chkNovoColaborador',
													nova_conta_pagar = '$chkNovaContaPagar',
													nova_conta_receber = '$chkNovaContaReceber',
													novo_cheque_recebido = '$chkNovoChequeRecebido',
													novo_recado = '$chkNovoRecado',
													novo_lancamento_caixa = '$chkNovoLancamentoCaixa',
													novo_vale = '$chkNovoVale',

													cad_centro_custo_conta = '$chkCadCentroCustoConta',
													cad_conta_caixa = '$chkCadContaCaixa',
													cad_banco = '$chkCadBanco',
													cad_conta_corrente = '$chkCadContaCorrente',
													cad_centro_custo_produto = '$chkCadCentroCustoProduto',
													cad_centro_custo_servico = '$chkCadCentroCustoServico',
													cad_produto = '$chkCadProduto',
													cad_produto_fotovideo = '$chkCadProdutoFotoVideo',
													cad_material = '$chkCadMaterial',
													cad_servico = '$chkCadServico',
													cad_brinde = '$chkCadBrinde',
													cad_repertorio = '$chkCadRepertorio',
													cad_tipo_local = '$chkCadTipoLocal',
													cad_musica = '$chkCadMusica',
													cad_funcao = '$chkCadFuncao',
													cad_cidade = '$chkCadCidade',
													cad_cursos = '$chkCadCurso',
													cad_tipo_doc = '$chkCadTipoDoc',

													evento_altera = '$chkEventoAltera',
													evento_exclui = '$chkEventoExclui',
													evento_relatorio = '$chkEventoRelatorio',
													evento_fotovideo = '$chkEventoFotovideo',
													evento_financeiro = '$chkEventoFinanceiro',

													evento_data_exibe = '$chkEventoDataExibe',
													evento_data_inclui = '$chkEventoDataInclui',
													evento_data_altera = '$chkEventoDataAltera',
													evento_data_exclui = '$chkEventoDataExclui',

													evento_participante_exibe = '$chkEventoParticipanteExibe',
													evento_participante_inclui = '$chkEventoParticipanteInclui',
													evento_participante_altera = '$chkEventoParticipanteAltera',
													evento_participante_exclui = '$chkEventoParticipanteExclui',

													evento_endereco_exibe = '$chkEventoEnderecoExibe',
													evento_endereco_inclui = '$chkEventoEnderecoInclui',
													evento_endereco_altera = '$chkEventoEnderecoAltera',
													evento_endereco_exclui = '$chkEventoEnderecoExclui',

													evento_produto_exibe = '$chkEventoProdutoExibe',
													evento_produto_inclui = '$chkEventoProdutoInclui',
													evento_produto_altera = '$chkEventoProdutoAltera',
													evento_produto_exclui = '$chkEventoProdutoExclui',

													evento_servico_exibe = '$chkEventoServicoExibe',
													evento_servico_inclui = '$chkEventoServicoInclui',
													evento_servico_altera = '$chkEventoServicoAltera',
													evento_servico_exclui = '$chkEventoServicoExclui',

													evento_terceiro_exibe = '$chkEventoTerceiroExibe',
													evento_terceiro_inclui = '$chkEventoTerceiroInclui',
													evento_terceiro_altera = '$chkEventoTerceiroAltera',
													evento_terceiro_exclui = '$chkEventoTerceiroExclui',

													evento_brinde_exibe = '$chkEventoBrindeExibe',
													evento_brinde_inclui = '$chkEventoBrindeInclui',
													evento_brinde_altera = '$chkEventoBrindeAltera',
													evento_brinde_exclui = '$chkEventoBrindeExclui',

													evento_repertorio_exibe = '$chkEventoRepertorioExibe',
													evento_repertorio_inclui = '$chkEventoRepertorioInclui',
													evento_repertorio_altera = '$chkEventoRepertorioAltera',
													evento_repertorio_exclui = '$chkEventoRepertorioExclui',

													evento_formando_exibe = '$chkEventoFormandoExibe',
													evento_formando_inclui = '$chkEventoFormandoInclui',
													evento_formando_altera = '$chkEventoFormandoAltera',
													evento_formando_exclui = '$chkEventoFormandoExclui',

													evento_fotovideo_exibe = '$chkEventoFotovideoExibe',
													evento_fotovideo_inclui = '$chkEventoFotovideoInclui',
													evento_fotovideo_altera = '$chkEventoFotovideoAltera',
													evento_fotovideo_exclui = '$chkEventoFotovideoExclui',

													evento_documento_exibe = '$chkEventoDocumentoExibe',
													evento_documento_inclui = '$chkEventoFotovideoInclui',
													evento_documento_altera = '$chkEventoFotovideoAltera',
													evento_documento_exclui = '$chkEventoFotovideoExclui',

													ativa_ger_usuario = '$chkAtivaGerUsuario',
													ativa_pesquisa = '$chkAtivaPesquisa',
													ativa_preferencias = '$chkAtivaPreferencias',

													relatorio_financeiro = '$chkRelFinanceiro',
													relatorio_cadastros = '$chkRelCadastros',
													relatorio_eventos = '$chkRelEventos',
													relatorio_rh = '$chkRelRh'
													WHERE usuario_id = $id");			 
							
					//Exibe a mensagem de inclusão com sucesso
        			echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Usuário alterado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
				}

				//Recebe os valores passados do form anterior para edição do registro
				if($_POST) 
				{
					
					$UsuarioId = $_POST["Id"]; 
				
				} 
				
				else 
				
				{
		  
					$UsuarioId = $_GET["Id"]; 
		
				}

				//Monta o sql
				$sql = "SELECT * FROM usuarios WHERE usuario_id = $UsuarioId";

				//Executa a query
				$resultado = mysql_query($sql);

				//Monta o array dos dados
				$campos = mysql_fetch_array($resultado);

				//Efetua o switch para o campo de ativo
				switch ($campos["ativo"]) 
				{
					
					case 1: 
						$status_ativo = "checked";
						$status_inativo = ""; 		  							 							
					break;

					case 0: 
						$status_ativo = "";
						$status_inativo = "checked"; 		  							 							
					break;
				
				}
		
				//Efetua o switch para o campo de nivel de acesso
				switch ($campos[nivel_acesso]) 
				{
					
					case 1: 
						$nivel_1 = "checked";
						$nivel_2 = ""; 		  
						$nivel_3 = "";
						$nivel_4 = "";
						$nivel_5 = "";							
					break;

					case 2: 
						$nivel_1 = "";	
						$nivel_2 = "checked";
						$nivel_3 = "";
						$nivel_4 = "";
						$nivel_5 = "";
					break;							

					case 3: 
						$nivel_1 = "";	
						$nivel_2 = ""; 		  
						$nivel_3 = "checked";
						$nivel_4 = "";
						$nivel_5 = "";
					break;							

					case 4: 
						$nivel_1 = "";	
						$nivel_2 = ""; 		  
						$nivel_3 = "";
						$nivel_4 = "checked";
						$nivel_5 = "";
					break;														

					case 5: 
						$nivel_1 = "";	
						$nivel_2 = ""; 		  
						$nivel_3 = "";
						$nivel_4 = "";
						$nivel_5 = "checked";
					break;														
				
				}

			?>

			<table cellspacing="0" cellpadding="0" width="100%" border="0">
				<tr>
					<td width="484"></td>
				</tr>
				<tr>
					<td style="PADDING-BOTTOM: 2px">
						<input name="Id" type="hidden" value="<?php echo $UsuarioId ?>" />
						<input name="Alterar" type="submit" class="button" id="Alterar" title="Salva as alterações do usuário" value="Salvar Alterações do Usuário" />
					</td>
					<td align="right">
						<input class="button" title="Retorna ao formulário de cadastro" name="btnVoltar" type="button" id="btnVoltar" value="Voltar" style="width:70px" onclick="window.location='sistema.php?ModuloNome=UsuarioGerencia';" />						 
					</td>
				</tr>
			</table>
           
			<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
				<tr>
					<td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="20">
						<table cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados do usuário e clique em [Salvar Usuário] </td>
							</tr>
						</table>             
					</td>
				</tr>
				<tr>
					<td class="dataLabel">Login</td>
					<td colspan="3" class="tabDetailViewDF">
						<input name="edtLogin" type="text" class="requerido" id="edtLogin" style="width: 170" maxlength="20" value="<?php echo $campos[login] ?>"/>
					</td>
				</tr>	
				<tr>
					<td class="dataLabel" width="18%">
						<span class="dataLabel">Nome:</span>           
					</td>
					<td width="31%" class="tabDetailViewDF">
						<input name="edtNome" type="text" class="requerido" id="edtNome" style="width: 170" maxlength="20" value="<?php echo $campos[nome] ?>"/>
					</td>
					<td width="20%" class="dataLabel">Sobrenome:</td>
					<td class="tabDetailViewDF">
						<input name="edtSobrenome" type="text" class="datafield" id="edtSobrenome" style="width: 170" maxlength="20" value="<?php echo $campos[sobrenome] ?>"/>
					</td>
				</tr>
				<tr>
					<td class="dataLabel" width="18%">
						<span class="dataLabel">Status do Usuário:</span>           
					 </td>
					<td colspan="3" class="tabDetailViewDF">
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr valign="middle">
								<td width="26" height="20">
									<input name="edtStatus" type="radio" value="1" <?php echo $status_ativo ?>/>
								</td>
								<td width="120"><b>Ativo</b></td>
								<td width="26" height="20">
									<input name="edtStatus" type="radio" value="0" <?php echo $status_inativo ?>/>
								</td>
								<td><b>Inativo</b>&nbsp;&nbsp;(Não possui mais acesso ao Work Eventos)</td>
							</tr>
						</table>
					</td>
				</tr>		   
			</table>
     	</td>
   	 </tr>
   <tr>
    <td class="text" valign="top">
    	<br />
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Permissões de Acesso</span></td>
			  </tr>
			  <tr>
			    <td colspan="5">
				    <img src="image/bt_espacohoriz.gif" width="100%" height="12">
				</td>
			  </tr>
			</table>
		</td>
	 </tr>
   <tr>
   	 <td>   
   		 <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
         <tr>
           <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px">
             <table cellspacing="0" cellpadding="0" width="100%" border="0">
               <tr>
                 <td class="tabDetailViewDL" style="TEXT-ALIGN: left"><span style="color: #990000"><b>MENU SUPERIOR</b></span></td>
			   		   </tr>
		       	 </table>             
					 </td>
	       </tr>
		   	 <tr>
		   	   <td class="tabDetailViewDF"> 
		   	   	 <table cellspacing="0" cellpadding="0" width="100%" border="0">
		   	   	 	<tr height="28">
		   	   	 		<td width="110">
						 			<input name="chkMenuCompromisso" type="checkbox" id="chkMenuCompromisso" value="1" <?php if($campos[menu_compromisso] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Compromissos</b></span>
					 			</td>
					 			<td width="110">
						 			<input name="chkMenuOrcamento" type="checkbox" id="chkMenuOrcamento" value="1" <?php if($campos[menu_orcamento] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Orçamentos</b></span>
					 			</td>
					 			<td width="110">
						 			<input name="chkMenuEvento" type="checkbox" id="chkMenuEvento" value="1" <?php if($campos[menu_evento] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Eventos</b></span>
					 			</td>
					 			<td width="110">
						 			<input name="chkMenuLocacao" type="checkbox" id="chkMenuLocacao" value="1" <?php if($campos[menu_locacao] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Locações</b></span>
					 			</td>
					 			<td width="110">
						 			<input name="chkMenuCliente" type="checkbox" id="chkMenuCliente" value="1" <?php if($campos[menu_cliente] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Clientes</b></span>
					 			</td>
					 			<td width="110">
						 			<input name="chkMenuFornecedor" type="checkbox" id="chkMenuFornecedor" value="1" <?php if($campos[menu_fornecedor] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Fornecedores</b></span>
					 			</td>
			 				</tr>
			 				<tr>
			 					<td>
						 			<input name="chkMenuColaborador" type="checkbox" id="chkMenuColaborador" value="1" <?php if($campos[menu_colaborador] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Colaboradores</b></span>
					 			</td>
					 			<td>
						 			<input name="chkMenuFinanceiro" type="checkbox" id="chkMenuFinanceiro" value="1" <?php if($campos[menu_financeiro] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Financeiro</b></span>
					 			</td>
								<td>													 			
						 			<input name="chkMenuRelatorio" type="checkbox" id="chkMenuRelatorio" value="1" <?php if($campos[menu_relatorio] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Relatórios</b></span>
				 				</td>
				 				<td colspan="3">
						 			<input name="chkMenuConfiguracao" type="checkbox" id="chkMenuConfiguracao" value="1" <?php if($campos[menu_configuracao] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Configurações</b></span>
								</td>
							</tr>
						</table>					 
					 </td>		   	     	    
   	   	 </tr>		   	 	   
	   	 </table>
	   	 <br />
     </td>
   </tr>   	
   <tr>
   	 <td>   
   		 <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
         <tr>
           <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px">
             <table cellspacing="0" cellpadding="0" width="100%" border="0">
               <tr>
                 <td class="tabDetailViewDL" style="TEXT-ALIGN: left"><span style="color: #990000"><b>FAVORITOS</b></span></td>
			   		   </tr>
		       	 </table>             
					 </td>
	       </tr>
		   	 <tr>
		   	   <td class="tabDetailViewDF"> 
		   	   	 <table cellspacing="0" cellpadding="0" width="100%" border="0">
		   	   	 	<tr height="28">
		   	   	 		<td width="150">
						 			<input name="chkNovoCompromisso" type="checkbox" id="chkNovoCompromisso" value="1" <?php if($campos[novo_compromisso] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Novo Compromisso</b></span>
					 			</td>
					 			<td width="110">
						 			<input name="chkNovoEvento" type="checkbox" id="chkNovoEvento" value="1" <?php if($campos[novo_evento] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Novo Evento</b></span>
					 			</td>
					 			<td width="110">
						 			<input name="chkNovaLocacao" type="checkbox" id="chkNovaLocacao" value="1" <?php if($campos[nova_locacao] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Nova Locação</b></span>
					 			</td>
					 			<td width="110">
						 			<input name="chkNovoCliente" type="checkbox" id="chkNovoCliente" value="1" <?php if($campos[novo_cliente] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Novo Cliente</b></span>
					 			</td>
					 			<td>
						 			<input name="chkNovoFornecedor" type="checkbox" id="chkNovoFornecedor" value="1" <?php if($campos[novo_fornecedor] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Novo Fornecedor</b></span>
					 			</td>
					 		</tr>
			 				<tr>
								<td>
						 			<input name="chkNovoColaborador" type="checkbox" id="chkNovoColaborador" value="1" <?php if($campos[novo_colaborador] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Novo Colaborador</b></span>
					 			</td>			 				
			 					<td>
						 			<input name="chkNovaContaPagar" type="checkbox" id="chkNovaContaPagar" value="1" <?php if($campos[nova_conta_pagar] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Nova Conta a Pagar</b></span>
					 			</td>
					 			<td>
						 			<input name="chkNovaContaReceber" type="checkbox" id="chkNovaContaReceber" value="1" <?php if($campos[nova_conta_receber] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Nova Conta a Receber</b></span>
					 			</td>
								<td>													 			
						 			<input name="chkNovoChequeRecebido" type="checkbox" id="chkNovoChequeRecebido" value="1" <?php if($campos[novo_cheque_recebido] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Novo Cheque Recebido</b></span>
				 				</td>
				 				<td>
						 			<input name="chkNovoLancamentoCaixa" type="checkbox" id="chkNovoLancamentoCaixa" value="1" <?php if($campos[novo_lancamento_caixa] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Novo Lançamento Caixa</b></span>
								</td>
							</tr>
              <tr height="28">
                <td>
						 			<input name="chkNovoVale" type="checkbox" id="chkNovoVale" value="1" <?php if($campos[novo_vale] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Novo Vale</b></span>
					 			</td>
		   	   	 		<td colspan="4">
						 			<input name="chkNovoRecado" type="checkbox" id="chkNovoRecado" value="1" <?php if($campos[novo_recado] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Novo Recado</b></span>
					 			</td>
              </tr>
						</table>						 
					 </td>		   	     	    
   	   	 </tr>		   	 	   
	   	 </table>
	   	 <br />
     </td>
   </tr> 
	 
	 
   <tr>
   	 <td>   
   		 <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
         <tr>
           <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px">
             <table cellspacing="0" cellpadding="0" width="100%" border="0">
               <tr>
                 <td class="tabDetailViewDL" style="TEXT-ALIGN: left"><span style="color: #990000"><b>MÓDULO DE EVENTOS</b></span></td>
			   		   </tr>
		       	 </table>             
					 </td>
	       </tr>
		   	 <tr>
		   	   <td class="tabDetailViewDF"> 
		   	   	 <table cellspacing="0" cellpadding="0" width="100%" border="0">
		   	   	 	<tr height="28">
		   	   	 		<td width="150">
						 			<input name="chkEventoAltera" type="checkbox" id="chkEventoAltera" value="1" <?php if($campos[evento_altera] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Alterar Evento</b></span>
					 			</td>
					 			<td width="130">
						 			<input name="chkEventoExclui" type="checkbox" id="chkEventoExclui" value="1" <?php if($campos[evento_exclui] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Excluir Evento</b></span>
					 			</td>
					 			<td width="130">
						 			<input name="chkEventoRelatorio" type="checkbox" id="chkEventoRelatorio" value="1" <?php if($campos[evento_relatorio] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Imprimir Evento</b></span>
					 			</td>
					 			<td width="130">
						 			<input name="chkEventoFinanceiro" type="checkbox" id="chkEventoFinanceiro" value="1" <?php if($campos[evento_financeiro] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Inform. Financeiras</b></span>
					 			</td>
					 			<td>
						 			<input name="chkEventoFotovideo" type="checkbox" id="chkEventoFotovideo" value="1" <?php if($campos[evento_fotovideo] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Inform. Foto e Vídeo</b></span>
					 			</td>
					 		</tr>
			 				<tr>
			 					<td colspan="5" style="padding-top: 8px; padding-bottom: 2px">
								  <span style="font-size: 12px; color: #000000"><b>Datas do Evento</b></span>					   	 			 						
			 					</td>
			 				</tr>
							<tr>
								<td>
						 			<input name="chkEventoDataExibe" type="checkbox" id="chkEventoDataExibe" value="1" <?php if($campos[evento_data_exibe] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Exibir Datas</b></span>
					 			</td>			 				
			 					<td>
						 			<input name="chkEventoDataInclui" type="checkbox" id="chkEventoDataInclui" value="1" <?php if($campos[evento_data_inclui] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Incluir Datas</b></span>
					 			</td>
					 			<td>
						 			<input name="chkEventoDataAltera" type="checkbox" id="chkEventoDataAltera" value="1" <?php if($campos[evento_data_altera] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Alterar Datas</b></span>
					 			</td>
								<td>													 			
						 			<input name="chkEventoDataExclui" type="checkbox" id="chkEventoDataExclui" value="1" <?php if($campos[evento_data_exclui] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Excluir Datas</b></span>
				 				</td>
				 				<td>
						 			&nbsp;
								</td>
							</tr>
							<tr>
			 					<td colspan="5" style="padding-top: 6px; padding-bottom: 2px">
								 <span style="font-size: 12px; color: #000000"><b>Participantes do Evento</b></span>					   	 			 						
			 					</td>
			 				</tr>
							<tr>
								<td>
						 			<input name="chkEventoParticipanteExibe" type="checkbox" id="chkEventoParticipanteExibe" value="1" <?php if($campos[evento_participante_exibe] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Exibir Participantes</b></span>
					 			</td>			 				
			 					<td>
						 			<input name="chkEventoParticipanteInclui" type="checkbox" id="chkEventoParticipanteInclui" value="1" <?php if($campos[evento_participante_inclui] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Incluir Participantes</b></span>
					 			</td>
					 			<td>
						 			<input name="chkEventoParticipanteAltera" type="checkbox" id="chkEventoParticipanteAltera" value="1" <?php if($campos[evento_participante_altera] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Alterar Participantes</b></span>
					 			</td>
								<td>													 			
						 			<input name="chkEventoParticipanteExclui" type="checkbox" id="chkEventoParticipanteExclui" value="1" <?php if($campos[evento_participante_exclui] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Excluir Participantes</b></span>
				 				</td>
				 				<td>
						 			&nbsp;
								</td>
							</tr>
							<tr>
			 					<td colspan="5" style="padding-top: 6px; padding-bottom: 2px">
								 <span style="font-size: 12px; color: #000000"><b>Endereços do Evento</b></span>					   	 			 						
			 					</td>
			 				</tr>
							<tr>
								<td>
						 			<input name="chkEventoEnderecoExibe" type="checkbox" id="chkEventoEnderecoExibe" value="1" <?php if($campos[evento_endereco_exibe] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Exibir Endereços</b></span>
					 			</td>			 				
			 					<td>
						 			<input name="chkEventoEnderecoInclui" type="checkbox" id="chkEventoEnderecoInclui" value="1" <?php if($campos[evento_endereco_inclui] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Incluir Endereços</b></span>
					 			</td>
					 			<td>
						 			<input name="chkEventoEnderecoAltera" type="checkbox" id="chkEventoEnderecoAltera" value="1" <?php if($campos[evento_endereco_altera] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Alterar Endereços</b></span>
					 			</td>
								<td>													 			
						 			<input name="chkEventoEnderecoExclui" type="checkbox" id="chkEventoEnderecoExclui" value="1" <?php if($campos[evento_endereco_exclui] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Excluir Endereços</b></span>
				 				</td>
				 				<td>
						 			&nbsp;
								</td>
							</tr>
							<tr>
			 					<td colspan="5" style="padding-top: 6px; padding-bottom: 2px">
								 <span style="font-size: 12px; color: #000000"><b>Produtos do Evento</b></span>					   	 			 						
			 					</td>
			 				</tr>
							<tr>
								<td>
						 			<input name="chkEventoProdutoExibe" type="checkbox" id="chkEventoProdutoExibe" value="1" <?php if($campos[evento_produto_exibe] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Exibir Produtos</b></span>
					 			</td>			 				
			 					<td>
						 			<input name="chkEventoProdutoInclui" type="checkbox" id="chkEventoProdutoInclui" value="1" <?php if($campos[evento_produto_inclui] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Incluir Produtos</b></span>
					 			</td>
					 			<td>
						 			<input name="chkEventoProdutoAltera" type="checkbox" id="chkEventoProdutoAltera" value="1" <?php if($campos[evento_produto_altera] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Alterar Produtos</b></span>
					 			</td>
								<td>													 			
						 			<input name="chkEventoProdutoExclui" type="checkbox" id="chkEventoProdutoExclui" value="1" <?php if($campos[evento_produto_exclui] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Excluir Produtos</b></span>
				 				</td>
				 				<td>
						 			&nbsp;
								</td>
							</tr>
							<tr>
			 					<td colspan="5" style="padding-top: 6px; padding-bottom: 2px">
								 <span style="font-size: 12px; color: #000000"><b>Serviços do Evento</b></span>					   	 			 						
			 					</td>
			 				</tr>
							<tr>
								<td>
						 			<input name="chkEventoServicoExibe" type="checkbox" id="chkEventoServicoExibe" value="1" <?php if($campos[evento_servico_exibe] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Exibir Serviços</b></span>
					 			</td>			 				
			 					<td>
						 			<input name="chkEventoServicoInclui" type="checkbox" id="chkEventoServicoInclui" value="1" <?php if($campos[evento_servico_inclui] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Incluir Serviços</b></span>
					 			</td>
					 			<td>
						 			<input name="chkEventoServicoAltera" type="checkbox" id="chkEventoServicoAltera" value="1" <?php if($campos[evento_servico_altera] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Alterar Serviços</b></span>
					 			</td>
								<td>													 			
						 			<input name="chkEventoServicoExclui" type="checkbox" id="chkEventoServicoExclui" value="1" <?php if($campos[evento_servico_exclui] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Excluir Serviços</b></span>
				 				</td>
				 				<td>
						 			&nbsp;
								</td>
							</tr>
							<tr>
			 					<td colspan="5" style="padding-top: 6px; padding-bottom: 2px">
								 <span style="font-size: 12px; color: #000000"><b>Terceiros do Evento</b></span>					   	 			 						
			 					</td>
			 				</tr>
							<tr>
								<td>
						 			<input name="chkEventoTerceiroExibe" type="checkbox" id="chkEventoTerceiroExibe" value="1" <?php if($campos[evento_terceiro_exibe] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Exibir Terceiros</b></span>
					 			</td>			 				
			 					<td>
						 			<input name="chkEventoTerceiroInclui" type="checkbox" id="chkEventoTerceiroInclui" value="1" <?php if($campos[evento_terceiro_inclui] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Incluir Terceiros</b></span>
					 			</td>
					 			<td>
						 			<input name="chkEventoTerceiroAltera" type="checkbox" id="chkEventoTerceiroAltera" value="1" <?php if($campos[evento_terceiro_altera] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Alterar Terceiros</b></span>
					 			</td>
								<td>													 			
						 			<input name="chkEventoTerceiroExclui" type="checkbox" id="chkEventoTerceiroExclui" value="1" <?php if($campos[evento_terceiro_exclui] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Excluir Terceiros</b></span>
				 				</td>
				 				<td>
						 			&nbsp;
								</td>
							</tr>
							<tr>
			 					<td colspan="5" style="padding-top: 6px; padding-bottom: 2px">
								 <span style="font-size: 12px; color: #000000"><b>Brindes do Evento</b></span>					   	 			 						
			 					</td>
			 				</tr>
							<tr>
								<td>
						 			<input name="chkEventoBrindeExibe" type="checkbox" id="chkEventoBrindeExibe" value="1" <?php if($campos[evento_brinde_exibe] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Exibir Brindes</b></span>
					 			</td>			 				
			 					<td>
						 			<input name="chkEventoBrindeInclui" type="checkbox" id="chkEventoBrindeInclui" value="1" <?php if($campos[evento_brinde_inclui] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Incluir Brindes</b></span>
					 			</td>
					 			<td>
						 			<input name="chkEventoBrindeAltera" type="checkbox" id="chkEventoBrindeAltera" value="1" <?php if($campos[evento_brinde_altera] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Alterar Brindes</b></span>
					 			</td>
								<td>													 			
						 			<input name="chkEventoBrindeExclui" type="checkbox" id="chkEventoBrindeExclui" value="1" <?php if($campos[evento_brinde_exclui] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Excluir Brindes</b></span>
				 				</td>
				 				<td>
						 			&nbsp;
								</td>
							</tr>
							<tr>
			 					<td colspan="5" style="padding-top: 6px; padding-bottom: 2px">
								 <span style="font-size: 12px; color: #000000"><b>Repertório do Evento</b></span>					   	 			 						
			 					</td>
			 				</tr>
							<tr>
								<td>
						 			<input name="chkEventoRepertorioExibe" type="checkbox" id="chkEventoRepertorioExibe" value="1" <?php if($campos[evento_repertorio_exibe] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Exibir Repertórios</b></span>
					 			</td>			 				
			 					<td>
						 			<input name="chkEventoRepertorioInclui" type="checkbox" id="chkEventoRepertorioInclui" value="1" <?php if($campos[evento_repertorio_inclui] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Incluir Repertórios</b></span>
					 			</td>
					 			<td>
						 			<input name="chkEventoRepertorioAltera" type="checkbox" id="chkEventoRepertorioAltera" value="1" <?php if($campos[evento_repertorio_altera] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Alterar Repertórios</b></span>
					 			</td>
								<td>													 			
						 			<input name="chkEventoRepertorioExclui" type="checkbox" id="chkEventoRepertorioExclui" value="1" <?php if($campos[evento_repertorio_exclui] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Excluir Repertórios</b></span>
				 				</td>
				 				<td>
						 			&nbsp;
								</td>
							</tr>	
							<tr>
			 					<td colspan="5" style="padding-top: 6px; padding-bottom: 2px">
								 <span style="font-size: 12px; color: #000000"><b>Formandos do Evento</b></span>					   	 			 						
			 					</td>
			 				</tr>
							<tr>
								<td>
						 			<input name="chkEventoFormandoExibe" type="checkbox" id="chkEventoFormandoExibe" value="1" <?php if($campos[evento_formando_exibe] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Exibir Formandos</b></span>
					 			</td>			 				
			 					<td>
						 			<input name="chkEventoFormandoInclui" type="checkbox" id="chkEventoFormandoInclui" value="1" <?php if($campos[evento_formando_inclui] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Incluir Formandos</b></span>
					 			</td>
					 			<td>
						 			<input name="chkEventoFormandoAltera" type="checkbox" id="chkEventoFormandoAltera" value="1" <?php if($campos[evento_formando_altera] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Alterar Formandos</b></span>
					 			</td>
								<td>													 			
						 			<input name="chkEventoFormandoExclui" type="checkbox" id="chkEventoFormandoExclui" value="1" <?php if($campos[evento_formando_exclui] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Excluir Formandos</b></span>
				 				</td>
				 				<td>
						 			&nbsp;
								</td>
							</tr>
							<tr>
			 					<td colspan="5" style="padding-top: 6px; padding-bottom: 2px">
								 <span style="font-size: 12px; color: #000000"><b>Foto e Vídeo do Evento</b></span>					   	 			 						
			 					</td>
			 				</tr>
							<tr>
								<td>
						 			<input name="chkEventoFotovideoExibe" type="checkbox" id="chkEventoFotovideoExibe" value="1" <?php if($campos[evento_fotovideo_exibe] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Exibir Foto e Vídeo</b></span>
					 			</td>			 				
			 					<td>
						 			<input name="chkEventoFotovideoInclui" type="checkbox" id="chkEventoFotovideoInclui" value="1" <?php if($campos[evento_fotovideo_inclui] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Incluir Foto e Vídeo</b></span>
					 			</td>
					 			<td>
						 			<input name="chkEventoFotovideoAltera" type="checkbox" id="chkEventoFotovideoAltera" value="1" <?php if($campos[evento_fotovideo_altera] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Alterar Foto e Vídeo</b></span>
					 			</td>
								<td>													 			
						 			<input name="chkEventoFotovideoExclui" type="checkbox" id="chkEventoFotovideoExclui" value="1" <?php if($campos[evento_fotovideo_exclui] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Excluir Foto e Vídeo</b></span>
				 				</td>
				 				<td>
						 			&nbsp;
								</td>
							</tr>							
							<tr>
			 					<td colspan="5" style="padding-top: 6px; padding-bottom: 2px">
								 <span style="font-size: 12px; color: #000000"><b>Documentos do Evento</b></span>					   	 			 						
			 					</td>
			 				</tr>
							<tr>
								<td>
						 			<input name="chkEventoDocumentoExibe" type="checkbox" id="chkEventoDocumentoExibe" value="1" <?php if($campos[evento_documento_exibe] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Exibir Documentos</b></span>
					 			</td>			 				
			 					<td>
						 			<input name="chkEventoDocumentoInclui" type="checkbox" id="chkEventoDocumentoInclui" value="1" <?php if($campos[evento_documento_inclui] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Incluir Documentos</b></span>
					 			</td>
					 			<td>
						 			<input name="chkEventoDocumentoAltera" type="checkbox" id="chkEventoDocumentoAltera" value="1" <?php if($campos[evento_documento_altera] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Alterar Documentos</b></span>
					 			</td>
								<td>													 			
						 			<input name="chkEventoDocumentoExclui" type="checkbox" id="chkEventoDocumentoExclui" value="1" <?php if($campos[evento_documento_exclui] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Excluir Documentos</b></span>
				 				</td>
				 				<td>
						 			&nbsp;
								</td>
							</tr>																																		
						</table>						 
					 </td>		   	     	    
   	   	 </tr>		   	 	   
	   	 </table>
	   	 <br />
     </td>
   </tr> 	   	 

   <tr>
   	 <td>   
   		 <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
         <tr>
           <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px">
             <table cellspacing="0" cellpadding="0" width="100%" border="0">
               <tr>
                 <td class="tabDetailViewDL" style="TEXT-ALIGN: left"><span style="color: #990000"><b>CADASTROS</b></span></td>
			   		   </tr>
		       	 </table>             
					 </td>
	       </tr>
		   	 <tr>
		   	   <td class="tabDetailViewDF"> 
		   	   	 <table cellspacing="0" cellpadding="0" width="100%" border="0">
		   	   	 	<tr height="28">
		   	   	 		<td width="150">
						 			<input name="chkCadCentroCustoConta" type="checkbox" id="chkCadCentroCustoConta" value="1" <?php if($campos[cad_centro_custo_conta] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Centro Custo Conta</b></span>
					 			</td>
					 			<td width="150">
						 			<input name="chkCadContaCaixa" type="checkbox" id="chkCadContaCaixa" value="1" <?php if($campos[cad_conta_caixa] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Conta Caixa</b></span>
					 			</td>
					 			<td width="150">
						 			<input name="chkCadBanco" type="checkbox" id="chkCadBanco" value="1" <?php if($campos[cad_banco] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Bancos</b></span>
					 			</td>
					 			<td width="150">
						 			<input name="chkCadContaCorrente" type="checkbox" id="chkCadContaCorrente" value="1" <?php if($campos[cad_conta_corrente] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Conta Corrente</b></span>
					 			</td>
					 			<td>
						 			<input name="chkCadCentroCustoProduto" type="checkbox" id="chkCadCentroCustoProduto" value="1" <?php if($campos[cad_centro_custo_produto] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Centro Custo Produto</b></span>
					 			</td>
			 				</tr>
			 				<tr height="28">
					 			<td>
						 			<input name="chkCadCentroCustoServico" type="checkbox" id="chkCadCentroCustoServico" value="1" <?php if($campos[cad_centro_custo_servico] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Centro Custo Serviço</b></span>
					 			</td>
			 					<td>
						 			<input name="chkCadProduto" type="checkbox" id="chkCadProduto" value="1" <?php if($campos[cad_produto] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Produtos</b></span>
					 			</td>
					 			<td>
						 			<input name="chkCadProdutoFotoVideo" type="checkbox" id="chkCadProdutoFotoVideo" value="1" <?php if($campos[cad_produto_fotovideo] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Produtos Foto Vídeo</b></span>
					 			</td>
								<td>													 			
						 			<input name="chkCadMaterial" type="checkbox" id="chkCadMaterial" value="1" <?php if($campos[cad_material] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Materiais</b></span>
				 				</td>
				 				<td>
						 			<input name="chkCadServico" type="checkbox" id="chkCadServico" value="1" <?php if($campos[cad_servico] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Serviços</b></span>
								</td>
							</tr>
							<tr height="28">
								<td>
						 			<input name="chkCadBrinde" type="checkbox" id="chkCadBrinde" value="1" <?php if($campos[cad_brinde] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Brindes</b></span>
								</td>
								<td>
						 			<input name="chkCadRepertorio" type="checkbox" id="chkCadRepertorio" value="1" <?php if($campos[cad_repertorio] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Repertório</b></span>
								</td>
								<td>
						 			<input name="chkCadTipoLocal" type="checkbox" id="chkCadTipoLocal" value="1" <?php if($campos[cad_tipo_local] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Tipo de Local</b></span>
								</td>
								<td>
						 			<input name="chkCadMusica" type="checkbox" id="chkCadMusica" value="1" <?php if($campos[cad_musica] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Músicas</b></span>
								</td>
								<td>
						 			<input name="chkCadFuncao" type="checkbox" id="chkCadFuncao" value="1" <?php if($campos[cad_funcao] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Funções</b></span>
								</td>														
							</tr>
							<tr height="28">
								<td>
						 			<input name="chkCadCidade" type="checkbox" id="chkCadCidade" value="1" <?php if($campos[cad_cidade] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Cidades</b></span>
								</td>
								<td>
						 			<input name="chkCadCurso" type="checkbox" id="chkCadCurso" value="1" <?php if($campos[cad_cursos] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Cursos</b></span>
								</td>
								<td colspan="3">
						 			<input name="chkCadTipoDoc" type="checkbox" id="chkCadTipoDoc" value="1" <?php if($campos[cad_tipo_doc] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Tipos Documento</b></span>
								</td>
							</tr>
						</table>					 
					 </td>		   	     	    
   	   	 </tr>		   	 	   
	   	 </table>
	   	 <br />
     </td>
   </tr>
   <tr>
   	 <td>   
   		 <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
         <tr>
           <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px">
             <table cellspacing="0" cellpadding="0" width="100%" border="0">
               <tr>
                 <td class="tabDetailViewDL" style="TEXT-ALIGN: left"><span style="color: #990000"><b>PERMISSÕES ESPECIAIS</b></span></td>
			   		   </tr>
		       	 </table>             
					 </td>
	       </tr>
		   	 <tr>
		   	   <td class="tabDetailViewDF"> 
		   	   	 <table cellspacing="0" cellpadding="0" width="100%" border="0">
		   	   	 	<tr height="28">
		   	   	 		<td>
						 			<input name="chkAtivaGerUsuario" type="checkbox" id="chkAtivaGerUsuario" value="1" <?php if($campos[ativa_ger_usuario] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Permitir Gerenciar Usuários</b></span>
					 			</td>
			 				</tr>
              <tr height="28">
		   	   	 		<td>
						 			<input name="chkAtivaPreferencias" type="checkbox" id="chkAtivaPreferencias" value="1" <?php if($campos[ativa_preferencias] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Permitir Preferências do Sistema</b></span>
					 			</td>
			 				</tr>
			 				<tr height="28">
					 			<td>
						 			<input name="chkAtivaPesquisa" type="checkbox" id="chkAtivaPesquisa" value="1" <?php if($campos[ativa_pesquisa] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Permitir Utilizar a Super Pesquisa</b></span>
					 			</td>
							</tr>
						</table>					 
					 </td>		   	     	    
   	   	 </tr>		   	 	   
	   	 </table>
	   	 <br />
     </td>
   </tr>
   
   <tr>
   	 <td>   
   		 <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
         <tr>
           <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px">
             <table cellspacing="0" cellpadding="0" width="100%" border="0">
               <tr>
                 <td class="tabDetailViewDL" style="TEXT-ALIGN: left"><span style="color: #990000"><b>RELATORIOS</b></span></td>
			   		   </tr>
		       	 </table>             
					 </td>
	       </tr>
		   	 <tr>
		   	   <td class="tabDetailViewDF"> 
		   	   	 <table cellspacing="0" cellpadding="0" width="100%" border="0">
		   	   	 	<tr height="28">
		   	   	 		<td width="160">
						 			<input name="chkRelFinanceiro" type="checkbox" id="chkRelFinanceiro" value="1" <?php if($campos[relatorio_financeiro] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Relatórios Financeiros</b></span>
					 			</td>
					 			<td width="160">
						 			<input name="chkRelCadastros" type="checkbox" id="chkRelCadastros" value="1" <?php if($campos[relatorio_cadastros] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Relatórios de Cadastros</b></span>
					 			</td>
					 			<td width="160">
						 			<input name="chkRelEventos" type="checkbox" id="chkRelEventos" value="1" <?php if($campos[relatorio_eventos] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Relatórios de Eventos</b></span>
					 			</td>
					 			<td width="150">
						 			<input name="chkRelRh" type="checkbox" id="chkRelRh" value="1" <?php if($campos[relatorio_rh] == 1){ echo 'checked="checked"'; } ?> />&nbsp;<span style="font-size: 11px"><b>Relatórios do RH</b></span>
					 			</td>					 			
			 				</tr>
						</table>					 
					 </td>		   	     	    
   	   	 </tr>		   	 	   
	   	 </table>
	   	 <br />
     </td>
   </tr>    
	  
	</table>  	 

  </tr>
</table>
</form>