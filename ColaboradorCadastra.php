<?php 
###########
## Módulo para cadastro de colaboradores
## Criado: 16/04/2007 - Maycon Edinger
## Alterado: 19/06/2007 - Maycon Edinger
## Alterações: Fazer a inserção da imagem do funcionario
## 23/04/2007 - Acrescentado campo para valor_taxa_extra e renomeado campo valor_taxa
## 10/05/2007 - Removido a validação do campo email do colaborador
## 20/05/2007 - Inserido novos campos ao cadastro
## 28/05/2007 - Implementado o campo ClienteID para a tabela
##							Implementado as rotinas para vincular o colaborador a um usuário
## 05/06/2007 - Implementado que usuarios nivel 2 não cadastram valores de salarios
## 19/06/2007 - Aplicado objeto para campo money nos campos de valor
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) 
{
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas

// Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Inclui o arquivo para manipulação de valor monetário
include "./include/ManipulaMoney.php";
?>

<script language="JavaScript">
//Função que alterna a visibilidade do painel especificado.
function oculta(id){
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function valida_form() {
   var Form;
   Form = document.cadastro;
   if (Form.edtNome.value.length == 0) {
      alert("É necessário Informar o Nome do Colaborador !");
      Form.edtNome.focus();
      return false;
   }
	 if (Form.cmbFuncaoId.value == 0) {
      alert("É necessário selecionar uma Função para o Colaborador !");
      Form.cmbFuncaoId.focus();
      return false;
   }
	 //if (Form.cmbTipoColaboradorId.value == 0) {
      //alert("É necessário selecionar um Tipo para o Colaborador !");
      //Form.cmbTipoColaboradorId.focus();
      //return false;
   //}
	 if (Form.edtDataNascimento.value.length == 0) {
      alert("É necessário Informar a Data de Nascimento do Colaborador !");
      Form.edtDataNascimento.focus();
      return false;
   }    
   return true;
}

</script>
<link rel="stylesheet" type="text/css" href="include/workStyle.css">
</head>
<body>

<?php 
  //Monta o lookup aa tabela de funcoes
  //Monta o SQL
  $lista_funcao = "SELECT * FROM funcoes WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
	//Executa a query
  $dados_funcao = mysql_query($lista_funcao);
  
  //Efetua o lookup na tabela de tipos de colaborador
  //Monta o SQL  
	//$lista_tipo = "SELECT * FROM tipo_colaborador WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
	//Executa a query
  //$dados_tipo = mysql_query($lista_tipo);
  
	//Monta o lookup da tabela de cidades
	//Monta o SQL
	$lista_cidade = "SELECT * FROM cidades WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
	//Executa a query
	$dados_cidade = mysql_query($lista_cidade);  
	
	//Monta o lookup da tabela de usuários
	//Monta o SQL
	$lista_usuario = "SELECT * FROM usuarios WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
	//Executa a query
	$dados_usuario = mysql_query($lista_usuario);	

  //Adiciona o acesso a entidade de criação do componente data
  include("CalendarioPopUp.php");
  //Cria um objeto do componente data
  $objData = new tipData();
  //Define que não deve exibir a hora no calendario
  $objData->bolExibirHora = false;
  //Monta javaScript do calendario uma unica vez para todos os campos do tipo data
  $objData->MontarJavaScript(); 
?>

<form id="form" name="cadastro" action="sistema.php?ModuloNome=ColaboradorCadastra" enctype="multipart/form-data" method="post" onsubmit="return valida_form()">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
  <tr>
    <td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Cadastramento de Colaboradores</span></td>
			  </tr>
			  <tr>
			    <td colspan="5">
				    <img src="image/bt_espacohoriz.gif" width="100%" height="12">
					</td>
			  </tr>
			</table>

      <table id="2" width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="100%" class="text">

          <?php
					
          	//Caso tiver um nome de arquivo, armazena numa variável
          	$arq = $_FILES['foto']['name'];
          	
          	$Processa = 1;

          	//Gera um nome para a imagem e verifica se já não existe, caso exista, gera outro nome e assim sucessivamente..
						//Função Recursiva
						function nome($extensao){
							    
							global $config;
							
							// Gera um nome único para a imagem
							$temp = substr(md5(uniqid(time())), 0, 10);
							$imagem_nome = $temp . "." . $extensao;
							    
							// Verifica se o arquivo já existe, caso positivo, chama essa função novamente
							if(file_exists($config["diretorio"] . $imagem_nome)){
							    
							  	$imagem_nome = nome($extensao);
							  
							}
							    
							  return $imagem_nome;
							
							}
          	
          	
						//Verifica se for informado algum arquivo
          	if ($arq != ""){          		          	          	
          	          
	          	//ENVIO DA IMAGEM PARA A PASTA DO SERVIDOR
	          	//Prepara a variável caso o formulário tenha sido postado
							$arquivo = isset($_FILES["foto"]) ? $_FILES["foto"] : FALSE;
							
							$config = array();
							//Tamano máximo da imagem, em bytes
							$config["tamanho"] = 3000000; //3Megas																
							//Largura Máxima, em pixels
							$config["largura"] = 2000;
							//Altura Máxima, em pixels
							$config["altura"] = 3000;
							//Diretório onde a imagem será salva
							$config["diretorio"] = "imagem_colaborador/";
							

						
							if($arquivo){					   
							
								$erro = array();
						    
						    // Verifica o mime-type do arquivo para ver se é de imagem.
						    // Caso fosse verificar a extensão do nome de arquivo, o código deveria ser:
						    //
						    // if(!eregi("\.(jpg|jpeg|bmp|gif|png){1}$", $arquivo["name"])) {
						    //      $erro[] = "Arquivo em formato inválido! A imagem deve ser jpg, jpeg, bmp, gif ou png. Envie outro arquivo"; }
						    //
						    // Mas, o que ocorre é que alguns usuários mal-intencionados, podem pegar um vírus .exe e simplesmente mudar a extensão
						    // para alguma das imagens e enviar. Então, não adiantaria em nada verificar a extensão do nome do arquivo.
						    if(!eregi("^image\/(pjpeg|jpeg|png|gif|bmp)$", $arquivo["type"])){
						      
						    	$erro[] = "Arquivo em formato inválido! A imagem deve ser jpg, jpeg, bmp, gif ou png. Envie outro arquivo";
						    
						    } else {

						    	// Verifica tamanho do arquivo
						      if($arquivo["size"] > $config["tamanho"]){
						        
						      	$erro[] = "Arquivo em tamanho muito grande! A imagem deve ser de no máximo " . $config["tamanho"] . " bytes. Envie outro arquivo";
						      
						      }
						        
						      // Para verificar as dimensões da imagem
						      $tamanhos = getimagesize($arquivo["tmp_name"]);
						        
						      // Verifica largura
						      if($tamanhos[0] > $config["largura"]){
						      	
						      	$erro[] = "Largura da imagem não deve ultrapassar " . $config["largura"] . " pixels";
						      
						      }
						
						      // Verifica altura
						      if($tamanhos[1] > $config["altura"]) {
						        
						      	$erro[] = "Altura da imagem não deve ultrapassar " . $config["altura"] . " pixels";
						      
						      }
						    
						    }
						
						    if(!sizeof($erro)){
						      
						    	//Pega extensão do arquivo, o indice 1 do array conterá a extensão
						      preg_match("/\.(gif|bmp|png|jpg|jpeg){1}$/i", $arquivo["name"], $ext);
						        
						      //Gera nome único para a imagem
						      $imagem_nome = nome($ext[1]);
						
						      //Caminho de onde a imagem ficará
						      $imagem_dir = $config["diretorio"] . $imagem_nome;
						
						      //Faz o upload da imagem
						      move_uploaded_file($arquivo["tmp_name"], $imagem_dir);
						        
						      $Processa = 1;
						    
						    }
							
							}    
	
	          	if(sizeof($erro)){    				

	          		echo "<table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'>
	          						<tr>
	          							<td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'>
	          								<img src='./image/bt_informacao.gif' border='0' />
	          							</td>
	          							<td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'>
	          								<b>ATENÇÃO - Erro ao carregar a foto do colaborador:</b><br/>";
	          		
	          								foreach($erro as $err){
	
		          								echo $err . "<br/>";
	          		
	  						        		}
	  						        		
	  						       echo "<span style='color: #990000'><b>O colaborador NÃO foi salvo !</b></span><br/>
	  						       			</td>
	          								</tr>
	          							</table></td></tr><tr><td><br/>";	          			          		
	          		
	          		$Processa = 0;
							
	          	}
	          	
          	}
	          	          											
					//Recupera os valores vindos do formulário e armazena nas variaveis
          if($_POST["Submit"] AND $Processa == 1){

					$chkAtivo = $_POST["chkAtivo"];
          $edtEmpresaId = $empresaId;
					$edtNome = $_POST["edtNome"];
					$edtTipo = $_POST["edtTipo"];
          $edtEndereco = $_POST["edtEndereco"];
          $edtComplemento = $_POST["edtComplemento"];
          $edtBairro = $_POST["edtBairro"];
          $cmbCidadeId = $_POST["cmbCidadeId"];
          $edtUf = $_POST["edtUf"];
          $edtCep = $_POST["edtCep"];
          $edtRg = $_POST["edtRg"];
					$edtTitulo = $_POST["edtTitulo"];
					$edtCtps = $_POST["edtCtps"];
					$edtPis = $_POST["edtPis"];
					$edtNacionalidade = $_POST["edtNacionalidade"];
					$edtLocalNascimento = $_POST["edtLocalNascimento"];
					$edtDataNascimento = DataMySQLInserir($_POST["edtDataNascimento"]);
					$edtNomePai = $_POST["edtNomePai"];
					$edtNomeMae = $_POST["edtNomeMae"];
					$edtEstadoCivil = $_POST["edtEstadoCivil"];
					$edtConjuge = $_POST["edtConjuge"];
					$edtCpf = $_POST["edtCpf"];				
          $edtTelefone = $_POST["edtTelefone"];
          $edtFax = $_POST["edtFax"];
          $edtCelular = $_POST["edtCelular"];
          $edtEmail = $_POST["edtEmail"];
          $edtDataAdmissao = DataMySQLInserir($_POST["edtDataAdmissao"]);
          $edtDataDesligamento = DataMySQLInserir($_POST["edtDataDesligamento"]);
          $edtValorSalario = MoneyMySQLInserir($_POST["edtValorSalario"]);
          $edtValorTaxaNormal = MoneyMySQLInserir($_POST["edtValorTaxaNormal"]);
          $edtValorTaxaExtra = MoneyMySQLInserir($_POST["edtValorTaxaExtra"]);
          $edtValorHora = MoneyMySQLInserir($_POST["edtValorHora"]);
          $edtBancoHoras = $_POST["edtBancoHoras"];
          $cmbFuncaoId = $_POST["cmbFuncaoId"];
          //$cmbTipoColaboradorId = $_POST["cmbTipoColaboradorId"];
          $chkDirige = $_POST["chkDirige"];
          $chkFuma = $_POST["chkFuma"];
          $chkBebe = $_POST["chkBebe"];
          $chkBrinco = $_POST["chkBrinco"];
          $chkSemFumar = $_POST["chkSemFumar"];
          $chkTirarBrinco = $_POST["chkTirarBrinco"];
          $chkTirarBarba = $_POST["chkTirarBarba"];
          $chkTemFilho = $_POST["chkTemFilho"];
          $chkHoraExtra = $_POST["chkHoraExtra"];
          $chkTrabalharFds = $_POST["chkTrabalharFds"];
          $chkValeTransporte = $_POST["chkValeTransporte"];          
          $edtContato = $_POST["edtContato"];
          //Pega somente o nome do arquivo
          $edtFoto = $imagem_nome;
					$edtOperadorId = $usuarioId;
					$edtDadosComplementares = $_POST["edtDadosComplementares"];
          $edtObservacoes = $_POST["edtObservacoes"];

					//Monta o sql e executa a query de inserção dos clientes
    	    $sql = mysql_query("
                INSERT INTO colaboradores (
								ativo,
								empresa_id, 
								nome,
								tipo, 
								endereco, 
								complemento,
								bairro, 
								cidade_id, 
								uf, 
								cep,
								rg,
								titulo_eleitor,
								ctps,
								pis,
								nacionalidade,
								local_nascimento,
								data_nascimento,
								nome_pai,
								nome_mae,
								estado_civil,
								conjuge,
								cpf, 								
								telefone, 
								fax, 
								celular, 
								email,
								data_admissao,
								data_desligamento,
								valor_salario,
								valor_taxa_normal,
								valor_taxa_extra,
								valor_hora,
								banco_horas,
								funcao_id,
								chk_dirige,
								chk_fuma,
								chk_bebe,
								chk_brinco,
								chk_sem_fumar,
								chk_tirar_brinco,
								chk_tirar_barba,								
								chk_tem_filho,
								chk_hora_extra,
								chk_trabalha_fds,
								chk_vale_transporte,								
								foto,
								contato,
								dados_complementares,
								observacoes,
								cadastro_timestamp,
								cadastro_operador_id
				
								) VALUES (
				
								'$chkAtivo',
								'$edtEmpresaId',
								'$edtNome',
								'$edtTipo',
								'$edtEndereco',
								'$edtComplemento',
								'$edtBairro',
								'$cmbCidadeId',
								'$edtUf',
								'$edtCep',
								'$edtRg',
								'$edtTitulo',
								'$edtCtps',
								'$edtPis',
								'$edtNacionalidade',
								'$edtLocalNascimento',
								'$edtDataNascimento',
								'$edtNomePai',
								'$edtNomeMae',
								'$edtEstadoCivil',
								'$edtConjuge',
								'$edtCpf',
								'$edtTelefone',
								'$edtFax',
								'$edtCelular',
								'$edtEmail',
								'$edtDataAdmissao',
								'$edtDataDesligamento',
								'$edtValorSalario',
								'$edtValorTaxaNormal',
								'$edtValorTaxaExtra',
								'$edtValorHora',
								'$edtBancoHoras',								
								'$cmbFuncaoId',
								'$chkDirige',
								'$chkFuma',
								'$chkBebe',
								'$chkBrinco',
								'$chkSemFumar',
								'$chkTirarBrinco',
								'$chkTirarBarba',
			          '$chkTemFilho',
			          '$chkHoraExtra',
			          '$chkTrabalharFds',
			          '$chkValeTransporte',															
								'$edtFoto',
								'$edtContato',
								'$edtDadosComplementares',
								'$edtObservacoes',
								now(),
								'$edtOperadorId'				
								);");
	
					//Exibe a mensagem de inclusão com sucesso
        	echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Colaborador cadastrado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
        }
        ?>

        <table cellspacing="0" cellpadding="0" width="520" border="0">
          <tr>
	        	<td style="PADDING-BOTTOM: 2px">
	        		<input name="Submit" type="submit" class="button" id="Submit" title="Salva o registro atual" value="Salvar Colaborador" />
            	<input class="button" title="Limpa o conteúdo dos campos digitados" name="Reset" type="reset" id="Reset" value="Limpar Campos" />
            </td>
	       	</tr>
         </table>
           
         <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
           <tr>
             <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="21">
               <table cellspacing="0" cellpadding="0" width="100%" border="0">
                 <tr>
                   <td class="tabDetailViewDL" style="TEXT-ALIGN: left">
									 		<img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados do colaborador e clique em [Salvar Colaborador]									 
									 </td>
			     			 </tr>
		       	 		</table>					 
							</td>
	         </tr>
           <tr>
             <td width="140" class="dataLabel">
               <span class="dataLabel">Nome:</span>             
						 </td>
             <td colspan="4" class="tabDetailViewDF">
               <table width="100%" cellpadding="0" cellspacing="0">
                 <tr valign="middle">
                   <td width="462" height="20">
                     <input name="edtNome" type="text" class="requerido" id="edtNome" style="width: 370;color: #6666CC; font-weight: bold" size="84" maxlength="50">										 
									 </td>
                   <td width="119">
                     <div align="right">Cadastro Ativo
                       <input name="chkAtivo" type="checkbox" id="chkAtivo" value="1" checked>
                     </div>										 
									 </td>
                 </tr>
               </table>							
						</td>
           </tr>
           <tr>
            <td valign="top" class="dataLabel">Tipo de Colaborador:</td>
            <td colspan="4" class="tabDetailViewDF">
							<table width="100%" cellpadding="0" cellspacing="0">
	              <tr valign="middle">
	                <td width="117" height="20">
	                  <input name="edtTipo" type="radio" value="1" checked="checked" />
	                  Freelance 
	                </td>
	                <td width="117" height="20">
	                  <input name="edtTipo" type="radio" value="2" />
	                  Funcionário
	                </td>
                  <td height="20">
	                  <input name="edtTipo" type="radio" value="3" />
	                  Ex-Funcionário
	                </td>
	              </tr>
	            </table>
						</td>
          </tr>
           <tr>
             <td width="140" class="dataLabel">Fun&ccedil;&atilde;o:</td>
             <td colspan="3" valign="middle" class="tabDetailViewDF">
               <select name="cmbFuncaoId" id="cmbFuncaoId" style="width:350px" class="requerido">
                 <option value="0">Selecione uma Opção</option>
				 				 <?php 
									 //Monta o while para gerar o combo de escolha de funcao
									 while ($lookup_funcao = mysql_fetch_object($dados_funcao)) { 
								 ?>
                 <option value="<?php echo $lookup_funcao->id ?>"><?php echo $lookup_funcao->nome ?> </option>
                 <?php } ?>
               </select>						 
						</td>
             <td width="160" rowspan="7" align="center" valign="middle" class="tabDetailViewDF" style="border-left:1px solid; border-color:#dfdfdf; padding: 0px;">
								<div id="foto">								</div>						 
						 </td>
           </tr>
           <?php
           /* DESBILITADO O TIPO DE COLBORADOR
					 <tr>
             <td width="140" class="dataLabel" nowrap="nowrap">Tipo de Colaborador: </td>
             <td colspan="3" valign="middle" class="tabDetailViewDF">
               <select name="cmbTipoColaboradorId" id="cmbTipoColaboradorId" style="width:350px">
                 <option value="0">Selecione uma Opção</option>
				 				 <?php 
									 //Monta o while para gerar o combo de escolha de funcao
									 while ($lookup_tipo = mysql_fetch_object($dados_tipo)) { 
								 ?>
                 <option value="<?php echo $lookup_tipo->id ?>"><?php echo $lookup_tipo->nome ?> </option>
                 <?php } ?>
               </select>						 
						 </td>
           </tr>
           */
           ?>
           <tr>
             <td width="140" class="dataLabel">Endere&ccedil;o:</td>
             <td colspan="3" valign="middle" class="tabDetailViewDF">
               <input name="edtEndereco" type="text" class="datafield" id="edtEndereco" style="width: 300" size="84" maxlength="80" />						 
						 </td>
          </tr>
          <tr>
            <td width="140" class="dataLabel">
							<span class="dataLabel">Complemento:</span>						
						</td>
            <td colspan="3" class="tabDetailViewDF">
              <input name="edtComplemento" type="text" class="datafield" id="edtComplemento" style="width: 300" size="84" maxlength="50" />						
						</td>
          </tr>
          <tr>
            <td width="140" class="dataLabel">Bairro:</td>
            <td colspan="3" class="tabDetailViewDF">
              <input name="edtBairro" type="text" class="datafield" id="edtBairro" style="width: 300" size="52" maxlength="50">            
						</td>
          </tr>
          <tr>
            <td width="140" class="dataLabel">Cidade:</td>
            <td colspan="3" class="tabDetailViewDF">
               <select name="cmbCidadeId" id="cmbCidadeId" style="width:350px">
                 <option value="0">Selecione uma Opção</option>
				 				 <?php 
									 //Monta o while para gerar o combo de escolha
									 while ($lookup_cidade = mysql_fetch_object($dados_cidade)) { 
								 ?>
                 <option value="<?php echo $lookup_cidade->id ?>"><?php echo $lookup_cidade->nome ?> </option>
                 <?php } ?>
               </select>						
						</td>
          </tr>
          <tr>
            <td width="140" class="dataLabel">UF:</td>
            <td width="173" class="tabDetailViewDF">
							<select class="datafield"name="edtUf" id="edtUf">
				        <option value="AC">AC</option>
				        <option value="AL">AL</option>
				        <option value="AM">AM</option>
				        <option value="BA">BA</option>
				        <option value="CE">CE</option>
				        <option value="DF">DF</option>
				        <option value="ES">ES</option>
				        <option value="GO">GO</option>
				        <option value="MA">MA</option>
				        <option value="MG">MG</option>
				        <option value="MS">MS</option>
				        <option value="MT">MT</option>
				        <option value="PA">PA</option>
				        <option value="PB">PB</option>
				        <option value="PE">PE</option>
				        <option value="PI">PI</option>
				        <option value="PR">PR</option>
				        <option value="RJ">RJ</option>
				        <option value="RN">RN</option>
				        <option value="RO">RO</option>
				        <option value="RR">RR</option>
				        <option value="RS">RS</option>
			    	    <option value="SC">SC</option>
				        <option value="SE">SE</option>
				        <option value="SP">SP</option>
				        <option value="TO">TO</option>
				      </select>						
						</td>
            <td width="146" class="dataLabel">Cep:</td>
            <td width="129" valign="top" class="tabDetailViewDF">
							<input name="edtCep" type="text" class="datafield" id="edtCep" size="11" maxlength="9" onkeypress="return FormataCampo(document.cadastro, 'edtCep', '99999-999', event);">						</TD>
		   		</tr>
           
          <tr>
            <td width="140" valign="top" class="dataLabel">N&ordm; RG: </td>
            <td width="173" class="tabDetailViewDF">
              <input name="edtRg" type="text" class="datafield" id="edtRg" size="20" maxlength="18">            </td>
            <td width="146" class="dataLabel">CPF:</TD>
            <td colspan="2" class="tabDetailViewDF">
              <input name="edtCpf" type="text" class="datafield" id="edtCpf" size="17" maxlength="14" onkeypress="return FormataCampo(document.cadastro, 'edtCpf', '999.999.999-99', event);">            </TD>
            </tr>
          <tr>
            <td width="140" valign="top" class="dataLabel">T&iacute;tulo de Eleitor: </td>
            <td width="173" class="tabDetailViewDF"><input name="edtTitulo" type="text" class="datafield" id="edtTitulo" size="15" maxlength="11"></td>
            <td width="146" class="dataLabel">N&ordm; CTPS: </td>
            <td colspan="2" class="tabDetailViewDF"><input name="edtCtps" type="text" class="datafield" id="edtCtps" size="15" maxlength="10"></td>
          </tr>
          <tr>
            <td width="140" valign="top" class="dataLabel">N&ordm; PIS: </td>
            <td width="173" class="tabDetailViewDF"><input name="edtPis" type="text" class="datafield" id="edtPis" size="20" maxlength="14"></td>
            <td width="146" class="dataLabel">Nacionalidade:</td>
            <td colspan="2" class="tabDetailViewDF">
							<input name="edtNacionalidade" type="text" class="datafield" id="edtNacionalidade" style="width: 260" size="52" maxlength="60">						</TD>
          </tr>
          <tr>
            <td width="140" valign="top" class="dataLabel">Data Nascimento:</td>
            <td width="173" class="tabDetailViewDF">
							<?php
							    //Define a data do formulário
							    $objData->strFormulario = "cadastro";  
							    //Nome do campo que deve ser criado
							    $objData->strNome = "edtDataNascimento";
							    //Informa se deve ser requerido
							    $objData->strRequerido = true;
							    //Valor a constar dentro do campo (p/ alteração)
							    //$objData->strValor = Date('d/m/Y', mktime());
							    //Define o tamanho do campo 
							    //$objData->intTamanho = 15;
							    //Define o número maximo de caracteres
							    //$objData->intMaximoCaracter = 20;
							    //define o tamanho da tela do calendario
							    //$objData->intTamanhoCalendario = 200;
							    //Cria o componente com seu calendario para escolha da data
							    $objData->CriarData();
							?>						
						</td>
            <td width="146" class="dataLabel">Local Nascimento: </td>
            <td colspan="2" class="tabDetailViewDF">
							<input name="edtLocalNascimento" type="text" class="datafield" id="edtLocalNascimento" style="width: 260" size="52" maxlength="60">						
						</td>
          </tr>
          <tr>
            <td width="140" valign="top" class="dataLabel">Nome do Pai: </td>
            <td colspan="4" class="tabDetailViewDF"><input name="edtNomePai" type="text" class="datafield" id="edtNomePai" style="width: 300" size="52" maxlength="50"/></td>
            </tr>
          <tr>
            <td width="140" valign="top" class="dataLabel">Nome da M&atilde;e: </td>
            <td colspan="4" class="tabDetailViewDF"><input name="edtNomeMae" type="text" class="datafield" id="edtNomeMae" style="width: 300" size="52" maxlength="50"></td>
            </tr>
          <tr>
            <td width="140" valign="top" class="dataLabel">Estado Civil: </td>
            <td width="173" class="tabDetailViewDF">
              <select name="edtEstadoCivil" size="1" id="edtEstadoCivil">
                <option value="0">Selecione</option>
                <option value="Solteiro">Solteiro</option>
                <option value="Casado">Casado</option>
                <option value="Amaziado">Amaziado</option>
                <option value="Divorciado">Divorciado</option>
                <option value="Vi&uacute;vo">Vi&uacute;vo</option>
              </select>
						</td>
            <TD width="146" class=dataLabel>C&ocirc;njuge:</TD>
            <TD colspan="2" class=tabDetailViewDF><input name="edtConjuge" type="text" class="datafield" id="edtConjuge" style="width: 260" size="52" maxlength="50"></TD>
          </TR>
          <TR>
            <TD width="140" valign="top" class=dataLabel>Telefone:</TD>
            <TD width="173" class=tabDetailViewDF>
              <input name="edtTelefone" type="text" class="datafield" id="edtTelefone" size="16" maxlength="14" onKeyPress="return FormataCampo(document.cadastro, 'edtTelefone', '(99) 9999-9999', event);">						</TD>
            <TD width="146" class=dataLabel>Fax:</TD>
            <TD colspan="2" class=tabDetailViewDF>
              <input name="edtFax" type="text" class="datafield" id="edtFax" size="16" maxlength="14" onKeyPress="return FormataCampo(document.cadastro, 'edtFax', '(99) 9999-9999', event);">						</TD>
          </TR>
          <TR>
            <TD width="140" valign="top" class=dataLabel>Celular:</TD>
            <TD colspan="4" class=tabDetailViewDF>
              <input name="edtCelular" type="text" class="datafield" id="edtCelular" size="16" maxlength="14" onKeyPress="return FormataCampo(document.cadastro, 'edtCelular', '(99) 9999-9999', event);">						</TD>
          </TR>
          <TR>
            <TD width="140" valign="top" class=dataLabel>E-mail: </TD>
            <TD colspan="4" class=tabDetailViewDF>
              <input name="edtEmail" type="text" class="datafield" id="edtEmail" style="width: 300; text-transform:lowercase" size="52" maxlength="50">						</TD>
          </TR>
          <tr>
            <td width="140" valign="top" class="dataLabel">Data Admiss&atilde;o: </td>
            <td width="173" class="tabDetailViewDF">
							<?php
							    //Define a data do formulário
							    $objData->strFormulario = "cadastro";  
							    //Nome do campo que deve ser criado
							    $objData->strNome = "edtDataAdmissao";
							    $objData->strRequerido = false;
							    //Valor a constar dentro do campo (p/ alteração)
							    //$objData->strValor = Date('d/m/Y', mktime());
							    //Define o tamanho do campo 
							    //$objData->intTamanho = 15;
							    //Define o número maximo de caracteres
							    //$objData->intMaximoCaracter = 20;
							    //define o tamanho da tela do calendario
							    //$objData->intTamanhoCalendario = 200;
							    //Cria o componente com seu calendario para escolha da data
							    $objData->CriarData();
							?>						
						</td>
            <td width="146" class="dataLabel">Data Desligamento: </td>
            <td colspan="2" class="tabDetailViewDF">
							<?php
							    //Define a data do formulário
							    $objData->strFormulario = "cadastro";  
							    //Nome do campo que deve ser criado
							    $objData->strNome = "edtDataDesligamento";
							    $objData->strRequerido = false;
							    //Valor a constar dentro do campo (p/ alteração)
							    //$objData->strValor = Date('d/m/Y', mktime());
							    //Define o tamanho do campo 
							    //$objData->intTamanho = 15;
							    //Define o número maximo de caracteres
							    //$objData->intMaximoCaracter = 20;
							    //define o tamanho da tela do calendario
							    //$objData->intTamanhoCalendario = 200;
							    //Cria o componente com seu calendario para escolha da data
							    $objData->CriarData();
							?>						
						</td>
          </tr>
					<?php 
						//Verifica o nível de acesso do usuário
					  if ($nivelAcesso >= 3) {        
					?>          
					<tr>
            <td width="140" valign="top" class="dataLabel">Sal&aacute;rio:</td>
            <td width="173" class="tabDetailViewDF">
							<?php
								//Cria um objeto do tipo WDEdit 
								$objWDComponente = new WDEditReal();
								
								//Define nome do componente
								$objWDComponente->strNome = "edtValorSalario";
								//Define o tamanho do componente
								$objWDComponente->intSize = 16;
								//Busca valor definido no XML para o componente
								$objWDComponente->strValor = "";
								//Busca a descrição do XML para o componente
								$objWDComponente->strLabel = "";
								//Determina um ou mais eventos para o componente
								$objWDComponente->strEvento = "";
								//Define numero de caracteres no componente
								$objWDComponente->intMaxLength = 14;
								
								//Cria o componente edit
								$objWDComponente->Criar();  
							?>              
						</td>
            <td width="146" class="dataLabel">Valor Hora: </td>
            <td colspan="2" class="tabDetailViewDF">
							<?php
								//Cria um objeto do tipo WDEdit 
								$objWDComponente = new WDEditReal();
								
								//Define nome do componente
								$objWDComponente->strNome = "edtValorHora";
								//Define o tamanho do componente
								$objWDComponente->intSize = 16;
								//Busca valor definido no XML para o componente
								$objWDComponente->strValor = "";
								//Busca a descrição do XML para o componente
								$objWDComponente->strLabel = "";
								//Determina um ou mais eventos para o componente
								$objWDComponente->strEvento = "";
								//Define numero de caracteres no componente
								$objWDComponente->intMaxLength = 14;
								
								//Cria o componente edit
								$objWDComponente->Criar();  
							?>                            
						</td>
          </tr>
          <tr>
            <td width="140" valign="top" class="dataLabel">Valor Taxa Normal:</td>
            <td class="tabDetailViewDF">
							<?php
								//Cria um objeto do tipo WDEdit 
								$objWDComponente = new WDEditReal();
								
								//Define nome do componente
								$objWDComponente->strNome = "edtValorTaxaNormal";
								//Define o tamanho do componente
								$objWDComponente->intSize = 16;
								//Busca valor definido no XML para o componente
								$objWDComponente->strValor = "";
								//Busca a descrição do XML para o componente
								$objWDComponente->strLabel = "";
								//Determina um ou mais eventos para o componente
								$objWDComponente->strEvento = "";
								//Define numero de caracteres no componente
								$objWDComponente->intMaxLength = 14;
								
								//Cria o componente edit
								$objWDComponente->Criar();  
							?>                            
						</td>
            <td class="dataLabel">Valor Taxa Extra :</td>
            <td colspan="2" class="tabDetailViewDF">
							<?php
								//Cria um objeto do tipo WDEdit 
								$objWDComponente = new WDEditReal();
								
								//Define nome do componente
								$objWDComponente->strNome = "edtValorTaxaExtra";
								//Define o tamanho do componente
								$objWDComponente->intSize = 16;
								//Busca valor definido no XML para o componente
								$objWDComponente->strValor = "";
								//Busca a descrição do XML para o componente
								$objWDComponente->strLabel = "";
								//Determina um ou mais eventos para o componente
								$objWDComponente->strEvento = "";
								//Define numero de caracteres no componente
								$objWDComponente->intMaxLength = 14;
								
								//Cria o componente edit
								$objWDComponente->Criar();  
							?>                           
						</td>
          </tr>
          <tr>
            <td valign="top" class="dataLabel">Banco de Horas:</td>
            <td colspan="4" class="tabDetailViewDF">
							<table width="197" cellpadding="0" cellspacing="0">
	              <tr valign="middle">
	                <td width="117" height="20">
	                  <input name="edtBancoHoras" type="radio" value="1" checked="checked" />
	                  Sim </label>
	                </td>
	                <td width="78" height="20">
	                  <input type="radio" name="edtBancoHoras" value="0" />
	                  N&atilde;o </label>
	                </td>
	              </tr>
	            </table>
						</td>
          </tr>
          <?php
          	//Fecha a verificação de nivel do usuário
          	}
          ?>
          <tr>
            <td valign="top" class="dataLabel">Contato:</td>
            <td colspan="4" class="tabDetailViewDF">
              <input name="edtContato" type="text" class="datafield" id="edtContato" style="width: 260" size="52" maxlength="50" />            
            </td>
          </tr>
        
           <TR>
             <TD width="140" valign="top" class=dataLabel>Informa&ccedil;&otilde;es Complementares:</TD>
             <TD colspan="4" class=tabDetailViewDF>
						 		<table width="100%" cellpadding="0" cellspacing="0">                      
                   <tr valign="middle">
                     <td height="20" colspan="4"><strong>Caracter&iacute;sticas e Particularidades:</strong>                     </td>
                   </tr>
                   <tr valign="middle">
                     <td width="22" height="20">
                       <input name="chkDirige" type="checkbox" id="chkDirige" value="1">                    </td>
                     <td width="236">Dirige </td>
                     <td width="27">
                       <input name="chkSemFumar" type="checkbox" id="chkSemFumar" value="1">										 </td>
                     <td width="296">Fica sem fumar durante o trabalho </td>
                   </tr>
                   <tr valign="middle">
                     <td height="20">
                       <input name="chkFuma" type="checkbox" id="chkFuma" value="1">                           </td>
                     <td>Fuma</td>
                     <td>
                       <input name="chkTirarBrinco" type="checkbox" id="chkTirarBrinco" value="1">										 </td>
                     <td>Disposto a tirar o brinco </td>
                   </tr>
                   <tr valign="middle">
                     <td height="20">
                       <input name="chkBebe" type="checkbox" id="chkBebe" value="1">                           </td>
                     <td>Bebe</td>
                     <td>
                       <input name="chkTirarBarba" type="checkbox" id="chkTirarBarba" value="1">										 </td>
                     <td>Disposto a tirar a barba</td>
                   </tr>
                   <tr valign="middle">
                     <td height="20">
                       <input name="chkBrinco" type="checkbox" id="chkBrinco" value="1">										 </td>
                     <td> Usa brinco</td>
                     <td><input name="chkTemFilho" type="checkbox" id="chkTemFilho" value="1"></td>
                     <td>Possui filhos</td>
                   </tr>
                   <tr valign="middle">
                     <td height="20"><input name="chkHoraExtra" type="checkbox" id="chkHoraExtra" value="1"></td>
                     <td>Pode fazer hora extra</td>
                     <td><input name="chkTrabalharFds" type="checkbox" id="chkTrabalharFds" value="1"></td>
                     <td>Pode trabalhar nos finais de semana</td>
                   </tr>
                   <tr valign="middle">
                     <td height="20"><input name="chkValeTransporte" type="checkbox" id="chkValeTransporte" value="1"></td>
                     <td colspan="3">Precisa Vale-Transporte</td>
                   </tr>
                   <tr valign="middle">
                     <td height="14">&nbsp;</td>
                     <td colspan="3">&nbsp;</td>
                   </tr>
                   <tr valign="middle">
                     <td height="14" colspan="4"><strong>Foto</strong>: Selecione o local do arquivo com a foto do colaborador: </td>
                   </tr>
                   <tr valign="middle">
                     <td height="14" colspan="4"><input type="file" size="100" name="foto"></td>
                   </tr>
                   <tr valign="middle">
                     <td height="14">&nbsp;</td>
                     <td colspan="3">&nbsp;</td>
                   </tr>
                   <tr valign="middle">
                     <td height="14" colspan="4"><strong>Dados Complementares:</strong></td>
                   </tr>
                   <tr valign="middle">
                     <td height="20" colspan="4">
                       <textarea name="edtDadosComplementares" wrap="virtual" class="datafield" id="edtDadosComplementares" style="width: 98%; height: 80px"></textarea>										 
										 </td>
                   </tr>
                 </table>							 
							 </td>
           	</tr>
						
           <tr>
             <td valign="top" class="dataLabel">Observações:</td>
             <td colspan="4" class="tabDetailViewDF">
						   <textarea name="edtObservacoes" wrap="virtual" class="datafield" id="edtObservacoes" style="width: 98%; height: 130px"></textarea>
						 </td>
           </tr>           	
		
	   		</table>
     </td>
   </tr>
</form>
</table>  	 

</tr>
</table>
