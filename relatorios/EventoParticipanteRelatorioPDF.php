<?php
###########
## Módulo para montagem do relatório de Listagem de participantes do Evento em PDF
## Criado: 15/07/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########
//http://localhost/consoli/relatorios/EventoParticipanteRelatorioPDF.php?EventoId=1&UsuarioNome=Maycon%20Edinger&EmpresaNome=Nome%20da%20empresa&EmpresaId=1

//Acesso as rotinas do PDF
require("../fpdf/fpdf.php");

//Inclui o arquivo de conexão com o servidor
include "../conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "../include/ManipulaDatas.php";

//Recupera os valores para filtragem
$EventoId = $_GET["EventoId"];
$EmpresaId = $_GET["EmpresaId"];

//Recupera dos dados do evento
$sql_evento = "SELECT 
							eve.id,
							eve.nome,
							eve.descricao,
							eve.status,
							eve.cliente_id,
							eve.responsavel,
							eve.data_realizacao,
							eve.hora_realizacao,
							eve.duracao,
							eve.observacoes,
							cli.id as cliente_id,
							cli.nome as cliente_nome,
							cli.endereco as cliente_endereco,
							cli.complemento as cliente_complemento,
							cli.bairro as cliente_bairro,
							cli.cidade_id,
							cli.cep as cliente_cep,
							cli.uf as cliente_uf,
							cli.telefone as cliente_telefone,
							cli.fax as cliente_fax,
							cli.celular as cliente_celular,
							cli.email as cliente_email,
							cid.nome as cliente_cidade
							FROM eventos eve 
							INNER JOIN clientes cli ON cli.id = eve.cliente_id
							LEFT OUTER JOIN cidades cid ON cid.id = cli.cidade_id
							WHERE eve.id = '$EventoId'";

//Executa a query de consulta
$query_evento = mysql_query($sql_evento);

//Monta a matriz com os dados
$dados_evento = mysql_fetch_array($query_evento);

//Chama a classe para gerar o PDF
class PDF extends FPDF
{
//Cabeçalho do relatório
function Header()
{    
	//Recupera o nome da empresa
	$empresaNome = $_GET["EmpresaNome"];
  //Ajusta a fonte
  $this->SetFont("Arial","",9);
	$this->Cell(0,4, date("d/m/Y", mktime()),0,0,"R");
	$this->Ln();
	$this->Cell(0,4, "Pagina: ".$this->PageNo(),0,0,"R");
	$this->Ln();	    
	$this->SetFont("Arial","B",15);
  $this->Cell(0,6,"Relação de Participantes do Evento",0,0,"R");
  $this->SetFont("Arial","",9);
  
  //Imprime o logotipo da empresa
	$this->Image("../image/logo_consoli2.jpg",10,10,42);
	//$this->Image("../image/logo_consoli2.jpg",10,10,59);
	$this->SetY(25);
	$this->SetFont("Arial", "B", 10);
	$this->SetFillColor(178,178,178);
	
  //Line break
  $this->Ln(14);
}

//Rodapé do Relatório
function Footer()
{
   $usuarioNome = $_GET["UsuarioNome"];
   //Posiciona a 1.5 cm do final
   $this->SetY(-15);    
   //Arial italico 8
   $this->SetFont("Arial","I",7);
   $this->Line(10,281,200,281);
   $this->Cell(0,3,"Emitido por: " . $usuarioNome);
}
}

//Instancia a classe gerador de pdf
$pdf=new PDF();
//Define os atributos de propriedade do arquivo PDF
$pdf->SetCreator("work | eventos - CopyRight(c) - Desenvolvido por Maycon Edinger - edinger@bol.com.br");
$pdf->SetAuthor($usuarioNome . " - " . $empresaNome);
$pdf->SetTitle("Relação de Participantes do Evento");
$pdf->SetSubject("Relatório gerado automaticamente pelo sistema");
$pdf->AliasNbPages();

//Cria a página do relatório
$pdf->AddPage();

//Nova linha
//$pdf->ln();
$pdf->SetFont("Arial", "B", 12);
$pdf->Cell(0,7, "Evento:",1,0,"L");
$pdf->SetFont("Arial", "I", 12);
$pdf->SetX(30);
$pdf->Cell(0,7, $dados_evento["nome"],0,0,"L");

/* Dados que não serão usados, mas deixar aqui caso quiserem por depois
//Nova linha
$pdf->ln();
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(178,178,178);
$pdf->Cell(0,6, 'Descrição do Evento',1,0,'C',1);
$pdf->SetFont('Arial', 'I', 9);
$pdf->ln();
$pdf->MultiCell(0,4, $dados_evento['descricao'],1);

//Nova linha
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0,16, 'Cliente:',1,0,'L');
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetX(35);
$pdf->Cell(0,4, $dados_evento['cliente_nome'],0,0,'L');
$pdf->ln();
$pdf->SetFont('Arial', 'I', 7);
$pdf->SetX(35);
$pdf->Cell(0,3, $dados_evento['cliente_endereco'] . " - " . $dados_evento['cliente_complemento'],0,0,'L');
$pdf->ln();
$pdf->SetX(35);
$pdf->Cell(0,3, $dados_evento['cliente_bairro'] . " - " . $dados_evento['cliente_cep'] . " - " . $dados_evento['cliente_cidade'] . "/" . $dados_evento['cliente_uf'],0,0,'L');
$pdf->ln();
$pdf->SetX(35);
$pdf->Cell(0,3, "Fone: " . $dados_evento[cliente_telefone] . " - Fax: " . $dados_evento[cliente_fax] . " - Celular: " . $dados_evento[cliente_celular],0,0,'L');
$pdf->ln();
$pdf->SetX(35);
$pdf->Cell(0,3, "email: " . $dados_evento[cliente_email],0,0,'L');

//Nova linha
$pdf->ln();
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0,6, 'Responsável:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(35);
$pdf->Cell(0,6, $dados_evento['responsavel"],0,0,"L");

*/

//Nova linha
$pdf->ln();
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(60,6, "Data:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(30);
$pdf->Cell(0,6, DataMySQLRetornar($dados_evento["data_realizacao"]),0,0,"L");
$pdf->SetX(70);
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(60,6, "Hora:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(80);
$pdf->Cell(0,6, $dados_evento["hora_realizacao"],0,0,"L");
$pdf->SetX(130);
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(70,6, "Duração:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(146);
$pdf->Cell(0,6, $dados_evento["duracao"],0,0,"L");

/*
//Nova linha
$pdf->ln();
$pdf->SetFont("Arial", "B", 10);
$pdf->SetFillColor(178,178,178);
$pdf->Cell(0,6, "Informações Complementares",1,0,"C",1);
$pdf->ln();
$pdf->SetFont("Arial", "I", 10);
$pdf->MultiCell(0,4, $dados_evento["observacoes"],1);
*/


//*** Exibe os participantes do evento
$pdf->ln();
$pdf->SetFont("Arial", "B", 10);
$pdf->SetFillColor(178,178,178);
$pdf->Cell(0,6, "Participantes do Evento",1,0,"C",1);
$pdf->ln();

//verifica os participantes já cadastrados para este evento e exibe na tela
$sql_conta = mysql_query("SELECT
												 id														 
												 FROM eventos_participante
												 WHERE evento_id = '$EventoId'
												 ");

//Verifica o numero de registros retornados
$registros_conta = mysql_num_rows($sql_conta);

//Verifica a quantidade de registros
if ($registros_conta == 0 ) {
	//Exibe a mensagem que não foram encontrados registros
	$pdf->SetFont("Arial", "B", 9);
	$pdf->Cell(0,6, "Não há participantes cadastrados para este evento !",1,0,"L");

//Caso tiver
} else {

	//Cria a variável para armazenar o total de colaboradores do evento
	$registros_geral = 0;

	//Define a cor RGB do fundo da celula
	$pdf->SetFillColor(178,178,178);
	//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
	$pdf->SetFont("Arial", "B", 9);
	$pdf->Cell(0,6, "      Participante/Colaborador:",1,0,"L");
	$pdf->SetX(80);
	$pdf->Cell(0,6, "Custo:");
	$pdf->SetX(100);
	$pdf->Cell(0,6, "E-mail:");
	$pdf->SetX(154);
	$pdf->Cell(0,6, "Telefone:");
	$pdf->SetX(177);
	$pdf->Cell(0,6, "Celular:");		

	//Monta e execut ao sql para capturar os ids das funções que estão no evento
	$sql_funcao = mysql_query("SELECT part.funcao_id, func.nome as funcao_nome FROM eventos_participante part LEFT OUTER JOIN funcoes func ON func.id = part.funcao_id WHERE part.evento_id = '$EventoId' GROUP BY part.funcao_id");
	
	//Percorre o array das funcoes
	while ($dados_funcao = mysql_fetch_array($sql_funcao)){
	
		//Imprime o nome da funcao
		$pdf->ln(8);
		$pdf->SetFont("Arial","B",10);
		
		//Verifica se a funcao não tem o id 0 (devido as versões antigas do sistema)
		if ($dados_funcao["funcao_id"] == 0) {
			$nome_funcao = "Sem função definida";
		} else {
			$nome_funcao = $dados_funcao["funcao_nome"];
		}
		$pdf->Cell(6,4, $nome_funcao);

		//Executa a pesquisa dos colabores desta funcao no evento
		$sql_participante = mysql_query("SELECT
																		 par.id,
																		 par.colaborador_id,
																		 par.funcao_id,
																		 par.custo_funcionario,
																		 col.nome as colaborador_nome,
																		 col.telefone,
																		 col.celular,
																		 col.email														 
																		 FROM eventos_participante par
																		 INNER JOIN colaboradores col ON col.id = par.colaborador_id
																		 WHERE par.evento_id = '$EventoId'
																		 AND par.funcao_id = '$dados_funcao[funcao_id]'
																		 ORDER by col.nome
																		 ");	
		//Zera a variável de contador de colaboradores na funcao
		$registros_funcao = 0;
		
		//Percorre o array dos participantes
		while ($dados_participante = mysql_fetch_array($sql_participante)){

	  	//Imprime os dados dos contatos
		  $pdf->ln();
		  $pdf->SetFont("Arial","B",9);
			$pdf->SetX(15);
			$pdf->Cell(6,4, $dados_participante["colaborador_nome"]);
		  $pdf->SetX(80);
			$pdf->SetFont("Arial","",8);
		  $pdf->Cell(100,4, "R$ " . $dados_participante["custo_funcionario"]);
		  $pdf->SetX(100);
			$pdf->SetFont("Arial","",8);
		  $pdf->Cell(100,4, $dados_participante["email"]);
		  $pdf->SetX(154);
			$pdf->SetFont("Arial","",8);
		  $pdf->Cell(70,4, $dados_participante["telefone"]);
		  $pdf->SetX(177);
			$pdf->SetFont("Arial","",8);
		  $pdf->Cell(70,4, $dados_participante["celular"]);		  

			//Incrementa o contador do colaborador
			$registros_funcao++;
						
		//Fecha o while dos colaborades da funcao
		}	

		//Imprime o total de colaboradores da funcao
		$pdf->ln();
	  $pdf->SetFont("Arial","I",9);
		$pdf->SetX(15);
		$pdf->Cell(6,4, "Total de participantes nesta função: " . $registros_funcao);		
		
		//Incrmenta o valor do total de participantes ao total geral
		$registros_geral = $registros_geral + $registros_funcao;
			
	//Fecha o while da funcao
	}

//Imprime o total de colaboradores do evento
$pdf->ln(8);
$pdf->SetFont("Arial","I",9);
$pdf->Cell(0,4, "Total Geral de participantes no evento: " . $registros_geral);		

//Fecha o if de se tiver participantes
}
//Gera o PDF
$pdf->Output();

?>                                                      