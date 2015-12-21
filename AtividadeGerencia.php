<?php 
###########
## Módulo para alteração de conta a receber
## Criado: 17/05/2007 - Maycon Edinger
## Alterado: 11/07/2007 - Maycon Edinger
## Alterações:
## 06/06/2007 - Implementado todos os novos campos solicitados
## 19/06/2007 - Aplicado objeto para campo money
## 03/07/2007 - Implementado campo para condição de pagamento
## 05/07/2007 - Implementado para incluir o cheque na conta
## 11/07/2007 - Implementado campo para cadastro de subgrupos e nro do documento
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) 
{

	header("Content-Type: text/html;  charset=ISO-8859-1",true);

}

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
// Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Inclui o arquivo para manipulação de valor monetário
include "./include/ManipulaMoney.php";

//Adiciona o acesso a entidade de criação do componente data
include("CalendarioPopUp.php");
//Cria um objeto do componente data
$objData = new tipData();

//Define que não deve exibir a hora no calendario
$objData->bolExibirHora = false;

//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript();

//Recupera o id do Evento
if($_POST) 
{
	
	$EventoId = $_POST["EventoId"]; 
	$AtividadeId = $_POST["AtividadeId"]; 

} 

else 

{
  
	$EventoId = $_GET["EventoId"]; 
	$AtividadeId = $_GET["AtividadeId"]; 

}

//Recupera dos dados do evento
$sql_evento = "SELECT 
			ativ.id,
			ativ.data_prazo,
			ativ.data_execucao,
			ativ.status,
			ativ.obs,
			atividade.descricao,
			atividade.dias,
			eve.nome AS evento_nome
			FROM eventos_atividade ativ 
			LEFT OUTER JOIN eventos eve ON eve.id = ativ.evento_id
			LEFT OUTER JOIN atividades atividade ON atividade.id = ativ.atividade_id						
			WHERE ativ.id = $AtividadeId";
  
//Executa a query
$resultado = mysql_query($sql_evento);

//Monta o array dos campos
$dados_atividade = mysql_fetch_array($resultado);

switch ($dados_atividade[status]) 
{
	
	//Caso aberto
	case 0: 
		$tipo_aberto = 'checked';	  
		$tipo_fechado = '';
	break;
	//Fechado
	case 1: 
		$tipo_aberto = '';	  
		$tipo_fechado = 'checked';									
	break;
	default:
		$tipo_aberto = 'checked';	  
		$tipo_fechado = '';
	break;

}
?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<form name="frmAtividade" action="sistema.php?ModuloNome=AtividadeGerencia" method="post">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
	<tr>
		<td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Gerenciamento da Atividade</span></td>
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
						
							//Verifica se a flag está vindo de uma postagem para liberar a alteração
							if($_POST["Submit"])
							{

								//Recupera os valores do formulario e alimenta as variáveis
								$id = $_POST["AtividadeId"];
								
								$edtDataExecucao = DataMySQLInserir($_POST["edtDataExecucao"]);
            
								$edtStatus = $_POST["edtStatus"];
								$edtObservacoes = $_POST["edtObservacoes"];	
								$edtOperadorId = $usuarioId;

								//Executa a query de alteração da conta
								$sql = mysql_query("UPDATE eventos_atividade SET 
													data_execucao = '$edtDataExecucao',
													status = '$edtStatus',
													usuario_execucao = '$edtOperadorId', 
													obs = '$edtObservacoes'
													WHERE id = '$id' ");	
                                
								$data_atualizacao = date("Y-m-d", mktime());	
                                                                                                 

								//Exibe a mensagem de alteração com sucesso
								echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Atividade alterada com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div>";
							}
         	
			
		?>

        <table cellspacing="0" cellpadding="0" width="100%" border="0">
			<tr>
				<td width="100%"> </td>
			</tr>
			<tr>
	        	<td style="PADDING-BOTTOM: 2px">
	        		<input name="BtnVoltar" type="button" class="button" id="BtnVoltar" title="Retorna para a exibicao do Evento" value="Voltar" onclick="wdCarregarFormulario('EventoExibe.php?EventoId=<?php echo $EventoId ?>&headers=1','conteudo')" style="width: 110px">
					<input name="Submit" type="submit" class="button" id="Submit" title="Salva o registro atual [Alt+S]" value="Salvar Gerenciamento">
					<input class="button" title="Limpa o conteúdo dos campos digitados [Alt+L]" name="Reset" type="reset" id="Reset" value="Limpar Campos">
					<input name="AtividadeId" type="hidden" value="<?php echo $AtividadeId ?>" />
					<input name="EventoId" type="hidden" value="<?php echo $EventoId ?>" />
				</td>
				<td width="36" align="right">
					&nbsp;						
				</td>
	       	</tr>
        </table>
           
        <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
			<tr>
				<td width="140" class="dataLabel">
					<span class="dataLabel">Evento:</span>             
				</td>
				<td colspan="4" class="tabDetailViewDF">
					<b><?php echo $dados_atividade[evento_nome] ?></b>
				</td>
			</tr>           

			<tr>
				<td width="140" class="dataLabel">Atividade:</td>
				<td colspan="4" valign="middle" class="tabDetailViewDF">
					<b><span style='color: #990000'><?php echo $dados_atividade[descricao] ?></span></b>
				</td>
			</tr>
			<tr>
				<td width="140" class="dataLabel">Dias para Execução:</td>
				<td colspan="4" valign="middle" class="tabDetailViewDF">
					<?php echo $dados_atividade[dias] ?>								 						 
				</td>
			</tr>
			<tr>
				<td width="140" class="dataLabel">Prazo para Execução:</td>
				<td colspan="4" valign="middle" class="tabDetailViewDF">
					<b><?php echo DataMySQLRetornar($dados_atividade[data_prazo]) ?></b>								 						 
				</td>
			</tr>
			<tr>
				<td width="140" valign="top" class="dataLabel">Status:</td>
				<td colspan="4" class="tabDetailViewDF">
				
					<table cellpadding="0" cellspacing="0" width="100%">
						<tr valign="middle">
							<td width="180" height="20">
								<input type="radio" name="edtStatus" value="0" <?php echo $tipo_aberto ?> />
								Em Aberto
							</td>
							<td height="20">
								<input type="radio" name="edtStatus" value="1" <?php echo $tipo_fechado ?> />
								Concluido
							</td>
						</tr>
					</table>
				</td>
			</tr> 
          
			<tr>
				<td width="146" class="dataLabel">Data da Execução:</td>
				<td colspan="4" class="tabDetailViewDF">
					<?php
						
						//Pega a data atual
						$data_executa = DataMySQLRetornar($dados_atividade[data_execucao]);
						
						//Define a data do formulário
						$objData->strFormulario = "frmAtividade";  
						//Nome do campo que deve ser criado
						$objData->strNome = "edtDataExecucao";
						$objData->strRequerido = true;
						//Valor a constar dentro do campo (p/ alteração)
						$objData->strValor = $data_executa;
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
				<td width="140" valign="top" class="dataLabel">Informa&ccedil;&otilde;es Complementares:</td>
				<td colspan="5" class="tabDetailViewDF">
					<textarea name="edtObservacoes" wrap="virtual" class="datafield" id="edtObservacoes" style="width: 100%; height: 130px"><?php echo $dados_atividade[obs] ?></textarea>    							
				</td>
			</tr>				
		</table>			
		</td>
	</tr>
</table>
</form>