<?php
###########
## Módulo de relatório de controle de envio do foto e vídeo
## Criado: 07/12/2009 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

require("../fpdf/fpdf.php");

include "../conexao/ConexaoMySQL.php";

//Verifica se a funcão já foi declarada
if (function_exists("DataMySQLRetornar") == false) 
{
	
	//Inclui o arquivo para manipulação de datas
	include "../include/ManipulaDatas.php";

}

//Captura o evento informado
$empresaId = $_GET["EmpresaId"];
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
			form.data_venda,
			form.data_envio_lab,
			form.data_prev_lab,
			form.data_retorno_lab,
			form.data_entrega_cliente,
			form.data_envio_cliente,
			form.evento_id,
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
			form.data_venda,
			form.data_envio_lab,
			form.data_prev_lab,
			form.data_retorno_lab,
			form.data_entrega_cliente,
			form.data_envio_cliente,
			form.evento_id,
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
			form.data_venda,
			form.data_envio_lab,
			form.data_prev_lab,
			form.data_retorno_lab,
			form.data_entrega_cliente,
			form.data_envio_cliente,
			form.evento_id,
			eve.nome AS evento_nome
			FROM eventos_formando form
			LEFT OUTER JOIN eventos eve ON eve.id = form.evento_id
			WHERE form.empresa_id = $empresaId 
			$where_evento
			$where_fornecedor
			AND form.data_entrega_cliente BETWEEN '$edtDataIni' AND '$edtDataFim'
			ORDER BY form.data_entrega_cliente, form.nome";   

}
//echo $sql;
$query = mysql_query($sql);

$registros = mysql_num_rows($query);

class PDF extends FPDF
{

	//Cabeçalho do relatório
	function Header()
	{
		$edtEventoId = $_GET["EventoId"];
		$edtFornecedorId = $_GET["FornecedorId"];
		$edtEstagio = $_GET["Estagio"];	
		$edtDataIni = $_GET[DataIni];
		$edtDataFim = $_GET[DataFim];		
		$empresaNome = $_GET["EmpresaNome"];		
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
  
		//Ajusta a fonte
		$this->SetFont("Arial","",9);
		
		//Titulo do relatório
		$this->Cell(0,4, $empresaNome);
		$this->Cell(0,4, date("d/m/Y", mktime()),0,0,"R");
		$this->Ln();
		$this->SetFont("Arial","B",15);
		$this->Cell(0,6,"Relatório de Controle de Envio do Foto e Vídeo");
		$this->Ln();
		$this->SetFont("Arial","",11);
		$this->Cell(0,5,"Evento: " . $nome_evento);
		$this->Ln();
		$this->SetFont("Arial","",11);
		$this->Cell(0,5,"Data de Entrega ao Cliente Entre " . $edtDataIni . " a " . $edtDataFim);
		
		if ($edtEstagio > 0)
		{
			switch ($edtEstagio)
			{

				case 0:
					$texto_estagio = "";
				break;
				case 1:
					$texto_estagio = "Com o estágio: EM ATRASO";
				break;
				case 2:
					$texto_estagio = "Com o estágio: ENVIADO";
				break;
				case 3:
					$texto_estagio = "Com o estágio: AGUARDANDO";
				break;

			}
		
			$this->Ln();
			$this->SetFont("Arial","",11);
			$this->Cell(0,5, $texto_estagio);
		}
		
		if ($edtFornecedorId > 0)
		{
		
			//Busca o nome do evento
			//Monta o sql
			$sql_fornecedor = mysql_query("SELECT id, nome FROM fornecedores WHERE id = $edtFornecedorId");

			//Monta o array com os dados
			$dados_fornecedor = mysql_fetch_array($sql_fornecedor);
			
			$texto_fornecedor = 'No Fornecedor: ' . $dados_fornecedor["id"] . ' - ' . $dados_fornecedor["nome"];

			
			$this->Ln();
			$this->SetFont("Arial","",11);
			$this->Cell(0,5, $texto_fornecedor );
		}
		
		$this->SetFont("Arial","",9);
		$this->Cell(0,4, "Pagina: " . $this->PageNo(),0,0,"R");    
		//Line break
		$this->Ln(10);
	}

	//Rodapé do Relatório
	function Footer()
	{
	
		$usuarioNome = $_GET["UsuarioNome"];
		//Position at 1.5 cm from bottom
		$this->SetY(-15);    
		//Arial italic 8
		$this->SetFont('Arial','I',7);
		//Page number
		$this->Line(10,281,200,281);
		$this->Cell(0,3,'Emitido por: ' . $usuarioNome);
	}

}

//Instancia a classe gerador de pdf
$pdf = new PDF();

//Define os atributos de propriedade do arquivo PDF
$pdf->SetCreator('work | eventos');
$pdf->SetAuthor($usuarioNome . " - " . $empresaNome);
$pdf->SetTitle('Controle de Envio do Foto e Vídeo');
$pdf->SetSubject('Relatório gerado automaticamente pelo sistema');
$pdf->AliasNbPages();
$pdf->AddPage('L');

//Títulos das colunas
$pdf->SetFont('Arial', 'B', 10);

//Define a cor RGB do fundo da celula
$pdf->SetFillColor(178,178,178);

//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
$pdf->Cell(100,5, 'Formando/','LTR',0,'L',1);
$pdf->Cell(25,5, 'Data da','LTR',0,'C',1);
$pdf->Cell(25,5, 'Envio para','LTR',0,'C',1);
$pdf->Cell(25,5, 'Previsão do','LTR',0,'C',1);
$pdf->Cell(25,5, 'Retorno do','LTR',0,'C',1);
$pdf->Cell(25,5, 'Prazo Entrega','LTR',0,'C',1);
$pdf->Cell(25,5, 'Envio para','LTR',0,'C',1);
$pdf->Cell(0,5, 'Status','LTR',0,'C',1);
$pdf->Ln();
$pdf->Cell(100,5, 'Evento','LBR',0,'L',1);
$pdf->Cell(25,5, 'Venda','LBR',0,'C',1);
$pdf->Cell(25,5, 'Laboratório','LBR',0,'C',1);
$pdf->Cell(25,5, 'Laboratório','LBR',0,'C',1);
$pdf->Cell(25,5, 'Laboratório','LBR',0,'C',1);
$pdf->Cell(25,5, 'ao Cliente','LBR',0,'C',1);
$pdf->Cell(25,5, 'o Cliente','LBR',0,'C',1);
$pdf->Cell(0,5, 'Status','LBR',0,'C',1);

while ($dados = mysql_fetch_array($query))
{

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
	
		$status = "EM ATRASO";
		
	}
	
	else if ($timestamp1 == $timestamp2 AND $dados["data_envio_cliente"] == "0000-00-00")
	{
	
		$status = "ENVIAR HOJE";
		
	}
	
	else if ($dados["data_envio_cliente"] != "0000-00-00")
	{
	
		$status = "ENVIADO";
		
	}
	
	else
	
	{
	
		$status = "AGUARDANDO";
		
	}
	
	$pdf->ln();	
	$pdf->SetFont('Arial','B',11);
	$pdf->Cell(100,7, $dados['nome'],'LTR');
	$pdf->SetFont('Arial','',10);
	
	//Campo de data da venda								
	//Verifica se foi informado alguma data
	if ($dados["data_venda"] != "0000-00-00")
	{
		
		$pdf->Cell(25,7, DataMySQLRetornar($dados['data_venda']),'LTR',0,'C');
			
	}
		
	else
		
	{
		
		$pdf->Cell(25,7, " ",'LTR',0,'C');
		
	}
	
	//Campo de data do envio ao laboratorio							
	//Verifica se foi informado alguma data
	if ($dados["data_envio_lab"] != "0000-00-00")
	{
		
		$pdf->Cell(25,7, DataMySQLRetornar($dados['data_envio_lab']),'LTR',0,'C');
			
	}
		
	else
		
	{
		
		$pdf->Cell(25,7, " ",'LTR',0,'C');
		
	}
	
	//Campo de data do envio ao laboratorio							
	//Verifica se foi informado alguma data
	if ($dados["data_prev_lab"] != "0000-00-00")
	{
		
		$pdf->Cell(25,7, DataMySQLRetornar($dados['data_prev_lab']),'LTR',0,'C');
			
	}
		
	else
		
	{
		
		$pdf->Cell(25,7, " ",'LTR',0,'C');
		
	}
	
	//Campo de data do retorno do lab								
	//Verifica se foi informado alguma data
	if ($dados["data_retorno_lab"] != "0000-00-00")
	{
		
		$pdf->Cell(25,7, DataMySQLRetornar($dados['data_retorno_lab']),'LTR',0,'C');
			
	}
		
	else
		
	{
		
		$pdf->Cell(25,7, " ",'LTR',0,'C');
		
	}
	
	//Campo de data prazo entrega cliente
	//Verifica se foi informado alguma data
	if ($dados["data_entrega_cliente"] != "0000-00-00")
	{
		
		$pdf->Cell(25,7, DataMySQLRetornar($dados['data_entrega_cliente']),'LTR',0,'C');
			
	}
		
	else
		
	{
		
		$pdf->Cell(25,7, " ",'LTR',0,'C');
		
	}
	
	//Campo de data do envio para o cliente
	//Verifica se foi informado alguma data
	if ($dados["data_envio_cliente"] != "0000-00-00")
	{
		
		$pdf->Cell(25,7, DataMySQLRetornar($dados['data_envio_cliente']),'LTR',0,'C');
			
	}
		
	else
		
	{
		
		$pdf->Cell(25,7, " ",'LTR',0,'C');
		
	}
	
	$pdf->Cell(0,7, $status ,'LTR',0,'C');
	$pdf->ln();
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(100,4, "[" . $dados["evento_id"] . "] - " . substr($dados["evento_nome"],0,50),'LBR');
	$pdf->Cell(25,4, " ",'LBR');
	$pdf->Cell(25,4, " ",'LBR');
	$pdf->Cell(25,4, " ",'LBR');
	$pdf->Cell(25,4, " ",'LBR');
	$pdf->Cell(25,4, " ",'LBR');
	$pdf->Cell(25,4, " ",'LBR');
	$pdf->Cell(0,4, " ",'LBR');
	
}

$pdf->SetFont('Arial', 'B', 8);
$pdf->ln();
$pdf->Cell(0,6, 'Total de formandos listados: ' . $registros,'T');

//Gera o PDF
$pdf->Output();

?>