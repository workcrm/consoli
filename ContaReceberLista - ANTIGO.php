<?php
###########
## Módulo para Listagem das Contas a Receber
## Criado: 07/06/2007 - Maycon Edinger
## Alterado: 14/08/2007 - Maycon Edinger
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Pega os valores padrão que vem do formulario
$dataIni = DataMySQLInserir($_GET[DataIni]);
$dataFim = DataMySQLInserir($_GET[DataFim]);

//Verifica a situação informada
if ($_GET["TipoSituacao"] > 0 AND $_GET["TipoSituacao"] != 4){
	
		//Efetua o switch da situacao informada
		switch ($_GET["TipoSituacao"]) {
  	//Se for 1 então é visualização em aberto
		case 1:
			$texto_situacao = " <b>e com Situação:</b> <span style='color: #990000'>Em aberto</span>";
		break;		
  	//Se for 2 então é visualização das recebidas
		case 2:
			$texto_situacao = " <b>e com Situação:</b> <span style='color: #990000'>Recebidas</span>";
		break;
  	//Se for 3 então é visualização das vencidas
		case 3:
			$texto_situacao = " <b>e com Situação:</b> <span style='color: #990000'>Vencidas</span>";
		break;
		}	
		
	$TipoSituacao = $_GET["TipoSituacao"];
	$TextoSituacao = " AND rec.situacao = '$TipoSituacao'";
	
} 

//Verifica se foi informado alguma data para filtrar junto
if ($dataIni != 0) {
	$TextoFiltraData = "</br><b>E com data de vencimento entre: </b><span style='color: #990000'>$_GET[DataIni]</span><b> a </b><span style='color: #990000'>$_GET[DataFim]</span>";
	$TextoSQLData = "	 AND rec.data_vencimento >= '$dataIni' AND rec.data_vencimento <= '$dataFim' ";
}


//Recebe os valores vindos do formulário
//Efetua o switch para o campo de tipo de listagem
switch ($_GET[TipoListagem]) {
  //Se for 1 então é visualização por data
	case 1: 
		//Monta o título da página
		$titulo = "Relação de Contas a Pagar por data de vencimento"; 

		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo Contas a Receber com data de vencimento entre: </b><span style='color: #990000'>$_GET[DataIni]</span><b> a </b><span style='color: #990000'>$_GET[DataFim]</span> $texto_situacao";

		//Monta o sql
		$sql = "SELECT 
							rec.id,
							rec.data,
							rec.valor,
							rec.tipo_pessoa,
							rec.pessoa_id,
							rec.data_vencimento,
							rec.descricao,
							rec.situacao,
							rec.origem_conta,
							rec.valor_recebido,
              rec.boleto_id,
							eve.nome as evento_nome
							FROM contas_receber rec
							LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
							WHERE rec.empresa_id = '$empresaId' AND rec.data_vencimento >= '$dataIni' AND rec.data_vencimento <= '$dataFim' $TextoSituacao 
							ORDER BY rec.data_vencimento, rec.descricao";

		//Monta o texto para caso não houver registros
		$texto_vazio = "Não há contas a Receber entre as datas informadas";
	break;
  
	//Se for 2 então é visualização por centro de custo
	case 2: 
		//Monta o título da página
		$titulo = "Relação de Contas a Receber por Centro de Custo"; 
		$grupoId = $_GET[GrupoId];		
		
		//Recupera o nome do grupo selecionado
		$sql_grupo = mysql_query("SELECT nome FROM grupo_conta WHERE id = '$grupoId'");
		
		//Monta o array com os dados
		$dados_grupo = mysql_fetch_array($sql_grupo);
				
		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo Contas a Receber do centro de custo: </b><span style='color: #990000'>$dados_grupo[nome]</span>" . $TextoFiltraData . $texto_situacao;

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
							rec.id,
							rec.data,
							rec.valor,
							rec.tipo_pessoa,
							rec.pessoa_id,
							rec.data_vencimento,
							rec.descricao,
							rec.situacao,
							rec.origem_conta,
							rec.valor_recebido,
              rec.boleto_id,
							eve.nome as evento_nome
							FROM contas_receber rec
							LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
							WHERE rec.empresa_id = $empresaId AND rec.grupo_conta_id = '$grupoId' $TextoSQLData $TextoSituacao 
							ORDER BY rec.data_vencimento, rec.descricao";
		
		//Monta o texto para caso não houver registros
		$texto_vazio = "Não há contas a Receber para o centro de custo <span style='color: #990000'>$dados_grupo[nome]</span>";
	break;

	//Se for 3 então é visualização por evento
	case 3: 
		//Monta o título da página
		$titulo = "Relação de Contas a Receber por Evento"; 
		$eventoId = $_GET[EventoId];
		
		//Recupera o nome da categoria selecionada
		$sql_evento = mysql_query("SELECT nome FROM eventos WHERE id = '$eventoId'");
		
		//Monta o array com os dados
		$dados_evento = mysql_fetch_array($sql_evento);
		
		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo Contas a Receber do evento: </b><span style='color: #990000'>$dados_evento[nome]</span>" . $TextoFiltraData . $texto_situacao;

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
							rec.id,
							rec.data,
							rec.valor,
							rec.tipo_pessoa,
							rec.pessoa_id,
							rec.data_vencimento,
							rec.descricao,
							rec.situacao,
							rec.origem_conta,
							rec.valor_recebido,
              rec.boleto_id,
							eve.nome as evento_nome
							FROM contas_receber rec
							LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
							WHERE rec.empresa_id = '$empresaId' AND rec.evento_id = '$eventoId' $TextoSQLData $TextoSituacao 
							ORDER BY rec.data_vencimento, rec.descricao";
		
		//Monta o texto para caso não houver registros
		$texto_vazio = "Não há contas a Receber para o evento <span style='color: #990000'>$dados_evento[nome]</span>";
	break;
	
	//Se for 4 então é visualização por situacao
	case 4: 
		//Monta o título da página
		$titulo = "Relação de Contas a Receber por Situação"; 
		$TipoSituacao = $_GET[TipoSituacao];
		
		//Efetua o switch da situacao informada
		switch ($TipoSituacao) {
  	//Se for 1 então é visualização em aberto
		case 1:
			$texto_situacao = "Em aberto";
			$where_situacao = "rec.situacao = '$TipoSituacao'";
		break;		
  	//Se for 2 então é visualização das recebidas
		case 2:
			$texto_situacao = "Pagas";
			$where_situacao = "rec.situacao = '$TipoSituacao'";
		break;
  	//Se for 3 então é visualização das vencidas
		case 3:
			$texto_situacao = "Vencidas";
			$data_base_vencimento = date("Y-m-d", mktime());
			$where_situacao = "rec.situacao = '1' AND rec.data_vencimento < '$data_base_vencimento'";
		break;
		}				
		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo Contas a Receber com situação: </b><span style='color: #990000'>$texto_situacao</span>" . $TextoFiltraData;

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
							rec.id,
							rec.data,
							rec.valor,
							rec.tipo_pessoa,
							rec.pessoa_id,
							rec.data_vencimento,
							rec.descricao,
							rec.situacao,
							rec.origem_conta,
							rec.valor_recebido,
              rec.boleto_id,
							eve.nome as evento_nome
							FROM contas_receber rec
							LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
							WHERE rec.empresa_id = $empresaId AND $where_situacao $TextoSQLData 
							ORDER BY rec.data_vencimento, rec.descricao";
		
		//Monta o texto para caso não houver registros
		$texto_vazio = "Não há contas a Receber com a situação <span style='color: #990000'>$texto_situacao</span>";
	break;
	
	//Se for 5 então é visualização por sacado
	case 5: 
		//Monta o título da página
		$titulo = "Relação de Contas a Receber por Sacado"; 
		$TipoPessoa = $_GET[TipoPessoa];
		$PessoaId = $_GET[PessoaId];
		
		//Efetua o switch da pessoa informada
		switch ($TipoPessoa) {
  	//Se for 1 então é cliente
		case 1:
			//Recupera o nome do cliente
			$query_cliente = mysql_query("SELECT nome FROM clientes WHERE id = '$PessoaId'");
			$nome_cliente = mysql_fetch_array($query_cliente);			
			$texto_pessoa = "<b>Cliente:</b> <span style='color: #990000'>$nome_cliente[nome]</span>";
		break;		
  	//Se for 2 então é fornecedor
		case 2:
			//Recupera o nome do fornecedor
			$query_fornecedor = mysql_query("SELECT nome FROM fornecedores WHERE id = '$PessoaId'");
			$nome_fornecedor = mysql_fetch_array($query_fornecedor);
			$texto_pessoa = "<b>Fornecedor:</b> <span style='color: #990000'>$nome_fornecedor[nome]</span>";
		break;
  	//Se for 3 então é colaborador
		case 3:
			//Recupera o nome do colaborador
			$query_colaborador = mysql_query("SELECT nome FROM colaboradores WHERE id = '$PessoaId'");
			$nome_colaborador = mysql_fetch_array($query_colaborador);
			$texto_pessoa = "<b>Colaborador:</b> <span style='color: #990000'>$nome_colaborador[nome]</span>";
		break;
		}				
		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo Contas a Receber do </b><span style='color: #990000'>$texto_pessoa</span>" . $TextoFiltraData . $texto_situacao;

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
							rec.id,
							rec.data,
							rec.valor,
							rec.tipo_pessoa,
							rec.pessoa_id,
							rec.data_vencimento,
							rec.descricao,
							rec.situacao,
							rec.origem_conta,
              rec.boleto_id,
							rec.valor_recebido,
							eve.nome as evento_nome
							FROM contas_receber rec
							LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
							WHERE rec.empresa_id = $empresaId AND rec.tipo_pessoa = '$TipoPessoa' AND rec.pessoa_id = '$PessoaId' $TextoSQLData $TextoSituacao 
							ORDER BY rec.data_vencimento, rec.descricao";
		
		//Monta o texto para caso não houver registros
		$texto_vazio = "Não há contas a Receber do <span style='color: #990000'>$texto_pessoa</span>";
	break;
	
	//Se for 6 então é visualização por conta-caixa
	case 6: 
		//Monta o título da página
		$titulo = "Relação de Contas a Receber por Conta-caixa"; 
		$subgrupoId = $_GET[SubgrupoId];		
		
		//Recupera o nome do subgrupo selecionado
		$sql_subgrupo = mysql_query("SELECT nome FROM subgrupo_conta WHERE id = '$subgrupoId'");
		
		//Monta o array com os dados
		$dados_subgrupo = mysql_fetch_array($sql_subgrupo);
				
		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo Contas a Receber da conta-caixa: </b><span style='color: #990000'>$dados_subgrupo[nome]</span>" . $TextoFiltraData . $texto_situacao;

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
							rec.id,
							rec.data,
							rec.valor,
							rec.tipo_pessoa,
							rec.pessoa_id,
							rec.data_vencimento,
							rec.descricao,
							rec.situacao,
							rec.origem_conta,
							rec.valor_recebido,
              rec.boleto_id,
							eve.nome as evento_nome
							FROM contas_receber rec
							LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
							WHERE rec.empresa_id = $empresaId AND rec.subgrupo_conta_id = '$subgrupoId' $TextoSQLData $TextoSituacao 
							ORDER BY rec.data_vencimento, rec.descricao";
		
		//Monta o texto para caso não houver registros
		$texto_vazio = "Não há contas a Receber para a conta-caixa <span style='color: #990000'>$dados_subgrupo[nome]</span>";
	break;			
	
	//Se for 7 então é visualização agrupada
	case 7: 
		//Monta o título da página
		$titulo = "Relação de Contas a Receber por Conta-caixa e Centro de Custo"; 
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
		$desc_filtragem = "<b>Exibindo Contas a Receber da conta-caixa: </b><span style='color: #990000'>$dados_subgrupo[nome]</span><br>
											 <b>e do centro de custo: </b><span style='color: #990000'>$dados_grupo[nome]</span>" . $TextoFiltraData . $texto_situacao;

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
							rec.id,
							rec.data,
							rec.valor,
							rec.tipo_pessoa,
							rec.pessoa_id,
							rec.data_vencimento,
							rec.descricao,
							rec.situacao,
							rec.origem_conta,
							rec.valor_recebido,
              rec.boleto_id,
							eve.nome as evento_nome
							FROM contas_receber rec
							LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
							WHERE rec.empresa_id = $empresaId AND rec.grupo_conta_id = '$grupoId' AND rec.subgrupo_conta_id = '$subgrupoId' $TextoSQLData $TextoSituacao 
							ORDER BY rec.data_vencimento, rec.descricao";

		//Monta o texto para caso não houver registros
		$texto_vazio = "Não há contas a Receber para este agrupamento !";
	break;
  
 	//Se for 8 então é visualização por evento e formando
	case 8: 
		//Monta o título da página
		$titulo = "Relação de Contas a Receber por Evento e Formando"; 
		$eventoId = $_GET[EventoId];
    $formandoId = $_GET[FormandoId];
		
		//Recupera o nome da categoria selecionada
		$sql_evento = mysql_query("SELECT nome FROM eventos WHERE id = '$eventoId'");
		
		//Monta o array com os dados
		$dados_evento = mysql_fetch_array($sql_evento);
    
    //Recupera o nome do formando
		$sql_formando = mysql_query("SELECT nome FROM eventos_formando WHERE id = '$formandoId'");
		
		//Monta o array com os dados
		$dados_formando = mysql_fetch_array($sql_formando);
		
		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo Contas a Receber do evento: </b><span style='color: #990000'>$dados_evento[nome]</span><br/><strong>E do formando: </strong><span style='color: #990000'>$dados_formando[nome]</span>" . $TextoFiltraData . $texto_situacao;

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
							rec.id,
							rec.data,
							rec.valor,
							rec.tipo_pessoa,
							rec.pessoa_id,
							rec.data_vencimento,
							rec.descricao,
							rec.situacao,
							rec.origem_conta,
							rec.valor_recebido,
              rec.boleto_id,
							eve.nome as evento_nome
							FROM contas_receber rec
							LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
							WHERE rec.empresa_id = '$empresaId' AND rec.evento_id = '$eventoId' AND rec.formando_id = '$formandoId' $TextoSQLData $TextoSituacao 
							ORDER BY rec.data_vencimento, rec.descricao";
		
		//Monta o texto para caso não houver registros
		$texto_vazio = "Não há contas a Receber para este evento e formando";
	break;	
}

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="2" valign="top">
		  <table width="100%" cellpadding="0" cellspacing="0" border="0">
		    <tr>
		      <td>
			    	<img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo"><?php echo $titulo ?></span>			  	
					</td>
		    </tr>
		    <tr>
		      <td colspan="5">
			    	<img src="image/bt_espacohoriz.gif" width="100%" height="12">			    	
			    	<?php echo $desc_filtragem ?>
						<br/><br/>
		  	  </td>
		    </tr>
		  </table>
    </td>
  </tr>
  <tr>
    <td style="PADDING-BOTTOM: 2px">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td>
      			<input name="Button" type="button" class="button" id="Submit" title="Nova Conta a Receber" value="Nova Conta a Receber" onclick="window.location='sistema.php?ModuloNome=ContaReceberCadastra';" />
					</td>
					<td align="right">
						<input class="button" title="Retorna ao Módulo de Contas a Receber" name="btnVoltar" type="button" id="btnVoltar" value="Retornar ao Módulo de Contas a Receber" onclick="wdCarregarFormulario('ModuloContasReceber.php','conteudo')" />	
					</td>
				</tr>
			</table>
		</td>		
  </tr>
 </table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
			<?php					   
		  //Verifica se o sistema não está bloqueado
		  if ($bloqueio == "1") {
		  	//Define a variável de total de registros pra simular uma consulta zerada
		  	$tot_regs = 0;
		  	
		  	//Gera um erro maluco
		  	echo "</br><b>mysql_error:<b> <i>could not perform query. Please verify if the database engine system is up and running. Fatal error (0208)</br></br>";

		  } else {		  

				//Executa a Query
			  $query = mysql_query($sql);		  	  
			  //verifica o número total de registros
			  $tot_regs = mysql_num_rows($query);
			} 	    
		  
		  //Gera a variável com o total de contas a Receber
			$total_receber = 0;
			$saldo_receber = 0;
		  ?>
	   
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView">

	<?php
	  //Caso houverem registros
	  if ($tot_regs > 0) { 
	  echo "
        <tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
	      	<td>&nbsp;&nbsp;Dados do Sacado/Descrição da Conta a Receber</td>
          <td width='66' align='center'>Emissão</td>
          <td width='66' align='center'>Vencto</td>
          <td width='80' align='right'>Valor</td>
          <td width='80' align='right'>A Receber</td>
          <td width='65' align='center'>Situação</td>
          <td width='52' colspan='2' align='center' style='padding-right: 0px'>Ação</td>          
        </tr>
	  ";}
	  
	  //Caso não houverem registros
	  if ($tot_regs == 0) { 

	  //Exibe uma linha dizendo que nao há registros
	  echo "
	  	<tr height='24'>
        <td colspan='7' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' nowrap='nowrap'>
		  <slot><font color='#33485C'><strong>$texto_vazio</strong></font></slot>
			</td>
	  </tr>	
	  ";	  
	  } else {
				  
	  //Cria o array e o percorre para montar a listagem dinamicamente
	  while ($dados_rec = mysql_fetch_array($query)){

		//Efetua o switch para recuperar o nome do sacado
		switch ($dados_rec[tipo_pessoa]) {
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
      case 4: //Se for formando
				$sql = mysql_query("SELECT nome FROM eventos_formando WHERE id = '$dados_rec[pessoa_id]'");
				$desc_pessoa = "Formando";
				$dados_pessoa = mysql_fetch_array($sql);				
			break;
      case 5: //Se for por evento
				$sql = mysql_query("SELECT nome FROM eventos WHERE id = '$dados_rec[pessoa_id]'");
				$desc_pessoa = "Evento";
				$dados_pessoa = mysql_fetch_array($sql);				
			break;			
		}		

		//Efetua o switch para o campo de situacao
		switch ($dados_rec[situacao]) {
		  case 1: $desc_situacao = "<span style='color: #990000'>Em aberto</span>"; break;
			case 2: $desc_situacao = "Recebida"; break;
		}

		//Fecha o php, mas o while continua
	  ?>

	  <tr height="16">
      <td style="border-bottom: 1px solid" height="20">
        <font color="#CC3300" size="2" face="Tahoma">
				  <a title="Clique para exibir esta conta a Receber" href="#" onclick="wdCarregarFormulario('ContaReceberExibe.php?ContaId=<?php echo $dados_rec[id] ?>','conteudo')">&nbsp;<?php echo $dados_pessoa[nome]; ?></a>
				</font>
				<br/>
				<span style="font-size: 9px">&nbsp;<?php echo $dados_rec["descricao"] ?>
				<br/>
				<?php 
				
					if ($dados_rec["origem_conta"] == 2)
          {
						
						echo "<span style='color: #990000'>&nbsp;<b>$dados_rec[evento_nome]</b></span>";
					
					} 
          else 
          {
					
						if ($dados_rec[boleto_id] > 0) 
            {
					
						  echo "&nbsp;<b>Gerada pelo Contas a Receber&nbsp;<span style='color: #990000'>(VIA BOLETO)</span></b>";
              
            }
            else
            {
              
              echo "&nbsp;<b>Gerada pelo Contas a Receber</b>";
              
            }
            
					}
				
				?>
				</span>      
			</td>
			<td style="border-bottom: 1px solid" align="center">
				<?php echo DataMySQLRetornar($dados_rec[data]) ?>				
			</td>
			<td style="border-bottom: 1px solid" align="center">
				<?php echo DataMySQLRetornar($dados_rec[data_vencimento]) ?>				
			</td>			
      <td style="border-bottom: 1px solid" align="right">
        <?php 
					echo "R$ " . number_format($dados_rec[valor], 2, ",", ".");
					$total_receber = $total_receber + $dados_rec[valor]; 
				?>
			</td>
			<td style="border-bottom: 1px solid" align="right">
        <?php 
					echo "R$ " . number_format($dados_rec[valor] - $dados_rec[valor_recebido], 2, ",", ".");
				  $saldo_receber = $saldo_receber + ($dados_rec[valor] - $dados_rec[valor_recebido]);
				?>
			</td>
			<td style="border-bottom: 1px solid" align="center">
				<?php echo $desc_situacao ?>				
			</td>
			<td width="26" style="border-bottom: 1px solid">
				<?php 
					if ($dados_rec[boleto_id] > 0) {
					 
				?>
					<img src="image/bt_boleto.png" title="Visualizar Boleto" onclick="abreJanelaBoleto('./boletos/boleto_bb.php?TipoBol=1&BoletoId=<?php echo $dados_rec[boleto_id] ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>')" style="cursor: pointer" />					
				<?php
					} else {
					 
           echo "&nbsp;";
           
          }
        ?>
      </td>
      <td width="26" style="border-bottom: 1px solid">
        <?php

					if ($dados_rec[situacao] == 1) {
				?>
        	<img src="image/bt_receber_gd.gif" title="Receber esta conta" onclick="wdCarregarFormulario('ContaReceberQuita.php?ContaId=<?php echo $dados_rec[id] ?>&headers=1','conteudo')" style="cursor: pointer" />					
				<?php
					} else {
					 
           echo "&nbsp;";
           
         }
				?>			
			</td>			
	  </tr>
	<?php
	//Fecha o WHILE
	};
	
	//Fecha o if de se tem registros
	}

	//Verifica se precisa imprimir o rodapé
	if ($tot_regs > 0) { 
	?>

	<tr height="16">
    <td colspan="3" height="20" align="right"><strong>Total:&nbsp;&nbsp;</strong></td>
    <td height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="right">
      <span style="color: #990000"><?php echo "R$ " . number_format($total_receber, 2, ",", ".") ?></span>
		</td>
		<td height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="right">
      <span style="color: #990000"><?php echo "R$ " . number_format($saldo_receber, 2, ",", ".") ?></span>
		</td>					
	</tr>	
	
	<?php
	//Fecha o IF
	};
	?>
		
	</table>	
	</td>
  </tr>  
</table>
