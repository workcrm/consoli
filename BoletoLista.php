<?php
###########
## Módulo para Listagem dos Boletos
## Criado: 16/02/2010 - Maycon Edinger
## Alterado:
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
if ($_GET["TipoSituacao"] < 3){
	
		//Efetua o switch da situacao informada
		switch ($_GET["TipoSituacao"]) {
  	//Se for 1 então é visualização em aberto
		case 0:
			$texto_situacao = " <b>e com Situação:</b> <span style='color: #990000'>Em aberto</span>";
		break;		
  	//Se for 2 então é visualização das recebidas
		case 1:
			$texto_situacao = " <b>e com Situação:</b> <span style='color: #990000'>Recebidos</span>";
		break;
  	//Se for 3 então é visualização das vencidas
		case 2:
			$texto_situacao = " <b>e com Situação:</b> <span style='color: #990000'>Em Atrazo</span>";
		break;
		}	
		
	$TipoSituacao = $_GET["TipoSituacao"];
	$TextoSituacao = " AND boleto_recebido = '$TipoSituacao'";
	
} 

//Verifica se foi informado alguma data para filtrar junto
if ($dataIni != 0) {
	$TextoFiltraData = "</br><b>E com data de vencimento entre: </b><span style='color: #990000'>$_GET[DataIni]</span><b> a </b><span style='color: #990000'>$_GET[DataFim]</span>";
	$TextoSQLData = "	 AND data_vencimento >= '$dataIni' AND data_vencimento <= '$dataFim' ";
}


//Recebe os valores vindos do formulário
//Efetua o switch para o campo de tipo de listagem
switch ($_GET[TipoListagem]) {
  //Se for 1 então é visualização por data
	case 1: 
		//Monta o título da página
		$titulo = "Relação de Boletos por data de vencimento"; 

		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo Boletos com data de vencimento entre: <span style='color: #990000'>$_GET[DataIni]</span> a <span style='color: #990000'>$_GET[DataFim]</span> $texto_situacao</b>";

		//Monta o sql
		$sql = "SELECT * FROM boleto
		        WHERE data_vencimento >= '$dataIni' AND data_vencimento <= '$dataFim' $TextoSituacao 
		        ORDER BY data_vencimento";

		//Monta o texto para caso não houver registros
		$texto_vazio = "Não há contas a boletos entre as datas informadas";
	break;
  
	//Se for 2 então é visualização por grupo
	case 2: 
		//Monta o título da página
		$titulo = "Relação de boletos por Centro de Custo"; 
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

	//Se for 3 então é visualização por categoria
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
		$texto_vazio = "Não há contas a Receber para a categoria <span style='color: #990000'>$dados_categoria[nome]</span>";
	break;
	
	//Se for 4 então é visualização por situacao
	case 4: 
		//Monta o título da página
		$titulo = "Relação de Boletos por Situação"; 
		$TipoSituacao = $_GET[TipoSituacao];
		
		//Efetua o switch da situacao informada
		switch ($TipoSituacao) {
  	//Se for 1 então é visualização em aberto
		case 0:
			$texto_situacao = "Em aberto";
			$where_situacao = "boleto_recebido = '$TipoSituacao'";
		break;		
  	//Se for 2 então é visualização das recebidas
		case 1:
			$texto_situacao = "Recebidos";
			$where_situacao = "boleto_recebido = '$TipoSituacao'";
		break;
  	//Se for 3 então é visualização das vencidas
		case 2:
			$texto_situacao = "Em Atrazo";
			$data_base_vencimento = date("Y-m-d", mktime());
			$where_situacao = "boleto_recebido = 0 AND data_vencimento < '$data_base_vencimento'";
		break;
		}				
		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo boletos com situação: <span style='color: #990000'>$texto_situacao</span></b>" . $TextoFiltraData;

		//Monta o sql de filtragem das contas
		$sql = "SELECT * FROM boleto
		        WHERE $where_situacao $TextoSQLData 
		        ORDER BY data_vencimento";
		
		//Monta o texto para caso não houver registros
		$texto_vazio = "Não há boletos com a situação <span style='color: #990000'>$texto_situacao</span>";
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
	
	//Se for 6 então é visualização por subgrupo
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
		      <td>
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
					<td align="right">
						<input class="button" title="Retorna ao Módulo de Boletos" name="btnVoltar" type="button" id="btnVoltar" value="Retornar ao Módulo de Boletos" onclick="wdCarregarFormulario('ModuloBoletos.php','conteudo')" />	
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
		  	  
			//Executa a Query
		  $query = mysql_query($sql);		  	  
		  //verifica o número total de registros
		  $tot_regs = mysql_num_rows($query);	    
		  
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
          <td width='116' align='center'>Nosso Número</td>
	      	<td>Dados do Sacado/Evento/Formando</td>
          <td width='60' align='center'>Emissão</td>
          <td width='60' align='center'>Vencto</td>
          <td width='80' align='center'>Valor</td>
          <td width='65' align='center'>Situação</td>
          <td colspan='2' width='90' align='center' style='padding-right: 0px'>Ação</td>          
        </tr>
	  ";}
	  
	  //Caso não houverem registros
	  if ($tot_regs == 0) { 

	  //Exibe uma linha dizendo que nao há registros
	  echo "
	  	<tr height='24'>
        <td colspan='8' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' nowrap='nowrap'>
		  <slot><font color='#33485C'><strong>$texto_vazio</strong></font></slot>
			</td>
	  </tr>	
	  ";	  
	  } else {
				  
	  //Cria o array e o percorre para montar a listagem dinamicamente
	  while ($dados_rec = mysql_fetch_array($query)){

  		//Verifica a situação do boleto
  		switch ($dados_rec["boleto_recebido"]) {
  		  
        case 0: $desc_situacao = "<span style='color: #990000'>Em Aberto</span>"; break;		  
        case 1: $desc_situacao = "<span style='color: #6666CC'>Recebido</span>"; break;
        
      }

		//Fecha o php, mas o while continua
	  ?>

	  <tr height="16">
			<td style="border-bottom: 1px solid" align="center">
				<span style="color: #6666CC"><?php echo substr($dados_rec["nosso_numero"], 0,7) ?></span><span style="color: #990000"><?php echo substr($dados_rec["nosso_numero"], 7,3) ?></span><span style="color: #59AA08"><?php echo substr($dados_rec["nosso_numero"], 10,5) ?></span><?php echo substr($dados_rec["nosso_numero"], 15,2) ?>				
			</td>      			
      <td style="border-bottom: 1px solid" height="20">
        <font color="#CC3300" size="2" face="Tahoma">
				  <a title="Clique para exibir este boleto" href="#" onclick="abreJanelaBoleto('./boletos/boleto_bb.php?TipoBol=1&BoletoId=<?php echo $dados_rec[id] ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>')"><?php echo $dados_rec["sacado"]; ?></a>
				</font>
				<br/>
				<?php echo "<span style='color: #990000'><b>$dados_rec[demonstrativo2]</b></span><br/>$dados_rec[demonstrativo3]" ?>
				</span>      
			</td>
			<td style="border-bottom: 1px solid" align="center">
				<?php echo DataMySQLRetornar($dados_rec["data_documento"]) ?>				
			</td>
			<td style="border-bottom: 1px solid" align="center">
				<span style="color: #6666CC"><?php echo DataMySQLRetornar($dados_rec["data_vencimento"]) ?></span>			
			</td>			
      <td style="border-bottom: 1px solid" align="right">
        <?php 
					echo "R$ " . number_format($dados_rec["valor_boleto"], 2, ",", ".");
					$total_receber = $total_receber + $dados_rec["valor_boleto"]; 
				?>
			</td>
			<td style="border-bottom: 1px solid" align="center">
				<?php echo $desc_situacao ?>				
			</td>
      <td width="32" style="border-bottom: 1px solid">
        <?php

					if ($dados_rec["boleto_recebido"] == 0) {
				?>
        	<img src="image/bt_receber_gd.gif" title="Baixar o recebimento deste boleto" onclick="wdCarregarFormulario('BoletoQuita.php?BoletoId=<?php echo $dados_rec[id] ?>&headers=1','conteudo')" style="cursor: pointer" />					
				<?php
					} else {
					 
           echo "&nbsp;";
           
         }
				?>			
			</td>
      <td style="border-bottom: 1px solid">
        <input class="button" title="Exclui este Boleto e sua conta a receber vinculada" onclick="if(confirm('Confirma a exclusão deste boleto e sua conta a receber vinculada ?')) {wdCarregarFormulario('BoletoExclui.php?BoletoId=<?php echo $dados_rec[id] ?>&ContaReceberId=<?php echo $dados_rec[conta_receber_id] ?>','conteudo')}" type="button" value="Excluir" name="Delete" />			
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
    <td colspan="4" height="20" align="right"><strong>Total de boletos:&nbsp;&nbsp;</strong></td>
    <td height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="right">
      <span style="color: #990000"><?php echo "R$ " . number_format($total_receber, 2, ",", ".") ?></span>
		</td>
		<td colspan="3">
      &nbsp;
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
