<?php
###########
## Módulo de relatório atividades do evento
## Criado: 16/05/2012 - Maycon Edinger
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

//Recupera os valores para filtragem
$empresaId = $_GET["EmpresaId"];
$empresaNome = $_GET["EmpresaNome"];
$usuarioNome = $_GET["UsuarioNome"];
$DataIni = DataMySQLInserir($_GET["DataIni"]);
$DataFim = DataMySQLInserir($_GET["DataFim"]);
$cmbEventoId = $_GET["cmbEventoId"];
$cmbAtividadeId = $_GET["cmbAtividadeId"];
$cmbRegiaoId = $_GET["cmbRegiaoId"];
$edtTipoStatus = $_GET["TipoStatus"];

if ($edtTipoStatus < 3)
{
  
	switch ($edtTipoStatus) 
	{
		
		case 0: 
			$where_status = "AND ati.status = 0";
		break;
		case 1: 
			$hoje = date("Y-m-d", mktime());
			$where_status = "AND ati.status = 0 AND ati.data_prazo < '$hoje'"; 
		break;
		case 2: 
			$where_status = "AND ati.status = 1"; 
		break;
	
	}

}

if ($DataIni != '0-0-0')
{
	
	if ($edtTipoStatus ==2)
	{
	
		$where_datas = "AND ati.data_execucao BETWEEN '$DataIni' AND '$DataFim'";
		
	}
	
	else
	
	{
	
		$where_datas = "AND ati.data_prazo BETWEEN '$DataIni' AND '$DataFim'";
	
	}
	
}

if ($cmbEventoId > 0)
{
  
	$where_evento = "AND ati.evento_id = $cmbEventoId";

}

if ($cmbAtividadeId > 0)
{
  
	$where_atividade = "AND ati.atividade_id = $cmbAtividadeId";

}

if ($cmbRegiaoId > 0)
{
  
	$where_regiao = "AND eve.regiao_id = $cmbRegiaoId";

}

$sql = mysql_query("SELECT 
					ati.id,
					ati.evento_id,
					ati.atividade_id,
					ati.data_prazo,
					ati.data_execucao,				
					ati.status,
					ati.obs,
					ati.usuario_execucao,
					CONCAT(usu.nome, ' ', usu.sobrenome) AS usuario_nome,
					eve.nome AS evento_nome,
					atividade.descricao AS atividade_nome,
					atividade.dias AS atividade_dias
					FROM eventos_atividade ati
					LEFT OUTER JOIN eventos eve ON eve.id = ati.evento_id
					LEFT OUTER JOIN usuarios usu ON usu.usuario_id = ati.usuario_execucao
					LEFT OUTER JOIN atividades atividade ON atividade.id = ati.atividade_id
					WHERE 1=1
                    $where_tipo 
					$where_datas
                    $where_status
					$where_evento
					$where_atividade
					$where_regiao
                    ORDER BY ati.evento_id, ati.data_prazo");

$registros = mysql_num_rows($sql);  

class PDF extends FPDF
{
	
	//Cabeçalho do relatório
	function Header()
	{
		
		$DataIni = $_GET["DataIni"];
		$DataFim = $_GET["DataFim"]; 
		$edtTipoStatus = $_GET["TipoStatus"];
		$cmbEventoId = $_GET["cmbEventoId"];
		$cmbAtividadeId = $_GET["cmbAtividadeId"];
   
		$empresaNome = $_GET["EmpresaNome"];
		
		//Ajusta a fonte
		$this->SetFont("Arial","",9);
		//Titulo do relatório
		$this->Cell(0,4, $empresaNome);
		$this->Cell(0,4, date("d/m/Y", mktime()),0,0,"R");
		$this->Ln();
		$this->SetFont("Arial","B",15);
		$this->Cell(0,6,"Relação de Atividades em Eventos");
		
		if ($DataIni != '')
		{
			$this->Ln();
			$this->SetFont("Arial","",11);
			$this->Cell(0,6,"Período: " . $DataIni . " a " . $DataFim);
			
		}
		
		$this->SetFont("Arial","",9);
		$this->Cell(0,5, "Pagina: " . $this->PageNo(),0,0,"R");
		
		if ($desc_status < 3)
		{
		  
			switch ($edtTipoStatus) 
			{
				
				case 0: $desc_status = "Com Status: Em Aberto"; break;
				case 1: $desc_status = "Com Status: Em Atraso"; break;
				case 2: $desc_status = "Com Status: Concluído"; break;
			
			}
			
			$this->Ln();
			$this->SetFont("Arial","",11);
			$this->Cell(0,6, $desc_status);

		}
		
		if ($cmbEventoId > 0)
		{
		
			//Monta o SQL de pesquisa
			$lista_evento = "SELECT id, nome FROM eventos WHERE id = $cmbEventoId";

			//Executa a query
			$query_evento = mysql_query($lista_evento);
			
			$dados_evento = mysql_fetch_array($query_evento);
			
			$this->Ln();
			$this->Cell(0,5, "Evento: (" . $dados_evento["id"] .') - ' . $dados_evento["nome"]);
			
		}

		if ($cmbAtividadeId > 0)
		{
		
			//Monta o SQL de pesquisa
			$lista_atividade = "SELECT id, descricao FROM atividades WHERE id = $cmbAtividadeId";

			//Executa a query
			$query_atividade = mysql_query($lista_atividade);
			
			$dados_atividade = mysql_fetch_array($query_atividade);
			
			$this->Ln();
			$this->Cell(0,5, "Atividade: " . $dados_atividade["descricao"]);
			
		}		

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
		$this->SetFont("Arial", "I",7);
		//Page number
		$this->Line(10,281,200,281);
		$this->Cell(0,3,"Emitido por: " . $usuarioNome);
	
	}

}

//Instancia a classe gerador de pdf
$pdf=new PDF();

//Define os atributos de propriedade do arquivo PDF
$pdf->SetCreator("work | eventos");
$pdf->SetAuthor($usuarioNome . ' - ' . $empresaNome);
$pdf->SetTitle("Relação de Eventos por Data");
$pdf->SetSubject("Relatório gerado automaticamente pelo sistema");
$pdf->AliasNbPages();
$pdf->AddPage("L");

//Títulos das colunas
$pdf->SetFont("Arial", "B", 10);

//Define a cor RGB do fundo da celula
$pdf->SetFillColor(178,178,178);

//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
$pdf->Cell(0,6, "Status", 1, 0, "L",1);
$pdf->SetX(36);
$pdf->Cell(80,6, "Atividade");
$pdf->Cell(14,6, "Dias");
$pdf->Cell(30,6, "Prazo");
$pdf->Cell(30,6, "Execução");
$pdf->Cell(0,6, "Responsável");

//Monta as linhas com os dados da query
$pdf->SetFont("Arial","",10);

$soma_aberto = 0;
$soma_fechado = 0;
$quebra = 0;

while ($dados = mysql_fetch_array($sql))
{

	switch ($dados[status]) 
	{
		case 0: 
			$desc_status = "Em Aberto"; 
			$soma_aberto++;
		break;
		case 1: 
			$desc_status = "Concluído"; 
			$soma_fechado++;
		break;
	}

	//Verifica a quebra
	if ($quebra != $dados["evento_id"])
	{
	
		$pdf->ln(8);
		$pdf->SetFont("Arial","B",14);
		$pdf->Cell(0,7, '(' . $dados["evento_id"] . ') - ' . $dados["evento_nome"],'T');
		$pdf->SetFont("Arial","",10);
		$pdf->ln(4);
	
	}

	$pdf->ln();
	$pdf->Cell(26,5, $desc_status);
	$pdf->SetFont("Arial","B",10);
	$pdf->Cell(80,5, $dados["atividade_nome"]);
	$pdf->SetFont("Arial","",10);
	$pdf->Cell(14,5, $dados["atividade_dias"],0,'C');
	$pdf->Cell(30,5, DataMySQLRetornar($dados["data_prazo"]));
	
	if ($dados["data_execucao"] != '0000-00-00')
	{
	
		$pdf->Cell(30,5, DataMySQLRetornar($dados["data_execucao"]));
		
	}
	
	else
	
	{
	
		$pdf->Cell(30,5, ' ');
	
	}
	
	$pdf->Cell(0,5, $dados["usuario_nome"]);
	
	if ($dados["obs"] != '')
	{
		
		$pdf->ln();
		$pdf->setX(36);
		$pdf->SetFont("Arial","I",9);
		$pdf->Multicell(0,3, $dados["obs"]);
		$pdf->SetFont("Arial","",10);
		
	}
	
	$quebra = $dados["evento_id"];

}

$pdf->SetFont("Arial", "B", 10);
$pdf->ln();
$pdf->Cell(0,6, "Total Geral de Atividades: " . $registros, "T");
$pdf->ln();
$pdf->Cell(0,6, "Em Aberto: " . $soma_aberto, "T");
$pdf->ln();
$pdf->Cell(0,6, "Concluídas: " . $soma_fechado, "T");

//Gera o PDF
$pdf->Output();

?>