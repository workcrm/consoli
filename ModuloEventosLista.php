<?php
###########
## Módulo para Exibição dos eventos pesquisados
## Criado: 22/05/2007 - Maycon Edinger
## Alterado: 22/11/2007 - Maycon Edinger
## Alterações: 
## 13/06/2007 - Implementado a opção de exibir somente eventos em aberto, concluídos ou todos
## 20/06/2007 - Implementado para pesquisar o texto em qualquer parte do nome
## 08/10/2007 - Implementado para gerenciamento do repertório musical
## 22/11/2007 - Implementado para gerenciamento dos serviços do evento
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

//Efetua o switch para a forma de visualização
switch ($_GET["edtVisualizar"]) {
  case 1: 
		$where_vis = "";
		$nome_vis = "Visualizando: <span style='color: #990000'><b>Todos os Eventos</b></span>";		 
	break;
	case 2: 
		$where_vis = "AND eve.status = 0";
		$nome_vis = "Visualizando: <span style='color: #990000'><b>Eventos em Orçamento</b></span>"; 
	break;
	case 3: 
		$where_vis = "AND eve.status = 1";
		$nome_vis = "Visualizando: <span style='color: #990000'><b>Eventos em Aberto</b></span>"; 	
	break;
	case 4: 
		$where_vis = "AND eve.status = 2";
		$nome_vis = "Visualizando: <span style='color: #990000'><b>Eventos Realizados</b></span>"; 
	break;
	case 5: 
		$where_vis = "AND eve.status = 3";
		$nome_vis = "Visualizando: <span style='color: #990000'><b>Eventos Não-Realizados</b></span>"; 
	break;
}

//Efetua o switch para a forma de financeiro
switch ($_GET["edtFinanceiro"]) {
  case 0: 
		$where_fin = ""; 
		$nome_fin = "Posição Financeira: <span style='color: #990000'><b>Qualquer</b></span>";	
	break;
	case 1: 
		$where_fin = "AND eve.posicao_financeira = 1";
		$nome_fin = "Posição Financeira: <span style='color: #990000'><b>A Receber</b></span>";
	break;
	case 2: 
		$where_fin = "AND eve.posicao_financeira = 2";
		$nome_fin = "Posição Financeira: <span style='color: #990000'><b>Recebido</b></span>"; 
	break;
	case 3: 
		$where_fin = "AND eve.posicao_financeira = 3";
		$nome_fin = "Posição Financeira: <span style='color: #990000'><b>Cortesia</b></span>"; 
	break;
}

//Efetua o switch para a forma de ordenação
switch ($_GET["edtOrdenar"]) {
  case 1: 
		$where_ordem = "ORDER BY eve.data_realizacao"; 
		$nome_ordem = "Ordenados por: <span style='color: #990000'><b>Data do Evento</b></span>";	
	break;
	case 2: 
		$where_ordem = "ORDER BY eve.nome"; 
		$nome_ordem = "Ordenados por: <span style='color: #990000'><b>Título do Evento</b></span>";	
	break;
}

//Efetua o switch para a forma de classificação
switch ($_GET["edtClassificar"]) {
  case 1: 
		$where_classifica = ""; 
		$nome_classifica = "Classificados: <span style='color: #990000'><b>Ascendente (A-Z)</b></span>";	
	break;
	case 2: 
		$where_classifica = "DESC"; 
		$nome_classifica = "Classificados: <span style='color: #990000'><b>Descendente (Z-A)</b></span>";	
	break;
}

$edtDataIni = DataMySQLInserir($_GET[edtDataIni]);
$edtDataFim = DataMySQLInserir($_GET[edtDataFim]);

if ($_GET['edtDataIni'] != ""){
	
	$where_data = "AND eve.data_realizacao >= '$edtDataIni' AND eve.data_realizacao <= '$edtDataFim'";
	$data_classifica = "No período de <span style='color: #990000'><b>$_GET[edtDataIni]</b></span> a <span style='color: #990000'><b>$_GET[edtDataFim]</b></span>";

}


//Verifica se o usuário digitou um argumento para pesquisar	  
if ($_GET['edtPesquisa'] != "") {
	$pesquisa = $_GET["edtPesquisa"];
	
	//Monta a query
	$consulta = "SELECT 
									eve.id,
									eve.nome,
									eve.descricao,
									eve.status,
									eve.cliente_id,
									eve.responsavel,
									eve.data_realizacao,
									eve.hora_realizacao,
									eve.duracao,
									cli.id as cliente_id,
									cli.nome as cliente_nome
									FROM eventos eve 
									LEFT OUTER JOIN clientes cli ON cli.id = eve.cliente_id
									WHERE eve.nome LIKE '%$pesquisa%' AND eve.empresa_id = $empresaId $where_vis $where_fin $where_data $where_ordem $where_classifica";

//Se não, o usuário quer pesquisar por inicial da letra
} else {	  
		  
	$filtragem = $_GET['ChaveFiltragem'];
	//Caso clicar no botáo para exibir todas as clientes
	if ($filtragem == "todos") {
		//Monta a query pesquisando por empresa
	  //$consulta = "SELECT * FROM eventos WHERE empresa_id = $empresaId ORDER BY nome";
	  
		$consulta = "SELECT 
									eve.id,
									eve.nome,
									eve.descricao,
									eve.status,
									eve.cliente_id,
									eve.responsavel,
									eve.data_realizacao,
									eve.hora_realizacao,
									eve.duracao,
									cli.id as cliente_id,
									cli.nome as cliente_nome
									FROM eventos eve 
									LEFT OUTER JOIN clientes cli ON cli.id = eve.cliente_id
									WHERE eve.empresa_id = $empresaId  $where_vis $where_fin $where_data $where_ordem $where_classifica ";
										  
	} else {
		//Monta a query pesquisando por empresa e por inicial
	  //$consulta = "SELECT * FROM eventos WHERE nome LIKE '$filtragem%' AND empresa_id = $empresaId ORDER BY nome";		 

		$consulta = "SELECT 
									eve.id,
									eve.nome,
									eve.descricao,
									eve.status,
									eve.cliente_id,
									eve.responsavel,
									eve.data_realizacao,
									eve.hora_realizacao,
									eve.duracao,
									cli.id as cliente_id,
									cli.nome as cliente_nome
									FROM eventos eve 
									LEFT OUTER JOIN clientes cli ON cli.id = eve.cliente_id
									WHERE eve.nome LIKE '$filtragem%' AND eve.empresa_id = $empresaId $where_vis $where_fin $where_data $where_ordem $where_classifica ";

	}
  
}

//Executa a query
$listagem = mysql_query($consulta);

//Conta o numero de contas que a query retornou
$registros = mysql_num_rows($listagem);

//Determina a Quantidade de registros por página para cartão de visita
$regs_pagina_rec = "30"; 

//Recebe o numero da pagina para nevegar	
$pagina_rec = $_GET['PaginaRec']; 

//Caso não seja especificado uma página
if (!$pagina_rec) {
	//Define como a página 1
	$pc_rec = "1";
//Caso for especificado a pagina
} else {
	//Alimenta a variável com o numero da página
	$pc_rec = $pagina_rec;
}
	
$inicio_rec = $pc_rec - 1;
$inicio_rec = $inicio_rec * $regs_pagina_rec;
	  
//Monta e executa o sql limitando e fazendo a paginação
$limite_rec = mysql_query("$consulta LIMIT $inicio_rec, $regs_pagina_rec");

//Descomentar para visualizar o código sql que está sendo gerado
//echo "$consulta LIMIT $inicio_rec, $regs_pagina_rec</br></br>";
	  
//Monta e executa o sql contendo todos os registros
$todos_rec = mysql_query("$consulta");
  	  
//Verifica o número total de registros
$tot_regs_rec = mysql_num_rows($todos_rec);
     
//cria o contador inicial do numero do registro pra exibir na tela
if ($inicio_rec == 0) { 
  //Determina o contador inicial como 1
	$conta_inicial_rec = 1;
  //Verifica quantos registros está exibindo
  $conta_final_rec = mysql_num_rows($limite_rec); 
} else {
  $conta_inicial_rec = $inicio_rec + 1;
  //Workaround
  $conta_final_rec = (mysql_num_rows($limite_rec) + $conta_inicial_rec) - 1; 
}

// verifica o número total de páginas
$tot_pags_rec = $tot_regs_rec / $regs_pagina_rec; 

?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
    	<?php
        //Caso não tenha compromissos então não exibe a linha de cabeçalho.
    		if ($registros == 0) { 
      		echo "
					<table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>
        		<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
          		<td  scope='col'>&nbsp;&nbsp;Não foram encontrados Eventos para a pesquisa !</td>
        		</tr>
        	</table>	
    			";
				} else {

				//Monta os botões de paginação
				$anterior_rec = $pc_rec -1;
				$proximo_rec = $pc_rec +1;

				//Exibe a tabela com os dados de controle de paginação
      	echo "
				<table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>
        	<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
          	<td width='20'>";
									
							//Verifica se necessita exibir o botão de anterior
							if ($pc_rec > 1) {				
				  			//Exibe o botão
							echo "<a href='#' onClick=\"wdCarregarFormulario('ModuloEventosLista.php?edtPesquisa=$_GET[edtPesquisa]&edtVisualizar=$_GET[edtVisualizar]&ChaveFiltragem=$_GET[ChaveFiltragem]&Modo_vis=$_GET[Modo_vis]&PaginaRec=$anterior_rec','retornopesquisa','1')\" title='Exibe a Página Anterior'>&nbsp;<img src='./image/bt_anterior.gif' alt='Exibe a Página Anterior' border='0' align='middle'></a></td><td width='50'><a href='#' onClick=\"wdCarregarFormulario('ModuloEventosLista.php?edtPesquisa=$_GET[edtPesquisa]&edtVisualizar=$_GET[edtVisualizar]&ChaveFiltragem=$_GET[ChaveFiltragem]&Modo_vis=$_GET[Modo_vis]&PaginaRec=$anterior_rec','retornopesquisa','1')\" title='Exibe a Página Anterior'>Anterior</a></td>";
				  		}	 
				?>
				
				
						</td>
						<td>
							<span class='pageNumbers'>Exibindo Eventos: <span style="color: #990000"><b><?php echo $conta_inicial_rec ?></b></span>&nbsp;a&nbsp;<span style="color: #990000"><b><?php echo $conta_final_rec ?></b></span>&nbsp;de&nbsp;<span style="color: #990000"><b><?php echo $tot_regs_rec ?></b></span><br/><?php echo $nome_vis ?><br/><?php echo $nome_fin ?><br/><?php echo $nome_ordem ?><br/><?php echo $nome_classifica ?><br/><?php echo $data_classifica ?></span>
						</td>
						<td valign="middle" width="50" align="right">
				
				<?php 
				  //Verifica se necessita exibir o botão de próximo
				  if ($pc_rec < $tot_pags_rec) {				
				    //Exibe o botão
						echo "<a href='#' onClick=\"wdCarregarFormulario('ModuloEventosLista.php?edtPesquisa=$_GET[edtPesquisa]&edtVisualizar=$_GET[edtVisualizar]&ChaveFiltragem=$_GET[ChaveFiltragem]&Modo_vis=$_GET[Modo_vis]&PaginaRec=$proximo_rec','retornopesquisa','1')\" title='Exibe a próxima página'>Próximo&nbsp;</a></td><td width='20' align='right'><a href='#' onClick=\"wdCarregarFormulario('ModuloEventosLista.php?edtPesquisa=$_GET[edtPesquisa]&edtVisualizar=$_GET[edtVisualizar]&ChaveFiltragem=$_GET[ChaveFiltragem]&Modo_vis=$_GET[Modo_vis]&PaginaRec=$proximo_rec','retornopesquisa','1')\" title='Exibe a próxima página'><img src='./image/bt_proximo.gif' alt='Exibe a Próxima Página' border='0' align='middle'>&nbsp;</a>";
				  }	 
				
					//Fecha a tabela com os dados de controle da paginação
					echo "</td></tr></table></br>";
				
					//Monta e percorre o array dos dados
		    	while ($dados_rec = mysql_fetch_array($limite_rec))
          {

  					//Efetua o switch para o campo de status
  					switch ($dados_rec[status]) 
            {
  					  case 0: 
  							$desc_status = "Em Orçamento"; 
  								$status_fig = "<img src='./image/bt_evento_orcamento.png' title='Em Orçamento'>"; 
  						break;
  						case 1: 
  							$desc_status = "Em Aberto"; 
  							$status_fig = "<img src='./image/bt_evento_aberto.png' title='Em Aberto'>"; 
  						break;
  						case 2: 
  							$desc_status = "Realizado";
  							$status_fig = "<img src='./image/bt_evento_realiz.png' title='Realizado'>";
  						break;
  						case 3: 
  							$desc_status = "<span style='color: red'>Não-Realizado</span>"; 
  							$status_fig = "<img src='./image/bt_evento_nao_realiz.png' title='Não Realizado'>"; 
  						break;
  					}

					$data_realiz = DataMySQLRetornar($dados_rec[data_realizacao]);
					
					echo "
					<table width='100%' border='0' cellspacing='0' cellpadding='0' class='listView'>
					  <tr height='12'>
    					<td height='12' colspan='4' class='listViewPaginationTdS1'>      
	  						<font color='#444444'>Clique no título do evento para exibir os detalhes</font>
    					</td>
  					</tr>
  				</table>
					<table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>
	  		    <tr height='20'>
        		  <td width='800' class='listViewThS1'>
								&nbsp;&nbsp;$status_fig&nbsp;<font size='3'><span style='color: #990000'>[$dados_rec[id]]</span> - <a title='Clique para exibir os detalhes deste evento' href='#' onClick=\"wdCarregarFormulario('EventoExibe.php?EventoId=$dados_rec[id]&headers=1','conteudo')\"><font size='3'>";
														 
						
						//Verifica se o evento está como nao realizado
						if ($dados_rec[status] == 3){
							
							echo "<span style='color: #990000; text-decoration: line-through'>$dados_rec[nome]</span>";
											
						} else {
							
							echo $dados_rec[nome];
													
						}
							
							 
					
								
								echo "</font></a>
							</td>
							<td width='150' class='listViewThS1'>
								<div align='right'>
								  <span style='color: #990000; font-size: 14px'>Data: <strong>$data_realiz</strong>&nbsp;&nbsp;</span>
								</div>
							</td>
			  		</tr>";
			  		
			  		//Verifica o nível de acesso do usuário
					  if ($nivelAcesso >= 3) {
			  		  echo "
						<tr height='16'>
        	    <td colspan='2' valign='top' bgcolor='#fdfdfd' class='oddListRowS1' style='padding-left: 5px; padding-left: 5px; border-top: 1px solid'>
			  	  			<table width='100%' cellpadding='0' cellspacing='0' border='0' >
              			<tr valign='middle'>
                			<td width='28'>
                    		 <img src='./image/bt_data_evento_gd.gif' /> 
                			</td>
											<td width='95'>
                    		 <a title='Clique para gerenciar as datas deste evento' href='#' onClick=\"wdCarregarFormulario('DataEventoCadastra.php?EventoId=$dados_rec[id]&headers=1','conteudo')\">Datas</a>
                			</td>
											<td width='28'>
                    		 <img src='./image/bt_participante_gd.gif' /> 
                			</td>
											<td width='95'>
                    		 <a title='Clique para gerenciar os participantes deste evento' href='#' onClick=\"wdCarregarFormulario('ParticipanteEventoCadastra.php?EventoId=$dados_rec[id]&headers=1','conteudo')\">Participantes</a>
                			</td>
                			<td width='28'>
                    		 <img src='./image/bt_endereco_gd.gif' /> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar os endereços deste evento' href='#' onClick=\"wdCarregarFormulario('EnderecoEventoCadastra.php?EventoId=$dados_rec[id]&headers=1','conteudo')\">Endereços</a>
                			</td>
                			<td width='28'>
                    		 <img src='./image/bt_item_gd.gif'/> 
                			</td>
											<td width='80'>
                    		 <a title='Clique para gerenciar os produtos deste evento' href='#' onClick=\"wdCarregarFormulario('ItemEventoCadastra.php?EventoId=$dados_rec[id]&headers=1','conteudo')\">Produtos</a> 
                			</td>
                			<td width='28'>
                    		 <img src='./image/bt_servico_gd.gif'/> 
                			</td>
											<td width='80'>
                    		 <a title='Clique para gerenciar os serviços deste evento' href='#' onClick=\"wdCarregarFormulario('ServicoEventoCadastra.php?EventoId=$dados_rec[id]&headers=1','conteudo')\">Serviços</a> 
                			</td>                			
                			<td width='28'>
                    		 <img src='./image/bt_terceiro_gd.gif'/> 
                			</td>
											<td width='80'>
                    		 <a title='Clique para gerenciar os terceiros deste evento' href='#' onClick=\"wdCarregarFormulario('TerceiroEventoCadastra.php?EventoId=$dados_rec[id]&headers=1','conteudo')\">Terceiros</a>
                			</td> 
                			<td width='28'>
                    		 <img src='./image/bt_brinde_gd.gif'/> 
                			</td>
											<td width='80'>
                    		 <a title='Clique para gerenciar os brindes deste evento' href='#' onClick=\"wdCarregarFormulario('BrindeEventoCadastra.php?EventoId=$dados_rec[id]&headers=1','conteudo')\">Brindes</a>
                			</td> 
                			<td width='28'>
                    		 <img src='./image/bt_repertorio_gd.gif'/> 
                			</td>
											<td>
												 <a title='Clique para gerenciar repertório musical deste evento' href='#' onClick=\"wdCarregarFormulario('RepertorioEventoCadastra.php?EventoId=$dados_rec[id]&headers=1','conteudo')\">Repertório</a>  
                			</td> 
              			</tr>
              			<tr>
											<td width='28'>
                    		 <img src='./image/bt_formando_gd.gif' /> 
                			</td>
											<td width='95'>
                    		 <a title='Clique para gerenciar os formandos deste evento' href='#' onClick=\"wdCarregarFormulario('FormandoEventoCadastra.php?EventoId=$dados_rec[id]&headers=1','conteudo')\">Formandos</a>
                			</td>";										
												
											//Verifica o nível de acesso do usuário
											if ($nivelAcesso >= 4) {
												
											echo "					
                			<td width='28'>
                    		 <img src='./image/bt_fotovideo_gd.gif' /> 
                			</td>
											<td>
                    		 <a title='Clique para gerenciar o foto/vídeo deste evento' href='#' onClick=\"wdCarregarFormulario('FotoVideoEventoCadastra.php?EventoId=$dados_rec[id]&headers=1','conteudo')\">Foto/Vídeo</a>
                			</td>
											";
											
											} else {
												
												echo "
												
												<td width='28'>
                    		 <img src='./image/bt_fotovideo_gd_off.gif' title='Opção não habilitada para seu nível de acesso !'/> 
                			</td>
											<td>
                    		 &nbsp;
                			</td>";
											}
											
											echo "
                      <td width='28'>
                    		 <img src='./image/bt_documentos_gd.gif' /> 
                			</td>
											<td colspan='4'>
                    		 <a title='Clique para gerenciar os documentos deste evento' href='#' onClick=\"wdCarregarFormulario('DocumentosEventoCadastra.php?EventoId=$dados_rec[id]&headers=1','conteudo')\">Documentos</a>
                			</td>              			
              			</tr>
          				</table>
    					</td>
	  		  	</tr>";
	  		  	}
	  		  	
	  		  	echo "
			  		<tr height='16'>
        	    <td colspan='2' valign='top' bgcolor='#fdfdfd' class='oddListRowS1' style='padding-left: 5px; padding-left: 5px; border-top: 1px solid'>
								<b>$dados_rec[descricao]</b>
    					</td>
	  		  	</tr>
	  		  	<tr height='16'>
	    	    	<td colspan='2' valign='top' bgcolor='#fdfdfd' class='currentTabList' style='padding-left: 5px'>Status: <strong>$desc_status</strong></td>					
	    	  	</tr>
	  		  	<tr height='40'>
	    				<td colspan='2' valign='top' bgcolor='#fdfdfd' class='currentTabList' style='border-top: 1px solid'>
	      		  	<table width='100%' cellpadding='0' cellspacing='0' border='0'>
	      		  		<tr>
	      		  			<td width='70%' style='padding-left: 5px; border: 1px solid'>
	      		  			Cliente:
	      		  			<br>
	      		  			<b>$dados_rec[cliente_nome]</b>
	      		  			<br>
	      		  			Responsável: $dados_rec[responsavel]
	      		  			</td>
	      		  			<td style='border: 1px solid'>
	      		  			Hora: <strong>$dados_rec[hora_realizacao]</strong>
	      		  			</br>
	      		  			Duração: <strong>$dados_rec[duracao]</strong>
	      		  			</td>
	      		  		</tr>
	      		  	</table>
	    				</td>
			  		</tr>
	    		</table>
	    		</br>
				";
        }
				
       ?>
            
		   </br>

	     <?php
		   //Fecha o while
			 //}
			 //Fecha o if de se conter registros na consulta
			 }
		   ?>
		  </td>
	  </tr>
	</table>	
