<?php
###########
## Módulo de relatório de consolidação financeira
## Criado: 02/09/2011 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

require('../fpdf/fpdf.php');

include '../conexao/ConexaoMySQL.php';

//Inclui o arquivo para manipulação de datas
include '../include/ManipulaDatas.php';

//Recupera os valores para filtragem

$empresaId = $_GET['EmpresaId'];
$empresaNome = $_GET['EmpresaNome'];
$usuarioNome = $_GET['UsuarioNome'];

$dataIni_1 = DataMySQLInserir($_GET["DataIni_1"]);
$dataFim_1 = DataMySQLInserir($_GET["DataFim_1"]);

$dataIni_2 = DataMySQLInserir($_GET["DataIni_2"]);
$dataFim_2 = DataMySQLInserir($_GET["DataFim_2"]);

$dataIni_3 = DataMySQLInserir($_GET["DataIni_3"]);
$dataFim_3 = DataMySQLInserir($_GET["DataFim_3"]);

$dataIni_4 = DataMySQLInserir($_GET["DataIni_4"]);
$dataFim_4 = DataMySQLInserir($_GET["DataFim_4"]);

$dataIni_5 = DataMySQLInserir($_GET["DataIni_5"]);
$dataFim_5 = DataMySQLInserir($_GET["DataFim_5"]);


$dataPagarIni_1 = DataMySQLInserir($_GET["DataPagarIni_1"]);
$dataPagarFim_1 = DataMySQLInserir($_GET["DataPagarFim_1"]);

$dataPagarIni_2 = DataMySQLInserir($_GET["DataPagarIni_2"]);
$dataPagarFim_2 = DataMySQLInserir($_GET["DataPagarFim_2"]);

$dataPagarIni_3 = DataMySQLInserir($_GET["DataPagarIni_3"]);
$dataPagarFim_3 = DataMySQLInserir($_GET["DataPagarFim_3"]);

$dataPagarIni_4 = DataMySQLInserir($_GET["DataPagarIni_4"]);
$dataPagarFim_4 = DataMySQLInserir($_GET["DataPagarFim_4"]);

$dataPagarIni_5 = DataMySQLInserir($_GET["DataPagarIni_5"]);
$dataPagarFim_5 = DataMySQLInserir($_GET["DataPagarFim_5"]);


//Monta o sql de filtragem das contas
$sql = "SELECT 
		valor,
		situacao,
		data_vencimento
		FROM contas_receber
		WHERE subgrupo_conta_id = 14 
		AND situacao = 1
		AND data_vencimento BETWEEN '$dataIni_1' AND '$dataFim_1'";	

$query = mysql_query($sql);
	  	
$registros = mysql_num_rows($query);


//Verifica se foi informado uma segunda data
if ($dataIni_2 != '0000-00-00')
{

	//Monta o sql de filtragem das contas
	$sql_2 = "SELECT 
			valor,
			situacao,
			data_vencimento
			FROM contas_receber
			WHERE subgrupo_conta_id = 14 
			AND situacao = 1
			AND data_vencimento BETWEEN '$dataIni_2' AND '$dataFim_2'";	

	$query_2 = mysql_query($sql_2);
	  	
	$registros_2 = mysql_num_rows($query_2);

}


//Verifica se foi informado uma terceira data
if ($dataIni_3 != '0000-00-00')
{

	//Monta o sql de filtragem das contas
	$sql_3 = "SELECT 
			valor,
			situacao,
			data_vencimento
			FROM contas_receber
			WHERE subgrupo_conta_id = 14 
			AND situacao = 1
			AND data_vencimento BETWEEN '$dataIni_3' AND '$dataFim_3'";	

	$query_3 = mysql_query($sql_3);
	  	
	$registros_3 = mysql_num_rows($query_3);

}


//Verifica se foi informado uma quarta data
if ($dataIni_3 != '0000-00-00')
{

	//Monta o sql de filtragem das contas
	$sql_3 = "SELECT 
			valor,
			situacao,
			data_vencimento
			FROM contas_receber
			WHERE subgrupo_conta_id = 14 
			AND situacao = 1
			AND data_vencimento BETWEEN '$dataIni_3' AND '$dataFim_3'";	

	$query_3 = mysql_query($sql_3);
	  	
	$registros_3 = mysql_num_rows($query_3);

}


//Verifica se foi informado uma terceira data
if ($dataIni_3 != '0000-00-00')
{

	//Monta o sql de filtragem das contas
	$sql_3 = "SELECT 
			valor,
			situacao,
			data_vencimento
			FROM contas_receber
			WHERE subgrupo_conta_id = 14 
			AND situacao = 1
			AND data_vencimento BETWEEN '$dataIni_3' AND '$dataFim_3'";	

	$query_3 = mysql_query($sql_3);
	  	
	$registros_3 = mysql_num_rows($query_3);

}


//Verifica se foi informado uma quarta data
if ($dataIni_4 != '0000-00-00')
{

	//Monta o sql de filtragem das contas
	$sql_4 = "SELECT 
			valor,
			situacao,
			data_vencimento
			FROM contas_receber
			WHERE subgrupo_conta_id = 14 
			AND situacao = 1
			AND data_vencimento BETWEEN '$dataIni_4' AND '$dataFim_4'";	

	$query_4 = mysql_query($sql_4);
	  	
	$registros_4 = mysql_num_rows($query_4);

}

//Verifica se foi informado uma terceira data
if ($dataIni_3 != '0000-00-00')
{

	//Monta o sql de filtragem das contas
	$sql_3 = "SELECT 
			valor,
			situacao,
			data_vencimento
			FROM contas_receber
			WHERE subgrupo_conta_id = 14 
			AND situacao = 1
			AND data_vencimento BETWEEN '$dataIni_3' AND '$dataFim_3'";	

	$query_3 = mysql_query($sql_3);
	  	
	$registros_3 = mysql_num_rows($query_3);

}


//Verifica se foi informado uma quinta data
if ($dataIni_5 != '0000-00-00')
{

	//Monta o sql de filtragem das contas
	$sql_5 = "SELECT 
			valor,
			situacao,
			data_vencimento
			FROM contas_receber
			WHERE subgrupo_conta_id = 14 
			AND situacao = 1
			AND data_vencimento BETWEEN '$dataIni_5' AND '$dataFim_5'";	

	$query_5 = mysql_query($sql_5);
	  	
	$registros_5 = mysql_num_rows($query_5);

}


//Contas a pagar
//Verifica se foi informado uma primera data
if ($dataPagarIni_1 != '0000-00-00')
{

	//Monta o sql de filtragem das contas
	$sql_pagar_1 = "SELECT 
				valor,
				valor_pago,
				situacao,
				data_vencimento
				FROM contas_pagar
				WHERE situacao = 1
				AND data_vencimento BETWEEN '$dataPagarIni_1' AND '$dataPagarFim_1'";	

	$query_pagar_1 = mysql_query($sql_pagar_1);
	  	
	$registros_pagar_1 = mysql_num_rows($query_pagar_1);

}


//Verifica se foi informado uma segunda data
if ($dataPagarIni_2 != '0000-00-00')
{

	//Monta o sql de filtragem das contas
	$sql_pagar_2 = "SELECT 
				valor,
				valor_pago,
				situacao,
				data_vencimento
				FROM contas_pagar
				WHERE situacao = 1
				AND data_vencimento BETWEEN '$dataPagarIni_2' AND '$dataPagarFim_2'";	

	$query_pagar_2 = mysql_query($sql_pagar_2);
	  	
	$registros_pagar_2 = mysql_num_rows($query_pagar_2);

}


//Verifica se foi informado uma terceira data
if ($dataPagarIni_3 != '0000-00-00')
{

	//Monta o sql de filtragem das contas
	$sql_pagar_3 = "SELECT 
				valor,
				valor_pago,
				situacao,
				data_vencimento
				FROM contas_pagar
				WHERE situacao = 1
				AND data_vencimento BETWEEN '$dataPagarIni_3' AND '$dataPagarFim_3'";	

	$query_pagar_3 = mysql_query($sql_pagar_3);
	  	
	$registros_pagar_3 = mysql_num_rows($query_pagar_3);

}


//Verifica se foi informado uma quarta data
if ($dataPagarIni_4 != '0000-00-00')
{

	//Monta o sql de filtragem das contas
	$sql_pagar_4 = "SELECT 
				valor,
				valor_pago,
				situacao,
				data_vencimento
				FROM contas_pagar
				WHERE situacao = 1
				AND data_vencimento BETWEEN '$dataPagarIni_4' AND '$dataPagarFim_4'";	

	$query_pagar_4 = mysql_query($sql_pagar_4);
	  	
	$registros_pagar_4 = mysql_num_rows($query_pagar_4);

}


//Verifica se foi informado uma quinta data
if ($dataPagarIni_5 != '0000-00-00')
{

	//Monta o sql de filtragem das contas
	$sql_pagar_5 = "SELECT 
				valor,
				valor_pago,
				situacao,
				data_vencimento
				FROM contas_pagar
				WHERE situacao = 1
				AND data_vencimento BETWEEN '$dataPagarIni_5' AND '$dataPagarFim_5'";	

	$query_pagar_5 = mysql_query($sql_pagar_5);
	  	
	$registros_pagar_5 = mysql_num_rows($query_pagar_5);

}


class PDF extends FPDF
{

	//Cabeçalho do relatório
	function Header()
	{
	    
		$empresaNome = $_GET['EmpresaNome'];
		
		//Ajusta a fonte
		$this->SetFont('Arial','',9);
		//Titulo do relatório
		$this->Cell(0,4, $empresaNome);
		$this->Cell(0,4, date('d/m/Y', mktime()),0,0,'R');
		$this->Ln();
		$this->SetFont('Arial','B',15);
		$this->Cell(0,6,'Relatório de Consolidação Financeira');
		$this->SetFont('Arial','',9);
		$this->Cell(0,4, 'Pagina: '.$this->PageNo(),0,0,'R');    
		//Line break
		$this->Ln(2);
	}

	//Rodapé do Relatório
	function Footer()
	{
		$usuarioNome = $_GET['UsuarioNome'];
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
$pdf=new PDF();

//Define os atributos de propriedade do arquivo PDF
$pdf->SetCreator('work | eventos');
$pdf->SetAuthor($usuarioNome . ' - ' . $empresaNome);
$pdf->SetTitle('Relatório de Consolidação Financeira');
$pdf->SetSubject('Relatório gerado automaticamente pelo sistema');
$pdf->AliasNbPages();
$pdf->AddPage('L');

//Caso não houverem registros
if ($registros == 0) 
{ 

	//Exibe uma linha dizendo que nao há registros
	$pdf->ln(10);
	$pdf->Cell(0,6, "Não há registros para a data informada !",1,0);
		  
} 

else 

{
	   
	//Cria a data do vencimento para comparar se a conta está vencida
	$data_base_vencimento = date('Y-m-d', mktime());
	
	$total_vencido_1 = 0;
	$total_vencer_1 = 0;
      		  
	//Cria o array e o percorre para montar a listagem dinamicamente
	while ($dados_rec = mysql_fetch_array($query))
	{
  	

		if ($dados_rec["data_vencimento"] < $data_base_vencimento)
		{

			$total_vencido_1 = $total_vencido_1 + $dados_rec["valor"];

		}
	
		else
		
		{
			
			$total_vencer_1 = $total_vencer_1 + $dados_rec["valor"];
	  
		} 
    
    }
               
    $pdf->ln(10);
	$pdf->SetFont('Arial','B',10);
	$pdf->SetFillColor(178,178,178);
	$pdf->Cell(100,5, "Valor das Contas a Receber (CONTA CAIXA 14 - FOTOS): " ,1,0,'L',1);
	$pdf->Cell(40,5, "Vencidas: " ,1,0,'R',1);
	$pdf->Cell(40,5, "A Vencer: " ,1,0,'R',1);
	$pdf->Cell(40,5, "Total:" ,1,0,'R',1);
	
	$pdf->ln();
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(100,5, "Período: " . $_GET["DataIni_1"] . " a " . $_GET["DataFim_1"] ,1,0,'L',1);
	$pdf->Cell(40,5, number_format($total_vencido_1, 2, ",", ".") ,1,0,'R');
	$pdf->Cell(40,5, number_format($total_vencer_1, 2, ",", ".") ,1,0,'R');
	$pdf->Cell(40,5, number_format($total_vencido_1 + $total_vencer_1, 2, ",", ".") ,1,0,'R');
      
  
//Fecha o if de se tem registros
}

//Caso não houverem registros
if ($registros_2 > 0) 
{
	   
	//Cria a data do vencimento para comparar se a conta está vencida
	$data_base_vencimento = date('Y-m-d', mktime());
	
	$total_vencido_2 = 0;
	$total_vencer_2 = 0;
      		  
	//Cria o array e o percorre para montar a listagem dinamicamente
	while ($dados_rec_2 = mysql_fetch_array($query_2))
	{
  	

		if ($dados_rec_2["data_vencimento"] < $data_base_vencimento)
		{

			$total_vencido_2 = $total_vencido_2 + $dados_rec_2["valor"];

		}
	
		else
		
		{
			
			$total_vencer_2 = $total_vencer_2 + $dados_rec_2["valor"];
	  
		} 
    
    }
	
	$pdf->ln();
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(100,5, "Período: " . $_GET["DataIni_2"] . " a " . $_GET["DataFim_2"] ,1,0,'L',1);
	$pdf->Cell(40,5, number_format($total_vencido_2, 2, ",", ".") ,1,0,'R');
	$pdf->Cell(40,5, number_format($total_vencer_2, 2, ",", ".") ,1,0,'R');
	$pdf->Cell(40,5, number_format($total_vencido_2 + $total_vencer_2, 2, ",", ".") ,1,0,'R');
      
  
//Fecha o if de se tem registros
}


//Caso não houverem registros
if ($registros_3 > 0) 
{
	   
	//Cria a data do vencimento para comparar se a conta está vencida
	$data_base_vencimento = date('Y-m-d', mktime());
	
	$total_vencido_3 = 0;
	$total_vencer_3 = 0;
      		  
	//Cria o array e o percorre para montar a listagem dinamicamente
	while ($dados_rec_3 = mysql_fetch_array($query_3))
	{
  	

		if ($dados_rec_3["data_vencimento"] < $data_base_vencimento)
		{

			$total_vencido_3 = $total_vencido_3 + $dados_rec_3["valor"];

		}
	
		else
		
		{
			
			$total_vencer_3 = $total_vencer_3 + $dados_rec_3["valor"];
	  
		} 
    
    }
	
	$pdf->ln();
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(100,5, "Período: " . $_GET["DataIni_3"] . " a " . $_GET["DataFim_3"] ,1,0,'L',1);
	$pdf->Cell(40,5, number_format($total_vencido_3, 2, ",", ".") ,1,0,'R');
	$pdf->Cell(40,5, number_format($total_vencer_3, 2, ",", ".") ,1,0,'R');
	$pdf->Cell(40,5, number_format($total_vencido_3 + $total_vencer_3, 2, ",", ".") ,1,0,'R');
      
  
//Fecha o if de se tem registros
}


//Caso não houverem registros
if ($registros_4 > 0) 
{
	   
	//Cria a data do vencimento para comparar se a conta está vencida
	$data_base_vencimento = date('Y-m-d', mktime());
	
	$total_vencido_4 = 0;
	$total_vencer_4 = 0;
      		  
	//Cria o array e o percorre para montar a listagem dinamicamente
	while ($dados_rec_4 = mysql_fetch_array($query_4))
	{
  	

		if ($dados_rec_4["data_vencimento"] < $data_base_vencimento)
		{

			$total_vencido_4 = $total_vencido_4 + $dados_rec_4["valor"];

		}
	
		else
		
		{
			
			$total_vencer_4 = $total_vencer_4 + $dados_rec_4["valor"];
	  
		} 
    
    }
	
	$pdf->ln();
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(100,5, "Período: " . $_GET["DataIni_4"] . " a " . $_GET["DataFim_4"] ,1,0,'L',1);
	$pdf->Cell(40,5, number_format($total_vencido_4, 2, ",", ".") ,1,0,'R');
	$pdf->Cell(40,5, number_format($total_vencer_4, 2, ",", ".") ,1,0,'R');
	$pdf->Cell(40,5, number_format($total_vencido_4 + $total_vencer_4, 2, ",", ".") ,1,0,'R');
      
  
//Fecha o if de se tem registros
}


//Caso não houverem registros
if ($registros_5 > 0) 
{
	   
	//Cria a data do vencimento para comparar se a conta está vencida
	$data_base_vencimento = date('Y-m-d', mktime());
	
	$total_vencido_5 = 0;
	$total_vencer_5 = 0;
      		  
	//Cria o array e o percorre para montar a listagem dinamicamente
	while ($dados_rec_5 = mysql_fetch_array($query_5))
	{
  	

		if ($dados_rec_5["data_vencimento"] < $data_base_vencimento)
		{

			$total_vencido_5 = $total_vencido_5 + $dados_rec_5["valor"];

		}
	
		else
		
		{
			
			$total_vencer_5 = $total_vencer_5 + $dados_rec_5["valor"];
	  
		} 
    
    }
	
	$pdf->ln();
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(100,5, "Período: " . $_GET["DataIni_5"] . " a " . $_GET["DataFim_5"] ,1,0,'L',1);
	$pdf->Cell(40,5, number_format($total_vencido_5, 2, ",", ".") ,1,0,'R');
	$pdf->Cell(40,5, number_format($total_vencer_5, 2, ",", ".") ,1,0,'R');
	$pdf->Cell(40,5, number_format($total_vencido_5 + $total_vencer_5, 2, ",", ".") ,1,0,'R');
      
  
//Fecha o if de se tem registros
}


$total_geral = $total_vencido_1 + $total_vencer_1 + $total_vencido_2 + $total_vencer_2 + $total_vencido_3 + $total_vencer_3 + $total_vencido_4 + $total_vencer_4 + $total_vencido_5 + $total_vencer_5;

if ($total_geral > 0)
{

	$pdf->ln();
	$pdf->SetFont('Arial','B',10);
	$pdf->Cell(100,5, "TOTAL GERAL:" ,1,0,'L',1);
	$pdf->Cell(120,5, number_format($total_geral, 2, ",", ".") ,1,0,'R', 1);

}


//***************************
//Contas a pagar
//Caso não houverem registros
if ($registros_pagar_1 == 0) 
{ 

	//Exibe uma linha dizendo que nao há registros
	$pdf->ln(10);
	$pdf->Cell(0,6, "Não há registros para a data informada !",1,0);
		  
} 

else 

{
	   
	//Cria a data do vencimento para comparar se a conta está vencida
	$data_base_vencimento = date('Y-m-d', mktime());
	
	$total_pagar_1 = 0;
      		  
	//Cria o array e o percorre para montar a listagem dinamicamente
	while ($dados_pagar_1 = mysql_fetch_array($query_pagar_1))
	{
  	
		{
			
			$total_pagar_1 = $total_pagar_1 + ($dados_pagar_1["valor"] - $dados_pagar_1["valor_pago"]);
	  
		} 
    
    }
               
    $pdf->ln(15);
	$pdf->SetFont('Arial','B',10);
	$pdf->SetFillColor(178,178,178);
	$pdf->Cell(100,5, "Valor das Contas a Pagar (GERAL): " ,1,0,'L',1);
	$pdf->Cell(40,5, "A Vencer: " ,1,0,'R',1);
	
	$pdf->ln();
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(100,5, "Período: " . $_GET["DataPagarIni_1"] . " a " . $_GET["DataPagarFim_1"] ,1,0,'L',1);
	$pdf->Cell(40,5, number_format($total_pagar_1, 2, ",", ".") ,1,0,'R');
      
  
//Fecha o if de se tem registros
}

//Caso não houverem registros
if ($registros_pagar_2 > 0) 
{
	   
	//Cria a data do vencimento para comparar se a conta está vencida
	$data_base_vencimento = date('Y-m-d', mktime());
	
	$total_pagar_2 = 0;
      		  
	//Cria o array e o percorre para montar a listagem dinamicamente
	while ($dados_pagar_2 = mysql_fetch_array($query_pagar_2))
	{
  	
		{
			
			$total_pagar_2 = $total_pagar_2 + ($dados_pagar_2["valor"] - $dados_pagar_2["valor_pago"]);
	  
		} 
    
    }
    
	$pdf->ln();
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(100,5, "Período: " . $_GET["DataPagarIni_2"] . " a " . $_GET["DataPagarFim_2"] ,1,0,'L',1);
	$pdf->Cell(40,5, number_format($total_pagar_2, 2, ",", ".") ,1,0,'R');
      
  
//Fecha o if de se tem registros
}


//Caso não houverem registros
if ($registros_pagar_3 > 0) 
{
	   
	//Cria a data do vencimento para comparar se a conta está vencida
	$data_base_vencimento = date('Y-m-d', mktime());
	
	$total_pagar_3 = 0;
      		  
	//Cria o array e o percorre para montar a listagem dinamicamente
	while ($dados_pagar_3 = mysql_fetch_array($query_pagar_3))
	{
  	
		{
			
			$total_pagar_3 = $total_pagar_3 + ($dados_pagar_3["valor"] - $dados_pagar_3["valor_pago"]);
	  
		} 
    
    }
    
	$pdf->ln();
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(100,5, "Período: " . $_GET["DataPagarIni_3"] . " a " . $_GET["DataPagarFim_3"] ,1,0,'L',1);
	$pdf->Cell(40,5, number_format($total_pagar_3, 2, ",", ".") ,1,0,'R');
      
  
//Fecha o if de se tem registros
}


//Caso não houverem registros
if ($registros_pagar_4 > 0) 
{
	   
	//Cria a data do vencimento para comparar se a conta está vencida
	$data_base_vencimento = date('Y-m-d', mktime());
	
	$total_pagar_4 = 0;
      		  
	//Cria o array e o percorre para montar a listagem dinamicamente
	while ($dados_pagar_4 = mysql_fetch_array($query_pagar_4))
	{
  	
		{
			
			$total_pagar_4 = $total_pagar_4 + ($dados_pagar_4["valor"] - $dados_pagar_4["valor_pago"]);
	  
		} 
    
    }
    
	$pdf->ln();
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(100,5, "Período: " . $_GET["DataPagarIni_4"] . " a " . $_GET["DataPagarFim_4"] ,1,0,'L',1);
	$pdf->Cell(40,5, number_format($total_pagar_4, 2, ",", ".") ,1,0,'R');
      
  
//Fecha o if de se tem registros
}


//Caso não houverem registros
if ($registros_pagar_5 > 0) 
{
	   
	//Cria a data do vencimento para comparar se a conta está vencida
	$data_base_vencimento = date('Y-m-d', mktime());
	
	$total_pagar_5 = 0;
      		  
	//Cria o array e o percorre para montar a listagem dinamicamente
	while ($dados_pagar_5 = mysql_fetch_array($query_pagar_5))
	{
  	
		{
			
			$total_pagar_5 = $total_pagar_5 + ($dados_pagar_5["valor"] - $dados_pagar_5["valor_pago"]);
	  
		} 
    
    }
    
	$pdf->ln();
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(100,5, "Período: " . $_GET["DataPagarIni_5"] . " a " . $_GET["DataPagarFim_5"] ,1,0,'L',1);
	$pdf->Cell(40,5, number_format($total_pagar_5, 2, ",", ".") ,1,0,'R');
      
  
//Fecha o if de se tem registros
}


$total_pagar_geral = $total_pagar_1 + $total_pagar_2 + $total_pagar_3 + $total_pagar_4 + $total_pagar_5;

//Caso tenha alum valor de consulta contas pagar
if ($total_pagar_geral > 0)
{

	$pdf->ln();
	$pdf->SetFont('Arial','B',10);
	$pdf->Cell(100,5, "TOTAL GERAL:" ,1,0,'L',1);
	$pdf->Cell(40,5, number_format($total_pagar_geral, 2, ",", ".") ,1,0,'R',1);

}

//Gera o PDF
$pdf->Output();
?>