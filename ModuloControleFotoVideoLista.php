<?php
###########
## Módulo para Listagem da dos envios do foto e vídeo
## Criado: 05/05/2011 - Maycon Edinger
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

//Captura o evento informado
$edtEventoId = $_GET["EventoId"];
$edtEstagio = $_GET["Estagio"];
$edtDataIni = DataMySQLInserir($_GET[DataIni]);
$edtDataFim = DataMySQLInserir($_GET[DataFim]);

$edtFornecedorId = $_GET["FornecedorId"];

$nome_evento = "Todos os eventos";

if ($edtEventoId > 0)
{

	$where_evento = "AND form.evento_id = $edtEventoId";
	
	//Busca o nome do evento
	//Monta o sql
	$sql_evento = mysql_query("SELECT nome FROM eventos WHERE id = $edtEventoId");

	//Monta o array com os dados
	$dados_evento = mysql_fetch_array($sql_evento);
	
	$nome_evento = $dados_evento["nome"];

}

if ($edtFornecedorId > 0)
{

	$where_fornecedor = "AND form.lab_fornecedor_id = $edtFornecedorId";
	
	//Busca o nome do evento
	//Monta o sql
	$sql_fornecedor = mysql_query("SELECT id, nome FROM fornecedores WHERE id = $edtFornecedorId");

	//Monta o array com os dados
	$dados_fornecedor = mysql_fetch_array($sql_fornecedor);
	
	$texto_fornecedor = '<br/>No Fornecedor: <span style="color: #990000"><b>' . $dados_fornecedor["id"] . ' - ' . $dados_fornecedor["nome"] . '</b></span>';

}

switch ($edtEstagio)
{

	case 0:
		$texto_estagio = "";
	break;
	case 1:
		$texto_estagio = "<br/>Com o estágio: <span style='color: #990000;'><strong>EM ATRASO</strong></span>";
	break;
	case 2:
		$texto_estagio = "<br/>Com o estágio: <span style='color: green;'><strong>ENVIADO</strong></span>";
	break;
	case 3:
		$texto_estagio = "<br/>Com o estágio: <strong>AGUARDANDO</strong>";
	break;

}

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css" />

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td colspan="2" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td style="padding-bottom: 4px;">
						<img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Evento: <span style="color: #990000;"><?php echo $nome_evento ?></span></span>			  	
					</td>
				</tr>
				<tr>
					<td style="padding-bottom: 10px;">
						Data de entrega ao cliente entre: <span style="color: #990000;"><strong><?php echo DataMySQLRetornar($edtDataIni) ?></strong> a <strong><?php echo DataMySQLRetornar($edtDataFim) ?></strong></span>
						<?php echo $texto_estagio ?>
						<?php echo $texto_fornecedor ?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
  
<?php

//Verifica o tipo de estagio
//Caso for para somente os atrasados
if ($edtEstagio == 1)
{
	$hoje = date("Y-m-d", mktime());
	
	$edtAtraso = $_GET["Atraso"];
	
	//Caso seja atraso no envio
	if ($edtAtraso == 1)
	{
	
		//Monta o sql de filtragem dos formandos
		$sql = "SELECT 
				form.id,			
				form.nome,
				form.situacao,
				form.data_venda,
				form.data_envio_lab,
				form.data_prev_lab,
				form.data_retorno_lab,
				form.data_entrega_cliente,
				form.data_envio_cliente,
				form.evento_id,
				form.lab_fornecedor_id,
				eve.nome AS evento_nome
				FROM eventos_formando form
				LEFT OUTER JOIN eventos eve ON eve.id = form.evento_id
				WHERE form.empresa_id = $empresaId 
				$where_evento
				$where_fornecedor
				AND (form.data_entrega_cliente BETWEEN '$edtDataIni' AND '$edtDataFim')
				AND (form.data_entrega_cliente < '$hoje' AND form.data_envio_cliente = '0000-00-00')			
				ORDER BY form.data_entrega_cliente, form.nome";   
	
	}
	
	else
	
	{
	
		//Monta o sql de filtragem dos formandos
		$sql = "SELECT 
				form.id,			
				form.nome,
				form.situacao,
				form.data_venda,
				form.data_envio_lab,
				form.data_prev_lab,
				form.data_retorno_lab,
				form.data_entrega_cliente,
				form.data_envio_cliente,
				form.evento_id,
				form.lab_fornecedor_id,
				eve.nome AS evento_nome
				FROM eventos_formando form
				LEFT OUTER JOIN eventos eve ON eve.id = form.evento_id
				WHERE form.empresa_id = $empresaId 
				$where_evento
				$where_fornecedor
				AND (form.data_prev_lab BETWEEN '$edtDataIni' AND '$edtDataFim')
				AND (form.data_prev_lab < '$hoje' AND form.data_retorno_lab = '0000-00-00')			
				ORDER BY form.data_prev_lab, form.nome"; 
	
	}
}			

//Caso for para somente os atrasados
else if ($edtEstagio == 2)
{
	
	//Monta o sql de filtragem dos formandos
	$sql = "SELECT 
			form.id,			
			form.nome,
			form.situacao,
			form.data_venda,
			form.data_envio_lab,
			form.data_prev_lab,
			form.data_retorno_lab,
			form.data_entrega_cliente,
			form.data_envio_cliente,
			form.evento_id,
			form.lab_fornecedor_id,
			eve.nome AS evento_nome
			FROM eventos_formando form
			LEFT OUTER JOIN eventos eve ON eve.id = form.evento_id
			WHERE form.empresa_id = $empresaId 
			$where_evento
			$where_fornecedor
			AND (form.data_entrega_cliente BETWEEN '$edtDataIni' AND '$edtDataFim')
			AND (form.data_envio_cliente != '0000-00-00')			
			ORDER BY form.data_entrega_cliente, form.nome";   

}	

//Caso for para somente os aguardando
else if ($edtEstagio == 3)
{
	
	$hoje = date("Y-m-d", mktime());
	
	//Monta o sql de filtragem dos formandos
	$sql = "SELECT 
			form.id,			
			form.nome,
			form.situacao,
			form.data_venda,
			form.data_envio_lab,
			form.data_prev_lab,
			form.data_retorno_lab,
			form.data_entrega_cliente,
			form.data_envio_cliente,
			form.evento_id,
			form.lab_fornecedor_id,
			eve.nome AS evento_nome
			FROM eventos_formando form
			LEFT OUTER JOIN eventos eve ON eve.id = form.evento_id
			WHERE form.empresa_id = $empresaId 
			$where_evento
			$where_fornecedor
			AND (form.data_entrega_cliente BETWEEN '$edtDataIni' AND '$edtDataFim')
			AND (form.data_entrega_cliente >= '$hoje' AND form.data_envio_cliente = '0000-00-00')			
			ORDER BY form.data_entrega_cliente, form.nome";   

}				

else

{

	//Monta o sql de filtragem dos formandos
	$sql = "SELECT 
			form.id,			
			form.nome,
			form.situacao,
			form.data_venda,
			form.data_envio_lab,
			form.data_prev_lab,
			form.data_retorno_lab,
			form.data_entrega_cliente,
			form.data_envio_cliente,
			form.evento_id,
			form.lab_fornecedor_id,
			eve.nome AS evento_nome
			FROM eventos_formando form
			LEFT OUTER JOIN eventos eve ON eve.id = form.evento_id
			WHERE form.empresa_id = $empresaId 
			$where_evento
			$where_fornecedor
			AND form.data_entrega_cliente BETWEEN '$edtDataIni' AND '$edtDataFim'
			ORDER BY form.data_entrega_cliente, form.nome";   

}

$query = mysql_query($sql);

$registros = mysql_num_rows($query);

//Caso não encontrar contas
if ($registros == 0)
{
?>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
			Náo há material do foto e vídeo programado para entrega ao cliente na data especificada !
		</td>
	</tr>
</table>

<?php
	
}
	
else
	
{
	
?>

<table width="100%" border="0" cellpadding="0" cellspacing="0">	
	<tr>
        <td>
            <table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView">
				<tr class="listViewThS1" height="20" background="image/fundo_consulta.gif">                
        	      	<td style="padding-left: 4px">Formando/Evento</td>
					<td width="70" align="center" style="border-left: #aaa 1px dotted">Data Venda</td>
					<td width="74" align="center" style="border-left: #aaa 1px dotted">Envio ao Laboratório</td>
					<td width="74" align="center" style="border-left: #aaa 1px dotted">Prev. do Laboratório</td>
					<td width="74" align="center" style="border-left: #aaa 1px dotted">Retorno do Laboratório</td>
					<td width="70" align="center" style="border-left: #aaa 1px dotted">Entrega Cliente</td>
					<td width="70" align="center" style="border-left: #aaa 1px dotted">Envio ao Cliente</td>
					<td width="60" align="center" style="border-left: #aaa 1px dotted">Status</td>
                </tr>
				
				<?php
				
					//Percorre as contas
					while ($dados = mysql_fetch_array($query))
					{	

						//Se o formando estiver com restricoes financeiras, muda a cor da celula
						if ($dados["situacao"] == 2)
						{
						
							$cor_celula = "#F0D9D9";
							
						}
						
						else
						
						{
						
							$cor_celula = "#FFFFFF";
							
						}
						
						$hoje = date("Y-m-d", mktime());
						
						// Data1
						$date1 = $dados["data_entrega_cliente"];

						// Timestamp1
						$timestamp1 = strtotime($date1);

						// Data2
						$date2 = $hoje;

						// Timestamp2
						$timestamp2 = strtotime($date2); 

						
						//Monta o status do aluno
						if ($timestamp1 < $timestamp2 AND $dados["data_envio_cliente"] == "0000-00-00")
						{
						
							$status = "<span style='color: #990000; font-size: 9'><b>EM ATRASO</b></span>";
							
						}
						
						else if ($timestamp1 == $timestamp2 AND $dados["data_envio_cliente"] == "0000-00-00")
						{
						
							$status = "<span style='color: blue; font-size: 9'><b>ENVIAR HOJE</b></span>";
							
						}
						
						else if ($dados["data_envio_cliente"] != "0000-00-00")
						{
						
							$status = "<span style='color: green; font-size: 9'><b>ENVIADO</b></span>";
							
						}
						
						else
						
						{
						
							$status = "<span style='font-size: 9'><b>AGUARD.</b></span>";
							
						}
						
						?>
						
						<tr height="22">                
							<td bgcolor="<?php echo $cor_celula ?>" style="padding-left: 4px; border-bottom: #aaa 1px dotted">
							<b>
								<?php 
								
									echo $dados["nome"] . "<br/><span style='font-weight: normal; font-size: 9px'>[" .  $dados["evento_id"] . "] - " . $dados["evento_nome"] . "</span>";
								
								?>
							</b>
							</td>
							<td bgcolor="<?php echo $cor_celula ?>" align="center" style="border-left: #aaa 1px dotted; border-bottom: #aaa 1px dotted">
								<?php 
								
									//Verifica se foi informado alguma data
									if ($dados["data_venda"] != "0000-00-00")
									{
									
										echo DataMySQLRetornar($dados["data_venda"]);
										
									}
									
									else
									
									{
									
										echo "&nbsp;";
										
									}
								?>
							</td>
							<td bgcolor="<?php echo $cor_celula ?>" align="center" style="border-left: #aaa 1px dotted; border-bottom: #aaa 1px dotted">
								<?php 
								
									//Verifica se foi informado alguma data
									if ($dados["data_envio_lab"] != "0000-00-00")
									{
									
										echo DataMySQLRetornar($dados["data_envio_lab"]);
										
									}
									
									else
									
									{
									
										echo "&nbsp;";
										
									}
								?>
							</td>
							<td bgcolor="<?php echo $cor_celula ?>" align="center" style="border-left: #aaa 1px dotted; border-bottom: #aaa 1px dotted">
								<?php 
								
									//Verifica se foi informado alguma data
									if ($dados["data_prev_lab"] != "0000-00-00")
									{
									
										echo DataMySQLRetornar($dados["data_prev_lab"]);
										
									}
									
									else
									
									{
									
										echo "&nbsp;";
										
									}
								?>
							</td>
							<td bgcolor="<?php echo $cor_celula ?>" align="center" style="border-left: #aaa 1px dotted; border-bottom: #aaa 1px dotted">
								<?php 
								
									//Verifica se foi informado alguma data
									if ($dados["data_retorno_lab"] != "0000-00-00")
									{
									
										echo DataMySQLRetornar($dados["data_retorno_lab"]);
										
									}
									
									else
									
									{
									
										echo "&nbsp;";
										
									}
								?>
							</td>
							<td bgcolor="<?php echo $cor_celula ?>" align="center" style="border-left: #aaa 1px dotted; border-bottom: #aaa 1px dotted">
								<?php 
								
									//Verifica se foi informado alguma data
									if ($dados["data_entrega_cliente"] != "0000-00-00")
									{
									
										echo DataMySQLRetornar($dados["data_entrega_cliente"]);
										
									}
									
									else
									
									{
									
										echo "&nbsp;";
										
									}
								?>
							</td>
							<td bgcolor="<?php echo $cor_celula ?>" align="center" style="border-left: #aaa 1px dotted; border-bottom: #aaa 1px dotted">
								<?php 
								
									//Verifica se foi informado alguma data
									if ($dados["data_envio_cliente"] != "0000-00-00")
									{
									
										echo DataMySQLRetornar($dados["data_envio_cliente"]);
										
									}
									
									else
									
									{
									
										echo "&nbsp;";
										
									}
								?>
							</td>
							<td bgcolor="<?php echo $cor_celula ?>" align="center" style="border-left: #aaa 1px dotted; border-bottom: #aaa 1px dotted"><?php echo $status ?></td>							
						</tr>
							
						<?php
						
					
					}
					
				?>
				
				<tr>
					<td height="24" colspan="7" style="padding-left: 4px">
						Total de Formandos Listados: <span style="color: #990000"><b><?php echo $registros ?></b></span>
					</td>
				</tr>
				
			</table>
		</td>
	</tr>
</table>

<?php
	
	}
?>