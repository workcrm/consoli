<?php 
###########
## Módulo para Cadastro de Pedido de Foto e Vídeo
## Criado: 24/08/2010 - Maycon Edinger
## Alterado: 
## Alterações: 
## 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET['headers'] == 1) 
{
	header('Content-Type: text/html;  charset=ISO-8859-1',true);
}

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
// Processa as diretivas de segurança 
require('Diretivas.php');

//Estabelece a conexão com o banco de dados
include './conexao/ConexaoMySQL.php';

//Inclui o arquivo para manipulação de datas
include './include/ManipulaDatas.php';

//Efetua o lookup na tabela de eventos de formatura
//Monta o sql de pesquisa
$lista_eventos_formatura = "SELECT id, nome FROM eventos WHERE empresa_id = $empresaId AND tipo = 2 ORDER BY nome";
//Executa a query
$dados_eventos_formatura = mysql_query($lista_eventos_formatura);

//Adiciona o acesso a entidade de criação do componente data
include('CalendarioPopUp.php');
//Cria um objeto do componente data
$objData = new tipData();
//Define que não deve exibir a hora no calendario
$objData->bolExibirHora = false;
//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript(); 	

?>
<script>
function busca_formandos()
{
  
	var Form;
	Form = document.cadastro;   
  
	if (Form.cmbEventoId.value != 0)
	{  
    
		eventoId = Form.cmbEventoId.value;
   
		wdCarregarFormulario('PedidoFotoVideoBuscaFormando.php?EventoId=' + eventoId,'recebe_formandos');     
   
	} 
	else 
	{          
    
		document.getElementById("recebe_formandos").innerHTML = "[ Selecione um evento ]";
      
	}
      
}

function busca_produtos()
{
  
	var Form;
	Form = document.cadastro;   
  
	if (Form.cmbFormandoId.value != 0)
	{  
    
		eventoId = Form.cmbEventoId.value;
		formandoId = Form.cmbFormandoId.value;
   
		//alert('Evento: ' + eventoId + ' FormandoId= ' + formandoId);
    
		wdCarregarFormulario('PedidoFotoVideoBuscaProduto.php?EventoId=' + eventoId + '&FormandoId=' + formandoId,'recebe_produtos');     
   
	} 
	else 
	{          
    
		document.getElementById("recebe_pedidos").innerHTML = "";
      
	}
      
}

function valida_form() 
{
	var Form;
	Form = document.cadastro;   
	 
	if (Form.edtData.value.length == 0) 
	{
		alert("É necessário Informar a Data !");
		Form.edtData.focus();
		return false;
	}


	if (Form.cmbEventoId.value == 0) 
	{
		alert("É necessário selecionar um Evento!");
		return false;
	}
	
	if (Form.cmbFormandoId.value == 0) 
	{
		alert("É necessário selecionar um Formando !");
		return false;
	}
  
  
	if (Form.cmbFornecedorId.value == 0) 
	{
		alert("É necessário selecionar um Fornecedor !");
		return false;
	}	

	return true;
}
</script>
<form id="form" name="cadastro" action="sistema.php?ModuloNome=PedidoFotoVideoCadastra" method="post" onsubmit="return valida_form()">

<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td width="440">
			<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Novo Pedido de Foto e Vídeo</span>
		</td>
	</tr>
	<tr>
		<td colspan="5"><img src="image/bt_espacohoriz.gif" width="100%" height="12" /></td>
	</tr>
</table>
<?php
	
	//Recupera os valores vindos do formulário e armazena nas variaveis
	if($_POST['Submit'])
	{
    
		$edtTotalChk = $_POST['edtTotalChk'];
		$edtData = DataMySQLInserir($_POST['edtData']);
		$cmbEventoId = 	$_POST['cmbEventoId'];
		$cmbFormandoId = 	$_POST['cmbFormandoId'];
		$edtDataEntrega = DataMySQLInserir($_POST['edtDataEntrega']);
		$cmbFornecedorId = 	$_POST['cmbFornecedorId'];
		$edtObservacoes = $_POST["edtObservacoes"];
		$edtFilial = $_POST["edtFilial"];
		$edtOperadorId = $usuarioId;														
			
		//Monta o sql e executa a query de inserção da conta sem desmembrar
		$sql = mysql_query("
					INSERT INTO pedido_fv ( 
					data,
					evento_id,
					formando_id,
					data_entrega,
					fornecedor_id,
					observacoes,
					cadastro_timestamp,
					cadastro_operador_id
	
					) VALUES (
	
					'$edtData',
					'$cmbEventoId',
					'$cmbFormandoId',
					'$edtDataEntrega',
					'$cmbFornecedorId',										
					'$edtObservacoes',
					now(),
					'$edtOperadorId'				
					);"); 
     
		$pedidoId = mysql_insert_id();  
          
		//Define o valor inicial para efetuar o FOR
		for ($contador_for = 1; $contador_for <= $edtTotalChk; $contador_for++)
		{
		
			//Monta a variável com o nome dos campos
			$texto_formando = 'edtFormando' . $contador_for;
			$obs_formando = 'edtObsFormando' . $_POST['$texto_formando'];
			$texto_qtde = 'edtQuantidade' . $contador_for;								
			$texto_obs = 'edtObs' . $contador_for;							
			
			//Enquanto não chegar ao final do contador total de itens
			if ($_POST[$contador_for] != 0) 
			{
													
				$sql_insere_item = "INSERT INTO pedido_fv_produtos (
													 pedido_id, 
													 produto_id,
													 quantidade,
													 observacoes
													 ) VALUES (
													 '$pedidoId',
													 '$_POST[$contador_for]', 
													 '$_POST[$texto_qtde]',
													 '$_POST[$texto_obs]'
													 )";																		
				
				//Insere os registros na tabela de eventos_itens
				mysql_query($sql_insere_item);            
						
			}
      
		}
		
		//Faz o switch na filial para determinar o centro de custo
		switch ($edtFilial)
		{
		
			case 1:
				$CentroCusto = 6;
			break;
			//Joinville
			case 2:
				$CentroCusto = 2;
			break;
			//Guarapuava
			case 3:
				$CentroCusto = 19;
			break;
		}
		
		//Busca o nome do fornecedor
		//Monta o sql de pesquisa
		$lista_fornecedor = "SELECT nome FROM fornecedores WHERE id = $cmbFornecedorId";
		//Executa a query
		$query_fornecedor = mysql_query($lista_fornecedor);
		//Monta o array
		$dados_fornecedor = mysql_fetch_array($query_fornecedor);
		
		$NomeFornecedor = $dados_fornecedor["nome"];
		
		
		//Busca o nome do formando
		//Monta o sql de pesquisa
		$lista_formando = "SELECT nome FROM eventos_formando WHERE id = $cmbFormandoId";
		//Executa a query
		$query_formando = mysql_query($lista_formando);
		//Monta o array
		$dados_formando = mysql_fetch_array($query_formando);
		
		$NomeFormando = $dados_formando["nome"];
		
		
		
		//Busca o nome do evento
		//Monta o sql de pesquisa
		$lista_evento = "SELECT nome FROM eventos WHERE id = $cmbEventoId";
		//Executa a query
		$query_evento = mysql_query($lista_evento);
		//Monta o array
		$dados_evento = mysql_fetch_array($query_evento);
		
		$NomeEvento = $dados_evento["nome"];
		
		
		
		$edtObservacoesConta = "Conta gerada automaticamente pelo pedido $pedidoId do Foto e Video\n\n" . $edtObservacoes;
		
		//Monta o sql e executa a query de inserção da conta sem desmembrar
		$sql = mysql_query("INSERT INTO contas_pagar (
							empresa_id, 
							data,
							tipo_pessoa,
							pessoa_id,
							grupo_conta_id,
							subgrupo_conta_id,
							evento_id, 
							formando_id,													
							descricao,
							origem_conta,
							situacao,
							observacoes,
							pedido_id,
							cadastro_timestamp,
							cadastro_operador_id
			
							) VALUES (
			
							'1',
							'$edtData',
							'2',
							'$cmbFornecedorId',
							'$CentroCusto',
							'56',
							'$cmbEventoId',
							'$cmbFormandoId',
							'Pagamento $NomeFornecedor - $NomeFormando - $NomeEvento',
							3,
							1,										
							'$edtObservacoesConta',
							'$pedidoId',
							now(),
							'$edtOperadorId'				
							);"); 
       
		//Exibe a mensagem de inclusão com sucesso
		echo "<table width='100%' border='0' align='left' cellpadding='0' cellspacing='0'>
				<tr>
					<td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'>
						<img src='./image/bt_informacao.gif' border='0' />
					</td>
					<td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'>
						<strong>Pedido de Foto e Vídeo cadastrado com sucesso !</strong>
					</td>
				</tr>
				<tr>
					<td colspan='2' style='padding-top: 5px; padding-bottom: 10px'>
						<input class='button' title='Imprime o pedido recém efetuado' name='Imprime' type='button' id='Imprime' value='Imprimir Pedido' onclick='abreJanela(\"./relatorios/PedidoDetalheRelatorio.php?PedidoId=$pedidoId\")' />
					</td>
				</tr>
			</table><br/>aaaquiiii";

//Fecha o if de postagem
}
?>                
<table cellspacing="0" cellpadding="0" width="100%" border="0">
	<tr>
		<td style="PADDING-BOTTOM: 2px">
			<input name="Submit" type="submit" class="button" id="Submit" title="Salva o pedido do foto e vídeo" value="Salvar Pedido" />
			<input class="button" title="Limpa o conteúdo dos campos digitados" name="Reset" type="reset" id="Reset" value="Limpar Campos" />
		</td>
		<td width="36" align="right">	  </td>
	</tr>
</table>
 
<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
	<tr>
		<td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="21">
			<table cellspacing="0" cellpadding="0" width="100%" border="0">
				<tr>
					<td class="tabDetailViewDL" style="TEXT-ALIGN: left">
				 		<img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados do pedido e clique em [Salvar Pedido]
					</td>
				</tr>
			</table>			 
		</td>
	</tr>
	<tr>
		<td width="140" class="dataLabel">
			<span class="dataLabel">Data:</span>             
		</td>
		<td colspan="4" class="tabDetailViewDF">
			<?php
		    
				//Define a data do formulário
				$objData->strFormulario = "cadastro";  
				//Nome do campo que deve ser criado
				$objData->strNome = "edtData";
				$objData->strRequerido = true;
				//Valor a constar dentro do campo (p/ alteração)
				$objData->strValor = Date("d/m/Y", mktime());
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
		<td class="dataLabel" width="50">Evento:</td>
		<td colspan="4" width="490" class="tabDetailViewDF">								
			<div id="900">
				<select name="cmbEventoId" id="cmbEventoId" style="width: 400px" onchange="busca_formandos()">                  
					<option value="0">Selecione uma Opção</option>
						<?php 
							//Cria o componente de lookup de eventos formatura
							while ($lookup_eventos_formatura = mysql_fetch_object($dados_eventos_formatura)) { 
						?>
						<option value="<?php echo $lookup_eventos_formatura->id ?>"><?php echo $lookup_eventos_formatura->id . " - " . $lookup_eventos_formatura->nome ?></option>
						<?php 
							//Fecha o while
							} 
						?>
				</select>
			</div>
		</td>
	</tr>
  
	<tr>
		<td class="dataLabel" width="50">Formando:</td>
		<td colspan="4" width="490" class="tabDetailViewDF">
			<div id="recebe_formandos">
			[ Selecione um evento ] <input type="hidden" name="cmbFormandoId" id="cmbFormandoId" value="0">
			</div>
		</td>
	</tr>
	<tr>
		<td class="dataLabel">Regional/Filial:</td>
		<td colspan="4" class="tabDetailViewDF">
			<table cellpadding="0" cellspacing="0">
				<tr valign="middle">
					<td width="150" height="20">
						<input type="radio" name="edtFilial" value="1" checked="checked"/> Consoli Rio do Sul
					</td>
					<td width="150" height="20">
						<input type="radio" name="edtFilial" value="2" /> Consoli Joinville
					</td>
					<td height="20">
						<input type="radio" name="edtFilial" value="3" /> Consoli Guarapuava
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width="140" class="dataLabel">
			<span class="dataLabel">Data para Entrega:</span>             
		</td>
		<td colspan="4" class="tabDetailViewDF">
			<?php
				
				//Define a data do formulário
				$objData->strFormulario = "cadastro";  
				//Nome do campo que deve ser criado
				$objData->strNome = "edtDataEntrega";
				$objData->strRequerido = false;
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
</table>
<br/>

<div id="recebe_produtos"></div>
</td></tr><tr><td>