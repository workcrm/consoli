<?php
###########
## Módulo para opções do relatório do evento
## Criado: 21/06/2007 - Maycon Edinger
## Alterado: 30/09/2008 - Maycon Edinger
## Alterações: 
## 11/10/2007 - Implementado mudanças nas opções do relatório
## 05/11/2007 - Implementado opção para listar os materiais que compõe os itens do evento
## 25/11/2007 - Implementado opção para listar os serviços do evento
## 30/09/2008 - Implementado opção para listar os terceiros do evento
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header('Content-Type: text/html;  charset=ISO-8859-1',true);

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require('Diretivas.php');

//Estabelece a conexão com o banco de dados
include './conexao/ConexaoMySQL.php';

//Recupera o id do evento a imprimir
$EventoId = $_GET[EventoId];

//Recupera o nome do evento
$query_evento = mysql_query("SELECT nome FROM eventos WHERE id = '$EventoId'");

//Monta o array
$dados_evento = mysql_fetch_array($query_evento);

//Recupera dos dados do ultimo evento trabalhado
$sql_back = "SELECT 
			evento_fotovideo
			FROM usuarios
			WHERE usuario_id = $usuarioId";													  													  
						  
//Executa a query
$resultado_back = mysql_query($sql_back);

//Monta o array dos campos
$dados_usuario = mysql_fetch_array($resultado_back); 

?>

<script language="javascript">
function wdCarregarRelatorio() 
{
   
	var Form;
	Form = document.cadastro;

	//Captura o valor referente ao radio button selecionado
	var edtTipo = document.getElementsByName('edtTipo');
   
	for (var i=0; i < edtTipo.length; i++) 
	{
		if (edtTipo[i].checked == true) 
		{
			edtTipo = edtTipo[i].value;
			break;
		}
	}

	//Captura o valor referente ao radio button do tipo de capa
	var edtTipoCapa = document.getElementsByName('edtTipoCapa');
   
	for (var i=0; i < edtTipoCapa.length; i++) 
	{
		if (edtTipoCapa[i].checked == true) 
		{
			
			edtTipoCapa = edtTipoCapa[i].value;
			break;
		
		}
	
	}

	//Caso for relatorio de detalhamento do evento
	if (edtTipo == 1) 
	{
		 
		//Verifica se o checkbox de datas está marcado
		if (Form.chkDatas.checked) 
		{
			var chkDatasValor = 1;
		} 
		
		else 
		{
			var chkDatasValor = 0;
	 	}
		
		//Verifica se o checkbox de participantes está marcado
		if (Form.chkParticipantes.checked) 
		{
			var chkParticipantesValor = 1;
		} 
		
		else 
		{
			var chkParticipantesValor = 0;
	 	
		}

		//Verifica se o checkbox de endereços está ativo
		if (Form.chkEnderecos.checked) 
		{
			var chkEnderecosValor = 1;
		} 
		
		else 
		{
			var chkEnderecosValor = 0;
	 	}

		//Verifica se o checkbox de Repertório está ativo
		if (Form.chkRepertorio.checked) 
		{
			var chkRepertorioValor = 1;
		} 
		
		else 
		{
			var chkRepertorioValor = 0;
	 	}

		//Verifica se o checkbox de Itens está ativo
		if (Form.chkItens.checked) 
		{
	   	 
			var chkItensValor = 1;
		
		} 
		
		else 
		{
	   	 
			var chkItensValor = 0;
	 	}

		//Verifica se o checkbox de materiais está ativo
		if (Form.chkMateriais.checked) 
		{
			var chkMateriaisValor = 1;
		} 
		
		else 
		
		{
			
			var chkMateriaisValor = 0;
	 	}

		//Verifica se o checkbox de Serviços está ativo
		if (Form.chkServicos.checked) 
		{
			var chkServicosValor = 1;
		} 
		
		else 
		{
			var chkServicosValor = 0;
	 	}

		//Verifica se o checkbox de Terceiros está ativo
		if (Form.chkTerceiros.checked) 
		{
			var chkTerceirosValor = 1;
		} 
		
		else 
		
		{
			var chkTerceirosValor = 0;
	 	}

		//Verifica se o checkbox de Brindes está ativo
		if (Form.chkBrindes.checked) 
		{
			var chkBrindesValor = 1;
		} 
		
		else 
		{
			var chkBrindesValor = 0;
	 	}
		
		//Verifica se o checkbox de formandos está ativo
		if (Form.chkFormandos.checked) 
		{
			var chkFormandosValor = 1;
		} 
		
		else 
		{
			var chkFormandosValor = 0;
	 	}
	 	 
		//Verifica se o checkbox de valores está ativo
		if (Form.chkValores.checked) 
		{
			var chkValoresValor = 1;
		} 
		
		else 
		
		{
			
			var chkValoresValor = 0;
	 	
		}
	 	 
		//Verifica se o checkbox de valores dos serviços está ativo
		if (Form.chkValoresServicos.checked) 
		{
			var chkValoresServicoValor = 1;
		} 
		
		else 
		{
			var chkValoresServicoValor = 0;
	 	}	 	 

		//Monta url que do relatório que será carregado	
		url = "./relatorios/EventoDetalheRelatorioPDF.php?EventoId=<?php echo $EventoId ?>&UsuarioNome=<?php echo $usuarioNome . ' ' . $usuarioSobrenome ?>&Id=" + chkDatasValor + "&Ip=" + chkParticipantesValor + "&Ie=" + chkEnderecosValor + "&Ir=" + chkRepertorioValor + "&Ii=" + chkItensValor + "&Im=" + chkMateriaisValor + "&Is=" + chkServicosValor + "&It=" + chkTerceirosValor + "&Ib=" + chkBrindesValor + "&If=" + chkFormandosValor + "&Ivs=" + chkValoresServicoValor + "&Iv=" + chkValoresValor + "&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>&EmpresaId=<?php echo $empresaId ?>";
		 
	}
	
	//Caso for relatório de participantes do evento
	if (edtTipo == 2) 
	{

		//Monta url que do relatório que será carregado		
		url = "./relatorios/EventoParticipanteRelatorioPDF.php?EventoId=<?php echo $EventoId ?>&UsuarioNome=<?php echo $usuarioNome . ' ' . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>&EmpresaId=<?php echo $empresaId ?>";
	}

	
	//Caso for relatório de planilha de detalhamento
	if (edtTipo == 4)
	{
		
		//Captura o valor referente ao radio button da posição 
		var chkPosicao= document.getElementsByName('chkPosicaoAtualTipo');
   
		for (var i=0; i < chkPosicao.length; i++) 
		{
			if (chkPosicao[i].checked == true) 
			{
				
				chkPosicao = chkPosicao[i].value;
				break;
			}
		}
		
		if (chkPosicao < 3)
		{
		
			//Monta url que do relatório que será carregado		
			url = "./relatorios/EventoFotoVideoRelatorioPDF.php?EventoId=<?php echo $EventoId ?>&UsuarioNome=<?php echo $usuarioNome . ' ' . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>&EmpresaId=<?php echo $empresaId ?>&TipoRelatorio=" + chkPosicao;
	
		}
		
		else
		
		{
		
			//Monta url que do relatório que será carregado		
			url = "./relatorios/EventoFotoVideoSemCompraRelatorioPDF.php?EventoId=<?php echo $EventoId ?>&UsuarioNome=<?php echo $usuarioNome . ' ' . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>&EmpresaId=<?php echo $empresaId ?>&TipoRelatorio=" + chkPosicao;
	
		}
	}

	//Caso for relatório de orçamento do evento
	if (edtTipo == 3) 
	{
	
		//Verifica se o checkbox de valores está ativo
		if (Form.chkValoresOrca.checked) 
		{
			var chkValoresOrcaValor = 1;
		} 
		
		else 
		{
			var chkValoresOrcaValor = 0;
	 	}
	 	 
	 	//Verifica se o checkbox de valores está ativo
		if (Form.chkCapaOrca.checked) 
		{
			var chkCapaOrcaValor = 1;
		} 
		
		else 
		{
			var chkCapaOrcaValor = 0;
	 	}
	 	 
 		//Verifica se o checkbox de datas está marcado
		if (Form.chkDatasOrca.checked) 
		{
			var chkDatasOrcaValor = 1;
		} 
		
		else 
		{
			var chkDatasOrcaValor = 0;
	 	}
	 	 
	 	//Verifica se o checkbox de participantes está marcado
		if (Form.chkParticipantesOrca.checked) 
		{
			var chkParticipantesOrcaValor = 1;
		} 
		
		else 
		{
			var chkParticipantesOrcaValor = 0;
	 	}
	 	 
	 	//Verifica se o checkbox de participantes está marcado
		if (Form.chkParticipantesOrca.checked) 
		{
			var chkParticipantesOrcaValor = 1;
		} 
		
		else 
		{
			var chkParticipantesOrcaValor = 0;
	 	}
	 	 
	 	//Verifica se o checkbox de endereços está marcado
		if (Form.chkEnderecosOrca.checked) 
		{
			var chkEnderecosOrcaValor = 1;
		} 
		
		else 
		{
			var chkEnderecosOrcaValor = 0;
	 	}
	 	 
	 	//Verifica se o checkbox de terceiros está marcado
		if (Form.chkTerceirosOrca.checked) 
		{
			var chkTerceirosOrcaValor = 1;
		} 
		
		else 
		{
			var chkTerceirosOrcaValor = 0;
	 	}
	 	 
	 	//Verifica se o checkbox de brindes está marcado
		if (Form.chkBrindesOrca.checked) 
		{
			var chkBrindesOrcaValor = 1;
		} 
		
		else 
		{
			var chkBrindesOrcaValor = 0;
	 	}
	 	 
	 	//Verifica se o checkbox de repertorio está marcado
		if (Form.chkRepertorioOrca.checked) 
		{
			var chkRepertorioOrcaValor = 1;
		} 
		
		else 
		{
			var chkRepertorioOrcaValor = 0;
	 	}
	 	 
	 	//Verifica se o checkbox de formandos está marcado
		if (Form.chkFormandosOrca.checked) 
		{
			var chkFormandosOrcaValor = 1;
		} 
		
		else 
		{
			var chkFormandosOrcaValor = 0;
	 	}

		//Monta url que do relatório que será carregado		
		url = "./relatorios/EventoOrcamentoRelatorioPDF.php?EventoId=<?php echo $EventoId ?>&UsuarioNome=<?php echo $usuarioNome . ' ' . $usuarioSobrenome ?>&Id=" + chkDatasOrcaValor + "&It=" + chkTerceirosOrcaValor + "&Ie=" + chkEnderecosOrcaValor + "&Ip=" + chkParticipantesOrcaValor + "&Iv=" + chkValoresOrcaValor + "&Ib=" + chkBrindesOrcaValor + "&If=" + chkFormandosOrcaValor + "&Ir=" + chkRepertorioOrcaValor + "&Icapa=" + chkCapaOrcaValor + "&Tcapa=" + edtTipoCapa + "&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>&EmpresaId=<?php echo $empresaId ?>";
	}

	//Caso for etiquetas de endereçamento
	if (edtTipo == 5) 
	{

		//Monta url que do relatório que será carregado		
		url = "./relatorios/EventoEnderecoFormandoRelatorioPDF.php?EventoId=<?php echo $EventoId ?>";
	}

	//Executa o relatório selecionado
	abreJanela(url);	 	 
}

function wdTipoRelatorio() 
{
   
	var Form;
	Form = document.cadastro;
	 
	//Captura o valor referente ao radio button selecionado
	var edtTipo = document.getElementsByName("edtTipo");
   
	for (var i=0; i < edtTipo.length; i++) 
	{
     
		if (edtTipo[i].checked == true) 
		{
			
			edtTipo = edtTipo[i].value;
			break;
		}
	}

	//Se foi clicado no option 1 então reabilita os checkboxes
	if (edtTipo == 1) 
	{
		Form.chkDatas.disabled = 0;
		Form.chkParticipantes.disabled = 0;
		Form.chkEnderecos.disabled = 0;
		Form.chkRepertorio.disabled = 0;
		Form.chkItens.disabled = 0;
		Form.chkServicos.disabled = 0;
		Form.chkTerceiros.disabled = 0;
		Form.chkBrindes.disabled = 0;
		Form.chkFormandos.disabled = 0;
		Form.chkMateriais.disabled = 0;
		Form.chkValores.disabled = 0;		
		Form.chkValoresServicos.disabled = 0;
		Form.chkValoresOrca.disabled = 1;
		Form.chkValoresServicosOrca.disabled = 1;
		Form.chkCapaOrca.disabled = 1;
		Form.edtTipoCapa.disabled = true;
		Form.chkDatasOrca.disabled = true;
		Form.chkParticipantesOrca.disabled = true;
		Form.chkEnderecosOrca.disabled = true;
		Form.chkTerceirosOrca.disabled = true;
		Form.chkBrindesOrca.disabled = true;
		Form.chkRepertorioOrca.disabled = true;
		Form.chkFormandosOrca.disabled = true;
	} 
	
	else if (edtTipo == 3)
	{
		
		//Desabilita os checkboxes
		Form.chkDatas.disabled = 1;
		Form.chkParticipantes.disabled = 1;
		Form.chkEnderecos.disabled = 1;
		Form.chkRepertorio.disabled = 1;
		Form.chkItens.disabled = 1;
		Form.chkServicos.disabled = 1;
		Form.chkTerceiros.disabled = 1;
		Form.chkBrindes.disabled = 1;
		Form.chkFormandos.disabled = 1;
		Form.chkMateriais.checked = false;
		Form.chkMateriais.disabled = 1;
		Form.chkValores.disabled = 1;
		Form.chkValoresServicos.disabled = 1;
		Form.chkValoresOrca.disabled = 0;
		Form.chkValoresServicosOrca.disabled = 0;
		Form.chkCapaOrca.disabled = 0;
		Form.edtTipoCapa.disabled = false;
		Form.chkDatasOrca.disabled = false;
		Form.chkParticipantesOrca.disabled = false;
		Form.chkEnderecosOrca.disabled = false;
		Form.chkTerceirosOrca.disabled = false;
		Form.chkBrindesOrca.disabled = false;
		Form.chkRepertorioOrca.disabled = false;
		Form.chkFormandosOrca.disabled = false;
	} 
	
	else 
	{
		
		Form.chkDatas.disabled = 1;
		Form.chkParticipantes.disabled = 1;
		Form.chkEnderecos.disabled = 1;
		Form.chkRepertorio.disabled = 1;
		Form.chkItens.disabled = 1;
		Form.chkServicos.disabled = 1;
		Form.chkTerceiros.disabled = 1;
		Form.chkBrindes.disabled = 1;
		Form.chkFormandos.disabled = 1;
		Form.chkMateriais.checked = false;
		Form.chkMateriais.disabled = 1;
		Form.chkValores.disabled = 1;
		Form.chkValoresServicos.disabled = 1;
		Form.chkValoresOrca.disabled = 1;	
		Form.chkValoresServicosOrca.disabled = 1;
		Form.chkCapaOrca.disabled = 1;
		Form.edtTipoCapa.disabled = true;	
		Form.chkDatasOrca.disabled = true;
		Form.chkParticipantesOrca.disabled = true;
		Form.chkEnderecosOrca.disabled = true;
		Form.chkTerceirosOrca.disabled = true;
		Form.chkBrindesOrca.disabled = true;
		Form.chkRepertorioOrca.disabled = true;
		Form.chkFormandosOrca.disabled = true;
	}
}

function wdTipoItem()
{
	var Form;
	Form = document.cadastro;
   
	if (Form.chkItens.checked)
	{
		Form.chkMateriais.disabled = 0;
		Form.chkValores.disabled = 0;
	} 
	
	else 
	{
		Form.chkMateriais.checked = false;
		Form.chkMateriais.disabled = 1;
		Form.chkValores.checked = false;
		Form.chkValores.disabled = 1;		 
	}		
}

function wdTipoServico()
{
	var Form;
	Form = document.cadastro;
   
	if (Form.chkServicos.checked)
	{
		Form.chkValoresServicos.disabled = 0;
	} 
	
	else 
	{
		Form.chkValoresServicos.checked = false;
		Form.chkValoresServicos.disabled = 1;		 
	}		
}

</script>

<form id="form" name="cadastro" method="post">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="750">
						<img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Emissão dos Relatórios do Evento</span>			  	
					</td>
				</tr>
				<tr>
					<td colspan="5">
						<img src="image/bt_espacohoriz.gif" width="100%" height="12">
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td colspan="2">
						<input class="button" title="Retorna a exibição do detalhamento do evento" name="btnVoltar" type="button" id="btnRelatorio" value="Retornar ao Evento" style="width:120px" onclick="wdCarregarFormulario('EventoExibe.php?EventoId=<?php echo $EventoId ?>&headers=1','conteudo')">
						<input class="button" title="Emite o relatório do evento" name="btnRelatorio" type="button" id="btnRelatorio" value="Emitir Relatório" style="width:100px" onclick="wdCarregarRelatorio()" />
						<br />
						<br />	   	   		   		
 					</td>   
				</tr> 
				<tr>
					<td colspan="2">
						<span class="TituloModulo"><span style="color: #990000;"><?php echo $dados_evento[nome] ?></span></span><br/>
						<b>Selecione o Tipo de Relatório desejado:</b>
						<br/>
						<br/>				    
						<input name="edtTipo" type="radio" value="3" checked="checked" onclick="wdTipoRelatorio()"/><span style="color:#990000"><b>&nbsp;Relatório de Orçamento do Evento</b></span><br/>
					</td>
				</tr>
				<tr>
					<td width="20">			    
						&nbsp;	    
					</td>          
					<td width="440">
						Opções de emissão do orçamento:
						<br/>
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input name="chkDatasOrca" type="checkbox" id="chkDatasOrca" value="1" title="Marque esta caixa caso desejar incluir as datas do orçamento no relatório" style="border: 0px" checked="checked" />
									Incluir datas do evento no orçamento.
								</td>
							</tr>
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input name="chkParticipantesOrca" type="checkbox" id="chkParticipantesOrca" value="1" title="Marque esta caixa caso desejar incluir os dados dos participantes do evento no orçamento" style="border: 0px" checked="checked" />
									Incluir participantes do evento no orçamento.
								</td>
							</tr>
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input name="chkEnderecosOrca" type="checkbox" id="chkEnderecosOrca" value="1" title="Marque esta caixa caso desejar incluir os dados dos endereços do evento no orçamento" style="border: 0px" checked="checked" />
									Incluir endereços do evento no orçamento.
								</td>
							</tr>		          
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input name="chkValoresOrca" type="checkbox" id="chkValoresOrca" value="1" title="Marque esta caixa caso desejar incluir os valores dos produtos do evento no orçamento" style="border: 0px" checked="checked" />
									Incluir valores dos produtos no orçamento.
								</td>
							</tr>			          
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input name="chkValoresServicosOrca" type="checkbox" id="chkValoresServicosOrca" value="1" title="Marque esta caixa caso desejar incluir os valores dos serviços do evento no relatório" style="border: 0px" checked="checked" />
									Incluir valores dos serviços no orçamento.
								</td>
							</tr>
 							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input name="chkTerceirosOrca" type="checkbox" id="chkTerceirosOrca" value="1" title="Marque esta caixa caso desejar incluir os dados dos terceiros do evento no orçamento" style="border: 0px" checked="checked" />
									Incluir terceiros do evento no orçamento.
								</td>
							</tr>
 							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input name="chkBrindesOrca" type="checkbox" id="chkBrindesOrca" value="1" title="Marque esta caixa caso desejar incluir os dados dos brindes do evento no orçamento" style="border: 0px" checked="checked" />
									Incluir brindes do evento no orçamento.
								</td>
							</tr>
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input name="chkRepertorioOrca" type="checkbox" id="chkRepertorioOrca" value="1" title="Marque esta caixa caso desejar incluir os dados do repertório musical do evento no orçamento" style="border: 0px" checked="checked" />
									Incluir os momentos de repertório musical do evento no orçamento.
								</td>
							</tr>
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input name="chkFormandosOrca" type="checkbox" id="chkFormandosOrca" value="1" title="Marque esta caixa caso desejar incluir os dados dos formandos do evento no orçamento" style="border: 0px" checked="checked" />
									Incluir os formandos do evento no orçamento.
								</td>
							</tr>	
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input name="chkCapaOrca" type="checkbox" id="chkCapaOrca" value="1" title="Marque esta caixa caso desejar incluir página de abertura no relatório" style="border: 0px" checked="checked" />
									Incluir página de abertura no orçamento.<br/>
								</td>
							</tr>
							<tr valign="middle" style="padding: 1px">
								<td height="20" style="padding-left:24px">
									<input name="edtTipoCapa" type="radio" value="1" checked="checked"/> Capa para Formatura&nbsp;&nbsp;&nbsp;&nbsp;<input name="edtTipoCapa" type="radio" value="2" /> Capa para Casamento<br/><br/>						
								</td>
							</tr>									          
						</table>						
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input name="edtTipo" type="radio" value="1" onclick="wdTipoRelatorio()"/><span style="color:#990000"><b>&nbsp;Relatório de Detalhamento do Evento</b></span>
					</td>
				</tr>
				<tr>
					<td width="20">			    
						&nbsp;	    
					</td>          
					<td width="440">
						Opções de emissão do relatório:
						<br/>
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input name="chkDatas" type="checkbox" id="chkDatas" value="1" title="Marque esta caixa caso desejar incluir as datas do evento no relatório" style="border: 0px" checked="checked" />
									Incluir datas do evento.
								</td>
							</tr>
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input name="chkParticipantes" type="checkbox" id="chkParticipantes" value="1" title="Marque esta caixa caso desejar incluir os dados dos participantes do evento no relatório" style="border: 0px" checked="checked" />
									Incluir participantes do evento.
								</td>
							</tr>
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input name="chkEnderecos" type="checkbox" id="chkEnderecos" value="1" title="Marque esta caixa caso desejar incluir os dados dos endereços do evento no relatório" style="border: 0px" checked="checked" />
									Incluir endereços do evento.
								</td>
							</tr>		         		          
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input name="chkItens" type="checkbox" id="chkItens" value="1" title="Marque esta caixa caso desejar incluir os dados dos itens do evento no relatório" style="border: 0px" checked="checked" onclick="wdTipoItem()" />
									Incluir produtos do evento.
								</td>
							</tr>
							<tr valign="middle" style="padding: 1px">
								<td height="20" style="padding-left: 20px">
									<input name="chkMateriais" type="checkbox" id="chkMateriais" value="1" title="Marque esta caixa caso desejar incluir os dados dos materiais que compõe cada item do evento no relatório" style="border: 0px" />
									Incluir materiais que compõe cada produto do evento.
								</td>
							</tr>
							<tr valign="middle" style="padding: 1px">
								<td height="20" style="padding-left: 20px">
									<input name="chkValores" type="checkbox" id="chkValores" value="1" title="Marque esta caixa caso desejar incluir os valores financeiros dos itens do evento no relatório" style="border: 0px" checked="checked" />
									Incluir valores financeiros dos produtos do evento.
								</td>
							</tr>
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input name="chkServicos" type="checkbox" id="chkServicos" value="1" title="Marque esta caixa caso desejar incluir os dados dos serviços do evento no relatório" style="border: 0px" checked="checked" onclick="wdTipoServico()" />
									Incluir serviços do evento.
								</td>
							</tr>							
							<tr valign="middle" style="padding: 1px">
								<td height="20" style="padding-left: 20px">
									<input name="chkValoresServicos" type="checkbox" id="chkValoresServicos" value="1" title="Marque esta caixa caso desejar incluir os valores financeiros dos serviços do evento no relatório" style="border: 0px" checked="checked" />
									Incluir valores financeiros dos serviços do evento.
								</td>
							</tr>
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input name="chkTerceiros" type="checkbox" id="chkTerceiros" value="1" title="Marque esta caixa caso desejar incluir os dados dos terceiros do evento no relatório" style="border: 0px" checked="checked" />
									Incluir terceiros do evento.
								</td>
							</tr>
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input name="chkBrindes" type="checkbox" id="chkBrindes" value="1" title="Marque esta caixa caso desejar incluir os dados dos brindes do evento no relatório" style="border: 0px" checked="checked" />
									Incluir brindes do evento.
								</td>
							</tr>
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input name="chkRepertorio" type="checkbox" id="chkRepertorio" value="1" title="Marque esta caixa caso desejar incluir os dados do repertório musical do evento no relatório" style="border: 0px" checked="checked" />
									Incluir os momentos de repertório musical do evento.
								</td>
							</tr>
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input name="chkFormandos" type="checkbox" id="chkFormandos" value="1" title="Marque esta caixa caso desejar incluir os dados dos formandos do evento no relatório" style="border: 0px" checked="checked" />
									Incluir os formandos do evento.
								</td>
							</tr>									          																													          
						</table>						
					</td>
				</tr>
				<?php
					
					//Verifica o nível de acesso do usuário
					if ($nivelAcesso >= 4 OR $dados_usuario['evento_fotovideo'] == 1) 
					{
			
				?>
				<tr>
					<td colspan="2">
						<br/>
						<br/>				    
						<input name="edtTipo" type="radio" value="4" onclick="wdTipoRelatorio()"/><span style="color:#990000"><b>&nbsp;Planilha de Controle de Foto e Vídeo</b></span>
					</td>
				</tr>
				<tr valign="middle" style="padding: 1px">
					<td colspan="2" height="20" style="padding-left:24px">
						<input name="chkPosicaoAtualTipo" type="radio" value="1" checked="checked" />&nbsp;Relatório Detalhado.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="chkPosicaoAtualTipo" type="radio" value="2" />&nbsp;Somente Totais.&nbsp;&nbsp;&nbsp;&nbsp;<input name="chkPosicaoAtualTipo" type="radio" value="3" />&nbsp;Formandos sem compra.
					</td>
				</tr>
				<?php
				
				}
				
				?>
				<tr>
					<td colspan="2">
						<br/>				    
						<input name="edtTipo" type="radio" value="5" onclick="wdTipoRelatorio()"/><span style="color:#990000"><b>&nbsp;Etiquetas de Endereçamento (Correio) para Formandos do Evento</b></span>
					</td>
				</tr>        
				<tr>
					<td colspan="2">
						<br/>				    
						<input name="edtTipo" type="radio" value="2" onclick="wdTipoRelatorio()"/><span style="color:#990000"><b>&nbsp;Listagem de Participantes do Evento</b></span>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<br/>
			<input class="button" title="Retorna a exibição do detalhamento do evento" name="btnVoltar" type="button" id="btnRelatorio" value="Retornar ao Evento" style="width:120px" onclick="wdCarregarFormulario('EventoExibe.php?EventoId=<?php echo $EventoId ?>&headers=1','conteudo')">
			<input class="button" title="Emite o relatório do evento" name="btnRelatorio" type="button" id="btnRelatorio" value="Emitir Relatório" style="width:100px" onclick="wdCarregarRelatorio()" />
			<br />
			<br />	   	   		   		
 		</td>   
	</tr>  
</table>

</form>
<script>
document.cadastro.chkDatas.disabled = 1;
document.cadastro.chkParticipantes.disabled = 1;
document.cadastro.chkEnderecos.disabled = 1;
document.cadastro.chkRepertorio.disabled = 1;
document.cadastro.chkItens.disabled = 1;
document.cadastro.chkMateriais.disabled = 1;
document.cadastro.chkValores.disabled = 1;
document.cadastro.chkServicos.disabled = 1;
document.cadastro.chkTerceiros.disabled = 1;
document.cadastro.chkBrindes.disabled = 1;
document.cadastro.chkFormandos.disabled = 1;
document.cadastro.chkValoresServicos.disabled = 1;
</script>