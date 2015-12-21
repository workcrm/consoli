<?php
###########
## Módulo para Listagem dos pedidos do foto e vídeo
## Criado: 14/10/2010 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header('Content-Type: text/html;  charset=ISO-8859-1',true);

//Desativar o CSS redundante
//<link rel='stylesheet' type='text/css' href='include/workStyle.css'>

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require('Diretivas.php');

//Estabelece a conexão com o banco de dados
include './conexao/ConexaoMySQL.php';

//Inclui o arquivo para manipulação de datas
include './include/ManipulaDatas.php';

//Adiciona o acesso a entidade de criação do componente data
include('CalendarioPopUp.php');

//Cria um objeto do componente data
$objData = new tipData();
//Define que não deve exibir a hora no calendario
$objData->bolExibirHora = false;
//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript();

//Inclui o arquivo para manipulação de valor monetário
include './include/ManipulaMoney.php';

//Pega os valores padrão que vem do formulario
$dataIni = DataMySQLInserir($_GET[DataIni]);
$dataFim = DataMySQLInserir($_GET[DataFim]);
   

//Verifica se foi informado alguma data para filtrar junto
if ($dataIni != 0) 
{
	
  $TextoFiltraData = "</br><b>E com data de emissão entre: </b><span style='color: #990000'>$_GET[DataIni]</span><b> a </b><span style='color: #990000'>$_GET[DataFim]</span>";
	$TextoSQLData = "	 AND ped.data >= '$dataIni' AND ped.data <= '$dataFim' ";

}


//Recebe os valores vindos do formulário
//Efetua o switch para o campo de tipo de listagem
switch ($_GET[TipoListagem]) 
{
  //Se for 1 então é visualização por data
	case 1: 
		//Monta o título da página
		$titulo = 'Relação de Pedidos do Foto e Vídeo por Data de Emissão'; 

		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo pedidos com data de emissão entre: </b><span style='color: #990000'>$_GET[DataIni]</span><b> a </b><span style='color: #990000'>$_GET[DataFim]</span>";

		//Monta o sql
		$sql = "SELECT 
							ped.id,
							ped.data,
              ped.evento_id,
							ped.formando_id,
              ped.data_entrega,
              ped.fornecedor_id,
              ped.observacoes,
							eve.nome AS evento_nome,
							form.nome AS formando_nome,
							forn.nome AS fornecedor_nome
							FROM pedido_fv ped
							LEFT OUTER JOIN eventos eve ON eve.id = ped.evento_id
              LEFT OUTER JOIN eventos_formando form ON form.id = ped.formando_id
              LEFT OUTER JOIN fornecedores forn ON forn.id = ped.fornecedor_id
							WHERE ped.data >= '$dataIni' AND ped.data <= '$dataFim' 
							ORDER BY ped.data, eve.nome";

		//Monta o texto para caso não houver registros
		$texto_vazio = 'Não há pedidos emitidos entre as datas informadas';
	break;

	//Se for 2 então é visualização por evento
	case 2: 
		//Monta o título da página
		$titulo = 'Relação de Pedidos do Foto e Vídeo por Evento'; 
		$eventoId = $_GET[EventoId];
		
		//Recupera o nome da categoria selecionada
		$sql_evento = mysql_query("SELECT nome FROM eventos WHERE id = '$eventoId'");
		
		//Monta o array com os dados
		$dados_evento = mysql_fetch_array($sql_evento);
		
		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo Pedidos do Foto e Vídeo para o evento: </b><span style='color: #990000'>$dados_evento[nome]</span>" . $TextoFiltraData;

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
							ped.id,
							ped.data,
              ped.evento_id,
							ped.formando_id,
              ped.data_entrega,
              ped.fornecedor_id,
              ped.observacoes,
							eve.nome AS evento_nome,
							form.nome AS formando_nome,
							forn.nome AS fornecedor_nome
							FROM pedido_fv ped
							LEFT OUTER JOIN eventos eve ON eve.id = ped.evento_id
              LEFT OUTER JOIN eventos_formando form ON form.id = ped.formando_id
              LEFT OUTER JOIN fornecedores forn ON forn.id = ped.fornecedor_id
							WHERE ped.evento_id = '$eventoId' $TextoSQLData 
							ORDER BY ped.data";
		
		//Monta o texto para caso não houver registros
		$texto_vazio = "Não há pedidos para o evento <span style='color: #990000'>$dados_evento[nome]</span>";
	break;
	
  
 	//Se for 3 então é visualização por evento e formando
	case 3: 
		//Monta o título da página
		$titulo = 'Relação de Pedidos do Foto e Vídeo por Evento e Formando'; 
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
		$desc_filtragem = "<b>Exibindo Pedidos do Foto e Vídeo para o evento: </b><span style='color: #990000'>$dados_evento[nome]</span><br/><strong>E do formando: </strong><span style='color: #990000'>$dados_formando[nome]</span>" . $TextoFiltraData;

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
						ped.id,
						ped.data,
            ped.evento_id,
						ped.formando_id,
            ped.data_entrega,
            ped.fornecedor_id,
            ped.observacoes,
						eve.nome AS evento_nome,
						form.nome AS formando_nome,
						forn.nome AS fornecedor_nome
						FROM pedido_fv ped
						LEFT OUTER JOIN eventos eve ON eve.id = ped.evento_id
            LEFT OUTER JOIN eventos_formando form ON form.id = ped.formando_id
            LEFT OUTER JOIN fornecedores forn ON forn.id = ped.fornecedor_id
						WHERE ped.evento_id = '$eventoId' AND ped.formando_id = '$formandoId' $TextoSQLData
						ORDER BY ped.data";
		
		//Monta o texto para caso não houver registros
		$texto_vazio = 'Não há pedidos para este evento e formando';
	break;
  
  //Se for 4 então é visualização por evento, formando e fornecedor
	case 4: 
		//Monta o título da página
		$titulo = 'Relação de Pedidos do Foto e Vídeo por Evento, Formando e Fornecedor'; 
		$eventoId = $_GET[EventoId];
    $formandoId = $_GET[FormandoId];
    $fornecedorId = $_GET[FornecedorId];
		
		//Recupera o nome da categoria selecionada
		$sql_evento = mysql_query("SELECT nome FROM eventos WHERE id = '$eventoId'");
		
		//Monta o array com os dados
		$dados_evento = mysql_fetch_array($sql_evento);
    
    //Recupera o nome do formando
		$sql_formando = mysql_query("SELECT nome FROM eventos_formando WHERE id = '$formandoId'");
		
		//Monta o array com os dados
		$dados_formando = mysql_fetch_array($sql_formando);
    
    //Recupera o nome do fornecedor
		$sql_fornecedor = mysql_query("SELECT nome FROM fornecedores WHERE id = '$fornecedorId'");
		
		//Monta o array com os dados
		$dados_fornecedor = mysql_fetch_array($sql_fornecedor);
		
		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo Pedidos do Foto e Vídeo para o evento: </b><span style='color: #990000'>$dados_evento[nome]</span><br/><strong>Para o formando: </strong><span style='color: #990000'>$dados_formando[nome]</span><br/><strong>Para o fornecedor: </strong><span style='color: #990000'>$dados_fornecedor[nome]</span>" . $TextoFiltraData;

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
						ped.id,
						ped.data,
            ped.evento_id,
						ped.formando_id,
            ped.data_entrega,
            ped.fornecedor_id,
            ped.observacoes,
						eve.nome AS evento_nome,
						form.nome AS formando_nome,
						forn.nome AS fornecedor_nome
						FROM pedido_fv ped
						LEFT OUTER JOIN eventos eve ON eve.id = ped.evento_id
            LEFT OUTER JOIN eventos_formando form ON form.id = ped.formando_id
            LEFT OUTER JOIN fornecedores forn ON forn.id = ped.fornecedor_id
						WHERE ped.evento_id = '$eventoId' AND ped.formando_id = '$formandoId' AND ped.fornecedor_id = '$fornecedorId' $TextoSQLData
						ORDER BY ped.data";            
		
		//Monta o texto para caso não houver registros
		$texto_vazio = 'Não há pedidos para este evento, formando e fornecedor';
	break;
  
  //Se for 5 então é visualização por evento, formando e fornecedor
	case 5: 
		//Monta o título da página
		$titulo = 'Relação de Pedidos do Foto e Vídeo por Fornecedor'; 
    $fornecedorId = $_GET[FornecedorId];
    
    //Recupera o nome do fornecedor
		$sql_fornecedor = mysql_query("SELECT nome FROM fornecedores WHERE id = '$fornecedorId'");
		
		//Monta o array com os dados
		$dados_fornecedor = mysql_fetch_array($sql_fornecedor);
		
		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo Pedidos do Foto e Vídeo para o fornecedor: </strong><span style='color: #990000'>$dados_fornecedor[nome]</span>" . $TextoFiltraData;

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
						ped.id,
						ped.data,
            ped.evento_id,
						ped.formando_id,
            ped.data_entrega,
            ped.fornecedor_id,
            ped.observacoes,
						eve.nome AS evento_nome,
						form.nome AS formando_nome,
						forn.nome AS fornecedor_nome
						FROM pedido_fv ped
						LEFT OUTER JOIN eventos eve ON eve.id = ped.evento_id
            LEFT OUTER JOIN eventos_formando form ON form.id = ped.formando_id
            LEFT OUTER JOIN fornecedores forn ON forn.id = ped.fornecedor_id
						WHERE ped.fornecedor_id = '$fornecedorId' $TextoSQLData
						ORDER BY ped.data";            
		
		//Monta o texto para caso não houver registros
		$texto_vazio = 'Não há pedidos para este fornecedor';
	break;  	
}
			
//Executa a Query
$query = mysql_query($sql);		  	  
//verifica o número total de registros
$tot_regs = mysql_num_rows($query);	      

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<form id="form" name="cadastro" action="sistema.php?ModuloNome=ContaReceberListaAltera" method="post">

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
						<input class="button" title="Retorna ao Módulo de Pedidos do Foto e Vídeo" name="btnVoltar" type="button" id="btnVoltar" value="Retornar ao Módulo de Pedidos do FV" onclick="wdCarregarFormulario('ModuloPedidoFotoVideo.php?Headers=1','conteudo')" />	
					</td>
				</tr>
			</table>
		</td>		
	</tr>
 </table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>	   
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView">

	<?php
	  //Caso houverem registros
	  if ($tot_regs > 0) 
    {
	  
      echo "
        <table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>    
          <tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
            <td style='border-right: 1px dotted; border-bottom: 1px solid' width='85' align='center'>N&deg; do Pedido</td>
            <td style='border-right: 1px dotted; border-bottom: 1px solid' width='85' align='center'>Data de<br/>Emissão</td>            
  	      	<td style='border-right: 1px dotted; border-bottom: 1px solid'>&nbsp;&nbsp;Fornecedor/Formando/Evento</td>
            <td style='border-right: 1px dotted; border-bottom: 1px solid' width='85' align='center'>Data para<br/>Entrega</td>
          </tr>";
          
      //Cria o array e o percorre para montar a listagem dinamicamente
  	  while ($dados_rec = mysql_fetch_array($query))
      {
        
        ?>
        
   	  <tr height="16">
        <td align="center" style="border-top: 1px dotted; border-right: 1px dotted" height="20">
          <font color="#CC3300" size="2" face="Tahoma">
            <a title="Clique para exibir este pedido" href="#" onclick="wdCarregarFormulario('PedidoFVExibe.php?PedidoId=<?php echo $dados_rec[id] ?>','conteudo')"><?php echo $dados_rec[id] ?></a>
          </font>
        </td>
        <td align="center" style="border-top: 1px dotted; border-right: 1px dotted" height="20">
          <?php echo DataMySQLRetornar($dados_rec[data]) ?>
        </td>
        <td style="border-top: 1px dotted; border-right: 1px dotted; padding-bottom: 2px" height="20">        
          <font color="#CC3300" size="2" face="Tahoma">
  				  <a title="Clique para exibir este pedido" href="#" onclick="wdCarregarFormulario('PedidoFVExibe.php?PedidoId=<?php echo $dados_rec[id] ?>','conteudo')">&nbsp;<?php echo $dados_rec[fornecedor_nome] . " (" . $dados_rec[fornecedor_id] . ")"; ?></a>
  				</font>				
          <br/>
  				<span style="font-size: 9px">&nbsp;<?php echo $dados_rec["formando_nome"] . " (" . $dados_rec[formando_id] . ")" ?><br/>&nbsp;<?php echo $dados_rec["evento_nome"] . " (" . $dados_rec[evento_id] . ")" ?>
  				<br/>				
  				</span>             
  			</td>
        <td align="center" style="border-top: 1px dotted" height="20">
          <?php 
          
            if ($dados_rec[data_entrega] != "0000-00-00")
            {
              
              echo DataMySQLRetornar($dados_rec[data_entrega]);
              
            } 
            
            else
            
            {
              
              echo "&nbsp;";
              
            }
          ?>
        </td>					
  	  </tr>
        
        <?php
                
      }
                 
    }
	  
	  //Caso não houverem registros
	  if ($tot_regs == 0) 
    { 

  	  //Exibe uma linha dizendo que nao há registros
  	  echo "
  	  	<tr height='24'>
          <td colspan='10' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' nowrap='nowrap'>
  		  <slot><font color='#33485C'><strong>$texto_vazio</strong></font></slot>
  			</td>
  	  </tr>";
      	  
	  } 
    
 
	?>
		
	</table>	
	</td>
  </tr>  
</table>
</form>