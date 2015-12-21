<?php 
###########
## Módulo para quitação das contas a receber
## Criado: 25/02/2009 - Maycon Edinger
## Alterado:
## Alterações:
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) 
{
	
  header("Content-Type: text/html;  charset=ISO-8859-1",true);

}

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Inclui o arquivo para manipulação de valor monetário
include "./include/ManipulaMoney.php";

//Monta o lookup da tabela de bancos
//Monta o SQL
$lista_bancos = "SELECT * FROM bancos WHERE empresa_id = $empresaId AND ativo = 1 ORDER BY nome";

//Executa a query
$dados_bancos = mysql_query($lista_bancos);

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

//Recupera o id da conta a exibir
if ($_GET["ContaId"]) 
{
	
	$ContaId = $_GET["ContaId"];

} 

else 

{
  
	$ContaId = $_POST["Id"];

}


//Monta o lookup da tabela de cheques
//Monta o SQL
$lista_cheque = "SELECT id, numero_cheque FROM cheques WHERE empresa_id = $empresaId ORDER BY data_vencimento DESC";

//Executa a query
$dados_cheque = mysql_query($lista_cheque);

//Adiciona o acesso a entidade de criação do componente data
include("CalendarioPopUp.php");

//Cria um objeto do componente data
$objData = new tipData();

//Define que não deve exibir a hora no calendario
$objData->bolExibirHora = false;

//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript();

?>

<script language="JavaScript">

function wdSubmitContaQuita() 
{
	var Form;
	Form = document.frmContaQuita;
	if (Form.edtDataRecebimento.value.length == 0) 
	{
		alert("É necessário Informar a Data de Recebimento !");
		Form.edtDataRecebimento.focus();
		return false;
	}
	 
	//Captura o valor referente ao radio button selecionado
	var edtTipoRecebimentoValor = document.getElementsByName('edtTipoRecebimento');
   
	for (var i=0; i < edtTipoRecebimentoValor.length; i++) 
	{
	
		if (edtTipoRecebimentoValor[i].checked == true) 
		{
			
			edtTipoRecebimentoValor = edtTipoRecebimentoValor[i].value;
			break;
		}
	}

	if (edtTipoRecebimentoValor == 2) 
	{
	
		if (Form.cmbChequeId.value == 0) 
		{
			
			alert("É necessário Informar o Número do Cheque !");
			Form.cmbChequeId.focus();
			return false;
		}
	}

	if (Form.edtValorRecebido.value.length == 0) 
	{
		alert("É necessário Informar o Valor Recebido !");
		Form.edtValorRecebido.focus();
		return false;
	}
	 	
	return true;

}

//Função que alterna a visibilidade do painel especificado.
function oculta(id)
{
	
	ID = document.getElementById(id);
	ID.style.display = "none";

}

//Função que alterna a visibilidade do painel especificado.
function change(id)
{

	ID = document.getElementById(id);

	if(ID.style.display == "")
	{
		
		ID.style.display = "none";
	}
	
	else
	{
		
		ID.style.display = "";
		
	}

}
</script>

<form name="frmContaQuita" action="sistema.php?ModuloNome=ContaReceberQuita" method="post" onsubmit="return wdSubmitContaQuita()">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="440"><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Quitação da Conta a Receber</span></td>
				</tr>
				<tr>
					<td colspan="5">
						<img src="image/bt_espacohoriz.gif" width="100%" height="12">
					</td>
				</tr>
			</table>

			<table width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%" class="text">

						<?php
						
							//Verifica se a flag está vindo de uma postagem para liberar a alteração
							if($_POST["Submit"])
							{

								//Recupera os valores do formulario e alimenta as variáveis
								$id = $_POST["Id"];
  
								$total_valor_recebido_dinheiro = $_POST["ValorRecebido"] + MoneyMySQLInserir($_POST["edtValorRecebidoDinheiro"]);
								$total_valor_recebido = $_POST["ValorRecebido"] + MoneyMySQLInserir($_POST["edtValorRecebido"]);
								$valor_receber = $_POST["ValorReceber"];																			
								$edtDataRecebimento = DataMySQLInserir($_POST["edtDataRecebimento"]);
								$edtTipoRecebimento = $_POST["edtTipoRecebimento"];
								$cmbBancoId = $_POST["edtBancoTerceiro"];
								$edtChequeId = strtoupper($_POST["edtChequeId"]);
								$edtFavorecido = $_POST["edtFavorecido"];
								$edtPreDatado = $_POST["chkPredatado"];
								$edtBomPara = DataMySQLInserir($_POST["edtBomPara"]);
								$edtValorRecebidoDinheiro = MoneyMySQLInserir($_POST["edtValorRecebidoDinheiro"]);
								$edtValorRecebido = MoneyMySQLInserir($_POST["edtValorRecebido"]);											
								$edtAgencia = $_POST["edtAgenciaTerceiro"];
								$edtConta = $_POST["edtContaTerceiro"];          	
								$edtRecebido = DataMySQLInserir($_POST["edtRecebido"]);
								$edtObservacoesDinheiro = $_POST["edtObservacoesDinheiro"];
								$edtObservacoesCheque = $_POST["edtObservacoesCheque"];

								//Busca o total já recebido da conta
								$saldo_conta = mysql_query("SELECT id, valor, valor_recebido FROM contas_receber WHERE id = '$id'");
								$dados_conta = mysql_fetch_array($saldo_conta);
								$saldo_anterior = $dados_conta["valor"] - $dados_conta["valor_recebido"];


								//Verifica o tipo de recebimento
								//Para recebimento em dinheiro
								if ($edtTipoRecebimento == 1)
								{
							
									//Efetua o lançamento para os campos de dinheiro
									$sql = mysql_query("INSERT INTO contas_receber_recebimento (
														conta_receber_id,
														data_recebimento, 
														tipo_recebimento,
														total_recebido,
														obs,
														cadastro_timestamp,
														cadastro_operador_id													
														) values (				
														'$id',
														'$edtDataRecebimento',
														'1',
														'$edtValorRecebidoDinheiro',
														'$edtObservacoesDinheiro',
														now(),
														'$operadorId'
														);");
									
									//Faz o update do valor recebido da conta							
									$sql = mysql_query("UPDATE contas_receber SET valor_recebido = '$total_valor_recebido_dinheiro' WHERE id = $id");
					
									//Verifica o saldo se é negativo ou zero e quita a conta
									$saldo = 	$saldo_anterior - $edtValorRecebidoDinheiro;
								
									if ($saldo <= 0)
									{
									
										//Quita a conta
										$sql = mysql_query("UPDATE contas_receber SET situacao = 2 WHERE id = $id");
									
									}	
				  
									//Exibe a mensagem de alteração com sucesso
									echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Recebimento Efetuado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div>";																																															
								
							
								//Caso for pago com cheque de terceiro	
								} 
            
								else if ($edtTipoRecebimento == 3)
            
								{
							
							
									//Verifica se não existe o cheque já cadastrado
									$sql = mysql_query("SELECT * FROM cheques WHERE numero_cheque = '$edtChequeId' AND banco_id = '$cmbBancoId' AND agencia = '$edtAgencia' AND conta = '$edtConta'");
              
									//echo "SELECT * FROM cheques WHERE numero_cheque = $edtChequeId AND banco_id = '$cmbBancoId' AND agencia = '$edtAgencia' AND conta = '$edtConta'";
							
									//verifica o número total de registros
									$conta_cheque = mysql_num_rows($sql);
			  			
									if ($conta_cheque == 0)
									{
			  
										//Insere o recebimento na conta a receber
										$sql = mysql_query("INSERT INTO contas_receber_recebimento (
															conta_receber_id,
															data_recebimento, 
															tipo_recebimento,
															cheque_id,
															total_recebido,
															obs,
															cadastro_timestamp,
															cadastro_operador_id
															) values (				
															'$id',
															'$edtDataRecebimento',
															'3',
															'$edtChequeId',
															'$edtValorRecebido',
															'$edtObservacoesCheque',
															now(),
															'$operadorId'
															);");
  																
										//Recupera o Id da conta a receber cadastrado
										$ContaReceberId = mysql_insert_id();

										//Busca os dados do evneto da conta vinculada para vincular ao cheque
										$sql_conta = mysql_query("SELECT evento_id, formando_id FROM contas_receber WHERE id = $id");
										
										//Executa a query
										$conta_receber = mysql_fetch_array($sql_conta);
										
										$EventoId = $conta_receber["evento_id"];
										$FormandoId = $conta_receber["formando_id"];
							
										//Efetua o lançamento para os campos de cheque
										$sql = mysql_query("INSERT INTO cheques (
															numero_cheque,
															empresa_id,
															favorecido, 
															pre_datado,
															bom_para,
															banco_id,
															agencia,
															conta,
															data_recebimento,
															valor,
															disponivel,
															status,
															observacoes,
															conta_receber_id,
															origem,
															evento_id,
															formando_id															
															
															) values (				
																						  
															'$edtChequeId',
															'$empresaId',
															'$edtFavorecido',
															'$edtPreDatado',
															'$edtBomPara',
															'$cmbBancoId',
															'$edtAgencia',
															'$edtConta',
															'$edtRecebido',
															'$edtValorRecebido',
															1,
															1,
															'$edtObservacoesCheque',
															'$id',
															2,
															$EventoId,
															$FormandoId
															);");
																																
								
										//Faz o update do valor recebido da conta							
										$sql = mysql_query("UPDATE contas_receber SET valor_recebido = '$total_valor_recebido' WHERE id = $id");
                  
										//Verifica o saldo se é negativo ou zero e quita a conta
										$saldo = 	$saldo_anterior - $edtValorRecebido;                  
									
										if ($saldo <= 0 )
										{
										
											//Quita a conta
											$sql = mysql_query("UPDATE contas_receber SET situacao = '2' WHERE id = '$id'");
										
										}							 
		
										//Exibe a mensagem de alteração com sucesso
										echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Recebimento Efetuado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div>";
								
									//Caso o cheque já esteja cadastrado
									} 
                
									else 
                
									{
									
										echo "<table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Não foi possível efetuar o recebimento desta conta. O Cheque informado já está cadastrado como recebido para outra conta !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td>";
									
									}
							
								}
																					
							}
            
            
							//Monta o sql para recuperar os dados da conta
							$sql="SELECT 
								  con.id,
								  con.data,
								  con.tipo_pessoa,
								  con.pessoa_id,
								  con.grupo_conta_id,
								  con.subgrupo_conta_id,
								  con.evento_id,
								  con.descricao,
								  con.nro_documento,
								  con.condicao_pgto_id,
								  con.valor,
								  con.data_vencimento,
								  con.situacao,
								  con.data_recebimento,
								  con.tipo_recebimento,
								  con.cheque_id,
								  con.valor_recebido,
								  con.observacoes,
								con.cadastro_timestamp,
								con.cadastro_operador_id,
								con.alteracao_timestamp,
								con.alteracao_operador_id,
								usu_cad.nome as operador_cadastro_nome, 
								usu_cad.sobrenome as operador_cadastro_sobrenome,
								usu_alt.nome as operador_alteracao_nome, 
								usu_alt.sobrenome as operador_alteracao_sobrenome,
								cat.nome as categoria_nome,
								gru.nome as grupo_nome,
								sub.nome as subgrupo_nome,
								cond.nome as condicao_pgto_nome,
								evento.nome as evento_nome

								FROM contas_receber con
								LEFT OUTER JOIN usuarios usu_cad ON con.cadastro_operador_id = usu_cad.usuario_id 
								LEFT OUTER JOIN usuarios usu_alt ON con.alteracao_operador_id = usu_alt.usuario_id
								LEFT OUTER JOIN categoria_conta cat ON con.categoria_id = cat.id 
								LEFT OUTER JOIN grupo_conta gru ON con.grupo_conta_id = gru.id 							 
								LEFT OUTER JOIN subgrupo_conta sub ON con.subgrupo_conta_id = sub.id 							 
								LEFT OUTER JOIN condicao_pgto cond ON con.condicao_pgto_id = cond.id 							 
								LEFT OUTER JOIN eventos evento ON con.evento_id = evento.id 							 
										
								WHERE con.id = '$ContaId'";		
          			   
								//Executa a query
								$resultado = mysql_query($sql);
								
								//Monta o array dos dados
								$campos = mysql_fetch_array($resultado);
								
								//Efetua o switch para o campo de situacao
								switch ($campos[situacao]) 
								{
									case 1: $desc_situacao = "Em aberto"; break;
									case 2: $desc_situacao = "Recebido"; break;
								}    
                
								//Efetua o switch para o campo tipo de pessoa
								switch ($campos[tipo_pessoa]) 
								{
									case 1: 
										$desc_pessoa = "Cliente:"; 
										$busca_pessoa = mysql_query("SELECT id, nome FROM clientes WHERE id = '$campos[pessoa_id]'");
										$dados_pessoa = mysql_fetch_array($busca_pessoa);
										$id_pessoa = $dados_pessoa[id];
										$nome_pessoa = $dados_pessoa[nome];
									break;
									case 2: 
										$desc_pessoa = "Fornecedor:"; 
										$busca_pessoa = mysql_query("SELECT id, nome FROM fornecedores WHERE id = '$campos[pessoa_id]'");
										$dados_pessoa = mysql_fetch_array($busca_pessoa);
										$id_pessoa = $dados_pessoa[id];
										$nome_pessoa = $dados_pessoa[nome];	
									break;
									case 3: 
										$desc_pessoa = "Colaborador:"; 
										$busca_pessoa = mysql_query("SELECT id, nome FROM colaboradores WHERE id = '$campos[pessoa_id]'");
										$dados_pessoa = mysql_fetch_array($busca_pessoa);
										$id_pessoa = $dados_pessoa[id];
										$nome_pessoa = $dados_pessoa[nome];	
									break;	
								}    
             
							?>
						<table cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td style="padding-bottom: 2px">
									<input class="button" title="Retorna a exibição do registro" name="btnVoltar" type="button" id="btnVoltar" value="Retornar a Exibição da Conta" onclick="wdCarregarFormulario('ContaReceberExibe.php?ContaId=<?php echo $ContaId ?>','conteudo')" />					
								</td>
							</tr>
						</table>	

						<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="22">
									<table cellspacing="0" cellpadding="0" width="100%" border="0">
										<tr>
											<td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados para a quitação da conta e clique em [Efetuar Recebimento] </td>
										</tr>
									</table>             
								</td>
							<tr>
								<td width="140" class="dataLabel">
									<span class="dataLabel">Data:</span>             
								</td>
								<td colspan="4" class="tabDetailViewDF">
									<?php echo DataMySQLRetornar($campos[data]) ?>
								</td>
							</tr>
							<tr>
								<td width="140" class="dataLabel">Descrição:</td>
								<td colspan="4" valign="middle" class="tabDetailViewDF">
									<strong><?php echo $campos[descricao] ?></strong>
								</td>
							</tr>					           
           <tr>
             <td width="140" class="dataLabel">Conta-caixa:</td>
             <td colspan="4" valign="middle" class="tabDetailViewDF">
               <?php echo $campos[subgrupo_nome] ?>
						 </td>
           </tr>           
					 <tr>
             <td width="140" class="dataLabel">Centro de Custo:</td>
             <td colspan="4" valign="middle" class="tabDetailViewDF">
               <?php echo $campos[grupo_nome] ?>
						 </td>
           </tr>           
					 <tr>
             <td width="140" class="dataLabel">Tipo Pessoa/Sacado:</td>
             <td colspan="4" valign="middle" class="tabDetailViewDF">
               <?php echo $desc_pessoa ?><br/>
               <strong><?php echo $nome_pessoa ?></strong>
						 </td>
           </tr>           
					 <tr>
             <td width="140" class="dataLabel">Evento:</td>
             <td colspan="4" valign="middle" class="tabDetailViewDF">
               <?php echo $campos[evento_nome] ?>
						 </td>
           </tr>

           <tr>
             <td width="140" class="dataLabel">Nº do Documento:</td>
             <td colspan="4" valign="middle" class="tabDetailViewDF">
               <?php echo $campos[nro_documento] ?>
						 </td>
          </tr>

          <tr>
            <td width="140" valign="top" class="dataLabel">Valor a Receber: </td>
            <td width="173" class="tabDetailViewDF">
               <?php echo "R$ " . number_format($campos[valor], 2, ",", ".") ?>
						</td>
            <td width="146" class="dataLabel">Data Vencimento:</td>
            <td colspan="2" class="tabDetailViewDF">
               <?php echo DataMySQLRetornar($campos[data_vencimento]) ?>
						</td>
          </tr>
          <tr>
            <td width="140" valign="top" class="dataLabel">Valor Recebido:</td>
            <td width="173" class="tabDetailViewDF">
               <?php echo "R$ " . number_format($campos[valor_recebido], 2, ",", ".") ?>
						</td>
            <td width="146" class="dataLabel">Saldo a Receber:</td>
            <td colspan="2" class="tabDetailViewDF">
               <?php 
							 
							 	$saldo_receber = $campos[valor] - $campos[valor_recebido];
							 	
							 echo "<b>R$ " . number_format($saldo_receber, 2, ",", ".") . "</b>" ?>
						</td>
          </tr>
        <table>
    	
			
<?php 
//Monta um sql para pesquisar se há algum recebimento lançado para esta conta
$sql_consulta = mysql_query("SELECT * FROM contas_receber_recebimento WHERE conta_receber_id = $ContaId ORDER BY data_recebimento");
														 
$registros = mysql_num_rows($sql_consulta); 
												
?>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>  
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="440"><br/><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Recebimentos efetuados para esta conta:</span></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table width="100%" align="left" border="0" cellspacing="0" cellpadding="0" class="listView">

				<?php
				
					//Caso não houverem registros
					if ($registros == 0) 
					{ 
			
						//Exibe uma linha dizendo que nao registros
						echo "
						<tr height='24'>
							<td colspan='6' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1'>
							<font color='#33485C'><b>Não há recebimentos efetuados para esta conta a receber</b></font>
							</td>
						</tr>";	  
				  
					} 
					
					else 
					
					{		  			   

						//Exibe o cabeçalho da tabela
						echo "
						<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
							<td width='22' style='padding-left: 12px'>&nbsp;A</td>
							<td width='70' style='padding-left: 12px'>&nbsp;Data</td>
							<td width='110'>Tipo Recebimento</td>
							<td width='110' align='center'>Nº do Cheque</td>
							<td width='100' align='right'>Valor Recebido</td> 		      
							<td>&nbsp;&nbsp;Observações</td>
						</tr>";
					
						
						//Cria o array e o percorre para montar a listagem dinamicamente
						while ($dados_consulta = mysql_fetch_array($sql_consulta))
						{
			
							//Efetua o switch do tipo de recebimento
							switch ($dados_consulta[tipo_recebimento]) 
							{
				
								case 1: $nome_tipo = "Dinheiro";	break;
								case 2: $nome_tipo = "Cheque";	break;       	
								case 3: $nome_tipo = "Cheque de Terceiro";	break;
							}
			
						?>
      <tr valign="middle">
        <td valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" style="padding-bottom: 1px; padding-left: 12px">
					<img src="image/grid_exclui.gif" alt="Clique para estornar este recebimento" onclick="if(confirm('Confirma o estorno deste recebimento ?')) {wdCarregarFormulario('ContaReceberQuitaExclui.php?RecebimentoId=<?php echo $dados_consulta[id] ?>&ContaId=<?php echo $dados_consulta[conta_receber_id] ?>','conteudo')}" style="cursor: pointer" />
				</td>	
        <td height="18" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" >
          &nbsp;<?php echo DataMySQLRetornar($dados_consulta[data_recebimento]) ?>
				</font>        
				</td>
        <td height="18" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd">
          <?php echo $nome_tipo ?>
				</td>
				<td height="18" align="center" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd">
          <?php echo $dados_consulta[cheque_id] ?>
				</td>
				<td height="18" align="right" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd">
          <?php echo "R$ " . number_format($dados_consulta[total_recebido], 2, ",", ".") ?>
				</td>				
				<td height="18" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd">
          &nbsp;&nbsp;<?php echo nl2br($dados_consulta[obs]) ?>
				</td>						
  	  </tr>

		  <?php
		  //Fecha o WHILE
		  }
		  
		  //Fecha o if de se tiver recebimentos
		  }
		  ?>
		</table>
	</td>
</tr>
</table>

<?php 

//Verifica se a conta já não está quitada
if ($campos["situacao"] == 1){
  
?>

	      <br/>			
				<table cellspacing="0" cellpadding="0" width="100%" border="0">
	        <tr>
	      		<td style="padding-bottom: 2px">
							<input name="Id" type="hidden" value="<?php echo $ContaId ?>" />
							<input name="ValorReceber" type="hidden" value="<?php echo $campos[valor] ?>" />
							<input name="ValorRecebido" type="hidden" value="<?php echo $campos[valor_recebido] ?>" />
	        		<input name="Submit" type="submit" class="button" title="Efetua o recebimento da conta" value="Efetuar Recebimento" />
	        	</td>						
		  		</tr>
	    	</table>				
    		<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
          <tr>
            <td width="110" class="dataLabel">Data Recebimento:</td>
            <td width="570" colspan="4" class="tabDetailViewDF">
              <?php
							    
                  $dataRecebe = DataMySQLRetornar($campos["data_vencimento"]);
                  
                  //Define a data do formul&aacute;rio
							    $objData->strFormulario = "frmContaQuita";  
							    //Nome do campo que deve ser criado
							    $objData->strNome = "edtDataRecebimento";
							    //Valor a constar dentro do campo (p/ altera&ccedil;&atilde;o)
							    $objData->strValor = $dataRecebe;
							    //Define o tamanho do campo 
							    //$objData->intTamanho = 15;
							    //Define o n&uacute;mero maximo de caracteres
							    //$objData->intMaximoCaracter = 20;
							    //define o tamanho da tela do calendario
							    //$objData->intTamanhoCalendario = 200;
							    //Cria o componente com seu calendario para escolha da data
							    $objData->CriarData();
							?>
						</td>
          </tr>
          <tr>
            <td width="140" valign="top" class="dataLabel">Forma de Recebimento:</td>
            <td colspan="4" width="173" class="tabDetailViewDF">
              <table width="450" cellpadding="0" cellspacing="0">
                <tr valign="middle">
                  <td width="117" height="20">
                    <label>
                    <input name="edtTipoRecebimento" type="radio" value="1" checked="checked" onclick="oculta('dinheiro'); oculta('cheque'); change('dinheiro');"/>
                      Dinheiro </label>
                  </td>
                  <td height="20">
                     <input type="radio" name="edtTipoRecebimento" value="3" onclick="oculta('dinheiro'); oculta('cheque'); change('cheque');"/>
                      Cheque de Terceiro
                  </td>
                </tr>                
              </table>
				  	</td>
				  </tr>        				                           	
	   		</table>
			</td>
	  </tr>
	  
		
		
		<tr>
	  	<td>
	  	<br/>
	  	<div id="dinheiro">
			<span class="TituloModulo">
	  	Recebimento a Dinheiro
	  	</span><br/>
	  		<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
				  <tr>
            <td width="146" class="dataLabel">Valor Recebido: </td>
            <td colspan="4" class="tabDetailViewDF">
							<?php
							//Acerta a variável com o valor a alterar
							$valor_alterar = str_replace(".",",",$saldo_receber);
							
							//Cria um objeto do tipo WDEdit 
							$objWDComponente = new WDEditReal();
							
							//Define nome do componente
							$objWDComponente->strNome = "edtValorRecebidoDinheiro";
							//Define o tamanho do componente
							$objWDComponente->intSize = 16;
							//Busca valor definido no XML para o componente
							$objWDComponente->strValor = "$valor_alterar";
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
             <td width="140" valign="top" class="dataLabel">Informa&ccedil;&otilde;es Complementares:</td>
             <td colspan="4" class="tabDetailViewDF">
             	 <textarea name="edtObservacoesDinheiro" wrap="virtual" class="datafield" id="edtObservacoesDinheiro" style="width: 100%; height: 100px"></textarea>
						 </td>
      	  </tr>  
      	</table>
     	</div>
	  	</td>
	  </tr>
	  
		<tr>
	  	<td>
	  	<div id="cheque" style="display: none">
	  	<span class="TituloModulo">
	  	Recebimento em Cheque de Terceiros
	  	</span><br/>
	  		<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
				  <tr>
            <td width="146" class="dataLabel">Valor Recebido: </td>
            <td colspan="4" class="tabDetailViewDF">
							<?php
							//Acerta a variável com o valor a alterar
							$valor_alterar = str_replace(".",",",$saldo_receber);
							
							//Cria um objeto do tipo WDEdit 
							$objWDComponente = new WDEditReal();
							
							//Define nome do componente
							$objWDComponente->strNome = "edtValorRecebido";
							//Define o tamanho do componente
							$objWDComponente->intSize = 16;
							//Busca valor definido no XML para o componente
							$objWDComponente->strValor = "$valor_alterar";
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
             <td class="dataLabel" width="20%">
               <span class="dataLabel">Nº do Cheque:</span>             
						 </td>
             <td class="tabDetailViewDF">
               <input name="edtChequeId" type="text" class="datafield" id="edtChequeId" style="width: 80px; text-transform: uppercase" maxlength="15" >						 
						 </td>
						 <td class="dataLabel" width="100">
               <span class="dataLabel">Titular:</span>             
						 </td>
             <td class="tabDetailViewDF">
               <input name="edtFavorecido" type="text" class="datafield" id="edtFavorecido" size="50" maxlength="50" />						 
						 </td>
          </tr>
					<tr>
             <td class="dataLabel" width="20%">
               <span class="dataLabel">Pré-datado:</span>             
						 </td>
             <td class="tabDetailViewDF">
               <input name="chkPredatado" type="checkbox" id="chkPredatado" value="1"> Pré-datado				 
						 </td>
						 <td class="dataLabel" width="100">
               <span class="dataLabel">Bom Para:</span>             
						 </td>
             <td class="tabDetailViewDF">
               <?php
							    //Define a data do formul&aacute;rio
							    $objData->strFormulario = "frmContaQuita";  
							    //Nome do campo que deve ser criado
							    $objData->strNome = "edtBomPara";
							    //Valor a constar dentro do campo (p/ altera&ccedil;&atilde;o)
							    $objData->strValor = "";
							    //Define o tamanho do campo 
							    //$objData->intTamanho = 15;
							    //Define o n&uacute;mero maximo de caracteres
							    //$objData->intMaximoCaracter = 20;
							    //define o tamanho da tela do calendario
							    //$objData->intTamanhoCalendario = 200;
							    //Cria o componente com seu calendario para escolha da data
							    $objData->CriarData();
							 ?>				 
						 </td>
          </tr>
					<tr>
             <td class="dataLabel" width="20%">
               <span class="dataLabel">Banco:</span>             
						 </td>
             <td colspan="3" class="tabDetailViewDF">
             	 <select name="edtBancoTerceiro" id="edtBancoTerceiro" style="width:350px">
                 <option value="0">Selecione uma Opção</option>
				 <?php 
					 //Monta o while para gerar o combo de escolha
					 while ($lookup_bancos = mysql_fetch_object($dados_bancos)) { 
				 ?>
                 <option value="<?php echo $lookup_bancos->id ?>"><?php echo $lookup_bancos->codigo . " - " . $lookup_bancos->nome ?> </option>
                 <?php } ?>
               </select>					 
						 </td>
          </tr>
					<tr>
             <td class="dataLabel" width="20%">
               <span class="dataLabel">Agência:</span>             
						 </td>
             <td class="tabDetailViewDF">
               	<input name="edtAgenciaTerceiro" type="text" class="datafield" id="edtAgenciaTerceiro" size="10" maxlength="10">				 
						 </td>
						 <td class="dataLabel" width="100">
               <span class="dataLabel">Nº Conta:</span>             
						 </td>
             <td class="tabDetailViewDF">
               	<input name="edtContaTerceiro" type="text" class="datafield" id="edtContaTerceiro" size="10" maxlength="10">				 
						 </td>
          </tr>
					<tr>
             <td class="dataLabel" width="20%">
               <span class="dataLabel">Recebido Em:</span>             
						 </td>
             <td colspan="3" class="tabDetailViewDF">
               <?php
							    //Define a data do formul&aacute;rio
							    $objData->strFormulario = "frmContaQuita";  
							    //Nome do campo que deve ser criado
							    $objData->strNome = "edtRecebido";
								$objData->strRequerido = true;
							    //Valor a constar dentro do campo (p/ altera&ccedil;&atilde;o)
							    $objData->strValor = date("d/m/Y", mktime());
							    //Define o tamanho do campo 
							    //$objData->intTamanho = 15;
							    //Define o n&uacute;mero maximo de caracteres
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
             <td colspan="4" class="tabDetailViewDF">
             	 <textarea name="edtObservacoesCheque" wrap="virtual" class="datafield" id="edtObservacoesCheque" style="width: 100%; height: 100px"></textarea>
						 </td>
      	  </tr>  
      	</table>
     	</div>
      
        <?php
  }
  
  ?>
  
	  	</td>
	  </tr>	  
	</table>
	</td>
  </tr>
</table>
</form>