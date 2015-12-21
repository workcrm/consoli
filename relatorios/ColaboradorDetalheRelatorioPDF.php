<?php
###########
## Módulo para montagem do relatório de Detalhamento do Colaborador em PDF
## Criado: 23/04/07 - Maycon Edinger
## Alterado: 05/06/2007 - Maycon Edinger
## Alterações: 
## 20/05/2007 - Incluídos os novos campos
## 28/05/2007 - Implementado o campo ClienteID para a tabela
## 05/06/2007 - Implementado rotinas de segurança para ver os dados
###########

//Acesso as rotinas do PDF
require('../fpdf/fpdf.php');

//Inclui o arquivo de conexão com o servidor
include "../conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "../include/ManipulaDatas.php";

//Recupera os valores para filtragem
$UsuarioId = $_GET["UsuarioId"];
$ColaboradorId = $_GET["ColaboradorId"];
$nivelAcesso = $_GET["Ta"];

//Monta o SQL
$sql_conta = "SELECT 
			con.id,
		  	con.ativo,
		  	con.empresa_id,
		  	con.nome,
		  	con.tipo,
		  	con.endereco,
		  	con.complemento,
		  	con.bairro,
		  	con.cidade_id,
		  	con.uf,
		  	con.cep,
		  	con.rg,
		  	con.titulo_eleitor,
		  	con.ctps,
		  	con.pis,
		  	con.nacionalidade,
		  	con.local_nascimento,
		  	con.data_nascimento,
		  	con.nome_pai,
		  	con.nome_mae,
		  	con.estado_civil,
		  	con.conjuge,
		  	con.cpf,
		  	con.telefone,
		  	con.fax,
		  	con.celular,
		  	con.email,
		  	con.data_admissao,
		  	con.data_desligamento,
		  	con.valor_salario,
		  	con.valor_taxa_normal,
		  	con.valor_taxa_extra,
		  	con.valor_hora,
		  	con.banco_horas,
		  	con.funcao_id,
		  	con.tipo_colaborador_id,
		  	con.chk_dirige,
		  	con.chk_fuma,
		  	con.chk_bebe,
		  	con.chk_brinco,
		  	con.chk_sem_fumar,
		  	con.chk_tirar_brinco,
		  	con.chk_tirar_barba,
			con.chk_tem_filho,
			con.chk_hora_extra,
			con.chk_trabalha_fds,
			con.chk_vale_transporte,		  	
		  	con.foto,
		  	con.contato,
		  	con.dados_complementares,
		  	con.observacoes,
		  	con.cadastro_timestamp,
		  	con.cadastro_operador_id,
		  	con.alteracao_timestamp,
		  	con.alteracao_operador_id,
		  	usu_cad.nome as operador_cadastro_nome, 
		  	usu_cad.sobrenome as operador_cadastro_sobrenome,
		  	usu_alt.nome as operador_alteracao_nome, 
		  	usu_alt.sobrenome as operador_alteracao_sobrenome,
			fun.nome as funcao_nome,
			cid.nome as cidade_nome		  

		  	FROM colaboradores con
		  	INNER JOIN usuarios usu_cad ON con.cadastro_operador_id = usu_cad.usuario_id 
		  	LEFT OUTER JOIN usuarios usu_alt ON con.alteracao_operador_id = usu_alt.usuario_id
			INNER JOIN funcoes fun ON con.funcao_id = fun.id 
		  	LEFT OUTER JOIN cidades cid ON cid.id = con.cidade_id
		  	WHERE con.id = '$ColaboradorId'";

//Executa a query de consulta da cliente
$query_conta = mysql_query($sql_conta);

//Monta a matriz com os dados de cliente
$dados_conta = mysql_fetch_array($query_conta);

//Efetua o switch para o campo de ativo
switch ($dados_conta[ativo]) 
{
	case 0: $desc_ativo = "Cadastro Inativo"; break;
	case 1: $desc_ativo = "Cadastro Ativo"; break;
}    

//Efetua o switch para o campo de tipo
switch ($dados_conta[tipo]) 
{
	case 1: $desc_tipo = "FREELANCE"; break;
	case 2: $desc_tipo = "FUNCIONÁRIO"; break;
}

//Efetua o switch para o campo de banco de horas
switch ($dados_conta[banco_horas]) 
{
	case 1: $desc_banco = "Sim"; break;
	case 0: $desc_banco = "Não"; break;
}	

//Efetua o switch para o campo de dirige
switch ($dados_conta[chk_dirige]) 
{
	case 0: $x_dirige = ""; break;
	case 1: $x_dirige = "X"; break;
}    

//Efetua o switch para o campo de fumar
switch ($dados_conta[chk_fuma]) 
{
	case 0: $x_fuma = ""; break;
	case 1: $x_fuma = "X"; break;
} 

//Efetua o switch para o campo de bebe
switch ($dados_conta[chk_bebe]) 
{
	case 0: $x_bebe = ""; break;
	case 1: $x_bebe = "X"; break;
} 

//Efetua o switch para o campo de brinco
switch ($dados_conta[chk_brinco]) 
{
	case 0: $x_brinco = ""; break;
	case 1: $x_brinco = "X"; break;
} 

//Efetua o switch para o campo de sem fumar
switch ($dados_conta[chk_sem_fumar]) 
{
	case 0: $x_sem_fumar = ""; break;
	case 1: $x_sem_fumar = "X"; break;
} 

//Efetua o switch para o campo de tirar brinco
switch ($dados_conta[chk_tirar_brinco]) 
{
	case 0: $x_tirar_brinco = ""; break;
	case 1: $x_tirar_brinco = "X"; break;
} 

//Efetua o switch para o campo de dirige
switch ($dados_conta[chk_tirar_barba]) 
{
	case 0: $x_tirar_barba = ""; break;
	case 1: $x_tirar_barba = "X"; break;
} 

//Efetua o switch para o campo de filhos
switch ($dados_conta[chk_tem_filho]) 
{
	case 0: $x_filhos = ""; break;
	case 1: $x_filhos = "X"; break;
}

//Efetua o switch para o campo de hora extra
switch ($dados_conta[chk_hora_extra]) 
{
	case 0: $x_hora_extra = ""; break;
	case 1: $x_hora_extra = "X"; break;
}

//Efetua o switch para o campo trablhar fds
switch ($dados_conta[chk_trabalha_fds]) 
{
	case 0: $x_trabalha_fds = ""; break;
	case 1: $x_trabalha_fds = "X"; break;
}

//Efetua o switch para o campo de vale transporte
switch ($dados_conta[chk_vale_transporte]) 
{
	case 0: $x_vale_transporte = ""; break;
	case 1: $x_vale_transporte = "X"; break;
}
  
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
		//Titulo do relatório
		$this->Cell(0,4, $empresaNome);
		$this->Cell(0,4, date("d/m/Y", mktime()),0,0,"R");
		$this->Ln();
		$this->SetFont("Arial","B",15);
		$this->Cell(0,6,"Detalhamento do Colaborador");
		$this->SetFont("Arial","",9);
		$this->Cell(0,4, "Pagina: ".$this->PageNo(),0,0,"R");    
		//Line break
		$this->Ln(10);
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
$pdf = new PDF();
//Define os atributos de propriedade do arquivo PDF
$pdf->SetCreator("mayconedinger@gmail.com");
$pdf->SetAuthor($usuarioNome . " - " . $empresaNome);
$pdf->SetTitle("Detalhamento do colaborador");
$pdf->SetSubject("Relatório gerado automaticamente pelo sistema");
$pdf->AliasNbPages();


$pdf->AddPage();

//Verifica se há uma foto definida para o colaborador
if ($dados_conta["foto"] != "")
{
	
	$caminho_foto = "../imagem_colaborador/$dados_conta[foto]";

	//Imprime a foto do colaborador
	$pdf->Image($caminho_foto,167,30,32,43);
	
}

$pdf->SetY(25);
$pdf->SetFont("Arial", "B", 10);
$pdf->SetFillColor(178,178,178);
$pdf->Cell(0,6, "Detalhamento do Colaborador",1,0,"C",1);

//Nova Linha
$pdf->ln();
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(157,6, "Nome:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(37);
$pdf->Cell(0,6, $dados_conta["nome"],0,0,"L");
$pdf->SetX(167);
$pdf->Cell(0,6, "","LR",0,"L");

//Nova Linha
$pdf->ln();
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(157,6, "Tipo:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(37);
$pdf->Cell(0,6, $desc_tipo,0,0,"L");
$pdf->SetX(167);
$pdf->Cell(0,6, "","LR",0,"L");

//Nova Linha
$pdf->ln();
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(157,6, "Função:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(37);
$pdf->Cell(0,6, $dados_conta["funcao_nome"],0,0,"L");
$pdf->SetX(167);
$pdf->Cell(0,6, "","LR",0,"L");

//Nova Linha
$pdf->ln();
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(157,6, "Endereço:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(37);
$pdf->Cell(0,6, $dados_conta["endereco"],0,0,"L");
$pdf->SetX(167);
$pdf->Cell(0,6, "","LR",0,"L");

//Nova Linha
$pdf->ln();
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(95,6, "Complemento:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(37);
$pdf->Cell(0,6, $dados_conta["complemento"],0,0,"L");
$pdf->SetX(105);
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(62,6, "Bairro:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(120);
$pdf->Cell(0,6, $dados_conta["bairro"],0,0,"L");
$pdf->SetX(167);
$pdf->Cell(0,6, "","LR",0,"L");

$pdf->ln();
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(95,6, "Cidade/UF:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(37);
$pdf->Cell(0,6, $dados_conta["cidade_nome"] . " - " . $dados_conta["uf"],0,0,"L");
$pdf->SetX(105);
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(62,6, "Cep:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(120);
$pdf->Cell(0,6, $dados_conta["cep"],0,0,"L");
$pdf->SetX(167);
$pdf->Cell(0,6, "","LR",0,"L");

//Nova Linha
$pdf->ln();
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(95,6, "Nº RG:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(37);
$pdf->Cell(0,6, $dados_conta["rg"],0,0,"L");
$pdf->SetX(105);
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(62,6, "Cpf:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(120);
$pdf->Cell(0,6, $dados_conta["cpf"],0,0,"L");
$pdf->SetX(167);
$pdf->Cell(0,6, "","LR",0,"L");

//Nova Linha
$pdf->ln();
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(66,6, "Telefone:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(37);
$pdf->Cell(0,6, $dados_conta["telefone"],0,0,"L");
$pdf->SetX(76);
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(59,6, "Fax:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(87);
$pdf->Cell(0,6, $dados_conta["fax"],0,0,"L");
$pdf->SetX(135);
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(65,6, "Celular:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(149);
$pdf->Cell(0,6, $dados_conta["celular"],0,0,"L");

//Nova Linha
$pdf->ln();
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(66,6, "Título Eleit:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(37);
$pdf->Cell(0,6, $dados_conta["titulo_eleitor"],0,0,"L");
$pdf->SetX(76);
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(59,6, "CTPS:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(87);
$pdf->Cell(0,6, $dados_conta["ctps"],0,0,"L");
$pdf->SetX(135);
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(65,6, "Nº PIS:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(149);
$pdf->Cell(0,6, $dados_conta["pis"],0,0,"L");

//Nova Linha
$pdf->ln();
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(66,6, "Nacionalidade:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(37);
$pdf->Cell(0,6, $dados_conta["nacionalidade"],0,0,"L");
$pdf->SetX(76);
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(59,6, "Nasc:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(87);
$pdf->Cell(0,6, DataMySQLRetornar($dados_conta["data_nascimento"]),0,0,"L");
$pdf->SetX(135);
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(65,6, "Local:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(149);
$pdf->Cell(0,6, $dados_conta["local_nascimento"],0,0,"L");

//Nova Linha
$pdf->ln();
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(0,6, "Nome do Pai:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(37);
$pdf->Cell(0,6, $dados_conta["nome_pai"],0,0,"L");

//Nova Linha
$pdf->ln();
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(0,6, "Nome Mãe:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(37);
$pdf->Cell(0,6, $dados_conta["nome_mae"],0,0,"L");

//Nova Linha
$pdf->ln();
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(0,6, "Conjuge:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(37);
$pdf->Cell(0,6, $dados_conta["conjuge"],0,0,"L");

//Nova Linha
$pdf->ln();
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(95,6, "E-mail:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(37);
$pdf->Cell(0,6, $dados_conta["email"],0,0,"L");
$pdf->SetX(105);
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(0,6, "Contato:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(120);
$pdf->Cell(0,6, $dados_conta["contato"],0,0,"L");

//Nova Linha
$pdf->ln();
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(95,6, "Data Admissão:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(37);
$pdf->Cell(0,6, DataMySQLRetornar($dados_conta["data_admissao"]),0,0,"L");
$pdf->SetX(105);
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(0,6, "Data Desligamento:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(137);
$pdf->Cell(0,6, DataMySQLRetornar($dados_conta["data_desligamento"]),0,0,"L");

//Verifica o nível de acesso do usuário
if ($nivelAcesso >= 3) 
{        

	//Nova Linha
	$pdf->ln();
	$pdf->SetFont("Arial", "B", 9);
	$pdf->Cell(95,6, "Salário:",1,0,"L");
	$pdf->SetFont("Arial", "I", 9);
	$pdf->SetX(37);
	$pdf->Cell(0,6, "R$ " . $dados_conta["valor_salario"],0,0,"L");
	$pdf->SetX(105);
	$pdf->SetFont("Arial", "B", 9);
	$pdf->Cell(0,6, "Valor Hora:",1,0,"L");
	$pdf->SetFont("Arial", "I", 9);
	$pdf->SetX(137);
	$pdf->Cell(0,6, "R$ " . $dados_conta["valor_hora"],0,0,"L");
	
	//Nova Linha
	$pdf->ln();
	$pdf->SetFont("Arial", "B", 9);
	$pdf->Cell(95,6, "Vlr Taxa Normal:",1,0,"L");
	$pdf->SetFont("Arial", "I", 9);
	$pdf->SetX(37);
	$pdf->Cell(0,6, "R$ " . $dados_conta["valor_taxa_normal"],0,0,"L");
	$pdf->SetX(105);
	$pdf->SetFont("Arial", "B", 9);
	$pdf->Cell(0,6, "Vlr Taxa Extra:",1,0,"L");
	$pdf->SetFont("Arial", "I", 9);
	$pdf->SetX(137);
	$pdf->Cell(0,6, "R$ " . $dados_conta["valor_taxa_extra"],0,0,"L");
	
	//Nova Linha
	$pdf->ln();
	$pdf->SetFont("Arial", "B", 9);
	$pdf->Cell(0,6, "Banco de Horas:",1,0,"L");
	$pdf->SetFont("Arial", "I", 9);
	$pdf->SetX(37);
	$pdf->Cell(0,6, $desc_banco,0,0,"L");

	//Nova linha
	$pdf->ln();
	$pdf->SetFont("Arial", "B", 10);
	$pdf->SetFillColor(178,178,178);
	$pdf->Cell(0,6, "Informações Complementares",1,0,"C",1);
	
	//Nova linha
	$pdf->ln();
	$pdf->Cell(0,7, "","LR",0,"L");
	$pdf->SetX(14);
	$pdf->Cell(6,5, "",1);
	$pdf->SetX(22);
	$pdf->SetFont("Arial", "B", 9);
	$pdf->Cell(95,6, "Dirige",0,0,"L");
	$pdf->SetX(15);
	$pdf->SetFont("Arial", "B", 10);
	$pdf->Cell(6,6, $x_dirige,0);
	$pdf->SetX(74);
	$pdf->Cell(6,5, "",1);
	$pdf->SetX(82);
	$pdf->SetFont("Arial", "B", 9);
	$pdf->Cell(95,6, "Fica sem Fumar durante o Trabalho",0,0,"L");
	$pdf->SetX(75);
	$pdf->SetFont("Arial", "B", 10);
	$pdf->Cell(6,6, $x_sem_fumar,0);
	
	
	//Nova linha
	$pdf->ln();
	$pdf->Cell(0,7, "","LR",0,"L");
	$pdf->SetX(14);
	$pdf->Cell(6,5, "",1);
	$pdf->SetX(15);
	$pdf->SetFont("Arial", "B", 10);
	$pdf->Cell(6,5, $x_fuma,0);
	$pdf->SetX(22);
	$pdf->SetFont("Arial", "B", 9);
	$pdf->Cell(95,6, "Fuma",0,0,"L");
	$pdf->SetX(74);
	$pdf->Cell(6,5, "",1);
	$pdf->SetX(75);
	$pdf->SetFont("Arial", "B", 10);
	$pdf->Cell(6,5, $x_tirar_brinco,0);
	$pdf->SetX(82);
	$pdf->SetFont("Arial", "B", 9);
	$pdf->Cell(95,6, "Disposto a Tirar o Brinco",0,0,"L");
	
	//Nova linha
	$pdf->ln();
	$pdf->Cell(0,7, "","LR",0,"L");
	$pdf->SetX(14);
	$pdf->Cell(6,5, "",1);
	$pdf->SetX(15);
	$pdf->SetFont("Arial", "B", 10);
	$pdf->Cell(6,5, $x_bebe,0);
	$pdf->SetX(22);
	$pdf->SetFont("Arial", "B", 9);
	$pdf->Cell(95,6, "Bebe",0,0,"L");
	$pdf->SetX(74);
	$pdf->Cell(6,5, "",1);
	$pdf->SetX(75);
	$pdf->SetFont("Arial", "B", 10);
	$pdf->Cell(6,5, $x_tirar_barba,0);
	$pdf->SetX(82);
	$pdf->SetFont("Arial", "B", 9);
	$pdf->Cell(95,6, "Disposto a Tirar a Barba",0,0,"L");
	
	//Nova linha
	$pdf->ln();
	$pdf->Cell(0,7, "","LR",0,"L");
	$pdf->SetX(14);
	$pdf->Cell(6,5, "",1);
	$pdf->SetX(15);
	$pdf->SetFont("Arial", "B", 10);
	$pdf->Cell(6,5, $x_brinco,0);
	$pdf->SetX(22);
	$pdf->SetFont("Arial", "B", 9);
	$pdf->Cell(95,6, "Usa Brinco",0,0,"L");
	$pdf->SetX(74);
	$pdf->Cell(6,5, "",1);
	$pdf->SetX(75);
	$pdf->SetFont("Arial", "B", 10);
	$pdf->Cell(6,5, $x_filhos,0);
	$pdf->SetX(82);
	$pdf->SetFont("Arial", "B", 9);
	$pdf->Cell(95,6, "Possui Filhos",0,0,"L");
	
	//Nova linha
	$pdf->ln();
	$pdf->Cell(0,7, "","LR",0,"L");
	$pdf->SetX(14);
	$pdf->Cell(6,5, "",1);
	$pdf->SetX(15);
	$pdf->SetFont("Arial", "B", 10);
	$pdf->Cell(6,5, $x_hora_extra,0);
	$pdf->SetX(22);
	$pdf->SetFont("Arial", "B", 9);
	$pdf->Cell(95,6, "Pode fazer hora extra",0,0,"L");
	$pdf->SetX(74);
	$pdf->Cell(6,5, "",1);
	$pdf->SetX(75);
	$pdf->SetFont("Arial", "B", 10);
	$pdf->Cell(6,5, $x_trabalha_fds,0);
	$pdf->SetX(82);
	$pdf->SetFont("Arial", "B", 9);
	$pdf->Cell(95,6, "Pode trabalhar nos finais de semana",0,0,"L");
	
	//Nova linha
	$pdf->ln();
	$pdf->Cell(0,7, "","LR",0,"L");
	$pdf->SetX(14);
	$pdf->Cell(6,5, "",1);
	$pdf->SetX(15);
	$pdf->SetFont("Arial", "B", 10);
	$pdf->Cell(6,5, $x_vale_transporte,0);
	$pdf->SetX(22);
	$pdf->SetFont("Arial", "B", 9);
	$pdf->Cell(95,6, "Precisa Vale-Transporte",0,0,"L");
	
	//Nova linha
	$pdf->ln();
	$pdf->SetFont("Arial", "B", 12);
	$pdf->SetFillColor(178,178,178);
	$pdf->Cell(0,6, "Dados Complementares",1,0,"C",1);
	$pdf->ln();
	$pdf->SetFont("Arial", "I", 10);
	$pdf->MultiCell(0,4, $dados_conta["dados_complementares"],1);

//Fecha a rotina de verificação de nivel
}

//Nova linha
$pdf->ln();
$pdf->SetFont("Arial", "B", 12);
$pdf->SetFillColor(178,178,178);
$pdf->Cell(0,6, "Observações",1,0,"C",1);
$pdf->ln();
$pdf->SetFont("Arial", "I", 10);
$pdf->MultiCell(0,4, $dados_conta["observacoes"],1);

//******** VALES DO COLABORADOR ************
//verifica os vales deste colaborador e exibe na tela
$sql_consulta_permissao = mysql_query("SELECT novo_vale FROM usuarios WHERE usuario_id = $UsuarioId");

$dados_usuario = mysql_fetch_array($sql_consulta_permissao);

//verifica se o usuário pode ver este menu
if ($dados_usuario["novo_vale"] == 1)
{

	//Lista os vales fornecidos ao colaborador
	//verifica os vales deste colaborador e exibe na tela
	$sql_consulta = mysql_query("SELECT * FROM vales WHERE colaborador_id = $ColaboradorId ORDER by data");

	//Verifica o numero de registros retornados
	$registros = mysql_num_rows($sql_consulta); 

	$pdf->ln();
	$pdf->SetFont("Arial", "B", 12);
	$pdf->SetFillColor(178,178,178);
	$pdf->Cell(0,6, "Vales Fornecidos ao Colaborador",1,0,"C",1);
	$pdf->ln();

	//Verifica se há vales para o colaborador
	if ($registros == 0)
	{

		$pdf->SetFont("Arial", "BI", 10);
		$pdf->Cell(0,6, "Não há vales fornecidos a este colaborador",1,0,"L");

	} 
	
	else 
	
	{
	  
		$pdf->SetFont("Arial", "B", 10);
		$pdf->SetFillColor(178,178,178);
		$pdf->Cell(24,6, "Data",1,0,"C",1);
		$pdf->Cell(24,6, "Valor",1,0,"C",1);
		$pdf->Cell(112,6, "Observações",1,0,"C",1);
		$pdf->Cell(0,6, "Data Devolução",1,0,"C",1);

		//Cria a variável do total de vales
		$total_vales = 0;

		//Cria o array e o percorre para montar a listagem dinamicamente
		while ($dados_consulta = mysql_fetch_array($sql_consulta))
		{
		
			$pdf->ln();
			$pdf->SetFont("Arial", "B", 10);
			$pdf->Cell(24,5, DataMySQLRetornar($dados_consulta["data"]),1,0,"C");
			$pdf->Cell(24,5, "R$ " . number_format($dados_consulta["valor"], 2, ",", "."),1,0,"R");
			$pdf->Cell(112,5, $dados_consulta["observacoes"],1,0);
			$pdf->Cell(0,5, DataMySQLRetornar($dados_consulta["data_devolucao"]),1,0,"C");
			
			$total_vales = $total_vales + $dados_consulta[valor];
	  
		}
	  
		$pdf->ln();
		$pdf->SetFont("Arial", "BI", 10);
		$pdf->SetFillColor(178,178,178);
		$pdf->Cell(0,7, "Valor total de vales: R$ " . number_format($total_vales, 2, ",", "."),1,0);
	  
	}

}

//Gera o PDF
$pdf->Output();

?>                                                      