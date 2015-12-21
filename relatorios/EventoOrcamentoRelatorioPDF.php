<?php
###########
## Módulo para montagem do relatório de orçamentos do Evento em PDF
## Criado: 01/08/2007 - Maycon Edinger
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
$Iv = $_GET["Iv"];
$ImprimeCapa = $_GET["Icapa"];
$TipoCapa = $_GET["Tcapa"];
$ImprimeDatas = $_GET["Id"];
$ImprimeParticipantes = $_GET["Ip"];
$ImprimeEnderecos = $_GET["Ie"];
$ImprimeTerceiros = $_GET["It"];
$ImprimeBrindes = $_GET["Ib"];
$ImprimeFormandos = $_GET["If"];
$ImprimeRepertorio = $_GET["Ir"];

//Recupera dos dados do evento
$sql_evento = "SELECT 
							eve.id,
							eve.nome,
							eve.descricao,
							eve.status,
							eve.cliente_id,
							eve.responsavel_orcamento,
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
							WHERE eve.id = $EventoId";

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
  $this->Cell(0,6,"Orçamento do Evento",0,0,"R");
  $this->SetFont("Arial","",9);
  
  //Imprime o logotipo da empresa
	$this->Image("../image/logo_consoli2.jpg",10,10,40);
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
$pdf->SetTitle("Orçamento do Evento");
$pdf->SetSubject("Relatório gerado automaticamente pelo sistema");
$pdf->AliasNbPages();

//Cria a página do relatório
$pdf->AddPage();

//Verifica se deve imprimir a capa do relatório
if ($ImprimeCapa == 1){
	
	$pdf->SetFont("Times", "", 12);
	
	if ($TipoCapa == 1){
		$pdf->ln(20);
		$pdf->MultiCell(0,7, "Caro Formando:\n\nSua formatura é um momento especial. Por isso, estamos encaminhando este relatório para que a comissão possa escolher dentre as nossas variadas opções, e juntos possamos fazer de sua formatura um sucesso.\n\nCom sede em Rio do Sul, capital do Alto Vale, a Consoli Eventos dispõe de completa infra-estrutura para atender sua formatura. Iniciou suas atividades no ano de 1993, atuando na área de buffet e consultoria em toda a região do Alto Vale, expandiu-se  mais tarde atuando nas mais diversas áreas de eventos em todo o sul do país.\n\nHoje conta com serviços de consultoria, organização, buffet, decoração, foto e filmagem, dispõe de sala de reuniões climatizada, com toda estrutura de áudio e vídeo, show-room, e ainda com pessoal especializado para assessorá-los na confecção e contratação de serviços de convite de formatura, contratação de bandas, locação de espaços físicos, cadastramento de profissionais e fornecedores, dentre outros.
");
	}
	
	if ($TipoCapa == 2){
		$pdf->ln(20);
		$pdf->MultiCell(0,7, "Queridos noivos:\n\nO clima de encantamento continua com uma festa impecável. É um merecimento dos noivos e da família neste momento tão especial.\n\nSeu casamento necessita de um local bem decorado, com serviço requintado, cardápios personalizados, qualidade e confiança, detalhes que fazem a diferença.\n\nCom sede em Rio do Sul, capital do Alto Vale, a Consoli Eventos dispõe de completa infra-estrutura para atender seu casamento, iniciou suas atividades no ano de 1993, atuando na área de buffet e consultoria em toda a região do Alto Vale, expandiu-se mais tarde atuando nas mais diversas áreas de eventos em todo o sul do país.\n\nHoje conta com serviços de consultoria, organização, buffet, decoração, foto e filmagem, carro para noiva, dispõe de sala de reuniões climatizada, com toda estrutura de áudio e vídeo, show-room, e ainda com pessoal especializado para assesora-los na escolha de profissionais que realizarão seus sonhos.
");
	}
	
	//Cria a página do relatório
$pdf->AddPage();
	
	
}

//Nova linha
//$pdf->ln();
$pdf->SetFont("Arial", "B", 12);
$pdf->Cell(0,7, "Evento:",1,0,"L");
$pdf->SetFont("Arial", "I", 12);
$pdf->SetX(30);
$pdf->Cell(0,7, $dados_evento["nome"],0,0,"L");


//Nova linha
$pdf->ln();
$pdf->SetFont("Arial", "B", 10);
$pdf->SetFillColor(178,178,178);
$pdf->Cell(0,6, "Descrição do Evento",1,0,"C",1);
$pdf->SetFont("Arial", "I", 9);
$pdf->ln();
$pdf->MultiCell(0,4, $dados_evento["descricao"],1);

//Nova linha
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(0,16, "Cliente:",1,0,"L");
$pdf->SetFont("Arial", "B", 9);
$pdf->SetX(35);
$pdf->Cell(0,4, $dados_evento["cliente_nome"],0,0,"L");
$pdf->ln();
$pdf->SetFont("Arial", "I", 7);
$pdf->SetX(35);
$pdf->Cell(0,3, $dados_evento["cliente_endereco"] . " - " . $dados_evento["cliente_complemento"],0,0,"L");
$pdf->ln();
$pdf->SetX(35);
$pdf->Cell(0,3, $dados_evento["cliente_bairro"] . " - " . $dados_evento["cliente_cep"] . " - " . $dados_evento["cliente_cidade"] . "/" . $dados_evento["cliente_uf"],0,0,"L");
$pdf->ln();
$pdf->SetX(35);
$pdf->Cell(0,3, "Fone: " . $dados_evento[cliente_telefone] . " - Fax: " . $dados_evento[cliente_fax] . " - Celular: " . $dados_evento[cliente_celular],0,0,"L");
$pdf->ln();
$pdf->SetX(35);
$pdf->Cell(0,3, "email: " . $dados_evento[cliente_email],0,0,"L");

//Nova linha
$pdf->ln();
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(0,6, "Respons. Orçamento:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(45);
$pdf->Cell(0,6, $dados_evento["responsavel_orcamento"],0,0,"L");
$pdf->ln();
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(0,6, "Respons. Evento:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(45);
$pdf->Cell(0,6, $dados_evento["responsavel"],0,0,"L");

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

//Nova linha
$pdf->ln();
$pdf->SetFont("Arial", "B", 10);
$pdf->SetFillColor(178,178,178);
$pdf->Cell(0,6, "Informações Complementares",1,0,"C",1);
$pdf->ln();
$pdf->SetFont("Arial", "I", 10);
$pdf->MultiCell(0,4, $dados_evento["observacoes"],1);

//Verifica se deve imprimir as datas do evento
if ($ImprimeDatas == 1) {

	//*** Exibe as datas do evento
	$pdf->ln();
	$pdf->SetFont("Arial", "B", 10);
	$pdf->SetFillColor(178,178,178);
	$pdf->Cell(0,6, "Datas do Evento",1,0,"C",1);
	$pdf->ln();
	
	//verifica os participantes já cadastrados para este evento e exibe na tela
	$sql_datas = mysql_query("SELECT * FROM eventos_data WHERE evento_id = '$EventoId' ORDER BY data");
	
	//Verifica o numero de registros retornados
	$registros_datas = mysql_num_rows($sql_datas);
	
	//Verifica a quantidade de registros
	if ($registros_datas == 0 ) {
		//Exibe a mensagem que não foram encontrados registros
		$pdf->SetFont("Arial", "B", 9);
		$pdf->Cell(0,6, "Não há datas cadastradas para este evento !",1,0,"L");
	
	//Caso tiver
	} else {
		//Define a cor RGB do fundo da celula
		$pdf->SetFillColor(178,178,178);
		//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
		$pdf->SetFont("Arial", "B", 9);
		$pdf->Cell(0,6, "Data/Hora:",1,0,"L");
		$pdf->SetX(40);
		$pdf->Cell(0,6, "Descrição:");	
		$pdf->SetX(116);
		$pdf->Cell(0,6, "Observações:");	
	
		//Percorre o array 
		while ($dados_datas = mysql_fetch_array($sql_datas)){
	
		  //Imprime os dados dos contatos
		  $pdf->ln();
		  $pdf->SetFont("Arial","B",9);
			$pdf->Cell(6,4, DataMySQLRetornar($dados_datas["data"]) . " - " . substr($dados_datas["hora"], 0, 5));
		  $pdf->SetX(40);
			$pdf->SetFont("Arial","",9);
		  $pdf->Cell(100,4, $dados_datas["descricao"]);		  		  
		  $pdf->SetX(116);
			$pdf->SetFont("Arial","",8);
		  $pdf->Multicell(0,3, $dados_datas["observacoes"]);
	
		//Fecha o while
		}	
	
	//Fecha o If
	}
	
	$pdf->ln();

//Fecha o if de imprimir as datas
}

//Verifica se deve imprimir os participantes do evento
if ($ImprimeParticipantes == 1) {

	//*** Exibe os participantes do evento
	$pdf->ln();
	$pdf->SetFont("Arial", "B", 10);
	$pdf->SetFillColor(178,178,178);
	$pdf->Cell(0,6, "Participantes do Evento",1,0,"C",1);
	$pdf->ln();
	
	//verifica os participantes já cadastrados para este evento e exibe na tela
	$sql_participante = mysql_query("SELECT
															 par.id,
															 par.colaborador_id,
															 par.funcao_id,
															 col.nome as colaborador_nome,
															 col.telefone,
															 col.celular,
															 fun.nome as funcao_nome
															 FROM eventos_participante par
															 INNER JOIN colaboradores col ON col.id = par.colaborador_id
															 LEFT OUTER JOIN funcoes fun ON fun.id = par.funcao_id
															 WHERE par.evento_id = '$EventoId'
															 ORDER by col.nome
															 ");
	
	//Verifica o numero de registros retornados
	$registros_part = mysql_num_rows($sql_participante);
	
	//Verifica a quantidade de registros
	if ($registros_part == 0 ) {
		//Exibe a mensagem que não foram encontrados registros
		$pdf->SetFont("Arial", "B", 9);
		$pdf->Cell(0,6, "Não há participantes cadastrados para este evento !",1,0,"L");
	
	//Caso tiver
	} else {
		//Define a cor RGB do fundo da celula
		$pdf->SetFillColor(178,178,178);
		//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
		$pdf->SetFont("Arial", "B", 9);
		$pdf->Cell(0,6, "Participante/Colaborador:",1,0,"L");
		$pdf->SetX(100);
		$pdf->Cell(0,6, "Função no Evento:");
		$pdf->SetX(154);
		$pdf->Cell(0,6, "Telefone:");
		$pdf->SetX(177);
		$pdf->Cell(0,6, "Celular:");		
	
		//Percorre o array 
		while ($dados_participante = mysql_fetch_array($sql_participante)){
	
		  //Imprime os dados dos contatos
		  $pdf->ln();
		  $pdf->SetFont("Arial","B",9);
			$pdf->Cell(6,4, $dados_participante["colaborador_nome"]);
		  $pdf->SetX(100);
			$pdf->SetFont("Arial","",8);
		  $pdf->Cell(100,4, $dados_participante["funcao_nome"]);
		  $pdf->SetX(154);
			$pdf->SetFont("Arial","",8);
		  $pdf->Cell(70,4, $dados_participante["telefone"]);
		  $pdf->SetX(177);
			$pdf->SetFont("Arial","",8);
		  $pdf->Cell(70,4, $dados_participante["celular"]);		  
	
		//Fecha o while
		}	
	
	//Fecha o If
	}
	
	$pdf->ln();

//Fecha o if de imprimir os participantes
}	

//Verifica se deve imprimir os endereços do evento
if ($ImprimeEnderecos == 1) {
	
	//*** Exibe os endereços da conta
	$pdf->ln();
	$pdf->SetFont("Arial", "B", 10);
	$pdf->SetFillColor(178,178,178);
	$pdf->Cell(0,6, "Endereços do Evento",1,0,"C",1);
	$pdf->ln();
	
	//Efetua a pesquisa dos endereços
	//verifica os endereços já cadastrados para este evento e exibe na tela
	$sql_consulta = mysql_query("SELECT
																	 end.id,
																	 end.local_id,
																	 end.fornecedor_id,
																	 end.nome,
																	 end.endereco,
																	 end.complemento,
																	 end.bairro,
																	 end.cep,
																	 end.uf,
																	 end.hora_inicio,
																	 end.hora_termino,
																	 end.telefone,
																	 end.fax,
																	 end.celular,
																	 end.email,
																	 loc.nome as local_nome,
																	 cid.nome as cidade_nome,
																	 cid2.nome as fornecedor_cidade_nome,
																	 forn.nome as fornecedor_nome,
																	 forn.endereco as fornecedor_endereco,
																	 forn.complemento as fornecedor_complemento,
																	 forn.bairro as fornecedor_bairro,
																	 forn.cep as fornecedor_cep,
																	 forn.uf as fornecedor_uf,
																	 forn.telefone as fornecedor_telefone,
																	 forn.fax as fornecedor_fax,
																	 forn.celular as fornecedor_celular,
																	 forn.email as fornecedor_email							 																	 
																	 FROM eventos_endereco end
																	 INNER JOIN local_evento loc ON loc.id = end.local_id
																	 LEFT OUTER JOIN cidades cid ON cid.id = end.cidade_id
																	 LEFT OUTER JOIN fornecedores forn ON forn.id = end.fornecedor_id
																	 LEFT OUTER JOIN cidades cid2 ON cid2.id = forn.cidade_id														 
																	 WHERE end.evento_id = '$EventoId'
																	 ORDER BY loc.nome
															 ");
	
	//Verifica o numero de registros retornados
	$registros = mysql_num_rows($sql_consulta);
	
	//Verifica a quantidade de registros
	if ($registros == 0 ) {
		//Exibe a mensagem que não foram encontrados registros
		$pdf->SetFont("Arial", "B", 9);
		$pdf->Cell(0,6, "Não há endereços cadastrados para este evento !",1,0,"L");
		$pdf->ln();
		
	//Caso tiver
	} else {
		//Define a cor RGB do fundo da celula
		$pdf->SetFillColor(178,178,178);
		//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
		$pdf->SetFont("Arial", "B", 9);
		$pdf->Cell(0,6, "Local:",1,0,"L");
		$pdf->SetX(75);
		$pdf->Cell(0,6, "Nome/Endereço:");
		$pdf->SetX(172);
		$pdf->Cell(0,6, "Início:");
		$pdf->SetX(184);
		$pdf->Cell(0,6, "Término:");
			
		//Percorre o array 
		while ($dados_endereco = mysql_fetch_array($sql_consulta)){
	
		  //Imprime os dados dos contatos
		  $pdf->ln();
		  $pdf->SetFont("Arial","B",8);
			$pdf->Cell(6,4, $dados_endereco["local_nome"]);
		  $pdf->SetX(75);
			$pdf->SetFont("Arial","B",9);
			
			//Caso seja antigo e não tem fornecedor ID cadastrado
			if ($dados_endereco["fornecedor_id"] == 0){
			
		  	$pdf->Cell(100,4, $dados_endereco["nome"]);
			
			} else {
				
		  	$pdf->Cell(100,4, $dados_endereco["fornecedor_nome"]);
			}

		  $pdf->SetX(172);
		  $pdf->SetFont("Arial","",8);
			$pdf->Cell(48,4, $dados_endereco["hora_inicio"]);
		  $pdf->SetX(184);
		  $pdf->Cell(0,4, $dados_endereco["hora_termino"]);
	
		  $pdf->ln();
			$pdf->SetFont("Arial", "I", 7);
			$pdf->SetX(75);

			//Caso seja antigo e não tem fornecedor ID cadastrado
			if ($dados_endereco["fornecedor_id"] == 0){
			
		  	$pdf->Cell(0,3, $dados_endereco["endereco"] . " - " . $dados_endereco["complemento"],0,0,"L");
			
			} else {
				
		  	$pdf->Cell(0,3, $dados_endereco["fornecedor_endereco"] . " - " . $dados_endereco["fornecedor_complemento"],0,0,"L");
			}

			$pdf->ln();
			$pdf->SetX(75);

			//Caso seja antigo e não tem fornecedor ID cadastrado
			if ($dados_endereco["fornecedor_id"] == 0){
			
		  	$pdf->Cell(0,3, $dados_endereco["bairro"] . " - " . $dados_endereco["cep"] . " - " . $dados_endereco["cidade_nome"] . "/" . $dados_endereco["uf"],0,0,"L");
			
			} else {
				
		  	$pdf->Cell(0,3, $dados_endereco["fornecedor_bairro"] . " - " . $dados_endereco["fornecedor_cep"] . " - " . $dados_endereco["fornecedor_cidade_nome"] . "/" . $dados_endereco["fornecedor_uf"],0,0,"L");
			}

			$pdf->ln();
			$pdf->SetX(75);

			//Caso seja antigo e não tem fornecedor ID cadastrado
			if ($dados_endereco["fornecedor_id"] == 0){
			
		  	$pdf->Cell(0,3, "Fone: " . $dados_endereco[telefone] . " - Fax: " . $dados_endereco[fax] . " - Celular: " . $dados_endereco[celular],0,0,"L");
			
			} else {
				
		  	$pdf->Cell(0,3, "Fone: " . $dados_endereco[fornecedor_telefone] . " - Fax: " . $dados_endereco[fornecedor_fax] . " - Celular: " . $dados_endereco[fornecedor_celular],0,0,"L");
			}

			$pdf->ln();
			$pdf->SetX(75);


			//Caso seja antigo e não tem fornecedor ID cadastrado
			if ($dados_endereco["fornecedor_id"] == 0){
			
		  	$pdf->Cell(0,3, "email: " . $dados_endereco[email],0,0,"L");
			
			} else {
				
		  	$pdf->Cell(0,3, "email: " . $dados_endereco[fornecedor_email],0,0,"L");
			}

			$pdf->ln();
			$pdf->Cell(0,3, "",0,0,"L");
	
		//Fecha o while
		};
	//Fecha o if
	}
//Fecha o if de se deve imprimir os enderecos
}

//Verifica se deve imprimir os terceiros do evento
if ($ImprimeTerceiros == 1) {

	//*** Exibe os terceiros do evento
	$pdf->ln();
	$pdf->SetFont("Arial", "B", 10);
	$pdf->SetFillColor(178,178,178);
	$pdf->Cell(0,6, "Terceiros do Evento",1,0,"C",1);
	$pdf->ln();
	
	//verifica os participantes já cadastrados para este evento e exibe na tela
	$sql_terceiro = mysql_query("SELECT
														 ter.id,
														 ter.fornecedor_id,
														 ter.servico_contratado,
														 ter.custo,
														 ter.observacoes,
														 ter.status_contrato,
														 forn.nome as fornecedor_nome
														 FROM eventos_terceiro ter
														 LEFT OUTER JOIN fornecedores forn ON forn.id = ter.fornecedor_id
														 WHERE ter.evento_id = '$EventoId'
														 ORDER by ter.status_contrato, forn.nome");
	
	//Verifica o numero de registros retornados
	$registros_terc = mysql_num_rows($sql_terceiro);
	
	//Verifica a quantidade de registros
	if ($registros_terc == 0 ) {
		//Exibe a mensagem que não foram encontrados registros
		$pdf->SetFont("Arial", "B", 9);
		$pdf->Cell(0,6, "Não há terceiros cadastrados para este evento !",1,0,"L");
	
	//Caso tiver
	} else {
		//Define a cor RGB do fundo da celula
		$pdf->SetFillColor(178,178,178);
		//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
		$pdf->SetFont("Arial", "B", 9);
		$pdf->Cell(0,6, "Terceiro/Fornecedor:",1,0,"L");
		$pdf->SetX(100);
		$pdf->Cell(0,6, "Serviço Prestado:");
		$pdf->SetX(177);
		$pdf->Cell(0,6, "Valor Venda:");		
	
		$valor_geral = 0;
		
		$pdf->ln(7);
		$pdf->SetFont("Arial","B",11);
		$pdf->Cell(6,4, "A Contratar");
		
		$valor_quebra = 1;
		
	
		//Percorre o array 
		while ($dados_terceiro = mysql_fetch_array($sql_terceiro)){
	
			if($dados_terceiro[status_contrato] != $valor_quebra){
				
				$pdf->ln(7);
				$pdf->SetFont("Arial","B",11);
				$pdf->Cell(6,4, "Contratado");
						
						$valor_quebra = 2;
		  	
	  	}
	
		  //Imprime os dados dos contatos
		  $pdf->ln();
		  $pdf->SetFont("Arial","B",9);
			$pdf->SetX(15);
			$pdf->Cell(6,4, $dados_terceiro["fornecedor_nome"]);
		  $pdf->SetX(100);
			$pdf->SetFont("Arial","",8);
		  $pdf->Cell(100,4, $dados_terceiro["servico_contratado"]);
		  $pdf->SetX(177);
			$pdf->SetFont("Arial","",8);
		  $pdf->Cell(21,4, "R$: " . number_format($dados_terceiro["valor_venda"], 2, ",", "."),"0","0","R");	  

			//Incrmenta o valor do total dos terceiros ao total geral
			$valor_geral = $valor_geral + $dados_terceiro["valor_venda"];
	
		//Fecha o while
		}	

		//Imprime o total de serviços do evento
		$pdf->ln();
		$pdf->SetFont("Arial","BI",11);
		$pdf->Cell(0,6, "Total dos Terceiros do Evento: R$ " . number_format($valor_geral, 2, ",", "."),"1");	
	
	//Fecha o If
	}
	
	$pdf->ln(10);

//Fecha o if de imprimir os terceiros
}

//Verifica se deve imprimir os dados dos itens do evento
if ($ImprimeBrindes == 1) {
	
	//*** Exibe os itens do evento
	$pdf->ln();
	$pdf->SetFont("Arial", "B", 10);
	$pdf->SetFillColor(178,178,178);
	$pdf->Cell(0,6, "Brindes do Evento",1,0,"C",1);
	$pdf->ln();
		
	//Monta a query de filtragem dos brindes
	$filtra_brinde = "SELECT 
														bri.brinde_id,
														bri.quantidade,
														bri.observacoes,
														brinde.nome as brinde_nome
														FROM eventos_brinde bri
														LEFT OUTER JOIN brindes brinde ON brinde.id = bri.brinde_id
														WHERE bri.evento_id = '$EventoId'
														ORDER BY brinde.nome";
		
	//Executa a query
	$lista_brinde = mysql_query($filtra_brinde);
		
	//Cria um contador com o número de contar que a query retornou
	$registros = mysql_num_rows($lista_brinde);
		
	//Verifica a quantidade de registros
	if ($registros == 0 ) {
		//Exibe a mensagem que não foram encontrados registros
		$pdf->SetFont("Arial", "B", 9);
		$pdf->Cell(0,6, "Não há brindes cadastrados para este evento !",1,0,"L");
		$pdf->ln();

	//Caso tiver itens no banco
	} else {

			//Define a cor RGB do fundo da celula
			$pdf->SetFillColor(178,178,178);
			$pdf->SetFont("Arial", "B", 9);
			$pdf->Cell(0,6, "    Quant",1,0,"L");
			$pdf->SetX(28);
			$pdf->Cell(0,6, "Descrição dos Brindes");
			$pdf->SetX(110);
			$pdf->Cell(0,6, "Observações");												
				
				//Percorre o array 
				while ($dados_brinde = mysql_fetch_array($lista_brinde)){									
				
			  	//Imprime os dados dos itens
				  $pdf->ln();
				  $pdf->SetFont("Arial","B",9);
					$pdf->SetX(15);
					$pdf->Cell(13,4, $dados_brinde["quantidade"],"0","0","R");
				  $pdf->SetX(28);
					$pdf->SetFont("Arial","",9);
				  $pdf->Cell(100,4, $dados_brinde["brinde_nome"]);
				  $pdf->SetX(110);
					$pdf->SetFont("Arial","",8);
				  $pdf->MultiCell(0,3, $dados_brinde["observacoes"],0);			
					
				}											
		
		//Fecha o if de se tiver itens
		}				

		$pdf->ln();
//Fecha o if se deve imprimir os brindes do evento
}

//Verifica se deve imprimir o repertório musical do evento
if ($ImprimeRepertorio == 1) {
	
	//*** Exibe o repertório do evento
	$pdf->SetFont("Arial", "B", 10);
	$pdf->SetFillColor(178,178,178);
	$pdf->Cell(0,6, "Repertório Musical do Evento",1,0,"C",1);

	//Monta um sql para pesquisar se há repertório para este evento
	$sql_conta_rep = mysql_query("SELECT id
															 FROM eventos_repertorio
															 WHERE evento_id = '$EventoId'
															 ");

	//Verifica o numero de registros retornados																	 
	$registros = mysql_num_rows($sql_conta_rep);

	//Verifica as categorias cadastradas para o evento
	$sql_conta_categorias = mysql_query("SELECT 
																			rep.id, 
																			cat.id as categoria_id,
																			cat.nome as categoria_nome
																			FROM eventos_repertorio rep
																			INNER JOIN categoria_repertorio cat ON cat.id = rep.categoria_repertorio_id
																			WHERE rep.evento_id = '$EventoId'
																			GROUP BY rep.categoria_repertorio_id");																		
	
	
	//Verifica a quantidade de registros
	if ($registros == 0 ) {
		//Exibe a mensagem que não foram encontrados registros
		$pdf->ln();
		$pdf->SetFont("Arial", "B", 9);
		$pdf->Cell(0,6, "Não há repertório musical cadastrado para este evento !",1,0,"L");
		$pdf->ln();
		
	//Caso tiver
	} else {

		//Define a cor RGB do fundo da celula
		$pdf->SetFillColor(178,178,178);
		//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
		$pdf->ln();
		$pdf->SetFont("Arial", "B", 9);
		$pdf->Cell(0,6, "     Música:",1,0,"L");
		$pdf->SetX(110);
		$pdf->Cell(0,6, "Intérprete:");
		$pdf->ln();
		
		//Cria o array e o percorre para montar a listagem das categorias
    while ($dados_conta_categoria = mysql_fetch_array($sql_conta_categorias)){
    
			//Imprime o nome da categoria

			$pdf->SetFont("Arial","B",10);
			
			//Verifica se a categoria não tem o id 0 (devido as versões antigas do sistema)
			if ($dados_conta_categoria["categoria_id"] == 0) {
				$nome_conta_categoria = "Sem categoria definida";
			} else {
				$nome_conta_categoria = $dados_conta_categoria["categoria_nome"];
			}
			$pdf->Cell(6,4, $nome_conta_categoria);	
			
			//Monta a pesquisa das musicas listadas na categoria
			$sql_musica = mysql_query("SELECT
																	 rep.id,
																	 mus.nome as musica_nome,
																	 mus.interprete as musica_interprete
																	 FROM eventos_repertorio rep
																	 LEFT OUTER JOIN musicas mus ON mus.id = rep.musica_id
																	 WHERE rep.evento_id = '$EventoId' AND rep.categoria_repertorio_id = $dados_conta_categoria[categoria_id]
																	 ");		
			
	
			//Cria o array e o percorre para montar a listagem dinamicamente
	    while ($dados_musica = mysql_fetch_array($sql_musica)){
	    	
    		//Imprime as musicas da categoria
				$pdf->ln();
				$pdf->SetX(14);
				$pdf->SetFont("Arial","",9);
			  $pdf->Cell(70,4, $dados_musica["musica_nome"]);		  
				$pdf->SetX(110);
				$pdf->SetFont("Arial","",8);
			  $pdf->Cell(100,4, $dados_musica["musica_interprete"]);
			
			//Fecha o while das musicas das categorias	    	
	    }

			$pdf->ln(4);
		//Fecha o while das categorias
    }		

		//Salta uma linha pra não grudar na outra parte dos itens
		//$pdf->ln();	

	//Fecha o if
	}
//Fecha o if de se deve imprimir o repertório
}

//Verifica se deve imprimir os formandos do evento
if ($ImprimeFormandos == 1) {

	//*** Exibe os formandos do evento
	$pdf->ln();
	$pdf->SetFont("Arial", "B", 10);
	$pdf->SetFillColor(178,178,178);
	$pdf->Cell(0,6, "Formandos do Evento",1,0,"C",1);
	$pdf->ln();
	
	//verifica os formandos já cadastrados para este evento e exibe na tela
	$sql_formando = mysql_query("SELECT * FROM eventos_formando 
															 WHERE evento_id = '$EventoId'
															 ORDER by nome
															 ");
	
	//Verifica o numero de registros retornados
	$registros_part = mysql_num_rows($sql_formando);
	
	//Verifica a quantidade de registros
	if ($registros_part == 0 ) {
		//Exibe a mensagem que não foram encontrados registros
		$pdf->SetFont("Arial", "B", 9);
		$pdf->Cell(0,6, "Não há formandos cadastrados para este evento !",1,0,"L");
	
	//Caso tiver
	} else {
		//Define a cor RGB do fundo da celula
		$pdf->SetFillColor(178,178,178);
		//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
		$pdf->SetFont("Arial", "B", 9);
		$pdf->Cell(0,6, "Nome do Formando:",1,0,"L");
		$pdf->SetX(74);
		$pdf->Cell(0,6, "Telefone:");
		$pdf->SetX(95);
		$pdf->Cell(0,6, "Email:");
		$pdf->SetX(140);
		$pdf->Cell(0,6, "Observações:");		
	
		$numero_formandos = 0;
		//Percorre o array 
		while ($dados_formando = mysql_fetch_array($sql_formando)){
	
		  //Imprime os dados dos contatos
		  $pdf->ln();
		  $pdf->SetFont("Arial","B",9);
			$pdf->Cell(6,4, $dados_formando["nome"]);
		  $pdf->SetX(74);
			$pdf->SetFont("Arial","",8);
		  $pdf->Cell(100,4, $dados_formando["contato"]);
		  $pdf->SetX(95);
		  $pdf->Cell(100,4, $dados_formando["email"]);
		  $pdf->SetX(140);
		  $pdf->MultiCell(0,3, $dados_formando["observacoes"],0);					  
	
			$numero_formandos++;
		//Fecha o while
		}	

		//Imprime o total de serviços do evento
		$pdf->ln();
		$pdf->SetFont("Arial","BI",9);
		$pdf->Cell(0,6, "Total de Formandos no Evento: " . $numero_formandos,"1");
	
	//Fecha o If
	}
	
	$pdf->ln();

//Fecha o if de imprimir os formandos
}

//*** Exibe os itens do orçamento
$pdf->SetFont("Arial", "B", 10);
$pdf->SetFillColor(178,178,178);
$pdf->Cell(0,6, "Relação de Produtos do Orçamento",1,0,"C",1);
$pdf->ln();

//verifica os itens já cadastrados para este evento
$sql_conta = mysql_query("SELECT
												 evento_id														 
												 FROM eventos_item
												 WHERE evento_id = '$EventoId'
												 ");

//Verifica o numero de registros retornados
$registros_conta = mysql_num_rows($sql_conta);

//Verifica a quantidade de registros
if ($registros_conta == 0 ) {
	//Exibe a mensagem que não foram encontrados registros
	$pdf->SetFont("Arial", "B", 9);
	$pdf->Cell(0,6, "Não há produtos cadastrados para este evento !",1,0,"L");

//Caso tiver
} else {

	//Cria a variável para armazenar o total de colaboradores do evento
	$registros_geral = 0;
	
		//Define a cor RGB do fundo da celula
		$pdf->SetFillColor(178,178,178);
		$pdf->SetFont("Arial", "B", 9);
		$pdf->Cell(0,6, "    Quant",1,0,"L");
		$pdf->SetX(28);
		$pdf->Cell(0,6, "Un");
		$pdf->SetX(34);
		$pdf->Cell(0,6, "Descrição dos Serviços");
		
		//Verifica se deve imprimir os dados financeiros do evento
		if ($Iv == 1) {
			$pdf->SetX(110);
			$pdf->Cell(15,6, "Custo","0","0","R");		
		}
		
		$pdf->SetX(126);
		$pdf->Cell(0,6, "Observações");		

	//Monta a query para capturar as categorias que existem cadastrados itens
	$sql_categoria = mysql_query("SELECT 
											ite.id,
											ite.categoria_id,											
											cat.nome as categoria_nome,
											eve.valor_venda
											FROM item_evento ite
											LEFT OUTER JOIN categoria_item cat ON cat.id = ite.categoria_id
											INNER JOIN eventos_item eve ON eve.item_id = ite.id
											WHERE eve.evento_id = '$EventoId'											
											GROUP BY cat.nome
											ORDER BY cat.nome");

	
	//Percorre o array das funcoes
	while ($dados_categoria = mysql_fetch_array($sql_categoria)){
	
		//Imprime o nome da categoria
		$pdf->ln(8);
		$pdf->SetFont("Arial","B",10);
		
		//Verifica se a categoria não tem o id 0 (devido as versões antigas do sistema)
		if ($dados_categoria["categoria_id"] == 0) {
			$nome_categoria = "Produto sem Centro de Custo definido";
		} else {
			$nome_categoria = $dados_categoria["categoria_nome"];
		}
		$pdf->Cell(6,4, $nome_categoria);

		//Monta a query de filtragem dos itens
		$filtra_item = "SELECT 
											ite.id,
											ite.nome,
											ite.unidade,											
											cat.nome as categoria_nome,
											eve.quantidade,
											eve.valor_venda,
											eve.observacoes
											FROM item_evento ite
											LEFT OUTER JOIN categoria_item cat ON cat.id = ite.categoria_id
											INNER JOIN eventos_item eve ON eve.item_id = ite.id
											WHERE eve.evento_id = '$EventoId'
											AND ite.categoria_id = '$dados_categoria[categoria_id]'
											ORDER BY cat.nome, ite.nome";
		
		//Executa a query
		$lista_item = mysql_query($filtra_item);
		 
		//Cria um contador com o número de contar que a query retornou
		$nro_item = mysql_num_rows($lista_item);	
		
		//Zera a variável de total do custo
		$valor_categoria = 0;
		
		//Percorre o array dos itens
		while ($dados_item = mysql_fetch_array($lista_item)){

			//Define a variável do valor total do item
			$total_item = $dados_item[quantidade] * $dados_item[valor_venda];

	  	//Imprime os dados dos itens
		  $pdf->ln();
		  $pdf->SetFont("Arial","B",9);
			$pdf->SetX(15);
			$pdf->Cell(13,4, $dados_item["quantidade"],"0","0","R");
		  $pdf->SetX(28);
			$pdf->SetFont("Arial","",8);
		  $pdf->Cell(70,4, $dados_item["unidade"]);		  
			$pdf->SetX(34);
			$pdf->SetFont("Arial","",9);
		  $pdf->Cell(100,4, $dados_item["nome"]);
		  
			//Verifica se deve imprimir os dados financeiros do evento
			if ($Iv == 1) {
				$pdf->SetX(110);
				$pdf->SetFont("Arial","",9);
			  $pdf->Cell(16,4, number_format($total_item, 2, ",", "."),"0","0","R");		  
			}
			
		  $pdf->SetX(126);
			$pdf->SetFont("Arial","",8);
		  $pdf->MultiCell(0,3, $dados_item["observacoes"]);

			//Incrementa o contador do valor
			$valor_categoria = $valor_categoria + $total_item;
						
		//Fecha o while dos itens da categoria
		}	

		//Verifica se deve imprimir os dados financeiros do evento
		if ($Iv == 1) {
			//Imprime o total de itens da categoria
			$pdf->ln();
		  $pdf->SetFont("Arial","I",9);
			$pdf->SetX(66);
			$pdf->Cell(60,4, "Subtotal da categoria: R$ " . number_format($valor_categoria, 2, ",", "."),"T","0","R");		
		}
		
		//Incrmenta o valor do total dos itens ao total geral
		$valor_geral = $valor_geral + $valor_categoria;
			
	//Fecha o while da categoria
	}

//Imprime o total de itens do evento
$pdf->ln(8);

//Verifica se deve imprimir os dados financeiros do evento
if ($Iv == 1) {
	$pdf->SetFont("Arial","BI",11);
	$pdf->Cell(0,6, "Total Geral do evento: R$ " . number_format($valor_geral, 2, ",", "."),"1");		
	//Imprime o rodapé do orçamento
	$pdf->ln();
$pdf->ln();
}

$pdf->SetFont("Arial", "B", 10);
$pdf->SetFillColor(178,178,178);
$pdf->Cell(0,6, "ASSINATURA DO CONTRATO                 -                VALIDADE DA PROPOSTA: 30 dias",1,0,"C",1);
$pdf->ln();
$pdf->SetFont("Arial","B",10);
$pdf->MultiCell(0,4, "\n\n   _________________________________________________\n   " . $dados_evento["cliente_nome"],"1");	
$pdf->MultiCell(0,4, "\n\n   _________________________________________________\n   Consoli Eventos","1");

//Fecha o if de se tiver itens
}
//Gera o PDF
$pdf->Output();

?>                                                      