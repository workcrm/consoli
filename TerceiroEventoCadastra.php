<?php 
###########
## Módulo para cadastro de terceiros do evento
## Criado: 28/09/2008 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) 
{
	
	header("Content-Type: text/html;  charset=ISO-8859-1",true);

}

// Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Inclui o arquivo para manipulação financeira
include "./include/ManipulaMoney.php";

//Adiciona o acesso a entidade de criação do componente data
include("CalendarioPopUp.php");

//Cria um objeto do componente data
$objData = new tipData();

//Define que não deve exibir a hora no calendario
$objData->bolExibirHora = false;

//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript();

//Recupera o id do evento
if($_POST) 
{
	
	$EventoId = $_POST["EventoId"]; 

} 

else 

{

	$EventoId = $_GET["EventoId"]; 

}

//Converte uma data timestamp de mysql para normal
function TimestampMySQLRetornar($DATA)
{
	
	$ANO = 0000;
	$MES = 00;
	$DIA = 00;
	$HORA = "00:00:00";
	$data_array = split("[- ]",$DATA);
	if ($DATA <> "")
	{
		
		$ANO = $data_array[0];
		$MES = $data_array[1];
		$DIA = $data_array[2];
		$HORA = $data_array[3];
		return $DIA."/".$MES."/".$ANO. " - " . $HORA;
  
	}
	
	else 
	
	{
		
		$ANO = 0000;
		$MES = 00;
		$DIA = 00;
		return $DIA."/".$MES."/".$ANO;
  
	}
	
}

//Monitora o evento escolhido para o bactracking do usuário
$sql_backtracking = mysql_query("UPDATE usuarios SET evento_id = '$EventoId' WHERE usuario_id = '$usuarioId'");

echo "<script>wdCarregarFormulario('UltimoEvento.php','ultimo_evento',2)</script>";

//Recupera dos dados do evento
$sql_evento = mysql_query("SELECT 
							eve.id,
							eve.nome,
							eve.descricao,
							eve.status,
							eve.cliente_id,
							eve.grupo_id,
							eve.responsavel,
							eve.contato1,
							eve.contato_obs1,
							eve.contato_fone1,
							eve.contato2,
							eve.contato_obs2,
							eve.contato_fone2,
							eve.contato3,
							eve.contato_obs3,
							eve.contato_fone3,													
							eve.data_realizacao,
							eve.hora_realizacao,
							eve.duracao,
							eve.terceiros_timestamp,
							eve.terceiros_operador_id,
							concat(usu.nome , ' ', usu.sobrenome) as operador_nome,
							cli.nome as cliente_nome,
							gru.nome as grupo_nome
							FROM eventos eve 
							INNER JOIN clientes cli ON cli.id = eve.cliente_id
							LEFT OUTER JOIN grupo_conta gru ON gru.id = eve.grupo_id
							LEFT OUTER JOIN usuarios usu ON usu.usuario_id = eve.terceiros_operador_id
							WHERE eve.id = $EventoId");

//Cria o array dos dados
$dados_evento = mysql_fetch_array($sql_evento);

//Efetua o switch para o campo de status
switch ($dados_evento[status]) 
{

	case 0: $desc_status = "Em orçamento"; break;
	case 1: $desc_status = "Em aberto"; break;
	case 2: $desc_status = "Realizado"; break;
	case 3: $desc_status = "<span style='color: red'>Não-Realizado</span>"; break;

} 

//Monta o lookup da tabela de fornecedores (para a pessoa_id)
//Monta o SQL
$lista_fornecedor = "SELECT id, nome FROM fornecedores WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_fornecedor = mysql_query($lista_fornecedor);

//Monta o lookup da tabela de grupos
//Monta o SQL
$lista_grupo = "SELECT * FROM grupo_conta WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_grupo = mysql_query($lista_grupo);

//Monta o lookup da tabela de subgrupos (CONTA_CAIXA) filtrando tipo 2 que é saída (débito)
//Monta o SQL
$lista_subgrupo = "SELECT * FROM subgrupo_conta WHERE empresa_id = $empresaId AND ativo = '1' AND tipo = '2' ORDER BY nome";
//Executa a query
$dados_subgrupo = mysql_query($lista_subgrupo);

?>

<script language="JavaScript">
//Função que alterna a visibilidade do painel especificado.
function oculta(id)
{
  
	ID = document.getElementById(id);
	ID.style.display = "none";

}

function valida_form() 
{
   
	var Form;
	Form = document.cadastro;
   
	if (Form.cmbFornecedorId.value == 0) 
	{
		
		alert("É necessário selecionar um terceiro para o Evento !");
		Form.cmbFornecedorId.focus();
		return false;
   
	}
   
	if (Form.edtServicoContratado.value == 0) 
	{
		
		alert("É necessário Informar o serviço contratado !");
		Form.edtServicoContratado.focus();
		return false;
   
	}
   
	if (Form.edtCusto.value == 0) 
	{
		
		alert("É necessário informar o valor do custo no evento !");
		Form.edtCusto.focus();
		return false;
    
	}   
   
	//Verifica se o checkbox de datas está marcado
	if (Form.chkCriarConta.checked) 
	{

		if (Form.cmbSubgrupoId.value == 0) 
		{
			alert("É necessário selecionar uma Conta-caixa !");
			Form.cmbSubgrupoId.focus();
			return false;
		}

		if (Form.cmbGrupoId.value == 0) 
		{
			
			alert("É necessário selecionar um Centro de Custo para a Conta !");
			Form.cmbGrupoId.focus();
			return false;
		
		}
	  
	} 
	
	else 
	
	{
		
		var chkCriarConta = 0;
	
	}

  
   return true;
   
}

function marca_conta()
{
	
	var Form;
	Form = document.cadastro;
  
	//Verifica se o checkbox de datas está marcado
	if (Form.chkCriarConta.checked) 
	{
 
		Form.edtDataVencimento.disabled = 0;
		Form.cmbSubgrupoId.disabled = 0;
		Form.cmbGrupoId.disabled = 0;
	
	} 
	
	else 
	
	{
		
		Form.edtDataVencimento.disabled = 1;
		Form.cmbSubgrupoId.disabled = 1;
		Form.cmbGrupoId.disabled = 1;
		
	}
	
}
</script>
<link rel="stylesheet" type="text/css" href="include/workStyle.css">
</head>
<body>



<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
	<tr>
		<td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Cadastramento de Terceiros do Evento</span></td>
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
					
							//Recupera os valores vindos do formulário e armazena nas variaveis
							if($_POST['Submit'])
							{

								$edtEmpresaId = $empresaId;
								$edtEventoId = $_POST["EventoId"];
								$cmbFornecedorId = $_POST["cmbFornecedorId"];					
								$edtServicoContratado = $_POST["edtServicoContratado"];
								$edtCusto = MoneyMySQLInserir($_POST["edtCusto"]);
								$edtValorVenda = MoneyMySQLInserir($_POST["edtValorVenda"]);
								$chkCriarConta = $_POST["chkCriarConta"];
								$edtDataVencimento = DataMySQLInserir($_POST["edtDataVencimento"]);
								$edtObservacoes = $_POST["edtObservacoes"];
								$edtOperadorId = $usuarioId;
								$cmbGrupoId = $_POST["cmbGrupoId"];
								$cmbSubgrupoId = $_POST["cmbSubgrupoId"];
								$edtStatusContrato = $_POST["edtStatusContrato"];

								//Monta o sql e executa a query de inserção dos clientes
								$sql = mysql_query("INSERT INTO eventos_terceiro (
													empresa_id, 
													evento_id,
													fornecedor_id,
													servico_contratado,
													custo,
													valor_venda,								
													observacoes,
													cadastro_timestamp,
													cadastro_operador_id,
													status_contrato
									
													) VALUES (
									
													'$edtEmpresaId',
													'$edtEventoId',
													'$cmbFornecedorId',
													'$edtServicoContratado',
													'$edtCusto',
													'$edtValorVenda',
													'$edtObservacoes',
													now(),
													'$edtOperadorId',
													'$edtStatusContrato'				
													);");
								
								//Recupera o Id do terceiro cadastrado
								$terceiro_id = mysql_insert_id();

						
								//Verifica se o deve cadastrar uma conta a pagar para o participante
								if ($chkCriarConta == 1) 
								{
					
									//Busca os parâmetros de grupo e subgrupo na tabela de parametros
									$query_parametro = mysql_query("SELECT * FROM parametros_sistema WHERE ativo = '1'");
									
									//Monta o array com os parametros
									$dados_parametro = mysql_fetch_array($query_parametro);
									
									$data_conta = date("Y-m-d", mktime());
						
									//Monta o sql apra inserir a conta a pagar
									$sql = "INSERT INTO contas_pagar (
											empresa_id, 
											data,
											tipo_pessoa,
											pessoa_id,
											grupo_conta_id,
											subgrupo_conta_id, 
											evento_id,
											descricao,
											origem_conta,
											valor,
											data_vencimento, 
											situacao,
											observacoes, 
											cadastro_timestamp,
											cadastro_operador_id
							
											) VALUES (
							
											'$edtEmpresaId',
											'$data_conta',
											'2',
											'$cmbFornecedorId',
											'$cmbGrupoId',
											'$cmbSubgrupoId',
											'$edtEventoId',
											'Pagamento de Terceiro do Evento',
											2,
											'$edtCusto',
											'$edtDataVencimento',
											'1',
											'Conta a pagar incluída automaticamente pelo sistema.<br/><br/>Participação do Terceiro no Evento:<br/><b>$dados_evento[nome]</b><br/><br/>Na Função de: <b>$edtServicoContratado</b>',
											now(),
											'$edtOperadorId'				
											);";				
						
						
								//Executa a query de inserção da conta
								$query = mysql_query($sql);	
						
								//Recupera o Id da conta a pagar cadastrada
								$conta_id = mysql_insert_id();
						
								//Insere o valor do id da conta a pagar vinculado ao lançamento.
								$altera_id = mysql_query("UPDATE eventos_terceiro SET conta_pagar_id = $conta_id WHERE id = $terceiro_id");
							
							}
					
							//Configura a assinatura digital
							$sql = mysql_query("UPDATE eventos SET terceiros_timestamp = now(), terceiros_operador_id = $usuarioId WHERE id = $edtEventoId");

							//Exibe a mensagem de inclusão com sucesso
							echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Terceiro cadastrado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
        
							}
						
						?>
						<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td class="dataLabel" width="15%">Nome do Evento: </td>
								<td colspan="5" class="tabDetailViewDF">
									<span style="font-size: 16px; color: #990000"><b><?php echo $dados_evento["nome"] ?></b></span>
								</td>
							</tr>
							<tr>
								<td valign="top" class="dataLabel">Descri&ccedil;&atilde;o:</td>
								<td colspan="5" valign="middle" class="tabDetailViewDF">
									<?php echo $dados_evento["descricao"] ?>
								</td>
							</tr>
							<tr>
								<td valign="top" class="dataLabel">Status:</td>
								<td colspan="5" valign="middle" class="tabDetailViewDF">
									<?php echo $desc_status ?>
								<td>
							</tr>
							<tr>
								<td valign="top" class="dataLabel">Data:</td>
								<td valign="middle" class="tabDetailViewDF">
									<?php echo DataMySQLRetornar($dados_evento["data_realizacao"]) ?>
								</td>
								<td valign="middle" class="dataLabel">Hora:</td>
								<td width="19%" valign="middle" class="tabDetailViewDF">
									<?php echo $dados_evento["hora_realizacao"] ?>								
								</td>
								<td width="12%" valign="middle" class="dataLabel">Dura&ccedil;&atilde;o:</td>
								<td width="20%" valign="middle" class="tabDetailViewDF">
									<?php echo $dados_evento["duracao"] ?>								
								</td>
							</tr>
							<tr>
								<td valign="top" class="dataLabel">Cliente:</td>
								<td colspan="5" valign="middle" class="tabDetailViewDF">
									<?php echo $dados_evento["cliente_nome"] ?>
								</td>
							</tr>
							<tr>
								<td valign="top" class="dataLabel">Grupo:</td>
								<td colspan="5" valign="middle" class="tabDetailViewDF">
									<?php echo $dados_evento["grupo_nome"] ?>
								</td>
							</tr>              
							<tr>
								<td class="dataLabel">Respons&aacute;vel:</td>
								<td colspan="5" valign="middle" class="tabDetailViewDF">
									<?php echo $dados_evento["responsavel"] ?>								
								</td>
							</tr>
							<tr>
								<td valign="top" class="dataLabel">Contatos:</td>
								<td colspan="5" valign="middle" class="tabDetailViewDF">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tr valign="middle">
											<td width="300" height="20">
												Nome:
											</td>
											<td width="260" height="20">
												Observações:
											</td>
											<td height="20">
												Telefone:
											</td>
										</tr>
										<tr valign="middle">
	                   <td height="20">
	                     <span style="font-size: 12px"><?php echo $dados_evento[contato1] ?></span>
	                   </td>
	                   <td height="20">
	                     <span style="font-size: 12px"><?php echo $dados_evento[contato_obs1] ?></span>
	                   </td>
	                   <td height="20">
	                     <span style="font-size: 12px"><?php echo $dados_evento[contato_fone1] ?></span>
	                   </td>
	                 </tr>
	                 <tr valign="middle">
	                   <td height="20">
	                     <span style="font-size: 12px"><?php echo $dados_evento[contato2] ?></span>                   
										 </td>
	                   <td height="20">
	                     <span style="font-size: 12px"><?php echo $dados_evento[contato_obs2] ?></span>
	                   </td>
	                   <td height="20">
	                     <span style="font-size: 12px"><?php echo $dados_evento[contato_fone2] ?></span>
	                   </td>
	                 </tr>
	                 <tr valign="middle">
	                   <td height="20">
	                     <span style="font-size: 12px"><?php echo $dados_evento[contato3] ?></span>
	                   </td>
	                   <td height="20">
	                     <span style="font-size: 12px"><?php echo $dados_evento[contato_obs3] ?></span>
	                   </td>
	                   <td height="20">
	                     <span style="font-size: 12px"><?php echo $dados_evento[contato_fone3] ?></span>
	                   </td>
	                 </tr>                 
	               </table>               							 
	             </td>
	           </tr>
							<tr>
                <td colspan="6" valign="middle" class="tabDetailViewDF">
									<table width="100%" cellpadding="0" cellspacing="0" border="0" >
              			<tr valign="middle">
											<td colspan="8" style="padding-bottom: 4px">
                    		 <span style="font-size: 11px">Tarefas Adicionais:</span>
                			</td>
                		</tr>
                		<tr valign="middle">
											<td width="30">
                    		 <img src="./image/bt_evento_gd.gif"/> 
                			</td>                			
											<td width="85">
                    		 <a title="Clique para exibir o detalhamento deste evento" href="#" onclick="wdCarregarFormulario('EventoExibe.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Detalhamento</a>
                			</td>
											<td width="30">
                    		 <img src="./image/bt_data_evento_gd.gif" /> 
                			</td>
											<td width="85">
                    		 <a title="Clique para gerenciar as datas deste evento" href="#" onclick="wdCarregarFormulario('DataEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Datas</a>
                			</td>
											<td width="30">
                    		 <img src="./image/bt_participante_gd.gif" /> 
                			</td>
											<td width="85">
                    		 <a title="Clique para gerenciar os participantes deste evento" href="#" onclick="wdCarregarFormulario('ParticipanteEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Participantes</a>
                			</td>
                			<td width="30">
                    		 <img src="./image/bt_endereco_gd.gif" /> 
                			</td>
											<td width="85">
                    		 <a title="Clique para gerenciar os endereços deste evento" href="#" onclick="wdCarregarFormulario('EnderecoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Endereços</a>
                			</td>
                			<td width="30">
                    		 <img src="./image/bt_item_gd.gif"/> 
                			</td>
											<td width="85">
                    		 <a title="Clique para gerenciar os produtos deste evento" href="#" onclick="wdCarregarFormulario('ItemEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Produtos</a> 
                			</td>
                			<td width="30">
                    		 <img src="./image/bt_servico_gd.gif"/> 
                			</td>
											<td width="85">
                    		 <a title="Clique para gerenciar os serviços deste evento" href="#" onclick="wdCarregarFormulario('ServicoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Serviços</a> 
                			</td>
                			<td width="30">
                    		 <img src="./image/bt_brinde_gd.gif"/> 
                			</td>
											<td width="85">
                    		 <a title="Clique para gerenciar os brindes deste evento" href="#" onclick="wdCarregarFormulario('BrindeEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Brindes</a> 
                			</td>
                			
              			</tr>
              			
										<tr>
              				<td colspan="2">
												&nbsp;
											</td>
											<td width="30">
                    		 <img src="./image/bt_repertorio_gd.gif" /> 
                			</td>
											<td>
                    		 <a title="Clique para gerenciar o repertório deste evento" href="#" onclick="wdCarregarFormulario('RepertorioEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Repertório</a>
                			</td>
											<td width="30">
                    		 <img src="./image/bt_formando_gd.gif" /> 
                			</td>
											<td width="85">
                    		 <a title="Clique para gerenciar os formandos deste evento" href="#" onclick="wdCarregarFormulario('FormandoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Formandos</a>
                			</td>
											<?php 
											
											//Verifica o nível de acesso do usuário
											if ($nivelAcesso >= 4) {
											
											?>	
																 
                			<td width="30">
                    		 <img src="./image/bt_fotovideo_gd.gif" /> 
                			</td>
											<td>
                    		 <a title="Clique para gerenciar o foto e vídeo deste evento" href="#" onclick="wdCarregarFormulario('FotoVideoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Foto e Vídeo</a>
                			</td>
                			<?php
                			
                			}else{
                				
                			?>
                			
											<td width="30">
                    		<img src="./image/bt_fotovideo_gd_off.gif" title="Opção não habilitada para seu nível de acesso !"/>  
                			</td>
											<td>
                    		 &nbsp;
                			</td>
                			
                			<?php
                			
                			}
                			
                			?>
                      <td width="30">
                    		 <img src="./image/bt_documentos_gd.gif" /> 
                			</td>
											<td colspan="4">
                         <a title="Clique para gerenciar os documentos deste evento" href="#" onclick="wdCarregarFormulario('DocumentosEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Documentos</a>
                      </td>											
              			</tr>              			
          				</table>
				  			</td>
              </tr>              
          </table>

<?php 
//verifica os terceiros já cadastrados para este evento e exibe na tela
$sql_consulta = mysql_query("SELECT
							 ter.id,
							 ter.fornecedor_id,
							 ter.servico_contratado,
							 ter.custo,
							 ter.valor_venda,
							 ter.observacoes,
							 ter.status_contrato,
							 forn.nome as fornecedor_nome
							 FROM eventos_terceiro ter
							 LEFT OUTER JOIN fornecedores forn ON forn.id = ter.fornecedor_id
							 WHERE ter.evento_id = $EventoId
							 ORDER by ter.status_contrato, forn.nome
							 ");

$registros = mysql_num_rows($sql_consulta); 

?>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>  
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width="440"><br/><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Terceiros Cadastrados para o Evento:</span></td>
			  </tr>
			</table>
  	</td>
  </tr>
  <tr>
    <td>
      <table id="4" width="100%" align="left" border="0" cellspacing="0" cellpadding="0" class="listView">

  		<?php
      //Caso houverem registros
    	if ($registros > 0) { 

      	//Exibe o cabeçalho da tabela
				echo "
        <tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
		      <td width='300'>&nbsp;Terceiro/Fornecedor</td>
 		      <td>Serviço Prestado</td>
			<td width='60'><div align='right' style='padding-right: 5px'>Custo</div></td>
			<td width='60'><div align='right' style='padding-right: 5px'>Venda</div></td>
	        <td width='20'><div align='left'>&nbsp;</div></td>
        </tr>
    	";}
    	
		  //Caso não houverem registros
		  if ($registros == 0) { 
	
		  //Exibe uma linha dizendo que nao registros
		  echo "
		  <tr height='24'>
	      <td colspan='6' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1'>
			  	<font color='#33485C'><strong>Não há terceiros cadastrados para este evento</strong></font>
				</td>
		  </tr>	
		  ";	  
		  } else {
		  
			echo "<tr height='18' valign='middle'>
							<td colspan='5'>
								<span style='font-size: 14px; color: blue'><b>&nbsp;A Contratar:</b></span>
							</td>				
						</tr>";
						
						$valor_quebra = 1;
		  	
	  	}

		//Cria o array e o percorre para montar a listagem dinamicamente
    while ($dados_consulta = mysql_fetch_array($sql_consulta)){    	   	
    
			if($dados_consulta[status_contrato] != $valor_quebra){
				
        if ($dados_consulta[status_contrato] == 2){
        
  				echo "<tr height='18' valign='middle'>
  								<td colspan='5' style='padding-top: 8px'>
  									<span style='font-size: 14px; color: red'><b>&nbsp;Contratado:</b></span>
  								</td>				
  							</tr>";
  						
  						$valor_quebra = 2;
              
        } else if ($dados_consulta[status_contrato] == 3){
          
          echo "<tr height='18' valign='middle'>
  								<td colspan='5' style='padding-top: 8px'>
  									<span style='font-size: 14px'><b>&nbsp;Cancelado:</b></span>
  								</td>				
  							</tr>";
  						
  						$valor_quebra = 3;
          
        }
		  	
	  	}	
				
			
			?>

      <tr height="20" valign="middle">
        <td valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" style="padding-bottom: 1px; padding-left: 15px">
          <font color="#CC3300" size="2" face="Tahoma"><a title="Clique para editar dados deste terceiro" href="javascript: void(0);" onclick="wdCarregarFormulario('TerceiroEventoAltera.php?EventoId=<?php echo $EventoId ?>&Id=<?php echo $dados_consulta[id] ?>&headers=1','conteudo')"><?php echo $dados_consulta['fornecedor_nome']; ?></a></font>        
				</td>
        <td>
          <?php echo $dados_consulta[servico_contratado] ?>
				</td>
				<td align="right" style="padding-right: 8px">
					<?php echo "R$ " . number_format($dados_consulta[custo], 2, ",", ".") ?>
				</td>
				<td align="right" style="padding-right: 8px">
					<?php echo "R$ " . number_format($dados_consulta[valor_venda], 2, ",", ".") ?>
				</td>				
				<td valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" style="padding-top: 3px">
					<img src="image/grid_exclui.gif" alt="Clique para excluir este terceiro" onclick="if(confirm('Confirma a exclusão deste Terceiro ?\n\nCaso tenha alguma conta a pagar vinculada a este lançamento de terceiro de evento, esta será excluída automaticamente.')) {wdCarregarFormulario('TerceiroEventoExclui.php?TerceiroId=<?php echo $dados_consulta[id] ?>&EventoId=<?php echo $EventoId ?>','conteudo')}" style="cursor: pointer">
				</td>					        
  	  </tr>
		  <?php
					
					//Verifica se a variável de observações contem algum valor
					if ($dados_consulta[observacoes] != ""){
						echo "<tr><td colspan='6' style='padding-left: 40px'><b>Observações:</b>&nbsp;$dados_consulta[observacoes]</td></tr>";
						
					}

		  //Fecha o WHILE
		  }
		  ?>
						</table>
					</td>
				</tr>
			</table>
			<br/>
			<span class="TituloModulo">Assinatura Digital:</span>
			<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">	
				<tr>
					<td valign="top" width="120" class="dataLabel">Última Alteração:</td>
					<td class="tabDetailViewDF">
						<?php 
							
							//Exibe o timestamp do cadastro da conta
							echo TimestampMySQLRetornar($dados_evento[terceiros_timestamp]) 
						
						?>					
					</td>
					<td class="dataLabel">Operador:</td>
					<td class="tabDetailViewDF" width="200">
						<?php echo $dados_evento[operador_nome]	?>					
					</td>
				</tr>                 
			</table>														           
			<br/>
			<form id="form" name="cadastro" action="sistema.php?ModuloNome=TerceiroEventoCadastra" method="post" onsubmit="return valida_form()">
			<table cellspacing="0" cellpadding="0" width="520" border="0">
				<tr>
					<td style="PADDING-BOTTOM: 2px">
						<input name="Submit" type="submit" class="button" id="Submit" title="Salva o registro atual" value="Salvar Terceiro" />
						<input class="button" title="Limpa o conteúdo dos campos digitados" name="Reset" type="reset" id="Reset" value="Limpar Campos" />
						<input name="EventoId" type="hidden" value="<?php echo $EventoId ?>" />
					</td>
				</tr>
			</table>          
			<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
				<tr>
					<td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="20">
						<table cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados do terceiro do evento e clique em [Salvar Terceiro] </td>
							</tr>
						</table>             
			 		 </td>
				</tr>
				<tr>
					<td class="dataLabel" width="20%">
						<span class="dataLabel">Fornecedor:</span>             
					</td>
					<td colspan="3" class="tabDetailViewDF">           
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr valign="middle">
								<td width="360" height="20">
									<select name="cmbFornecedorId" id="cmbFornecedorId" style="width:350px">
										<option value="0">Selecione uma Opção</option>
						 				 <?php 
											 //Monta o while para gerar o combo de escolha
											 while ($lookup_fornecedor = mysql_fetch_object($dados_fornecedor)) { 
										 ?>
										<option value="<?php echo $lookup_fornecedor->id ?>"><?php echo $lookup_fornecedor->nome ?> </option>
										<?php } ?>
									</select>
								</td>
							</tr>
						</table>           					 					   						 
					</td>
				</tr>
				<tr>
					<td width="140" class="dataLabel">Serviço Contratado:</td>
					<td colspan="3" valign="middle" class="tabDetailViewDF">
						<input name="edtServicoContratado" id="edtServicoContratado" type="text" class="requerido" style="width: 300px" maxlength="80" title="Informe o serviço contratado do terceiro" />
					</td>
				</tr>
				<tr>
					<td width="140" class="dataLabel">Status:</td>
					<td colspan="3" valign="middle" class="tabDetailViewDF">
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr valign="middle">
								<td width="120" height="20">
									<input name="edtStatusContrato" type="radio" value="1" checked="checked" />&nbsp;A Contratar
								</td>
								<td width="120" height="20">
									<input name="edtStatusContrato" type="radio" value="2" />&nbsp;Contratado
								</td>
								<td height="20">
									<input name="edtStatusContrato" type="radio" value="3" disabled="disabled" />&nbsp;Cancelado <span style="color: #990000">(Disponível somente na alteração)</span>
								</td>	                                  
							</tr>
						</table>	
					</td>
				</tr>
				<tr>
					<td width="140" valign="top" class="dataLabel">Custo no Evento:</td>
					<td colspan="3" class="tabDetailViewDF">
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr>
								<td colspan="2">               	
									<?php
										//Cria um objeto do tipo WDEdit 
										$objWDComponente = new WDEditReal();
										
										//Define nome do componente
										$objWDComponente->strNome = "edtCusto";
										//Define o tamanho do componente
										$objWDComponente->intSize = 16;
										//Busca valor definido no XML para o componente
										$objWDComponente->strValor = "";
										//Busca a descrição do XML para o componente
										$objWDComponente->strLabel = "";
										//Determina um ou mais eventos para o componente
										$objWDComponente->strEvento = "";
										//Define numero de caracteres no componente
										$objWDComponente->intMaxLength = 14;
										
										//Cria o componente edit
										$objWDComponente->Criar(); 
										
									?>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<input name="chkCriarConta" type="checkbox" id="chkCriarConta" value="1" title="Marque caso desejar que o sistema efetue o lançamento de uma conta a pagar automaticamente para este terceiro" onclick="marca_conta()" />
									Inserir uma conta a pagar para este terceiro. 
								</td>
							</tr>
							<tr>
								<td width="100" class="dataLabel">Vencimento</td>
								<td>
									<?php
										
										//Gera a variável numerica do dia da semana do evento em si
										$dia_semana = date('w',strtotime($dados_evento["data_realizacao"]));
										
										//Efetua o switch para criar o valor do vencimento para a quarta feira subsequente
										switch ($dia_semana) 
										{
											//Caso o vencimento cair num domingo
											case 0: 
												//Cria a nova data prorrogada
												$data_vencto = som_data(DataMySQLRetornar($dados_evento["data_realizacao"]), 3);
											break;
											//Caso o vencimento cair na segunda
											case 1: 
												//Cria a nova data prorrogada
												$data_vencto = som_data(DataMySQLRetornar($dados_evento["data_realizacao"]), 2);
											break;
											//Caso o vencimento cair na terça
											case 2: 
												//Cria a nova data prorrogada
												$data_vencto = som_data(DataMySQLRetornar($dados_evento["data_realizacao"]), 1);
											break;
											//Caso o vencimento cair numa quarta
											case 3: 
												//Cria a nova data prorrogada
												$data_vencto = som_data(DataMySQLRetornar($dados_evento["data_realizacao"]), 7);
											break;
											//Caso o vencimento cair numa quinta
											case 4: 
												//Cria a nova data prorrogada
												$data_vencto = som_data(DataMySQLRetornar($dados_evento["data_realizacao"]), 6);
											break;
											//Caso o vencimento cair numa sexta
											case 5: 
												//Cria a nova data prorrogada
												$data_vencto = som_data(DataMySQLRetornar($dados_evento["data_realizacao"]), 5);
											break;
											//Caso o vencimento cair num sábado
											case 6: 
												//Cria a nova data prorrogada
												$data_vencto = som_data(DataMySQLRetornar($dados_evento["data_realizacao"]), 4);
											break;
										}
										
										//Define a data do formulário
										$objData->strFormulario = "cadastro";  
										//Nome do campo que deve ser criado
										$objData->strNome = "edtDataVencimento";
										//Valor a constar dentro do campo (p/ alteração)
										$objData->strValor = $data_vencto;
										//Define o tamanho do campo 
										//$objData->intTamanho = 15;
										//Define o número maximo de caracteres
										//$objData->intMaximoCaracter = 20;
										//define o tamanho da tela do calendario
										//$objData->intTamanhoCalendario = 200;
										//Cria o componente com seu calendario para escolha da data
										$objData->CriarData();
										
										echo "&nbsp;(4ª)";
									
									?>
								</td>
							</tr>
							<tr>
								<td width="100" class="dataLabel">Conta-caixa:</td>
								<td valign="middle">
									<select name="cmbSubgrupoId" id="cmbSubgrupoId" style="width:350px">
										<option value="0">Selecione uma Opção</option>
					 				 	<?php 
										 	//Monta o while para gerar o combo de escolha
										 	while ($lookup_subgrupo = mysql_fetch_object($dados_subgrupo)) { 
									 	?>
										<option value="<?php echo $lookup_subgrupo->id ?>"><?php echo $lookup_subgrupo->nome ?> </option>
										<?php } ?>
									</select>						 						 
								</td>
							</tr>
							<tr>
								<td width="100" class="dataLabel">Centro de Custo:</td>
								<td colspan="4" valign="middle">
									<select name="cmbGrupoId" id="cmbGrupoId" style="width:350px">
										<option value="0">Selecione uma Opção</option>
						 				 <?php 
											 //Monta o while para gerar o combo de escolha
											 while ($lookup_grupo = mysql_fetch_object($dados_grupo)) { 
										 ?>
										<option value="<?php echo $lookup_grupo->id ?>"><?php echo $lookup_grupo->nome ?> </option>
										<?php } ?>
									</select>						 						 
								</td>
							</tr>
						</table>              
					</td>
				</tr>
				<tr>
					<td width="140" valign="top" class="dataLabel">Valor de Venda:</td>
					<td colspan="3" class="tabDetailViewDF">
						<?php
							
							//Cria um objeto do tipo WDEdit 
							$objWDComponente = new WDEditReal();
							
							//Define nome do componente
							$objWDComponente->strNome = "edtValorVenda";
							//Define o tamanho do componente
							$objWDComponente->intSize = 16;
							//Busca valor definido no XML para o componente
							$objWDComponente->strValor = "";
							//Busca a descrição do XML para o componente
							$objWDComponente->strLabel = "";
							//Determina um ou mais eventos para o componente
							$objWDComponente->strEvento = "";
							//Define numero de caracteres no componente
							$objWDComponente->intMaxLength = 14;
							
							//Cria o componente edit
							$objWDComponente->Criar(); 
							
						?>
					</td>
				</tr>          
				<tr>
					<td valign="top" class="dataLabel">Informa&ccedil;&otilde;es Complementares:</td>
					<td colspan="3" class="tabDetailViewDF">
					   <textarea name="edtObservacoes" wrap="virtual" class="datafield" id="edtObservacoes" style="width: 100%; height: 130px"></textarea>
			  		</td>
				</tr>
	   		</table>
		</td>
	</tr>
</table>  	 

</tr>
</table>
</form>