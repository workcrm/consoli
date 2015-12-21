<?php
###########
## M�dulo de relat�rio geral de cheques da empresa
## Criado: 24/08/2011 - Maycon Edinger
## Alterado: 
## Altera��es: 
###########

require('../fpdf/fpdf.php');

include "../conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipula��o de datas
include "../include/ManipulaDatas.php";

//Recupera os valores para filtragem
$empresaId = $_GET["EmpresaId"];
$empresaNome = $_GET["EmpresaNome"];
$usuarioNome = $_GET["UsuarioNome"];
$dataIni = DataMySQLInserir($_GET[DataIni]);
$dataFim = DataMySQLInserir($_GET[DataFim]);

//Recebe os valores vindos do formul�rio
//Efetua o switch para o campo de tipo de listagem
switch ($_GET[TipoListagem]) 
{
	
	//Se for 1 ent�o � visualiza��o por situacao
	case 1: 
		
		$TipoSituacao = $_GET[TipoSituacao];
		
		//Efetua o switch da situacao informada
		switch ($TipoSituacao) 
		{
  		
			//Se for 0 ent�o � visualiza��o de todos
			case 0:
				$where_situacao = "";
			break;		
			//Se for 1 ent�o � visualiza��o dos recebidos
			case 1:
				$where_situacao = "AND che.status = '$TipoSituacao'";
			break;		
			//Se for 2 ent�o � visualiza��o dos compensados
			case 2:
				$where_situacao = "AND che.status = '$TipoSituacao'";
			break;
		
		}		
		
		//Verifica se foi informado alguma data para filtrar junto
		if ($dataIni != 0) 
		{
		
			$TextoSQLData = "AND che.data_emissao >= '$dataIni' AND che.data_emissao <= '$dataFim' ";

		}
		
		
		//Monta o sql de filtragem
		$sql = "SELECT 
				che.id,
				che.conta_corrente_id,
				che.numero_cheque,
				che.data_emissao,
				che.pre_datado,
				che.bom_para,
				che.valor,
				che.status,
				che.conta_pagar_id,
				che.data_compensacao,
				cont.nome AS conta_corrente_nome,
				cont.agencia,
				cont.conta,
				cpag.descricao AS conta_pagar_nome
				FROM cheques_empresa che
				LEFT OUTER JOIN conta_corrente cont ON cont.id = che.conta_corrente_id
				LEFT OUTER JOIN contas_pagar cpag ON cpag.id = che.conta_pagar_id
				WHERE 1 = 1 
				$where_situacao $TextoSQLData
				ORDER BY conta_corrente_nome, che.data_emissao";
				

		//Monta o texto para caso n�o houver registros
		$texto_vazio = "N�o h� cheques da empresa com a situa��o $texto_situacao";
	break;
	
	//Se for 2 ent�o � visualiza��o por pre datado
	case 2: 
		
		$TipoSituacao = $_GET[TipoSituacao];
		
		//Efetua o switch da situacao informada
		switch ($TipoSituacao) 
		{
  		
			//Se for 0 ent�o � visualiza��o de todos
			case 0:				
				$where_situacao = "";
			break;		
			//Se for 1 ent�o � visualiza��o dos recebidos
			case 1:				
				$where_situacao = "AND che.status = '$TipoSituacao'";
			break;		
			//Se for 2 ent�o � visualiza��o dos compensados
			case 2:
				$where_situacao = "AND che.status = '$TipoSituacao'";
			break;
		
		}		
		
		//Verifica se foi informado alguma data para filtrar junto
		if ($dataIni != 0) 
		{
		
			$TextoSQLData = "AND che.bom_para >= '$dataIni' AND che.bom_para <= '$dataFim' ";

		}
		
		
		//Monta o sql de filtragem
		$sql = "SELECT 
						che.id,
						che.conta_corrente_id,
						che.numero_cheque,
						che.data_emissao,
						che.pre_datado,
						che.bom_para,
						che.valor,
						che.status,
						che.data_compensacao,
						cont.nome AS conta_corrente_nome,
						cont.agencia,
						cont.conta,
						cpag.descricao AS conta_pagar_nome
						FROM cheques_empresa che
						LEFT OUTER JOIN conta_corrente cont ON cont.id = che.conta_corrente_id
						LEFT OUTER JOIN contas_pagar cpag ON cpag.id = che.conta_pagar_id
						WHERE 1 = 1 
						$where_situacao $TextoSQLData
						ORDER BY conta_corrente_nome, che.bom_para";

		//Monta o texto para caso n�o houver registros
		$texto_vazio = "N�o h� cheques pre-datados para o periodo especificado";
	break;
	
	//Se for 3 ent�o � visualiza��o por banco
	case 3: 
		
		$TipoSituacao = $_GET[TipoSituacao];
		$BancoId = $_GET[BancoId];
		
		//Efetua o switch da situacao informada
		switch ($TipoSituacao) 
		{
  		
			//Se for 0 ent�o � visualiza��o de todos
			case 0:
				$where_situacao = "";
			break;		
			//Se for 1 ent�o � visualiza��o dos recebidos
			case 1:
				$where_situacao = "AND che.status = '$TipoSituacao'";
			break;		
			//Se for 2 ent�o � visualiza��o dos compensados
			case 2:
				$where_situacao = "AND che.status = '$TipoSituacao'";
			break;
		
		}		
		
		//Verifica se foi informado alguma data para filtrar junto
		if ($dataIni != 0) 
		{
		
			$TextoSQLData = "AND che.bom_para >= '$dataIni' AND che.bom_para <= '$dataFim' ";

		}

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
						che.id,
						che.conta_corrente_id,
						che.numero_cheque,
						che.data_emissao,
						che.pre_datado,
						che.bom_para,
						che.valor,
						che.status,
						che.data_compensacao,
						cont.nome AS conta_corrente_nome,
						cont.agencia,
						cont.conta,
						cpag.descricao AS conta_pagar_nome
						FROM cheques_empresa che
						LEFT OUTER JOIN conta_corrente cont ON cont.id = che.conta_corrente_id
						LEFT OUTER JOIN contas_pagar cpag ON cpag.id = che.conta_pagar_id
						WHERE che.conta_corrente_id = $BancoId
						$where_situacao $TextoSQLData
						ORDER BY conta_corrente_nome, che.bom_para";

		//Monta o texto para caso n�o houver registros
		$texto_vazio = "N�o h� cheques desta conta-corrente para o periodo especificado";
		
	break;
	
	//Se for 4 ent�o � visualiza��o por evento
	case 4: 
		
		$EventoId = $_GET["EventoId"];
		
		//Efetua o switch da situacao informada
		switch ($TipoSituacao) 
		{
  		
			//Se for 0 ent�o � visualiza��o de todos
			case 0:
				$where_situacao = "";
			break;		
			//Se for 1 ent�o � visualiza��o dos recebidos
			case 1:
				$where_situacao = "AND che.status = '$TipoSituacao'";
			break;		
			//Se for 2 ent�o � visualiza��o dos compensados
			case 2:
				$where_situacao = "AND che.status = '$TipoSituacao'";
			break;
		
		}		
		
		//Verifica se foi informado alguma data para filtrar junto
		if ($dataIni != 0) 
		{
		
			$TextoSQLData = "AND che.bom_para >= '$dataIni' AND che.bom_para <= '$dataFim' ";

		}

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
						che.id,
						che.conta_corrente_id,
						che.numero_cheque,
						che.data_emissao,
						che.pre_datado,
						che.bom_para,
						che.valor,
						che.status,
						che.data_compensacao,
						cont.nome AS conta_corrente_nome,
						cont.agencia,
						cont.conta,
						cpag.descricao AS conta_pagar_nome
						FROM cheques_empresa che
						LEFT OUTER JOIN conta_corrente cont ON cont.id = che.conta_corrente_id
						LEFT OUTER JOIN contas_pagar cpag ON cpag.id = che.conta_pagar_id
						WHERE che.evento_id = $EventoId 
						$where_situacao $TextoSQLData
						ORDER BY conta_corrente_nome, che.bom_para";

		//Monta o texto para caso n�o houver registros
		$texto_vazio = "N�o h� cheques do evento para o periodo especificado";
		
	break;
	
} 

class PDF extends FPDF
{

	//Cabe�alho do relat�rio
	function Header()
	{
  
		//Pega a data inicial pra ver se veio vazia
		$dataIni = DataMySQLInserir($_GET[DataIni]);
	  
		$empresaNome = $_GET["EmpresaNome"];
		
		//Recebe os valores vindos do formul�rio
		//Efetua o switch para o campo de tipo de listagem
		switch ($_GET[TipoListagem]) 
		{
			
			//Se for 1 ent�o � visualiza��o por situacao
			case 1: 
				//Monta o t�tulo da p�gina
				$titulo = "Rela��o de Cheques da Empresa por Situa��o"; 
				$TipoSituacao = $_GET[TipoSituacao];
				
				//Efetua o switch da situacao informada
				switch ($TipoSituacao) 
				{
				
					//Se for 0 ent�o � visualiza��o de todos
					case 0:
						$texto_topo = "Listando Todos os Cheques da Empresa";
					break;		
					//Se for 1 ent�o � visualiza��o dos recebidos
					case 1:
						$texto_topo = "Listando Cheques da Empresa com o Status: Emitido";
						
						$ExibeTopo = 1;
					break;		
					//Se for 2 ent�o � visualiza��o dos compensados
					case 2:
						$texto_topo = "Listando Cheques da Empresa com o Status: Compensado";
						
						$ExibeTopo = 1;
					break;
				
				}		
				
				//Verifica se foi informado alguma data para filtrar junto
				if ($dataIni != 0) 
				{
				
					$ExibeTopo = 1;
					
					$texto_topo .= "\nEmitidos entre: $_GET[DataIni] a $_GET[DataFim]";

				}
				
			break;
			
			//Se for 2 ent�o � visualiza��o por pre datado
			case 2: 
				//Monta o t�tulo da p�gina
				$titulo = "Rela��o de Cheques Pre-datados"; 
				$TipoSituacao = $_GET[TipoSituacao];
				
				//Efetua o switch da situacao informada
				switch ($TipoSituacao) 
				{
				
					//Se for 0 ent�o � visualiza��o de todos
					case 0:
						$ExibeTopo = 1;
						
						$texto_topo = "Listando Todos os Cheques da Empresa";
					break;		
					//Se for 1 ent�o � visualiza��o dos recebidos
					case 1:
						$texto_topo = "Listando Cheques da Empresa com o Status: Emitido";
						
						$ExibeTopo = 1;
					break;		
					//Se for 2 ent�o � visualiza��o dos compensados
					case 2:
						$texto_topo = "Listando Cheques da Empresa com o Status: Compensado";
						
						$ExibeTopo = 1;
					break;
				
				}		
				
				//Verifica se foi informado alguma data para filtrar junto
				if ($dataIni != 0) 
				{
				
					$ExibeTopo = 1;
					
					$texto_topo .= "\nE com a data de Pre-data��o (Bom Para) entre: $_GET[DataIni] a $_GET[DataFim]";

				}
				
			break;
			
			//Se for 3 ent�o � visualiza��o por banco
			case 3: 
				//Monta o t�tulo da p�gina
				$titulo = "Rela��o de Cheques por Conta-Corrente"; 
				$TipoSituacao = $_GET[TipoSituacao];
				$BancoId = $_GET[BancoId];
				
				//Efetua o switch da situacao informada
				switch ($TipoSituacao) 
				{
				
					//Se for 0 ent�o � visualiza��o de todos
					case 0:
						$texto_topo = "Listando Todos os Cheques da Empresa";
					break;		
					//Se for 1 ent�o � visualiza��o dos recebidos
					case 1:
						$texto_topo = "Listando Cheques da Empresa com o Status: Emitido";
						
						$ExibeTopo = 1;
					break;		
					//Se for 2 ent�o � visualiza��o dos compensados
					case 2:
						$texto_topo = "Listando Cheques da Empresa com o Status: Compensado";
						
						$ExibeTopo = 1;
					break;
				
				}		
				
				//Verifica se foi informado alguma data para filtrar junto
				if ($dataIni != 0) 
				{
				
					$ExibeTopo = 1;
					
					$texto_topo .= "\nE com a data de Pre-data��o (Bom Para) entre: $_GET[DataIni] a $_GET[DataFim]";

				}
				
				//Captura o valor do banco
				$lista_banco = mysql_query("SELECT
											cco.id,
											cco.nome AS conta_nome,
											cco.agencia,
											cco.conta,
											ban.nome AS banco_nome
											FROM conta_corrente cco 
											LEFT OUTER JOIN bancos ban ON ban.id = cco.banco_id
											WHERE cco.id = $BancoId");
						
				//Executa a query
				$dados_banco = mysql_fetch_array($lista_banco); 
				
				$ExibeTopo = 1;
				
				$BancoNome = $dados_banco["conta_nome"]  . " - Ag: " . $dados_banco["agencia"] . " - Conta: " . $dados_banco["conta"];
				
				//Monta a descri��o a exibir
				$texto_topo .="\nE da Conta-Corrente: $BancoNome";
				
			break;
			
			//Se for 4 ent�o � visualiza��o por evento
			case 4: 
				//Monta o t�tulo da p�gina
				$titulo = "Rela��o de Cheques por Evento"; $TipoSituacao = $_GET[TipoSituacao];
				$EventoId = $_GET["EventoId"];
				
				//Efetua o switch da situacao informada
				switch ($TipoSituacao) 
				{
				
					//Se for 0 ent�o � visualiza��o de todos
					case 0:
						$texto_topo = "Listando Todos os Cheques da Empresa";
					break;		
					//Se for 1 ent�o � visualiza��o dos recebidos
					case 1:
						$texto_topo = "Listando Cheques da Empresa com o Status: Emitido";
						
						$ExibeTopo = 1;
					break;		
					//Se for 2 ent�o � visualiza��o dos compensados
					case 2:
						$texto_topo = "Listando Cheques da Empresa com o Status: Compensado";
						
						$ExibeTopo = 1;
					break;
				
				}		
				
				//Verifica se foi informado alguma data para filtrar junto
				if ($dataIni != 0) 
				{
				
					$ExibeTopo = 1;
					
					$texto_topo .= "\nE com a data de Pre-data��o (Bom Para) entre: $_GET[DataIni] a $_GET[DataFim]";

				}
				
				//Captura o valor do evento
				$lista_evento = mysql_query("SELECT nome FROM eventos WHERE id = $EventoId");
				
				//Executa a query
				$dados_evento = mysql_fetch_array($lista_evento); 
				
				$EventoNome = "(" . $EventoId . ") - " . $dados_evento["nome"];
				
				$ExibeTopo = 1;
				
				//Monta a descri��o a exibir
				$texto_topo .="\nExibindo Cheques do Evento: $EventoNome" . $TextoFiltraData;
				
			break;
			
		}

		//Ajusta a fonte
		$this->SetFont('Arial','',9);
		
		//Titulo do relat�rio
		$this->Cell(0,4, $empresaNome);
		$this->Cell(0,4, date('d/m/Y', mktime()),0,0,'R');
		$this->Ln();
		$this->SetFont('Arial','B',15);
		$this->Cell(0,6,'Rela��o Geral de Cheques da Empresa');
		$this->SetFont('Arial','',9);
		$this->Cell(0,4, 'Pagina: '.$this->PageNo(),0,0,'R');    
		$this->Ln(5);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(19,5,'Filtragem:');
		$this->SetFont('Arial', '', 10);
		
		if ($ExibeTopo == 1)
		{
			
			$this->Multicell(0,5, $texto_topo);
			//Line break
			//$this->Ln(6);

		}
	
	}

	//Rodap� do Relat�rio
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
$pdf->SetTitle('Rela��o Geral de Cheques da Empresa');
$pdf->SetSubject('Relat�rio gerado automaticamente pelo sistema');
$pdf->AliasNbPages();
$pdf->AddPage('L');

//T�tulos das colunas
$pdf->SetFont('Arial', 'B', 10);

//Define a cor RGB do fundo da celula
$pdf->SetFillColor(178,178,178);

//Faz a c�lula ser preenchida. Isso � feito setando 1 ap�s a expressao de alinhamento
$pdf->ln();
$pdf->Cell(0,6, 'Nr Cheque',1,0,'L',1);
$pdf->SetX(30);
$pdf->Cell(0,6, 'Descri��o da Conta');
$pdf->SetX(180);
$pdf->Cell(30,6, 'Valor',0,0,'R');
$pdf->SetX(216);
$pdf->Cell(0,6, 'Emiss�o');
$pdf->SetX(238);
$pdf->Cell(0,6, 'Bom Para');
$pdf->SetX(260);
$pdf->Cell(24,6, 'Situa��o');

//Executa a Query
$query = mysql_query($sql);		  	  

//verifica o n�mero total de registros
$tot_regs = mysql_num_rows($query);

$total_geral = 0;
$total_recebido = 0;
$total_compensado = 0;
$total_devolvido = 0;

//Caso houverem registros
if ($tot_regs > 0) 
{ 

	//Percorre o array dos dados
	while ($dados = mysql_fetch_array($query))
	{
		
		//Efetua o switch para a descri��o do status
		switch ($dados["status"]) 
		{

			//Se for em aberto
			case 1: 
				$desc_status = "Emitido";
				$total_recebido = $total_recebido + $dados_rec[valor];								
			break;
			//Se for compensado
			case 2: 
				$desc_status = "Compensado"; 
				$total_compensado = $total_compensado + $dados_rec[valor];
			break;
					
		
		}
		
		//Caso seja de outra conta-corrente
		if ($dados["conta_corrente_id"] != $edtContaCorrenteId)
		{
		
			//Verifica se nao e a primeira conta listada, para dai mostrar o total
			if ($edtContaCorrenteId != 0)
			{
			
				$pdf->ln();
				$pdf->SetFont('Arial', 'B', 10);
				$pdf->SetX(160);
				$pdf->Cell(20,6, 'Total da Conta:',0,0,'R');
				$pdf->SetX(180);
				$pdf->Cell(30,6, "R$ " . number_format($edtTotalValor, 2, ",", "."),0,0,'R');
				
				$edtTotalValor = 0;
				
			}
			
			$pdf->ln();
			$pdf->SetFont('Arial', 'B', 12);
			$pdf->Cell(0,6, $dados["conta_corrente_nome"] . " - Ag: " . $dados["agencia"] . " - Conta: " . $dados["conta"],"T");
		
		}		
			
	  
		$pdf->ln();
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(0,5, $dados['numero_cheque']);
		$pdf->SetFont('Arial','',9);
		$pdf->SetX(30);
		$pdf->Cell(0,5, $dados['conta_pagar_nome']);
		$pdf->SetX(180);
		$pdf->Cell(30,5, number_format($dados['valor'], 2, ",", "."),0,0,"R");
		$pdf->SetX(216);
		$pdf->Cell(0,5, DataMySQLRetornar($dados['data_emissao']));
		$pdf->SetX(238);
		$pdf->Cell(0,5, DataMySQLRetornar($dados['bom_para']));  
		$pdf->SetX(260);
		$pdf->Cell(0,5, $desc_status);
		
		//Acumula o valor a pagar
		$total_pagar = $total_pagar + $dados['valor'];	

		$edtTotalValor = $edtTotalValor + $dados["valor"];
					
		$edtTotalGeralValor = $edtTotalGeralValor + $dados["valor"];
		
		$edtContaCorrenteId = $dados["conta_corrente_id"];
	
	}

	
	$pdf->ln();
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->SetX(160);
	$pdf->Cell(20,6, 'Total da Conta:',0,0,'R');
	$pdf->SetX(180);
	$pdf->Cell(30,6, "R$ " . number_format($edtTotalValor, 2, ",", "."),0,0,'R');
	
	$pdf->ln();
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->SetX(160);
	$pdf->Cell(20,6, 'Total Geral:',0,0,'R');
	$pdf->SetX(180);
	$pdf->Cell(30,6, "R$ " . number_format($edtTotalGeralValor, 2, ",", "."),0,0,'R');

}

else

{

	$pdf->ln();
	$pdf->Cell(0,6, $texto_vazio, 'T');
	
}

//Gera o PDF
$pdf->Output();
?>