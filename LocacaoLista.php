<?php 
###########
## Módulo para Listagem das locações pendentes no form principal
## Criado: 11/11/2009 - Maycon Edinger
## Alterado:
## Alterações: 
###########

//Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Monta e executa a query para buscar as locações em aberto
$sql = mysql_query("SELECT 
									loc.id,
									loc.data,
									loc.tipo_pessoa,
									loc.pessoa_id,
									loc.descricao,
									loc.situacao,
									loc.devolucao_prevista,
									loc.devolucao_realizada,
									loc.observacoes,
									loc.recebido_por,
									cli.id as cliente_id,
									cli.nome as cliente_nome,
									forn.id as fornecedor_id,
									forn.nome as fornecedor_nome,
									col.id as colaborador_id,
									col.nome as colaborador_nome
									FROM locacao loc 
									LEFT OUTER JOIN clientes cli ON cli.id = loc.pessoa_id
									LEFT OUTER JOIN fornecedores forn ON forn.id = loc.pessoa_id
									LEFT OUTER JOIN colaboradores col ON col.id = loc.pessoa_id
									WHERE loc.empresa_id = $empresaId  AND loc.situacao = 1 ORDER BY loc.data");

//Conta o numero de compromissos que a query retornou
$registros = mysql_num_rows($sql);

//Verifica se a funcão já foi declarada
if (function_exists("DataMySQLRetornar") == false) {
	//Inclui o arquivo para manipulação de datas
	include "./include/ManipulaDatas.php";
}

?>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
  	<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width="440">
						<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Locações Pendentes</span>
					</td>
			  </tr>
			</table>
  	</td>
  </tr>
  <tr>
    <td>
	  <table id="2" width='100%' align='left' border='0' cellspacing='0' cellpadding='0' class="listView">
	  	<tr height="12">
	    	<td height="12" colspan='5' class="listViewPaginationTdS1">
	      	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
	      		<tr>
	      			<td width="40">
	      				<img src="image/bt_locacao_gd.gif" />
							</td>
							<td>
							<?php 
							
								if ($registros == 0) {
									
									$str_titulo = "locações";	
									$mensagem_regs = "Não há ";
								
								} else {
									
									if ($registros > 1) {
										$str_titulo = "locações";
									} else {
										$str_titulo = "locação";
									}
									
									$mensagem_regs = "Há <span style='color: #990000'>$registros </span>";
								} 
							
							?>
	  					<span style="font-size: 12px; color: #444444"><b><?php echo $mensagem_regs . " " . $str_titulo ?> pendentes.</b></span></span>
	  				</td>
	  			</tr>
	  		</table>
    	</td>
  	</tr>

  <?php
    //Caso não tenha compromissos então não exibe a linha de cabeçalho.
    if ($registros > 0) { 
      
			//Define o style para fechar com a parte dos eventos para 7 dias
			$style_tabela = "style='border-top: 1px #9E9E9E solid'";
			
			echo "
        <tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>          
					<td width='370' class='listViewThS1'>&nbsp;&nbsp;Descrição da Locação</td>
       		  <td width='70' align='center' class='listViewThS1'>Data</td>
					  <td width='230' class='listViewThS1'>Cliente</td>
       		  <td width='65' align='center' class='listViewThS1'>Devolução Prevista</td>
        	  <td class='listViewThS1' align='center'>Ações</td>
        </tr>";
		}
		
		//Monta e percorre o array dos dados
    while ($dados = mysql_fetch_array($sql)){   
    		//Efetua o switch para o campo de pessoa
					switch ($dados[tipo_pessoa]) {
					  //Se for cliente
						case 1: 
							$pessoa_nome = $dados[cliente_nome]; 
						break;
						//Se for fornecedor
						case 2:  
							$pessoa_nome = $dados[fornecedor_nome];
						break;
						//Se for colaborador
						case 3:  
							$pessoa_nome = $dados[colaborador_nome];							
						break;
					}

					$data = DataMySQLRetornar($dados[data]);
					$data_prevista = DataMySQLRetornar($dados[devolucao_prevista]);

    ?>

    <tr height="16">	      
			<td valign="middle" bgcolor="#fdfdfd" class="oddListRowS1">
		    <a title="Clique para exibir os detalhes desta locação" href="#" onclick="wdCarregarFormulario('LocacaoExibe.php?LocacaoId=<?php echo $dados[id] ?>&headers=1','conteudo')"><?php echo $dados[descricao] ?></a>
		  </td>

      <td valign="middle" align="center" bgcolor="#fdfdfd" class="currentTabList">
		    <?php echo $data ?>
		  </td>

			<td valign="middle" bgcolor="#fdfdfd" class="currentTabList">
		    <?php echo $pessoa_nome ?>
		  </td>

      <td valign="middle" align="center" bgcolor="#fdfdfd" class="currentTabList">
		    <?php echo $data_prevista ?>
		  </td>
		
      <td valign="middle" bgcolor="#fdfdfd" class="currentTabList">
		    <div align="center">
					<img src="./image/bt_item.gif" title="Clique para gerenciar os itens/produtos desta locação" onclick="wdCarregarFormulario('ItemLocacaoCadastra.php?LocacaoId=<?php echo $dados[id] ?>&headers=1','conteudo')" style="cursor: pointer" />
				</div>
		  </td>

	  </tr>

  <?php
  //Fecha o while
  }
  ?>
  </table>
</td>
</tr>
</table>
