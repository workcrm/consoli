<?php 
###########
## Módulo para cadastro de documentos
## Criado: 10/01/2011 - Maycon Edinger
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

//pesquisa as diretivas do usuário
$sql_usuario = "SELECT * FROM usuarios WHERE usuario_id = $usuarioId";													  													  
							  
//Executa a query
$resultado_usuario = mysql_query($sql_usuario);

//Monta o array dos campos
$dados_usuario = mysql_fetch_array($resultado_usuario);

//Recupera dos dados do evento
$sql_evento = mysql_query("SELECT 
							eve.id,
							eve.nome,
							eve.descricao,
							eve.status,
							eve.cliente_id,
							eve.responsavel,
							eve.contato1,
							eve.grupo_id,
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
							cli.nome as cliente_nome,
							gru.nome as grupo_nome
							FROM eventos eve 
							INNER JOIN clientes cli ON cli.id = eve.cliente_id
							LEFT OUTER JOIN grupo_conta gru ON gru.id = eve.grupo_id
							WHERE eve.id = '$EventoId'");
							

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

switch ($dados_evento[grupo_id]) 
{
	case 1: $grupo_status = "Consoli Rio do Sul"; break;
	case 2: $grupo_status = "Consoli Joinville"; break;
	case 3: $grupo_status = "Gerri Adriani Consoli ME"; break;	
}

//Monta o lookup da tabela de tipos de documentos
//Monta o SQL
$lista_tipo = "SELECT id, nome FROM tipos_documento WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";

//Executa a query
$dados_tipo = mysql_query($lista_tipo);

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
	if (Form.edtData.value == 0) 
	{
		alert("É necessário informar a data para o Evento !");
		Form.edtData.focus();
		return false;
	}
	if (Form.edtDescricao.value == 0) 
	{
		alert("É necessário informar a descricao da data do Evento !");
		Form.edtDescricao.focus();
		return false;
	}
	if (Form.edtAnexo.value == 0) 
	{
		alert("É necessário selecionar um anexo a salvar para o Evento !");
		Form.edtAnexo.focus();
		return false;
	}
	return true;
}
</script>
<link rel="stylesheet" type="text/css" href="include/workStyle.css">
</head>
<body>

<table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'>
	<tr>
		<td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width='440'><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Cadastramento de Documentos do Evento</span></td>
				</tr>
				<tr>
					<td colspan='5'>
						<img src="image/bt_espacohoriz.gif" width="100%" height="12">
					</td>
				</tr>
			</table>

			<table id='2' width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
				<tr>
					<td width='100%' class='text'>

						<?php
						          
							//Recupera os valores vindos do formulário e armazena nas variaveis
							if($_POST["Submit"])
							{
            
								//Caso tiver um nome de arquivo, armazena numa variável
								$arq = $_FILES['edtAnexo']['name'];
        	          	
								$arquivo = $_FILES["edtAnexo"];
          
								//Diretório onde a imagem será salva
								$pasta = "documentos/". $arq;
				
								//Faz o upload da imagem
								move_uploaded_file($arquivo["tmp_name"], $pasta);
          
								$edtEmpresaId = $empresaId;
								$edtEventoId = $_POST["EventoId"];
								$edtData = DataMySQLInserir($_POST["edtData"]);
								$cmbTipoDocumentoId = $_POST["cmbTipoDocumentoId"];
								$edtDescricao = $_POST["edtDescricao"];
								$edtObservacoes = $_POST["edtObservacoes"];

								//Monta o sql e executa a query de inserção
								$sql = mysql_query("
													INSERT INTO eventos_documento (
													empresa_id, 
													evento_id,
													data,								
													tipo_documento_id,
													descricao,
													observacoes,
													arquivo,
													cadastro_timestamp,
													cadastro_operador_id

													) VALUES (

													'$edtEmpresaId',
													'$edtEventoId',
													'$edtData',
													'$cmbTipoDocumentoId',
													'$edtDescricao',
													'$edtObservacoes',
													'$pasta',
													now(),
													'$edtOperadorId'
													);");
								
								//Configura a assinatura digital
								$sql = mysql_query("UPDATE eventos SET documentos_timestamp = now(), documentos_operador_id = $usuarioId WHERE id = $edtEventoId"); 
	
								//Exibe a mensagem de inclusão com sucesso
								echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Documento cadastrado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
        
							}
						?>
						<table class='tabDetailView' cellspacing='0' cellpadding='0' width='100%' border='0'>
							<tr>
								<td class='dataLabel' width='15%'>Nome do Evento: </td>
								<td colspan='5' class="tabDetailViewDF">
									<span style="font-size: 16px; color: #990000"><b><?php echo $dados_evento["nome"] ?></b></span>
								</td>
							</tr>
							<tr>
								<td valign="top" class='dataLabel'>Descri&ccedil;&atilde;o:</td>
								<td colspan="5" valign="middle" class="tabDetailViewDF">
									<?php echo nl2br($dados_evento["descricao"]) ?>
								</td>
							</tr>
							<tr>
								<td valign="top" class='dataLabel'>Status:</td>
								<td colspan="5" valign="middle" class="tabDetailViewDF">
									<?php echo $desc_status ?>
								</td>
							</tr>
							<tr>
								<td valign="top" class='dataLabel'>Data:</td>
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
								<td valign="top" class='dataLabel'>Cliente:</td>
								<td colspan="5" valign="middle" class="tabDetailViewDF">
									<?php echo $dados_evento["cliente_nome"] ?>
								</td>
							</tr>
							<tr>
								<td valign="top" class='dataLabel'>Grupo:</td>
								<td colspan="5" valign="middle" class="tabDetailViewDF">
									<?php echo $grupo_status ?>
								</td>
							</tr>               
							<tr>
								<td class='dataLabel'>Respons&aacute;vel:</td>
								<td colspan="5" valign="middle" class="tabDetailViewDF">
									<?php echo $dados_evento["responsavel"] ?>								
								</td>
								</td>
							<tr>
								<td valign="top" class='dataLabel'>Contatos:</td>
								<td colspan="5" valign="middle" class="tabDetailViewDF">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tr valign="middle">
											<td width="300" height='20'>
												Nome:
											</td>
											<td width="260" height='20'>
												Observações:
											</td>
											<td height="20">
												Telefone:
											</td>
										</tr>
										<tr valign="middle">
											<td height='20'>
												<span style="font-size: 12px"><?php echo $dados_evento[contato1] ?></span>
											</td>
											<td height='20'>
												<span style="font-size: 12px"><?php echo $dados_evento[contato_obs1] ?></span>
											</td>
											<td height="20">
												<span style="font-size: 12px"><?php echo $dados_evento[contato_fone1] ?></span>
											</td>
										</tr>
										<tr valign="middle">
											<td height='20'>
												<span style="font-size: 12px"><?php echo $dados_evento[contato2] ?></span>                   
											</td>
											<td height='20'>
												<span style="font-size: 12px"><?php echo $dados_evento[contato_obs2] ?></span>
											</td>
											<td height="20">
												<span style="font-size: 12px"><?php echo $dados_evento[contato_fone2] ?></span>
											</td>
										</tr>
										<tr valign="middle">
											<td height='20'>
												<span style="font-size: 12px"><?php echo $dados_evento[contato3] ?></span>
											</td>
											<td height='20'>
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
									<table width='100%' cellpadding='0' cellspacing='0' border='0' >
										<tr valign='middle'>
											<td colspan="8" style="padding-bottom: 4px">
												<span style="font-size: 11px">Tarefas Adicionais:</span>
											</td>
										</tr>
										<tr valign='middle'>
											<td width='30'>
												<img src='./image/bt_evento_gd.gif'/> 
											</td>
											<td width='85'>
												<a title='Clique para exibir o detalhamento deste evento' href='#' onclick="wdCarregarFormulario('EventoExibe.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Detalhamento</a> 
											</td>
											<td width="30">
												<img src="./image/bt_data_evento_gd.gif" />
											</td>
											<td width="85">
												<a title="Clique para gerenciar as datas deste evento" href="#" onclick="wdCarregarFormulario('DataEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Datas</a>
											</td>
											<td width='30'>
												<img src='./image/bt_participante_gd.gif' /> 
											</td>
											<td width='85'>
												<a title='Clique para gerenciar os participantes deste evento' href='#' onclick="wdCarregarFormulario('ParticipanteEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Participantes</a>
											</td>
											<td width='30'>
												<img src='./image/bt_endereco_gd.gif'/> 
											</td>
											<td width='85'>
												<a title='Clique para gerenciar os endereços deste evento' href='#' onclick="wdCarregarFormulario('EnderecoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Endereços</a>
											</td>
											<td width='30'>
												<img src='./image/bt_item_gd.gif'/> 
											</td>
											<td width='85'>
												<a title='Clique para gerenciar os produtos deste evento' href='#' onclick="wdCarregarFormulario('ItemEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Produtos</a> 
											</td>                			
											<td width='30'>
												<img src='./image/bt_servico_gd.gif'/> 
											</td>
											<td width='85'>
												<a title='Clique para gerenciar os serviços deste evento' href='#' onclick="wdCarregarFormulario('ServicoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Serviços</a> 
											</td>                			
											<td width='30'>
												<img src='./image/bt_brinde_gd.gif'/> 
											</td>
											<td width='85'>
												<a title='Clique para gerenciar os brindes deste evento' href='#' onclick="wdCarregarFormulario('BrindeEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Brindes</a> 
											</td>											
										</tr>              			
										<tr>
											<td colspan="2">&nbsp;</td>
											<td width='30'>
												<img src='./image/bt_repertorio_gd.gif' /> 
											</td>
											<td width="85">
												<a title='Clique para gerenciar o repertório deste evento' href='#' onclick="wdCarregarFormulario('RepertorioEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Repertório</a>
											</td>
											<td width='30'>
												<img src='./image/bt_terceiro_gd.gif'/> 
											</td>
											<td>
												<a title='Clique para gerenciar os terceiros deste evento' href='#' onclick="wdCarregarFormulario('TerceiroEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Terceiros</a> 
											</td>
											<td width='30'>
												<img src='./image/bt_formando_gd.gif' /> 
											</td>
											<td width='85'>
												<a title='Clique para gerenciar os formandos deste evento' href='#' onclick="wdCarregarFormulario('FormandoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Formandos</a>
											</td>				 
											<td width='30'>
												<img src='./image/bt_fotovideo_gd.gif' /> 
											</td>
											<td>
												<a title='Clique para gerenciar o foto e vídeo deste evento' href='#' onclick="wdCarregarFormulario('FotoVideoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Foto e Vídeo</a>
											</td>									
										</tr>              			
									</table>
								</td>
							</tr>              
						</table>
						<?php 

							//Monta um sql para pesquisar se há documentos para este evento
							$sql_datas = mysql_query("SELECT
								                          doc.id,
								                          doc.data,
								                          doc.descricao,
								                          doc.observacoes,
								                          doc.arquivo,
								                          tipo.nome AS tipo_documento_nome
							                          FROM 
							                          	eventos_documento doc 
							                          LEFT OUTER JOIN 
							                          	tipos_documento tipo ON tipo.id = doc.tipo_documento_id
							                          WHERE 
							                          	doc.evento_id = '$EventoId' 
							                          ORDER BY 
							                          	doc.data");
																					 
							$registros = mysql_num_rows($sql_datas); 														

						?>

<table width='100%' border='0' cellpadding='0' cellspacing='0'>
  <tr>  
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width='440'><br/><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Documentos vinculados ao Evento:</span></td>
			  </tr>
			</table>
  	</td>
  </tr>
  <tr>
    <td>
      <table id="4" width='100%' align='left' border='0' cellspacing='0' cellpadding='0' class="listView">
  		<?php

      //Caso houverem registros
    	if ($registros > 0) { 

      	//Exibe o cabeçalho da tabela
				echo "<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
					      <td width='70' style='padding-left: 8px'>Data</td>
			 		      <td>Descrição</td>
			          <td width='250' >Tipo do Documento</td>
			 		      <td width='60'>&nbsp;</td>
			          <td width='20'>&nbsp;</td>
			        </tr>";

    	}
    	
		  //Caso não houverem registros
		  if ($registros == 0) { 
	
			  //Exibe uma linha dizendo que nao registros
			  echo "<tr height='24'>
					      <td colspan='6' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1'>
							  	<font color='#33485C'><b>Não há documentos vinculados a este evento</b></font>
								</td>
						  </tr>";	  
		  }     	

			//Cria o array e o percorre para montar a listagem das categorias
	    while ($dados_consulta = mysql_fetch_array($sql_datas))
	    {

				//Exibe a descrição da categoria
				?>
	      <tr valign='middle'>
	        <td valign='middle' nowrap='nowrap' bgcolor='#fdfdfd' style="padding-left: 8px">
	          <?php echo DataMySQLRetornar($dados_consulta[data]) ?>
					</td>
					<td valign='middle' bgcolor='#fdfdfd' style="padding-bottom: 1px;">
	          <font color='#CC3300' size='2' face="Tahoma">
							<a title="Clique para alterar este documento" href="#" onclick="wdCarregarFormulario('DocumentosEventoAltera.php?Id=<?php echo $dados_consulta[id] ?>&EventoId=<?php echo $EventoId ?>&headers=1','conteudo')"><?php echo $dados_consulta['descricao']; ?></a>
						</font>        
					</td>
					<td valign='middle' bgcolor='#fdfdfd' style="padding-top: 3px">
						<?php echo $dados_consulta["tipo_documento_nome"] ?>
					</td>
	        <td align="right" valign='middle' bgcolor='#fdfdfd' style="padding-top: 3px; padding-right: 6px">
						<font color='#CC3300' size='2' face="Tahoma">
							<a title="Clique para visualizar este documento" href="<?php echo $dados_consulta["arquivo"] ?>" target="_blank">[Visualizar]</a>
						</font> 
					</td>
	        <td valign='middle' bgcolor='#fdfdfd' style="padding-top: 3px">
						<?php
						
							if ($dados_usuario["evento_financeiro"] == 1 || $usuarioNome == "Karina"){
							
								?>
								<img src="image/grid_exclui.gif" alt="Clique para excluir este documento" onclick="if(confirm('Confirma a exclusão deste Documento ?')) {wdCarregarFormulario('DocumentosEventoExclui.php?DocumentoId=<?php echo $dados_consulta[id] ?>&EventoId=<?php echo $EventoId ?>','conteudo')}" style="cursor: pointer">
								<?php

							}

						?>
					</td>        					
	  	  </tr>
	  	  <tr>
	  	  	<td colspan="5" style="padding-left: 80px">
	  	  		<?php echo nl2br($dados_consulta['observacoes']) ?>
	  	  	</td>
	  	  </tr>
			  <?php

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
			echo TimestampMySQLRetornar($dados_evento[documentos_timestamp]) 
		?>					
	</td>
  <td class="dataLabel">Operador:</td>
  <td class="tabDetailViewDF" width="200">
		<?php echo $dados_evento[operador_nome]	?>					
	</td>
</tr>                 
</table>
													           
<br/>

<table cellspacing="0" cellpadding="0" width="520" border="0">
  <tr>
    <td width="484">
      <form id="form" name="cadastro" enctype="multipart/form-data" action="sistema.php?ModuloNome=DocumentosEventoCadastra" method="post" onsubmit="return valida_form()">
    </td>
  </tr>
  <tr>
    <td style="PADDING-BOTTOM: 2px">
    	<input name="Submit" type="submit" class="button" id="Submit" title="Salva o registro atual" value='Salvar Data' />
      <input class="button" title="Limpa o conteúdo dos campos digitados" name='Reset' type='reset' id='Reset' value='Limpar Campos' />
			<input name="EventoId" type="hidden" value="<?php echo $EventoId ?>" />
    </td>
    <td width="36" align="right">	  </td>
 </tr>
</table>
           
<table class='tabDetailView' cellspacing='0' cellpadding='0' width='100%' border='0'>
 <tr>
   <td class='listViewPaginationTdS1' style='PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px' colSpan='20'>
     <table cellspacing="0" cellpadding="0" width="100%" border="0">
       <tr>
         <td class='tabDetailViewDL' style='TEXT-ALIGN: left'><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados da data do evento e clique em [Salvar Data] </TD>
       </tr>
     </table>             
   </td>
 </tr>
 <tr>
	 <td width="120" valign="middle" class="dataLabel">Data:</td>
   <td valign="middle" class="tabDetailViewDF">
		 <?php

		    //Define a data do formulário
		    $objData->strFormulario = "cadastro";  
		    //Nome do campo que deve ser criado
		    $objData->strNome = "edtData";
		    //Valor a constar dentro do campo (p/ alteração)
		    $objData->strValor = "";
		    //Define o tamanho do campo 
		    //$objData->intTamanho = 15;
		    //Define o número maximo de caracteres
		    //$objData->intMaximoCaracter = 20;
		    //define o tamanho da tela do calendario
		    //$objData->intTamanhoCalendario = 200;
		    //Cria o componente com seu calendario para escolha da data
		    $objData->CriarData();

			?>				 
	 	</td>
	</tr>
  <tr>
    <td class='dataLabel'>
      <span class="dataLabel">Tipo de Documento:</span>             
	 </td>
   <td class="tabDetailViewDF">                       
			<select name="cmbTipoDocumentoId" id="cmbTipoDocumentoId" style="width:350px">
				<option value="0">Selecione uma Opção</option>
				<?php 

					//Monta o while para gerar o combo de escolha de funcao
					while ($lookup_tipo = mysql_fetch_object($dados_tipo)) { 

						?>
						<option value="<?php echo $lookup_tipo->id ?>"><?php echo $lookup_tipo->nome ?> </option>			               
						<?php 

					//Fecha o while
					} 
				
				?>
			</select>           					 					   						 
	  </td>
  </tr>
  <tr>
    <td class='dataLabel'>Descrição:</td>
    <td class='tabDetailViewDF'>
       <input name="edtDescricao" type="text" class='datafield' id="edtDescricao" style="width: 450px; color: #6666CC; font-weight: bold" maxlength="75">
    </td>
  </tr>          
   <tr>
     <td valign="top" class="dataLabel">Observações:</td>
     <td class="tabDetailViewDF">
		   <textarea name="edtObservacoes" wrap="virtual" class="datafield" id="edtObservacoes" style="width: 100%; height: 130px"></textarea>
		  </td>
    </tr>
    <tr>
      <td valign="top" class="dataLabel">Anexo:</td>
      <td class="tabDetailViewDF">
       <input type="file" size="100" name="edtAnexo" />
      </td>
    </tr>
    </table>
   </td>
 </tr>
</form>
</table>  	 

</tr>
</table>
