<?php 
###########
## Módulo para cadastro de Participantes do evento
## Criado: 31/05/2007 - Maycon Edinger
## Alterado: 22/11/2007 - Maycon Edinger
## Alterações: 
## 28/06/2007 - Implementado rotinas para acessar os módulos de itens e endereços
## 14/07/2007 - Implementado campo para escolha da função do colabor no evento
## 13/08/2007 - Implementado campo para informar o custo do colaborador no evento
## 22/08/2007 - Implementado a exibição do grupo do evento
## 22/11/2007 - Incluído link para gerenciamento de serviços do evento
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) {
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//http://localhost/consoli/sistema.php?ModuloNome=EnderecoEventoCadastra&EventoId=1

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
if($_POST) {
  $EventoId = $_POST["EventoId"]; 
} else {
  $EventoId = $_GET["EventoId"]; 
}

//Converte uma data timestamp de mysql para normal
function TimestampMySQLRetornar($DATA){
  $ANO = 0000;
  $MES = 00;
  $DIA = 00;
  $HORA = "00:00:00";
  $data_array = split("[- ]",$DATA);
  if ($DATA <> ""){
    $ANO = $data_array[0];
    $MES = $data_array[1];
    $DIA = $data_array[2];
		$HORA = $data_array[3];
    return $DIA."/".$MES."/".$ANO. " - " . $HORA;
  }else {
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
													eve.participantes_timestamp,
													eve.participantes_operador_id,
													concat(usu.nome , ' ', usu.sobrenome) as operador_nome,
													cli.nome as cliente_nome,
													gru.nome as grupo_nome
													FROM eventos eve 
													INNER JOIN clientes cli ON cli.id = eve.cliente_id
													LEFT OUTER JOIN grupo_conta gru ON gru.id = eve.grupo_id
													LEFT OUTER JOIN usuarios usu ON usu.usuario_id = eve.participantes_operador_id
													WHERE eve.id = '$EventoId'");

//Cria o array dos dados
$dados_evento = mysql_fetch_array($sql_evento);

//Efetua o switch para o campo de status
switch ($dados_evento[status]) {
  case 0: $desc_status = "Em orçamento"; break;
  case 1: $desc_status = "Em aberto"; break;
	case 2: $desc_status = "Realizado"; break;
  case 3: $desc_status = "<span style='color: red'>Não-Realizado</span>"; break;
} 

switch ($dados_evento[grupo_id]) {
  case 1: $grupo_status = "Consoli Rio do Sul"; break;
	case 2: $grupo_status = "Consoli Joinville"; break;
  case 3: $grupo_status = "Gerri Adriani Consoli ME"; break;	
}

//Monta o lookup da tabela de colaboradores
//Monta o SQL
$lista_colaborador = "SELECT id, nome FROM colaboradores WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_colaborador = mysql_query($lista_colaborador);

//Monta o lookup aa tabela de funcoes
//Monta o SQL
$lista_funcao = "SELECT * FROM funcoes WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_funcao = mysql_query($lista_funcao);
?>

<script language="JavaScript">
//Função que alterna a visibilidade do painel especificado.
function oculta(id){
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function valida_form() {
   var Form;
   Form = document.cadastro;
	 if (Form.cmbColaboradorId.value == 0) {
      alert("É necessário selecionar um colaborador para o Evento !");
      Form.cmbColaboradorId.focus();
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
			    <td width='440'><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Cadastramento de Participantes do Evento</span></td>
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
          if($_POST['Submit']){

          $edtEmpresaId = $empresaId;
          $edtEventoId = $_POST["EventoId"];
          $cmbColaboradorId = $_POST["cmbColaboradorId"];

					//Verifica se vai utilizar a função original do colaborador
					$checaFuncao = $_POST["cmbFuncaoId"];
				
					if ($checaFuncao == 0) {
						
						//Busca a funcao original do colaborador na base
						$sql_funcao = mysql_query("SELECT funcao_id FROM colaboradores WHERE id = '$cmbColaboradorId'");
						$dados_funcao = mysql_fetch_array($sql_funcao);
						
						//Cria a variável da funcaoID com o valor original do colaborador
						$cmbFuncaoId  = $dados_funcao["funcao_id"];		
					} else {
					
						//Cria a variavel com o valor vindo do combo de escolha de função
						$cmbFuncaoId = $_POST["cmbFuncaoId"];
					}
					
          $chkNotificar = $_POST["chkNotificar"];
          $edtCusto = $_POST["edtCusto"];
          $chkCriarConta = $_POST["chkCriarConta"];
          $edtDataVencimento = DataMySQLInserir($_POST["edtDataVencimento"]);
          $edtObservacoes = $_POST["edtObservacoes"];
          $edtOperadorId = $usuarioId;

					//Monta o sql e executa a query de inserção dos clientes
    	    $sql = mysql_query("
                INSERT INTO eventos_participante (
								empresa_id, 
								evento_id,
								colaborador_id,
								funcao_id,
								custo_funcionario,
								notificar,								
								observacoes,
								cadastro_timestamp,
								cadastro_operador_id
				
								) VALUES (
				
								'$edtEmpresaId',
								'$edtEventoId',
								'$cmbColaboradorId',
								'$cmbFuncaoId',
								'$edtCusto',
								'$chkNotificar',
								'$edtObservacoes',
								now(),
								'$edtOperadorId'				
								);");
	
					//Configura a assinatura digital
    	    $sql = mysql_query("UPDATE eventos SET participantes_timestamp = now(), participantes_operador_id = $usuarioId WHERE id = $edtEventoId");
    	    
					//Verifica se o usuário deve ser notificado
					if ($chkNotificar == 1) {
	
						//Monta o sql para verificar se o colaborador possui um usuário no sistema
						$sql_procura_usuario = mysql_query("SELECT usuario_id FROM colaboradores WHERE id = '$cmbColaboradorId'");
		
						//Verifica se encontrou o id do usuario do sistema
						$registros_usuario = mysql_num_rows($sql_procura_usuario);
						
						//Verifica se tem o id > que 1
						if ($registros_usuario > 0) {
						
							//Monta o array dos dados
							$dados_notifica = mysql_fetch_array($sql_procura_usuario);
						
							//Monta os dados para inclusão na agenda
							$usuario_notifica = $dados_notifica["usuario_id"];
							$dia_notifica = substr($dados_evento["data_realizacao"], 8, 2);
							$mes_notifica = substr($dados_evento["data_realizacao"], 5, 2);
							$ano_notifica = substr($dados_evento["data_realizacao"], 0, 4);
							$hora_notifica = $dados_evento["hora_realizacao"];
							$duracao_notifica = $dados_evento["duracao"];
							$atividade_notifica = 3;
							$assunto_notifica = "Notificação de Evento: " . $dados_evento["nome"];
							$prioridade_notifica = 1;
							$categoria_notifica = 3;
							$local_notifica = "Conforme cronograma do evento";
							$descricao_notifica = "Esta é uma notificação automática gerada pelo sistema.</br><a href=\"sistema.php?ModuloNome=EventoExibe&EventoId=$dados_evento[id]\">Clique aqui para visualizar os detalhes completos do evento</a>";						
													
							//Insere um registro na agenda do usuario
							$insere_notificação = mysql_query("INSERT INTO compromissos (
																								usuario_id, 
																								dia, 
																								mes, 
																								ano, 
																								hora, 
																								duracao, 
																								atividade, 
																								assunto, 
																								prioridade, 
																								categoria, 
																								local, 
																								descricao,
																								evento_id
																								
																								) VALUES (							
																								
																								'$usuario_notifica',
																								'$dia_notifica',
																								'$mes_notifica',
																								'$ano_notifica',
																								'$hora_notifica',
																								'$duracao_notifica',
																								'$atividade_notifica',
																								'$assunto_notifica',
																								'$prioridade_notifica',
																								'$categoria_notifica',
																								'$local_notifica',
																								'$descricao_notifica',
																								'$EventoId'
																								)");	
							
						//Fecha o IF do encontrou o usuario
						}

					//Fecha o IF do notificar
					}

					//Verifica se o deve cadastrar uma conta a pagar para o participante
					if ($chkCriarConta == 1) {
					
						//Busca os parâmetros de grupo e subgrupo na tabela de parametros
						$query_parametro = mysql_query("SELECT * FROM parametros_sistema WHERE ativo = '1'");
						
						//Monta o array com os parametros
						$dados_parametro = mysql_fetch_array($query_parametro);
						
						//Monta o sql apra inserir a conta a pagar
						$sql = "INSERT INTO contas_pagar (
										empresa_id, 
										data,
										tipo_pessoa,
										pessoa_id,
										grupo_conta_id,
										subgrupo_conta_id, 
										categoria_id, 
										descricao,
										valor,
										condicao_pgto_id, 
										data_vencimento, 
										situacao,
										observacoes, 
										cadastro_timestamp,
										cadastro_operador_id
						
										) VALUES (
						
										'$edtEmpresaId',
										'$dados_evento[data_realizacao]',
										'3',
										'$cmbColaboradorId',
										'$dados_evento[grupo_id]',
										'$dados_parametro[sub_grupo_id]',
										'$dados_parametro[categoria_conta_id]',
										'Pagamento de Participante do Evento',
										'$edtCusto',
										'$dados_parametro[condicao_pgto_id]',
										'$edtDataVencimento',
										'1',
										'Conta a pagar incluída automaticamente pelo sistema.<br/><br/>Por Participação do Colaborador no Evento:<br/><b>$dados_evento[nome]</b>',
										now(),
										'$edtOperadorId'				
										);";				
						
						//Executa a query de inserção da conta
						$query = mysql_query($sql);	
					
					}

					//Exibe a mensagem de inclusão com sucesso
        	echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Participante cadastrado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
        }
        ?>
          <TABLE class='tabDetailView' cellSpacing='0' cellPadding='0' width='100%' border='0'>
            <TBODY>
              <TR>
                <TD class='dataLabel' width='15%'> Nome do Evento : </TD>
                <TD colspan='5' class=tabDetailViewDF>
									<span style="font-size: 16px; color: #990000"><b><?php echo $dados_evento["nome"] ?></b></span>
				  			</TD>
              </TR>
              <TR>
                <TD valign="top" class='dataLabel'>Descri&ccedil;&atilde;o:</TD>
                <TD colspan="5" valign="middle" class=tabDetailViewDF>
									<?php echo $dados_evento["descricao"] ?>
				  			</TD>
              </TR>
              <TR>
                <TD valign="top" class='dataLabel'>Status:</TD>
                <TD colspan="5" valign="middle" class=tabDetailViewDF>
								  <?php echo $desc_status ?>
				  			</TD>
              </TR>
              <TR>
                <TD valign="top" class='dataLabel'>Data:</TD>
                <TD valign="middle" class=tabDetailViewDF>
									<?php echo DataMySQLRetornar($dados_evento["data_realizacao"]) ?>
				  			</TD>
                <TD valign="middle" class=dataLabel>Hora:</TD>
                <TD width="19%" valign="middle" class=tabDetailViewDF>
									<?php echo $dados_evento["hora_realizacao"] ?>								
				  			</TD>
                <TD width="12%" valign="middle" class=dataLabel>Dura&ccedil;&atilde;o:</TD>
                <TD width="20%" valign="middle" class=tabDetailViewDF>
									<?php echo $dados_evento["duracao"] ?>								
				  			</TD>
              </TR>
              <TR>
                <TD valign="top" class='dataLabel'>Cliente:</TD>
                <TD colspan="5" valign="middle" class=tabDetailViewDF>
									<?php echo $dados_evento["cliente_nome"] ?>
				  			</TD>
              </TR>
							<TR>
                <TD valign="top" class='dataLabel'>Grupo:</TD>
                <TD colspan="5" valign="middle" class=tabDetailViewDF>
									<?php echo $grupo_status ?>
				  			</TD>
              </TR>              
              <TR>
                <TD class='dataLabel'>Respons&aacute;vel:</TD>
                <TD colspan="5" valign="middle" class=tabDetailViewDF>
									<?php echo $dados_evento["responsavel"] ?>								
				  			</TD>
              </TR>
	           <TR>
	             <TD valign="top" class='dataLabel'>Contatos:</TD>
	             <TD colspan="5" valign="middle" class=tabDetailViewDF>
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
	             </TD>
	           </TR>
							<TR>
                <td colspan="6" valign="middle" class=tabDetailViewDF>
									<table width='100%' cellpadding='0' cellspacing='0' border='0' >
              			<tr valign='middle'>
											<td colspan="8" style="padding-bottom: 3px">
                    		 <span style="font-size: 11px">Tarefas Adicionais:</span>
                			</td>
                		</tr>
              			<tr valign='middle'>
											<td width='30'>
                    		 <img src='./image/bt_evento_gd.gif'/> 
                			</td>                			
											<td width='85'>
                    		 <a title='Clique para exibir o detalhamento deste evento' href='#' onClick="wdCarregarFormulario('EventoExibe.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Detalhamento</a>
                			</td>
                			<td width='30'>
                    		 <img src='./image/bt_data_evento_gd.gif' /> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar as datas deste evento' href='#' onClick="wdCarregarFormulario('DataEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Datas</a>
                			</td>
											<td width='30'>
                    		 <img src='./image/bt_endereco_gd.gif' /> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar os endereços deste evento' href='#' onClick="wdCarregarFormulario('EnderecoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Endereços</a>
                			</td>
                			<td width='30'>
                    		 <img src='./image/bt_item_gd.gif'/> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar os produtos deste evento' href='#' onClick="wdCarregarFormulario('ItemEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Produtos</a> 
                			</td>
                			<td width='30'>
                    		 <img src='./image/bt_servico_gd.gif'/> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar os serviços deste evento' href='#' onClick="wdCarregarFormulario('ServicoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Serviços</a> 
                			</td>
                			<td width='30'>
                    		 <img src='./image/bt_terceiro_gd.gif'/> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar os terceiros deste evento' href='#' onClick="wdCarregarFormulario('TerceiroEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Terceiros</a> 
                			</td>
                			<td width='30'>
                    		 <img src='./image/bt_brinde_gd.gif'/> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar os brindes deste evento' href='#' onClick="wdCarregarFormulario('BrindeEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Brindes</a> 
                			</td>                			
              			</tr>
              			
										<tr>
              				<td colspan="2">
												&nbsp;
											</td>
											<td width='30'>
                    		 <img src='./image/bt_repertorio_gd.gif' /> 
                			</td>
											<td>
                    		 <a title='Clique para gerenciar o repertório deste evento' href='#' onClick="wdCarregarFormulario('RepertorioEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Repertório</a>
                			</td>
											<td width='30'>
                    		 <img src='./image/bt_formando_gd.gif' /> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar os formandos deste evento' href='#' onClick="wdCarregarFormulario('FormandoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Formandos</a>
                			</td>
											<?php 
											
											//Verifica o nível de acesso do usuário
											if ($nivelAcesso >= 4) {
											
											?>	
																 
                			<td width='30'>
                    		 <img src='./image/bt_fotovideo_gd.gif' /> 
                			</td>
											<td>
                    		 <a title='Clique para gerenciar o foto e vídeo deste evento' href='#' onclick="wdCarregarFormulario('FotoVideoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Foto e Vídeo</a>
                			</td>
                			<?php
                			
                			}else{
                				
                			?>
                			
											<td width='30'>
                    		<img src='./image/bt_fotovideo_gd_off.gif' title='Opção não habilitada para seu nível de acesso !'/>  
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
//verifica os participantes já cadastrados para este evento e exibe na tela
$sql_consulta = mysql_query("SELECT
														 par.id,
														 par.colaborador_id,
														 par.funcao_id,
														 par.notificar,
														 par.custo_funcionario,
														 col.nome as colaborador_nome,
														 col.usuario_id,
														 fun.nome as funcao_nome
														 FROM eventos_participante par
														 INNER JOIN colaboradores col ON col.id = par.colaborador_id
														 LEFT OUTER JOIN funcoes fun ON fun.id = par.funcao_id
														 WHERE par.evento_id = '$EventoId'
														 ORDER by col.nome
														 ");

$registros = mysql_num_rows($sql_consulta); 

?>

<table width='100%' border='0' cellpadding='0' cellspacing='0'>
  <tr>  
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width='440'></br><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Participantes Cadastrados para o Evento:</span></td>
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
				echo "
        <tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
		      <td width='300'>&nbsp;Participante/Colaborador</td>
 		      <td width='260'>Função no Evento</td>
	        ";
	        
					//Verifica o nível de acesso do usuário
				  if ($nivelAcesso >= 4) {
						echo "<td width='80'><div align='right' style='padding-right: 8px'>Custo</div></td>";
					}
					
					echo "
	        <td width='20'><div align='left'>&nbsp;</div></td>
	        <td width='20'><div align='left'>&nbsp;</div></td>
        </tr>
    	";}
    	
		  //Caso não houverem registros
		  if ($registros == 0) { 
	
		  //Exibe uma linha dizendo que nao registros
		  echo "
		  <tr height='24'>
	      <td colspan='6' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1'>
			  	<font color='#33485C'><strong>Não há participantes cadastrados para este evento</strong></font>
				</td>
		  </tr>	
		  ";	  
		  }     	

		//Cria o array e o percorre para montar a listagem dinamicamente
    while ($dados_consulta = mysql_fetch_array($sql_consulta)){
    	
		//Efetua o switch para exibir a imagem para quando o cadastro estiver para notificar
		switch ($dados_consulta[notificar]) {
		 	case 00: $ativo_figura = "";	break;
		  case 01: $ativo_figura = "<img src='./image/grid_ativo.gif' alt='Participante Notificado' />";	break;       	
		}    	
    
?>
      <tr height="20" valign='middle'>
        <td valign='middle' nowrap='nowrap' bgcolor='#fdfdfd' style="padding-bottom: 1px">
          &nbsp;<font color='#CC3300' size='2' face="Tahoma"><a title="Clique para exibir os dados deste participante" href="#" onclick="wdCarregarFormulario('ColaboradorExibe.php?ColaboradorId=<?php echo $dados_consulta[colaborador_id] ?>&headers=1','conteudo')"><?php echo $dados_consulta['colaborador_nome']; ?></a></font>        
				</td>
        <td>
          <?php echo $dados_consulta[funcao_nome] ?>
				</td>
        <?php
					//Verifica o nível de acesso do usuário
				  if ($nivelAcesso >= 4) {
				?>
				<td align="right" style="padding-right: 8px">
          <?php echo "R$ " . number_format($dados_consulta[custo_funcionario], 2, ",", ".") ?>
				</td>        
				<?php
					//Fecha o if
					}
				?>
				<td valign='middle' nowrap='nowrap' bgcolor='#fdfdfd'>
         <?php echo $ativo_figura ?>
				</td>											
        <td valign='middle' nowrap='nowrap' bgcolor='#fdfdfd' style="padding-top: 3px">
					<img src="image/grid_exclui.gif" alt="Clique para excluir este participante" onclick="if(confirm('Confirma a exclusão deste Participante ?')) {wdCarregarFormulario('ParticipanteEventoExclui.php?ParticipanteId=<?php echo $dados_consulta[colaborador_id] ?>&UsuarioNotificaId=<?php echo $dados_consulta[usuario_id] ?>&EventoId=<?php echo $EventoId ?>','conteudo')}" style="cursor: pointer">
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
							echo TimestampMySQLRetornar($dados_evento[participantes_timestamp]) 
						?>					
					</td>
          <td class="dataLabel">Operador:</td>
          <td class="tabDetailViewDF" width="200">
						<?php echo $dados_evento[operador_nome]	?>					
					</td>
        </tr>                 
  	  </table>

														           
          <br/>
          
          <table cellspacing='0' cellpadding='0' width='520' border='0'>
          <tr>
            <td width="484">
              <form id='form' name='cadastro' action='sistema.php?ModuloNome=ParticipanteEventoCadastra' method='post' onSubmit='return valida_form()'>
            </td>
          </tr>
          <tr>
	        <td style="PADDING-BOTTOM: 2px">
	        	<input name="Submit" type='submit' class="button" id="Submit" title="Salva o registro atual" value='Salvar Participante'>
            <input class="button" title="Limpa o conteúdo dos campos digitados [Alt+L]" accessKey='L' name='Reset' type='reset' id='Reset' value='Limpar Campos'>
						<input name="EventoId" type="hidden" value="<?php echo $EventoId ?>" />
          </TD>
          <TD width="36" align=right>	  </TD>
	       </TR>
         </TBODY>
         </TABLE>
           
         <TABLE class='tabDetailView' cellSpacing='0' cellPadding='0' width='100%' border='0'>
         <TBODY>
           <TR>
             <TD class='listViewPaginationTdS1' style='PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px' colSpan='20'>
               <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
               <TBODY>
                 <TR>
                   <TD class='tabDetailViewDL' style='TEXT-ALIGN: left'><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados do participante do evento e clique em [Salvar Participante] </TD>
			     </TR>
		       </TBODY>
		       </TABLE>             
			 		</TD>
	      </TR>
         <TR>
           <TD class='dataLabel' width='20%'>
             <span class="dataLabel">Colaborador:</span>             
					 </TD>
           <TD colspan='3' class=tabDetailViewDF>           
             <table width="100%" cellpadding="0" cellspacing="0">
               <tr valign="middle">
                 <td width="360" height='20'>
										<select name="cmbColaboradorId" id="cmbColaboradorId" style="width:350px">
			               <option value="0">Selecione uma Opção</option>
						 				 <?php 
											 //Monta o while para gerar o combo de escolha de funcao
											 while ($lookup_colaborador = mysql_fetch_object($dados_colaborador)) { 
											 //Efetua a pesquisa para ver se o colaborador já está no evento
											 $sql_verifica = mysql_query("SELECT evento_id, colaborador_id FROM eventos_participante WHERE colaborador_id = $lookup_colaborador->id AND evento_id = $EventoId");
											 
											 //Conta o numero de registros que retornou
											 $registro_verifica = mysql_num_rows($sql_verifica);
											 
											 if ($registro_verifica == 0) {
										 ?>
			               <option value="<?php echo $lookup_colaborador->id ?>"><?php echo $lookup_colaborador->nome ?> </option>			               
										 <?php 
										 //Fecha o IF de verifica se te registro										
										 }
										 
										 //Fecha o while
										 } 
										 ?>
			             </select>
                 </td>
                 <td>
                   <div align="right">Notificar Participante
                     <input name="chkNotificar" type="checkbox" id="chkNotificar" value="1" title="Marque caso desejar que o participante seja notificado através da agenda do sistema" checked>
                   </div>
                 </td>
               </tr>
             </table>           					 					   						 
					 </TD>
        </TR>
       <tr>
          <td width="140" valign="top" class=dataLabel>Custo no Evento:</tdD>
          <td colspan="3" class=tabDetailViewDF>
						<table width="100%" cellpadding="0" cellspacing="0">
               <tr>
               	<td width="125">               	
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
								<td width="330">
								 <input name="chkCriarConta" type="checkbox" id="chkCriarConta" value="1" title="Marque caso desejar que o sistema efetue o lançamento de uma conta a pagar automaticamente para este participante" checked>
								 Inserir uma conta a pagar para este participante.  Vencimento: 
								</td>
								<td>
									<?php
								    //Gera a variável numerica do dia da semana do evento em si
										$dia_semana = date('w',strtotime($dados_evento["data_realizacao"]));
										
										//Efetua o switch para criar o valor do vencimento para a quarta feira subsequente
  	  							switch ($dia_semana) {
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
						</table>              
					</td>
				</tr>
				<TR>
         <TD width="140" class='dataLabel'>Fun&ccedil;&atilde;o no Evento:</TD>
         <TD colspan="3" valign="middle" class=tabDetailViewDF>
           <select name="cmbFuncaoId" id="cmbFuncaoId" style="width:350px">
             <option value="0">Manter a função original do cadastro do colaborador</option>
		 				 <?php 
							 //Monta o while para gerar o combo de escolha de funcao
							 while ($lookup_funcao = mysql_fetch_object($dados_funcao)) { 
						 ?>
             <option value="<?php echo $lookup_funcao->id ?>"><?php echo $lookup_funcao->nome ?> </option>
             <?php } ?>
           </select>						 
				 </TD>
       </TR>

          
           <TR>
             <TD valign="top" class=dataLabel>Informa&ccedil;&otilde;es Complementares :</TD>
             <TD colspan="3" class=tabDetailViewDF>
						   <textarea name="edtObservacoes" wrap="virtual" class="datafield" id="edtObservacoes" style="width: 100%; height: 130px"></textarea>
				  </TD>
           </TR>
         </TBODY>
	   </TABLE>
     </td>
   </tr>
</FORM>
</table>  	 

</tr>
</table>
