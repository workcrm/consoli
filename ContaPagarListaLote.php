<?php
###########
## Módulo para Listagem das Contas a Pagar em LOTE
## Criado: 07/06/2007 - Maycon Edinger
## Alterado: 14/08/2007 - Maycon Edinger
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
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

//Pega os valores padrão que vem do formulario
$dataIni = DataMySQLInserir($_GET[DataIni]);
$dataFim = DataMySQLInserir($_GET[DataFim]);

//Verifica a situação informada
if ($_GET["TipoSituacao"] > 0 AND $_GET["TipoSituacao"] != 4)
{
	
	//Efetua o switch da situacao informada
	switch ($_GET["TipoSituacao"]) 
	{
		//Se for 1 então é visualização em aberto
		case 1:
			$texto_situacao = " <b>e com Situação:</b> Em aberto";
		break;		
		//Se for 2 então é visualização das pagas
		case 2:
			$texto_situacao = " <b>e com Situação:</b> Pagas";
		break;
		//Se for 3 então é visualização das vencidas
		case 3:
			$texto_situacao = " <b>e com Situação:</b> Vencidas";
		break;
	}	
	
	$TipoSituacao = $_GET["TipoSituacao"];
	$TextoSituacao = " AND pag.situacao = '$TipoSituacao'";
	
} 

//Verifica a regiao informada
if ($_GET["Regiao"] > 0)
{
	
	$Regiao = $_GET["Regiao"];
	
	//Recupera o nome do grupo selecionado
	$sql_regiao = mysql_query("SELECT nome FROM regioes WHERE id = '$Regiao'");
	
	//Monta o array com os dados
	$dados_regiao = mysql_fetch_array($sql_regiao);
	
	$texto_regiao = "<br/><b>Regional:</b> " . $dados_regiao["nome"];	
	
	$TipoSituacao = $_GET["TipoSituacao"];
	$WhereRegiao = " AND pag.regiao_id = $Regiao";
	
} 

//Verifica se foi informado alguma data para filtrar junto
if ($dataIni != 0) 
{
	$TextoFiltraData = "</br><b>E com data de vencimento entre: </b>$_GET[DataIni]<b> a </b>$_GET[DataFim]";
	$TextoSQLData = "	 AND pag.data_vencimento >= '$dataIni' AND pag.data_vencimento <= '$dataFim' ";
}


//Recebe os valores vindos do formulário
//Efetua o switch para o campo de tipo de listagem
switch ($_GET[TipoListagem]) 
{
	//Se for 1 então é visualização por data
	case 1: 
		//Monta o título da página
		$titulo = "Relação de Contas a Pagar por data de vencimento"; 

		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo Contas a pagar com data de vencimento entre: </b>$_GET[DataIni]<b> a </b>$_GET[DataFim] $texto_situacao $texto_regiao";

		//Monta o sql
		$sql = "SELECT 
							pag.id,
							pag.data,
							pag.valor,
							pag.tipo_pessoa,
							pag.pessoa_id,
							pag.data_vencimento,
							pag.descricao,
							pag.situacao,
							pag.origem_conta,
							pag.valor_pago,
							eve.nome as evento_nome
						FROM 
							contas_pagar pag
						LEFT OUTER JOIN 
							eventos eve ON eve.id = pag.evento_id
						WHERE 
							pag.empresa_id = '$empresaId' 
						AND 
							pag.data_vencimento >= '$dataIni' 
						AND 
							pag.data_vencimento <= '$dataFim' 
						AND
							pag.situacao = 1
							$WhereRegiao
						ORDER BY 
							pag.data_vencimento, pag.descricao";

		//Monta o texto para caso não houver registros
		$texto_vazio = "Não há contas a pagar entre as datas informadas";
	break;
  
	//Se for 2 então é visualização por grupo
	case 2: 
		//Monta o título da página
		$titulo = "Relação de Contas a Pagar por Centro de Custo"; 
		$grupoId = $_GET[GrupoId];		
		
		//Recupera o nome do grupo selecionado
		$sql_grupo = mysql_query("SELECT nome FROM grupo_conta WHERE id = '$grupoId'");
		
		//Monta o array com os dados
		$dados_grupo = mysql_fetch_array($sql_grupo);
				
		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo Contas a pagar do centro de custo: </b>$dados_grupo[nome]" . $TextoFiltraData . $texto_situacao . $texto_regiao;

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
							pag.id,
							pag.data,
							pag.valor,
							pag.tipo_pessoa,
							pag.pessoa_id,
							pag.data_vencimento,
							pag.descricao,
							pag.situacao,
							pag.origem_conta,
							pag.valor_pago,
							eve.nome as evento_nome
						FROM 
							contas_pagar pag
						LEFT OUTER JOIN 
							eventos eve ON eve.id = pag.evento_id
						WHERE 
							pag.empresa_id = '$empresaId' 
						AND 
							pag.grupo_conta_id = '$grupoId' 
						AND
							pag.situacao = 1
							$TextoSQLData 
							$WhereRegiao				
						ORDER BY 
							pag.data_vencimento, pag.descricao";

		//Monta o texto para caso não houver registros
		$texto_vazio = "Não há contas a pagar para o centro de custo $dados_grupo[nome]";
	break;

	//Se for 3 então é visualização por categoria
	case 3: 
		//Monta o título da página
		$titulo = "Relação de Contas a Pagar por Evento"; 
		$eventoId = $_GET[EventoId];
		
		//Recupera o nome da categoria selecionada
		$sql_evento = mysql_query("SELECT nome FROM eventos WHERE id = '$eventoId'");
		
		//Monta o array com os dados
		$dados_evento = mysql_fetch_array($sql_evento);
		
		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo Contas a pagar do evento: </b>$dados_evento[nome]" . $TextoFiltraData . $texto_situacao . $texto_regiao;

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
							pag.id,
							pag.data,
							pag.valor,
							pag.tipo_pessoa,
							pag.pessoa_id,
							pag.data_vencimento,
							pag.descricao,
							pag.situacao,
							pag.origem_conta,
							pag.valor_pago,
							eve.nome as evento_nome
						FROM 
							contas_pagar pag
						LEFT OUTER JOIN 
							eventos eve ON eve.id = pag.evento_id
						WHERE 
							pag.empresa_id = '$empresaId' 
						AND 
							pag.evento_id = '$eventoId' 
						AND
							pag.situacao = 1
							$TextoSQLData 
							$WhereRegiao
						ORDER BY 
							pag.data_vencimento, pag.descricao";
		
		//Monta o texto para caso não houver registros
		$texto_vazio = "Não há contas a pagar para a categoria $dados_categoria[nome]";
	break;
	
	//Se for 4 então é visualização por situacao
	case 4: 
		//Monta o título da página
		$titulo = "Relação de Contas a Pagar por Situação"; 
		$TipoSituacao = $_GET[TipoSituacao];
		
		//Efetua o switch da situacao informada
		switch ($TipoSituacao) 
		{
			//Se for 1 então é visualização em aberto
			case 1:
				$texto_situacao = "Em aberto";
				$where_situacao = "pag.situacao = '$TipoSituacao'";
			break;		
			//Se for 2 então é visualização das pagas
			case 2:
				$texto_situacao = "Pagas";
				$where_situacao = "pag.situacao = '$TipoSituacao'";
			break;
			//Se for 3 então é visualização das vencidas
			case 3:
				$texto_situacao = "Vencidas";
				$data_base_vencimento = date("Y-m-d", mktime());
				$where_situacao = "pag.situacao = '1' AND pag.data_vencimento < '$data_base_vencimento'";
			break;
		}				
		
		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo Contas a pagar com situação: </b>$texto_situacao" . $TextoFiltraData . $texto_regiao;

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
							pag.id,
							pag.data,
							pag.valor,
							pag.tipo_pessoa,
							pag.pessoa_id,
							pag.data_vencimento,
							pag.descricao,
							pag.situacao,
							pag.origem_conta,
							pag.valor_pago,
							eve.nome as evento_nome
						FROM 
							contas_pagar pag
						LEFT OUTER JOIN 
							eventos eve ON eve.id = pag.evento_id
						WHERE 
							pag.empresa_id = '$empresaId' 
						AND
							pag.situacao = 1 
							$TextoSQLData 
							$WhereRegiao
						ORDER BY 
							pag.data_vencimento, pag.descricao";
		
		//Monta o texto para caso não houver registros
		$texto_vazio = "Não há contas a pagar com a situação $texto_situacao";
	break;
	
	//Se for 5 então é visualização por sacado
	case 5: 
		//Monta o título da página
		$titulo = "Relação de Contas a Pagar por Sacado"; 
		$TipoPessoa = $_GET[TipoPessoa];
		$PessoaId = $_GET[PessoaId];
		
		//Efetua o switch da pessoa informada
		switch ($TipoPessoa) 
		{
			//Se for 1 então é cliente
			case 1:
				//Recupera o nome do cliente
				$query_cliente = mysql_query("SELECT nome FROM clientes WHERE id = '$PessoaId'");
				$nome_cliente = mysql_fetch_array($query_cliente);			
				$texto_pessoa = "<b>Cliente:</b> $nome_cliente[nome]";
			break;		
			//Se for 2 então é fornecedor
			case 2:
				//Recupera o nome do fornecedor
				$query_fornecedor = mysql_query("SELECT nome FROM fornecedores WHERE id = '$PessoaId'");
				$nome_fornecedor = mysql_fetch_array($query_fornecedor);
				$texto_pessoa = "<b>Fornecedor:</b> $nome_fornecedor[nome]";
			break;
			//Se for 3 então é colaborador
			case 3:
			//Recupera o nome do colaborador
				$query_colaborador = mysql_query("SELECT nome FROM colaboradores WHERE id = '$PessoaId'");
				$nome_colaborador = mysql_fetch_array($query_colaborador);
				$texto_pessoa = "<b>Colaborador:</b> $nome_colaborador[nome]";
			break;
		}				
		
		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo Contas a Pagar do </b>$texto_pessoa" . $TextoFiltraData . $texto_situacao . $texto_regiao;

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
							pag.id,
							pag.data,
							pag.valor,
							pag.tipo_pessoa,
							pag.pessoa_id,
							pag.data_vencimento,
							pag.descricao,
							pag.situacao,
							pag.origem_conta,
							pag.valor_pago,
							eve.nome as evento_nome
						FROM 
							contas_pagar pag
						LEFT OUTER JOIN 
							eventos eve ON eve.id = pag.evento_id
						WHERE 
							pag.empresa_id = '$empresaId' 
						AND 
							pag.tipo_pessoa = '$TipoPessoa' 
						AND 
							pag.pessoa_id = '$PessoaId'
						AND
							pag.situacao = 1 
							$TextoSQLData
							$WhereRegiao
						ORDER BY 
							pag.data_vencimento, pag.descricao";
		
		//Monta o texto para caso não houver registros
		$texto_vazio = "Não há contas a pagar do $texto_pessoa";
	break;
	
	//Se for 6 então é visualização por subgrupo
	case 6: 
		//Monta o título da página
		$titulo = "Relação de Contas a Pagar por Conta-caixa"; 
		$subgrupoId = $_GET[SubgrupoId];		
		
		//Recupera o nome do subgrupo selecionado
		$sql_subgrupo = mysql_query("SELECT nome FROM subgrupo_conta WHERE id = '$subgrupoId'");
		
		//Monta o array com os dados
		$dados_subgrupo = mysql_fetch_array($sql_subgrupo);
				
		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo Contas a pagar da conta-caixa: </b>$dados_subgrupo[nome]" . $TextoFiltraData . $texto_situacao . $texto_regiao;

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
							pag.id,
							pag.data,
							pag.valor,
							pag.tipo_pessoa,
							pag.pessoa_id,
							pag.data_vencimento,
							pag.descricao,
							pag.situacao,
							pag.origem_conta,
							pag.valor_pago,
							eve.nome as evento_nome
						FROM 
							contas_pagar pag
						LEFT OUTER JOIN 
							eventos eve ON eve.id = pag.evento_id
						WHERE 
							pag.empresa_id = '$empresaId' 
						AND 
							pag.subgrupo_conta_id = '$subgrupoId'
						AND
							pag.situacao = 1
							$TextoSQLData 
						ORDER BY 
							pag.data_vencimento, pag.descricao";
		
		//Monta o texto para caso não houver registros
		$texto_vazio = "Não há contas a pagar para a conta-caixa $dados_subgrupo[nome]";
	break;			
	
	//Se for 7 então é visualização agrupada
	case 7: 
		//Monta o título da página
		$titulo = "Relação de Contas a Pagar por Conta-caixa e Centro de Custo"; 
		$grupoId = $_GET[GrupoId];
		$subgrupoId = $_GET[SubgrupoId];		
		
		//Recupera o nome do grupo selecionado
		$sql_grupo = mysql_query("SELECT nome FROM grupo_conta WHERE id = '$grupoId'");
		
		//Monta o array com os dados
		$dados_grupo = mysql_fetch_array($sql_grupo);
		
		//Recupera o nome do subgrupo selecionado
		$sql_subgrupo = mysql_query("SELECT nome FROM subgrupo_conta WHERE id = '$subgrupoId'");
		
		//Monta o array com os dados
		$dados_subgrupo = mysql_fetch_array($sql_subgrupo);
						
		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo Contas a pagar da conta-caixa: </b>$dados_subgrupo[nome]<br>
											 <b>e do centro de custo: </b>$dados_grupo[nome]" . $TextoFiltraData . $texto_situacao . $texto_regiao;

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
							pag.id,
							pag.data,
							pag.valor,
							pag.tipo_pessoa,
							pag.pessoa_id,
							pag.data_vencimento,
							pag.descricao,
							pag.situacao,
							pag.origem_conta,
							pag.valor_pago,
							eve.nome as evento_nome
						FROM 
							contas_pagar pag
						LEFT OUTER JOIN 
							eventos eve ON eve.id = pag.evento_id
						WHERE 
							pag.empresa_id = '$empresaId' 
						AND 
							pag.grupo_conta_id = '$grupoId' 
						AND 
							pag.subgrupo_conta_id = '$subgrupoId' 
						AND
							pag.situacao = 1
							$TextoSQLData 
							$WhereRegiao				
						ORDER BY 
							pag.data_vencimento, pag.descricao";

		//Monta o texto para caso não houver registros
		$texto_vazio = "Não há contas a pagar para este agrupamento !";
	break;	
}

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">
<form id="cpag" name="cpag" method="post" action="ContaPagarListaLoteProcessa.php" target="frame">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td colspan="2" valign='top'>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td>
						<img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo"><?php echo $titulo ?> - EM LOTE</span>			  	
					</td>
				</tr>
				<tr>
					<td colspan='5'>
						<img src="image/bt_espacohoriz.gif" width="100%" height="12">			    	
						<?php echo $desc_filtragem ?>
						</br></br>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style="PADDING-BOTTOM: 2px">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="180">
						<input name='Button' type='button' class="button" id="Submit" title="Processar Pagamento" value='Processar Pagamento' onclick="if(confirm('Confirma o Pagamento das Contas selecionadas ?')) {document.cpag.submit();}" />
					</td>
					<td width="110">
						Data Processamento:
					</td>
					<td>
						<?php

							//Define a data do formulário
							$objData->strFormulario = "cadastro";  
							//Nome do campo que deve ser criado
							$objData->strNome = "edtDataProcessa";
							//Valor a constar dentro do campo (p/ alteração)
							$objData->strValor = Date("d/m/Y", mktime());
							//Cria o componente com seu calendario para escolha da data
							$objData->CriarData();

						?>
					</td>
					<td width="250" align='right'>
						<input class="button" title="Retorna ao Módulo de Contas a Pagar" name='btnVoltar' type='button' id='btnVoltar' value='Retornar a Contas a Pagar' onclick="wdCarregarFormulario('ModuloContasPagar.php','conteudo')" />	
					</td>
				</tr>
			</table>
		</td>		
	</tr>
</table>
<iframe id="frame" name="frame" frameborder="0" style="width: 500px; height: 40px;"></iframe>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td>
			<?php					   
					  

				//Executa a Query
				$query = mysql_query($sql);		  	  
				
				//verifica o número total de registros
				$tot_regs = mysql_num_rows($query);
		  
				//Gera a variável com o total de contas a pagar
				$total_pagar = 0;
				$saldo_pagar = 0;

			?>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView">
				<?php
				
					//Caso houverem registros
					if ($tot_regs > 0) 
					{ 
						
						echo "<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
										<td width='35' align='center'>A</td>
										<td>&nbsp;&nbsp;Dados do Sacado/Descrição da Conta a Pagar</td>
										<td width='66' align='center'>Emissão</td>
										<td width='66' align='center'>Vencto</td>
										<td width='80' align='right'>Valor</td>
										<td width='80' align='right'>A Pagar</td>
										<td width='65' align='center'>Situação</td>
										<td>&nbsp;</td>          
									</tr>";
					
					}
	  
					//Caso não houverem registros
					if ($tot_regs == 0) 
					{ 

						//Exibe uma linha dizendo que nao há registros
						echo "<tr height='24'>
										<td colspan='8' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' nowrap='nowrap'>
											<font color='#33485C'><strong>$texto_vazio</strong></font>
										</td>
									</tr>";

					} 
					
					else 
					
					{
				  
						//Cria o array e o percorre para montar a listagem dinamicamente
						while ($dados_rec = mysql_fetch_array($query))
						{

							//Efetua o switch para recuperar o nome do sacado
							switch ($dados_rec[tipo_pessoa]) 
							{
								
								case 1: //Se for cliente
									$sql = mysql_query("SELECT nome FROM clientes WHERE id = '$dados_rec[pessoa_id]'");
									$desc_pessoa = "Cliente";
									$dados_pessoa = mysql_fetch_array($sql);				
								break;
								case 2: //Se for fornecedor
									$sql = mysql_query("SELECT nome FROM fornecedores WHERE id = '$dados_rec[pessoa_id]'");
									$desc_pessoa = "Fornecedor";
									$dados_pessoa = mysql_fetch_array($sql);				
								break;
								case 3: //Se for colaborador
									$sql = mysql_query("SELECT nome FROM colaboradores WHERE id = '$dados_rec[pessoa_id]'");
									$desc_pessoa = "Colaborador";
									$dados_pessoa = mysql_fetch_array($sql);				
								break;			
							}		

							//Efetua o switch para o campo de situacao
							switch ($dados_rec[situacao]) 
							{
								case 1: $desc_situacao = "Em aberto"; break;
								case 2: $desc_situacao = "Pago"; break;
							}

							//Fecha o php, mas o while continua
							?>
							<tr height="16">
								<td align="center" style="border-top: 1px dotted">
									<?php

										//So mostra o checkbox se a conta estiver em aberto
										if ($dados_rec[situacao] == 1)
										{

											?>
											<input id="CPG_<?php echo $dados_rec['id'] ?>" name="CPG_<?php echo $dados_rec['id'] ?>" class="chk_conta" type="checkbox" value="<?php echo $dados_rec['id'] ?>" checked onclick="aloca_checkbox(document.cpag, 'CPG_', 'conta_id');">		
											<?php

										}

									?>
								</td>
								<td height="20" style="padding-left: 4px; border-top: 1px dotted">
									<font color='#CC3300' size='2' face="Tahoma">
									<a title="Clique para exibir esta conta a pagar" href="#" onclick="wdCarregarFormulario('ContaPagarExibe.php?ContaId=<?php echo $dados_rec[id] ?>','conteudo')"><?php echo $dados_pessoa[nome]; ?></a>
									</font>
									</br>
									<span style="font-size: 9px"><?php echo $dados_rec['descricao'] ?>
									<br/>
									<?php 
					
										if ($dados_rec["origem_conta"] == 2)
										{
											
											echo "<span style='color: #990000'>&nbsp;<b>$dados_rec[evento_nome]</b></span>";
										
										} 
										else if ($dados_rec["origem_conta"] == 1)
										{
										
											echo "<b>Gerada pelo Contas a Pagar</b>";	
										}
										else if ($dados_rec["origem_conta"] == 3)
										{
										
											echo "<b>Gerada por Pedido do Foto e Vídeo</b>";	
										}
					
									?>
									</span>      
								</td>
								<td align="center" style="border-top: 1px dotted">
									<?php echo DataMySQLRetornar($dados_rec[data]) ?>				
								</td>
								<td align="center" style="border-top: 1px dotted">
									<?php echo DataMySQLRetornar($dados_rec[data_vencimento]) ?>				
								</td>			
								<td align="right" style="border-top: 1px dotted">
									<?php 
										
										echo "R$ " . number_format($dados_rec[valor], 2, ",", ".");
										$total_pagar = $total_pagar + $dados_rec[valor]; 
									
									?>
								</td>
								<td align="right" style="border-top: 1px dotted">
									<?php 
										
										echo "R$ " . number_format($dados_rec[valor] - $dados_rec[valor_pago], 2, ",", ".");
										$saldo_pagar = $saldo_pagar + ($dados_rec[valor] - $dados_rec[valor_pago]);
									
									?>
								</td>
								<td align="center" style="border-top: 1px dotted">
									<?php echo $desc_situacao ?>				
								</td>
								<td style="border-top: 1px dotted">
									&nbsp;									
								</td>			
							</tr>
							<?php
								
						//Fecha o WHILE
						};
	
					//Fecha o if de se tem registros
					}

					//Verifica se precisa imprimir o rodapé
					if ($tot_regs > 0) 
					{ 
					
						?>
						<tr height='16'>
							<td colspan="4" height="20" align="right"><strong>Total:&nbsp;&nbsp;</strong></td>
							<td height="20" valign='middle' nowrap='nowrap' bgcolor='#fdfdfd' style="border-top: 1px solid" align="right">
								<?php echo "R$ " . number_format($total_pagar, 2, ",", ".") ?>
							</td>
							<td height="20" valign='middle' nowrap='nowrap' bgcolor='#fdfdfd' style="border-top: 1px solid" align="right">
								<?php echo "R$ " . number_format($saldo_pagar, 2, ",", ".") ?>
							</td>					
						</tr>	
						<?php
				
					//Fecha o IF
					};

				?>
			</table>	
		</td>
	</tr>
	<tr>
		<td>
			<textarea id="conta_id" name="conta_id" style="display: none; width: 500px; height: 300px;"></textarea>
		</td>
	</tr>
</table>
</form>
<script>


//Função para selecionar todos os elementos de checkbox do formulário
function aloca_checkbox(formulario, campo_pesquisa, campo_retorno)
{
  
  var Form = formulario;
  var total = '';

  //Percorre o array dos checkboxes de usuários	e verifica se ele está marcado
  for (i = 0; i < Form.elements.length; i++) 
  {

    if (Form.elements[i].type == "checkbox" && isNaN(Form.elements[i].value) == false) 
    {
      
      nome_campo = Form.elements[i].id;

      if (nome_campo.substr(0, 4) == campo_pesquisa)
      {

        if (Form.elements[i].checked == true) 
        {
          total = total + Form.elements[i].value + ';'
        }
        
      }

    }

  }

  document.getElementById(campo_retorno).value = total;

}

	$(document).ready(function() 
  {

  	aloca_checkbox(document.cpag, 'CPG_', 'conta_id');

  })


</script>