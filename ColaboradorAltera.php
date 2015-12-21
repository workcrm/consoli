<?php 
###########
## Módulo para alteração dos colaboradores
## Criado: 20/04/2007 - Maycon Edinger
## Alterado: 19/06/2007 - Maycon Edinger
## Alterações: 
## 23/04/2007 - Acrescentado campo para valor_taxa_extra e renomeado campo valor_taxa
## 10/05/2007 - Removido validação do campo email do colaborador
## 20/05/2007 - Adicionado novos campos
## 28/05/2007 - Implementado o campo ClienteID para a tabela
## 05/06/2007 - Implementado que usuarios nivel 2 não cadastram valores de salarios
## 19/06/2007 - Aplicado objeto para campo money nos campos de valor
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) {
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

//Adiciona o acesso a entidade de criação do componente data
include("CalendarioPopUp.php");
//Cria um objeto do componente data
$objData = new tipData();
//Define que não deve exibir a hora no calendario
$objData->bolExibirHora = false;
//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript(); 

//Monta o lookup aa tabela de funcoes
//Monta o SQL
$lista_funcao = "SELECT * FROM funcoes WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_funcao = mysql_query($lista_funcao);


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

//Verifica se é para excluir um anexo
//ExcluiAnexo=1&Id=<?php echo $campos[id] &Anexo=1&headers=1
if ($_GET["ExcluiFoto"] == 1){

	$colaborador_id = $_GET["Id"];
	
	//Deleta o anexo do documento especificado
	$sql = "UPDATE colaboradores SET foto = '' WHERE id = $colaborador_id";
	
	//Executa a query
	$resultado = mysql_query($sql);

}

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">
<script language="JavaScript">
//Função que alterna a visibilidade do painel especificado.
function oculta(id){
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function wdSubmitColaboradorAltera() {
   var Form;
   Form = document.frmColaboradorAltera;
   
   if (Form.edtNome.value.length == 0) {
      alert("É necessário informar o Nome do Colaborador !");
      Form.edtNome.focus();
      return false;
   }
	 if (Form.cmbFuncaoId.value == 0) {
        alert("É necessário selecionar uma função para o Colaborador !");
        Form.cmbFuncaoId.focus();
        return false;
   }  	  
  
	 return true;
}
</script>

<form name="frmColaboradorAltera" action="sistema.php?ModuloNome=ColaboradorAltera" enctype="multipart/form-data" method="post" onsubmit="return wdSubmitColaboradorAltera()">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
  <tr>
    <td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Alteração do Colaborador</span></td>
			  </tr>
			  <tr>
			    <td colspan="5">
				    <img src="image/bt_espacohoriz.gif" width="100%" height="12">
					</td>
			  </tr>
			</table>

      <table id="2" width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="text">

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
          
						//Verifica se a flag está vindo de uma postagem para liberar a alteração
            if($_POST["Submit"]){
            	
          	if ($_POST[Arquivo] == ''){
            		
            		$anexa = $imagem_nome;
            		
            	} else {
            		
            		$anexa = $_POST[Arquivo];
            		
            	}
            	
						
						$id = $_POST["Id"];
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
            $edtFoto = strrchr($_POST["edtFoto"], "\\");
	          $edtContato = $_POST["edtContato"];
						$edtOperadorId = $usuarioId;
						$edtDadosComplementares = $_POST["edtDadosComplementares"];
	          $edtObservacoes = $_POST["edtObservacoes"];

						//Verifica o nível de acesso do usuário
				    if ($nivelAcesso >= 3) {
				    	
						//Executa a query de alteração normal completa para user > 3
    	    	$sql = mysql_query("UPDATE colaboradores SET 																 
																ativo = '$chkAtivo',
																nome  = '$edtNome',
																tipo = '$edtTipo',
																endereco  = '$edtEndereco',
																complemento = '$edtComplemento',
																bairro  = '$edtBairro',
																cidade_id  = '$cmbCidadeId',
																uf  = '$edtUf',
																cep = '$edtCep',
																rg = '$edtRg',
																titulo_eleitor  = '$edtTitulo',
																ctps = '$edtCtps',
																pis = '$edtPis',
																nacionalidade  = '$edtNacionalidade',
																local_nascimento  = '$edtLocalNascimento',
																data_nascimento  = '$edtDataNascimento',
																nome_pai = '$edtNomePai',
																nome_mae  = '$edtNomeMae',
																estado_civil = '$edtEstadoCivil',
																conjuge = '$edtConjuge',
																cpf  = '$edtCpf', 								
																telefone = '$edtTelefone', 
																fax = '$edtFax', 
																celular = '$edtCelular', 
																email = '$edtEmail',
																data_admissao = '$edtDataAdmissao',
																data_desligamento = '$edtDataDesligamento',
																valor_salario = '$edtValorSalario',
																valor_taxa_normal = '$edtValorTaxaNormal',
																valor_taxa_extra = '$edtValorTaxaExtra',
																valor_hora = '$edtValorHora',
																banco_horas = '$edtBancoHoras',
																funcao_id = '$cmbFuncaoId',
																chk_dirige = '$chkDirige',
																chk_fuma = '$chkFuma',
																chk_bebe = '$chkBebe',
																chk_brinco = '$chkBrinco',
																chk_sem_fumar = '$chkSemFumar',
																chk_tirar_brinco = '$chkTirarBrinco',
																chk_tirar_barba = '$chkTirarBarba',
																chk_tem_filho = '$chkTemFilho',
																chk_hora_extra = '$chkHoraExtra',
																chk_trabalha_fds = '$chkTrabalharFds',
																chk_vale_transporte = '$chkValeTransporte',
																contato = '$edtContato',
																dados_complementares = '$edtDadosComplementares',
																observacoes = '$edtObservacoes',
																alteracao_timestamp = now(),
																alteracao_operador_id = '$edtOperadorId',
																foto = '$anexa'
																WHERE id = '$id' ");

						} else {
					
						//Caso seja acesso restrito, não altera os campos de salário e informações complementares
    	    	$sql = mysql_query("UPDATE colaboradores SET 																 
																ativo = '$chkAtivo',
																nome  = '$edtNome',
																tipo = '$edtTipo',
																endereco  = '$edtEndereco',
																complemento = '$edtComplemento',
																bairro  = '$edtBairro',
																cidade_id  = '$cmbCidadeId',
																uf  = '$edtUf',
																cep = '$edtCep',
																rg = '$edtRg',
																titulo_eleitor  = '$edtTitulo',
																ctps = '$edtCtps',
																pis = '$edtPis',
																nacionalidade  = '$edtNacionalidade',
																local_nascimento  = '$edtLocalNascimento',
																data_nascimento  = '$edtDataNascimento',
																nome_pai = '$edtNomePai',
																nome_mae  = '$edtNomeMae',
																estado_civil = '$edtEstadoCivil',
																conjuge = '$edtConjuge',
																cpf  = '$edtCpf', 								
																telefone = '$edtTelefone', 
																fax = '$edtFax', 
																celular = '$edtCelular', 
																email = '$edtEmail',
																data_admissao = '$edtDataAdmissao',
																data_desligamento = '$edtDataDesligamento',
																funcao_id = '$cmbFuncaoId',
																tipo_colaborador_id = '$cmbTipoColaboradorId',	
																contato = '$edtContato',
																observacoes = '$edtObservacoes',
																alteracao_timestamp = now(),
																alteracao_operador_id = '$edtOperadorId',
																foto = '$anexa'
																WHERE id = '$id' ");
						};						

				//Exibe a mensagem de alteração com sucesso
        echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Colaborador alterado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 3500); </script>";
        	}

        //RECEBENDO OS VALORES PARA A ALTERAÇAO DO REGISTRO
				//Captura o id da cleinte a alterar
        if ($_GET["Id"]) {
					$ColaboradorId = $_GET["Id"];
				} else {
				  $ColaboradorId = $_POST["Id"];
				}
				
				//Monta o sql para busca do colaborador
        $sql = "SELECT * FROM colaboradores WHERE id = $ColaboradorId";

        //Executa a query
				$resultado = mysql_query($sql);

				//Monta o array dos dados
        $campos = mysql_fetch_array($resultado);
		
				//Efetua o switch para o check de ativo
				switch ($campos[ativo]) {
          case 00: $ativo_status = "value='1'";	  break;
          case 01: $ativo_status = "value='1' checked";  break;
				}
            
      	//Efetua o switch para o check de tipo
				switch ($campos[tipo]) {
          case 1: 
						$tipo_free = "checked='checked'";	  
						$tipo_func = '';
            $tipo_ex = '';
					break;
          case 2: 
						$tipo_free = '';	  
						$tipo_func = "checked='checked'";
            $tipo_ex = '';  
					break;
          case 3: 
						$tipo_free = '';	  
						$tipo_func = '';
            $tipo_ex = "checked='checked'";  
					break;
				}
				
				//Efetua o switch para o campo de banco de horas
				switch ($campos[banco_horas]) {
          case 01: $banco_1 = "checked";	$banco_2 = ""; 		  break;
          case 00: $banco_1 = "";		$banco_2 = "checked";  break;
				}	

				//Efetua o switch para o check de dirige
				switch ($campos[chk_dirige]) {
          case 00: $dirige_status = "value='1'";	  break;
          case 01: $dirige_status = "value='1' checked";  break;
				}

				//Efetua o switch para o check de fuma
				switch ($campos[chk_fuma]) {
          case 00: $fuma_status = "value='1'";	  break;
          case 01: $fuma_status = "value='1' checked";  break;
				}
				
				//Efetua o switch para o check de bebe
				switch ($campos[chk_bebe]) {
          case 00: $bebe_status = "value='1'";	  break;
          case 01: $bebe_status = "value='1' checked";  break;
				}				

				//Efetua o switch para o check de brinco
				switch ($campos[chk_brinco]) {
          case 00: $brinco_status = "value='1'";	  break;
          case 01: $brinco_status = "value='1' checked";  break;
				}

				//Efetua o switch para o check de sem fumar
				switch ($campos[chk_sem_fumar]) {
          case 00: $sem_fumar_status = "value='1'";	  break;
          case 01: $sem_fumar_status = "value='1' checked";  break;
				}

				//Efetua o switch para o check de tirar brinco
				switch ($campos[chk_tirar_brinco]) {
          case 00: $tirar_brinco_status = "value='1'";	  break;
          case 01: $tirar_brinco_status = "value='1' checked";  break;
				}

				//Efetua o switch para o check de tirar barba
				switch ($campos[chk_tirar_barba]) {
          case 00: $tirar_barba_status = "value='1'";	  break;
          case 01: $tirar_barba_status = "value='1' checked";  break;
				}

				//Efetua o switch para o check de tem filhos
				switch ($campos[chk_tem_filho]) {
          case 00: $filho_status = "value='1'";	  break;
          case 01: $filho_status = "value='1' checked";  break;
				}

				//Efetua o switch para o check de hora extra
				switch ($campos[chk_hora_extra]) {
          case 00: $hora_extra_status = "value='1'";	  break;
          case 01: $hora_extra_status = "value='1' checked";  break;
				}

				//Efetua o switch para o check de trabalha fds
				switch ($campos[chk_trabalha_fds]) {
          case 00: $trabalha_fds_status = "value='1'";	  break;
          case 01: $trabalha_fds_status = "value='1' checked";  break;
				}

				//Efetua o switch para o check de vale tranporte
				switch ($campos[chk_vale_transporte]) {
          case 00: $vale_transporte_status = "value='1'";	  break;
          case 01: $vale_transporte_status = "value='1' checked";  break;
				}
        					
			?>

        <table cellspacing="0" cellpadding="0" width="100%" border="0">
          <tr>
            <td width="100%"> </td>
          </tr>
          <tr>
	        	<td style="PADDING-BOTTOM: 2px">
	        		<input name="Id" type="hidden" value="<?php echo $ColaboradorId ?>" />
	        		<input name="Arquivo" type="hidden" value="<?php echo $campos[foto] ?>" />
            	<input name="Submit" type="submit" class="button" title="Salva o registro atual" value="Salvar Colaborador" />
            	<input class="button" title="Cancela as alterações efetuadas no registro" name="Reset" type="reset" id="Reset" value="Cancela Alterações" />
           	</td>
           	<td width="36" align="right">
							<input class="button" title="Retorna a exibição do registro" name="btnVoltar" type="button" id="btnVoltar" value="Retornar ao Colaborador" onclick="wdCarregarFormulario('ColaboradorExibe.php?ColaboradorId=<?php echo $ColaboradorId ?>','conteudo')" />						
						</td>
	       	</tr>
        </table>
           
         <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
           <tr>
             <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="20">
               <table cellspacing="0" cellpadding="0" width="100%" border="0">
                 <tr>
                   <td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados do colaborador e clique em [Salvar Colaborador] <br />
                     <br />
                     <span class="style1">Aten&ccedil;&atilde;o:</span> Esta transa&ccedil;&atilde;o ser&aacute; monitorada pelo sistema e ser&aacute; gerado um log da atividade para fins de auditoria.</td>
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
                       <input name="edtNome" type="text" class="datafield" id="edtNome" style="width: 370;color: #6666CC; font-weight: bold" size="84" maxlength="50" value="<?php echo $campos[nome] ?>">										 </td>
                     <td width="119">
                       <div align="right">Cadastro Ativo
                         <input name="chkAtivo" type="checkbox" id="chkAtivo" <?php echo $ativo_status ?>>
                       </div>										 
										 </td>
                   </tr>
                 </table>							
							 </td>
           </tr>
		       <tr>
             <td width="140" class="dataLabel">Tipo de Colaborador: </td>
             <td colspan="3" valign="middle" class="tabDetailViewDF">
               <table width="100%" cellpadding="0" cellspacing="0">
	              <tr valign="middle">
	                <td width="117" height="20">
	                  <input name="edtTipo" type="radio" value="1" <?php echo $tipo_free ?> />
	                  Freelance 
	                </td>
	                <td width="117" height="20">
	                  <input name="edtTipo" type="radio" value="2" <?php echo $tipo_func ?> />
	                  Funcionário
	                </td>
                  <td height="20">
	                  <input name="edtTipo" type="radio" value="3" <?php echo $tipo_ex ?> />
	                  Ex-Funcionário
	                </td>
	              </tr>
	            </table>						 
						 </td>
       			 <td width="160" rowspan="7" align="center" valign="middle" class="tabDetailViewDF" style="border-left:1px solid; border-color:#dfdfdf; padding: 0px;">
								<?php
									//Verifica se existe um caminho de foto no banco							
									if ($campos[foto] != "") {
									?>
									<img src="imagem_colaborador/<?php echo $campos[foto] ?>" width="155" height="200" />	
									<?php
									//fecha o IF
									} else { 
										echo "Sem foto definida !"; 
									};
									?>						 
							</td>
           </tr>
           <tr>
             <td width="140" class="dataLabel">Fun&ccedil;&atilde;o:</td>
             <td colspan="3" valign="middle" class="tabDetailViewDF">
               <select name="cmbFuncaoId" id="cmbFuncaoId" style="width:350px">
                 	<?php while ($lookup_funcao = mysql_fetch_object($dados_funcao)) { ?>
                 <option <?php if ($lookup_funcao->id == $campos[funcao_id]) {
                        echo " selected ";
                      } ?>
                     value="<?php echo $lookup_funcao->id ?>"><?php echo $lookup_funcao->nome ?>				 </option>
        	      <?php } ?>
               </select>						 
						 </td>
           </tr>
					<?php
					/*
		       <tr>
             <td width="140" class="dataLabel">Tipo de Colaborador: </td>
             <td colspan="3" valign="middle" class="tabDetailViewDF">
               <select name="cmbTipoColaboradorId" id="cmbTipoColaboradorId" style="width:350px">
                 	<?php while ($lookup_tipo_colaborador = mysql_fetch_object($dados_tipo_colaborador)) { ?>
                 <option <?php if ($lookup_tipo_colaborador->id == $campos[tipo_colaborador_id]) {
                        echo " selected ";
                      } ?>
                     value="<?php echo $lookup_tipo_colaborador->id ?>"><?php echo $lookup_tipo_colaborador->nome ?>									</option>
        	      <?php } ?>
               </select>						 
						 </td>
           </tr>

           */
           ?>
           <TR>
             <TD width="140" class="dataLabel">Endere&ccedil;o:</TD>
             <TD colspan="3" valign="middle" class=tabDetailViewDF>
               <input name="edtEndereco" type="text" class="datafield" id="edtEndereco" style="width: 300" size="84" maxlength="80" value="<?php echo $campos[endereco] ?>">						 </TD>
          </TR>
          <TR>
            <TD width="140" class=dataLabel>
							<span class="dataLabel">Complemento:</span>						</TD>
            <TD colspan="3" class="tabDetailViewDF">
              <input name="edtComplemento" type="text" class="datafield" id="edtComplemento" style="width: 300" size="84" maxlength="50" value="<?php echo $campos[complemento] ?>">						</TD>
          </TR>
          <TR>
            <TD width="140" class=dataLabel>Bairro:</TD>
            <TD colspan="3" class="tabDetailViewDF">
              <input name="edtBairro" type="text" class="datafield" id="edtBairro" style="width: 300" size="52" maxlength="50" value="<?php echo $campos[bairro] ?>">            </TD>
            </TR>
          <TR>
            <TD width="140" class="dataLabel">Cidade:</TD>
            <TD colspan="3" class="tabDetailViewDF">
               <select name="cmbCidadeId" id="cmbCidadeId" style="width:350px">
                 <?php while ($lookup_cidade = mysql_fetch_object($dados_cidade)) { ?>
                 <option <?php if ($lookup_cidade->id == $campos[cidade_id]) {
                        echo " selected ";
                      } ?>
                     value="<?php echo $lookup_cidade->id ?>"><?php echo $lookup_cidade->nome ?>				 
								 </option>
        	      <?php } ?>
               </select>						
						</TD>
            </TR>
          <TR>
            <TD width="140" class="dataLabel">UF:</TD>
            <TD width="173" class="tabDetailViewDF">
							<select class="datafield"name="edtUf" id="edtUf">
				        <option selected value="<?php echo $campos[uf] ?>"><?php echo $campos[uf] ?></option>
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
				      </select>						</TD>
            <TD width="146" class=dataLabel>Cep:</TD>
            <TD width="129" vAlign=top class=tabDetailViewDF>
							<input name="edtCep" type="text" class="datafield" id="edtCep" size="11" maxlength="9" onKeyPress="return FormataCampo(document.cadastro, 'edtCep', '99999-999', event);" value="<?php echo $campos[cep] ?>">						</TD>
		   		</TR>
           
          <TR>
            <TD width="140" valign="top" class=dataLabel>N&ordm; RG: </TD>
            <TD width="173" class=tabDetailViewDF>
              <input name="edtRg" type="text" class="datafield" id="edtRg" size="20" maxlength="18" value="<?php echo $campos[rg] ?>">            </TD>
            <TD width="146" class=dataLabel>CPF:</TD>
            <TD colspan="2" class=tabDetailViewDF>
              <input name="edtCpf" type="text" class="datafield" id="edtCpf" size="17" maxlength="14" onKeyPress="return FormataCampo(document.cadastro, 'edtCpf', '999.999.999-99', event);" value="<?php echo $campos[cep] ?>">            </TD>
            </TR>
          <TR>
            <TD width="140" valign="top" class=dataLabel>T&iacute;tulo de Eleitor: </TD>
            <TD width="173" class=tabDetailViewDF><input name="edtTitulo" type="text" class="datafield" id="edtTitulo" size="15" maxlength="11" value="<?php echo $campos[titulo_eleitor] ?>"></TD>
            <TD width="146" class=dataLabel>N&ordm; CTPS: </TD>
            <TD colspan="2" class=tabDetailViewDF><input name="edtCtps" type="text" class="datafield" id="edtCtps" size="15" maxlength="10" value="<?php echo $campos[ctps] ?>"></TD>
          </TR>
          <TR>
            <TD width="140" valign="top" class=dataLabel>N&ordm; PIS: </TD>
            <TD width="173" class=tabDetailViewDF><input name="edtPis" type="text" class="datafield" id="edtPis" size="20" maxlength="14" value="<?php echo $campos[pis] ?>"></TD>
            <TD width="146" class=dataLabel>Nacionalidade:</TD>
            <TD colspan="2" class=tabDetailViewDF>
							<input name="edtNacionalidade" type="text" class="datafield" id="edtNacionalidade" style="width: 260" size="52" maxlength="60" value="<?php echo $campos[nacionalidade] ?>">						</TD>
          </TR>
          <TR>
            <TD width="140" valign="top" class=dataLabel>Data Nascimento:</TD>
            <TD width="173" class=tabDetailViewDF>
							<?php
							    //Define a data do formulário
							    $objData->strFormulario = "frmColaboradorAltera";  
							    //Nome do campo que deve ser criado
							    $objData->strNome = "edtDataNascimento";
							    //Valor a constar dentro do campo (p/ alteração)
							    $objData->strValor = DataMySQLRetornar($campos[data_nascimento]);
							    //Define o tamanho do campo 
							    //$objData->intTamanho = 15;
							    //Define o número maximo de caracteres
							    //$objData->intMaximoCaracter = 20;
							    //define o tamanho da tela do calendario
							    //$objData->intTamanhoCalendario = 200;
							    //Cria o componente com seu calendario para escolha da data
							    $objData->CriarData();
							?>						</TD>
            <TD width="146" class=dataLabel>Local Nascimento: </TD>
            <TD colspan="2" class=tabDetailViewDF>
							<input name="edtLocalNascimento" type="text" class="datafield" id="edtLocalNascimento" style="width: 260" size="52" maxlength="60" value="<?php echo $campos[local_nascimento] ?>">						</TD>
          </TR>
          <TR>
            <TD width="140" valign="top" class=dataLabel>Nome do Pai: </TD>
            <TD colspan="4" class=tabDetailViewDF><input name="edtNomePai" type="text" class="datafield" id="edtNomePai" style="width: 300" size="52" maxlength="50" value="<?php echo $campos[nome_pai] ?>"></TD>
            </TR>
          <TR>
            <TD width="140" valign="top" class=dataLabel>Nome da M&atilde;e: </TD>
            <TD colspan="4" class=tabDetailViewDF><input name="edtNomeMae" type="text" class="datafield" id="edtNomeMae" style="width: 300" size="52" maxlength="50" value="<?php echo $campos[nome_mae] ?>"></TD>
            </TR>
          <TR>
            <TD width="140" valign="top" class=dataLabel>Estado Civil: </TD>
            <TD width="173" class=tabDetailViewDF><label>
              <select name="edtEstadoCivil" size="1" id="edtEstadoCivil">
 				        <?php 
 				          if ($campos[estado_civil] == '0'){
 				            echo "<option selected value='0'>Selecione</option>";
 				          } else {
 				    				echo "<option selected value='$campos[estado_civil]'>$campos[estado_civil]</option>";
 				          }
								?>
                <option value="Solteiro">Solteiro</option>
                <option value="Casado">Casado</option>
                <option value="Amaziado">Amaziado</option>
                <option value="Divorciado">Divorciado</option>
                <option value="Viúvo">Vi&uacute;vo</option>
              </select>
            </label></TD>
            <TD width="146" class=dataLabel>C&ocirc;njuge:</TD>
            <TD colspan="2" class=tabDetailViewDF><input name="edtConjuge" type="text" class="datafield" id="edtConjuge" style="width: 260" size="52" maxlength="50" value="<?php echo $campos[conjuge] ?>"></TD>
          </TR>
          <TR>
            <TD width="140" valign="top" class=dataLabel>Telefone:</TD>
            <TD width="173" class=tabDetailViewDF>
              <input name="edtTelefone" type="text" class="datafield" id="edtTelefone" size="16" maxlength="14" onKeyPress="return FormataCampo(document.cadastro, 'edtTelefone', '(99) 9999-9999', event);" value="<?php echo $campos[telefone] ?>">						</TD>
            <TD width="146" class=dataLabel>Fax:</TD>
            <TD colspan="2" class=tabDetailViewDF>
              <input name="edtFax" type="text" class="datafield" id="edtFax" size="16" maxlength="14" onKeyPress="return FormataCampo(document.cadastro, 'edtFax', '(99) 9999-9999', event);" value="<?php echo $campos[fax] ?>">						</TD>
          </TR>
          <TR>
            <TD width="140" valign="top" class=dataLabel>Celular:</TD>
            <TD colspan="4" class=tabDetailViewDF>
              <input name="edtCelular" type="text" class="datafield" id="edtCelular" size="16" maxlength="14" onKeyPress="return FormataCampo(document.cadastro, 'edtCelular', '(99) 9999-9999', event);" value="<?php echo $campos[celular] ?>">						</TD>
          </TR>
          <TR>
            <TD width="140" valign="top" class=dataLabel>E-mail: </TD>
            <TD colspan="4" class=tabDetailViewDF>
              <input name="edtEmail" type="text" class='datafield' id="edtEmail" style="width: 300; text-transform:lowercase" size="52" maxlength="50" value="<?php echo $campos[email] ?>">						</TD>
          </TR>
          <TR>
            <TD width="140" valign="top" class=dataLabel>Data Admiss&atilde;o: </TD>
            <TD width="173" class=tabDetailViewDF>
							<?php
							    //Define a data do formulário
							    $objData->strFormulario = "frmColaboradorAltera";  
							    //Nome do campo que deve ser criado
							    $objData->strNome = "edtDataAdmissao";
							    //Valor a constar dentro do campo (p/ alteração)
							    $objData->strValor = DataMySQLRetornar($campos[data_admissao]);;
							    //Define o tamanho do campo 
							    //$objData->intTamanho = 15;
							    //Define o número maximo de caracteres
							    //$objData->intMaximoCaracter = 20;
							    //define o tamanho da tela do calendario
							    //$objData->intTamanhoCalendario = 200;
							    //Cria o componente com seu calendario para escolha da data
							    $objData->CriarData();
							?>						</TD>
            <TD width="146" class=dataLabel>Data Desligamento: </TD>
            <TD colspan="2" class=tabDetailViewDF>
							<?php
							    //Define a data do formulário
							    $objData->strFormulario = "frmColaboradorAltera";  
							    //Nome do campo que deve ser criado
							    $objData->strNome = "edtDataDesligamento";
							    //Valor a constar dentro do campo (p/ alteração)
							    $objData->strValor = DataMySQLRetornar($campos[data_desligamento]);
							    //Define o tamanho do campo 
							    //$objData->intTamanho = 15;
							    //Define o número maximo de caracteres
							    //$objData->intMaximoCaracter = 20;
							    //define o tamanho da tela do calendario
							    //$objData->intTamanhoCalendario = 200;
							    //Cria o componente com seu calendario para escolha da data
							    $objData->CriarData();
							?>						</TD>
          </TR>
          
					<?php
					//Verifica o nível de acesso do usuário
					if ($nivelAcesso >= 3) {
					?>					
					
					<TR>
            <TD width="140" valign="top" class=dataLabel>Sal&aacute;rio:</TD>
            <TD width="173" class=tabDetailViewDF>
							<?php
							//Acerta a variável com o valor a alterar
							$valor_alterar = str_replace(".",",",$campos[valor_salario]);							
							
							//Cria um objeto do tipo WDEdit 
							$objWDComponente = new WDEditReal();
							
							//Define nome do componente
							$objWDComponente->strNome = "edtValorSalario";
							//Define o tamanho do componente
							$objWDComponente->intSize = 16;
							//Busca valor definido no XML para o componente
							$objWDComponente->strValor = "$valor_alterar";
							//Busca a descrição do XML para o componente
							$objWDComponente->strLabel = "";
							//Determina um ou mais eventos para o componente
							$objWDComponente->strEvento = "";
							//Define numero de caracteres no componente
							$objWDComponente->intMaxLength = 14;
							
							//Cria o componente edit
							$objWDComponente->Criar();  
							?>								
						</TD>
            <TD width="146" class=dataLabel>Valor Hora: </TD>
            <TD colspan="2" class=tabDetailViewDF>
							<?php
							//Acerta a variável com o valor a alterar
							$valor_alterar = str_replace(".",",",$campos[valor_hora]);
							
							//Cria um objeto do tipo WDEdit 
							$objWDComponente = new WDEditReal();
							
							//Define nome do componente
							$objWDComponente->strNome = "edtValorHora";
							//Define o tamanho do componente
							$objWDComponente->intSize = 16;
							//Busca valor definido no XML para o componente
							$objWDComponente->strValor = "$valor_alterar";
							//Busca a descrição do XML para o componente
							$objWDComponente->strLabel = "";
							//Determina um ou mais eventos para o componente
							$objWDComponente->strEvento = "";
							//Define numero de caracteres no componente
							$objWDComponente->intMaxLength = 14;
							
							//Cria o componente edit
							$objWDComponente->Criar();  
							?>	              							
						</TD>
          </TR>
          <TR>
            <TD width="140" valign="top" class=dataLabel>Valor Taxa Normal:</TD>
            <TD class=tabDetailViewDF>
							<?php
							//Acerta a variável com o valor a alterar
							$valor_alterar = str_replace(".",",",$campos[valor_taxa_normal]);
														
							//Cria um objeto do tipo WDEdit 
							$objWDComponente = new WDEditReal();
							
							//Define nome do componente
							$objWDComponente->strNome = "edtValorTaxaNormal";
							//Define o tamanho do componente
							$objWDComponente->intSize = 16;
							//Busca valor definido no XML para o componente
							$objWDComponente->strValor = "$valor_alterar";
							//Busca a descrição do XML para o componente
							$objWDComponente->strLabel = "";
							//Determina um ou mais eventos para o componente
							$objWDComponente->strEvento = "";
							//Define numero de caracteres no componente
							$objWDComponente->intMaxLength = 14;
							
							//Cria o componente edit
							$objWDComponente->Criar();  
							?>	              
						</TD>
            <TD class=dataLabel>Valor Taxa Extra :</TD>
            <TD colspan="2" class=tabDetailViewDF>
							<?php
							//Acerta a variável com o valor a alterar
							$valor_alterar = str_replace(".",",",$campos[valor_taxa_extra]);
														
							//Cria um objeto do tipo WDEdit 
							$objWDComponente = new WDEditReal();
							
							//Define nome do componente
							$objWDComponente->strNome = "edtValorTaxaExtra";
							//Define o tamanho do componente
							$objWDComponente->intSize = 16;
							//Busca valor definido no XML para o componente
							$objWDComponente->strValor = "$valor_alterar";
							//Busca a descrição do XML para o componente
							$objWDComponente->strLabel = "";
							//Determina um ou mais eventos para o componente
							$objWDComponente->strEvento = "";
							//Define numero de caracteres no componente
							$objWDComponente->intMaxLength = 14;
							
							//Cria o componente edit
							$objWDComponente->Criar();  
							?>	                        
						</TD>
          </TR>
          <TR>
            <TD valign="top" class=dataLabel>Banco de Horas:</TD>
            <TD colspan="4" class=tabDetailViewDF>
              <table width="197" cellpadding="0" cellspacing="0">
                <tr valign="middle">
                  <td width="117" height="20">
                    <label>
                    <input name="edtBancoHoras" type="radio" value="1" <?php echo $banco_1 ?>>
                      Sim </label>
                  </td>
                  <td width="78" height="20">
                    <label>
                    <input type="radio" name="edtBancoHoras" value="0" <?php echo $banco_2 ?>>
                      Não </label>
                  </td>
                </tr>
              </table>						
						</TD>
          </TR>
					<?php
						//Fecha a verificação de nivel de acesso
						}
					?>
          <TR>
            <TD valign="top" class=dataLabel>Contato:</TD>
            <TD colspan="4" class=tabDetailViewDF>
              <input name="edtContato" type="text" class="datafield" id="edtContato" style="width: 260" size="52" maxlength="50" value="<?php echo $campos[contato] ?>" />            </TD>
          </TR>

					<?php
					//Verifica o nível de acesso do usuário
					if ($nivelAcesso >= 3) {
					?>
          
           <TR>
             <TD width="140" valign="top" class=dataLabel>Informa&ccedil;&otilde;es Complementares:</TD>
             <TD colspan="4" class=tabDetailViewDF>
						 		<table width="100%" cellpadding="0" cellspacing="0">                      
                   <tr valign="middle">
                     <td height="20" colspan="4"><strong>Caracter&iacute;sticas e Particularidades:</strong>                     </td>
                   </tr>
                   <tr valign="middle">
                     <td width="22" height="20">
                       <input name="chkDirige" type="checkbox" id="chkDirige" <?php echo $dirige_status ?>>                    </td>
                     <td width="236">Dirige </td>
                     <td width="27">
                       <input name="chkSemFumar" type="checkbox" id="chkSemFumar" <?php echo $sem_fumar_status ?>>										 </td>
                     <td width="296">Fica sem fumar durante o trabalho </td>
                   </tr>
                   <tr valign="middle">
                     <td height="20">
                       <input name="chkFuma" type="checkbox" id="chkFuma" <?php echo $fuma_status ?>>                           </td>
                     <td>Fuma</td>
                     <td>
                       <input name="chkTirarBrinco" type="checkbox" id="chkTirarBrinco" <?php echo $tirar_brinco_status ?>>										 </td>
                     <td>Disposto a tirar o brinco </td>
                   </tr>
                   <tr valign="middle">
                     <td height="20">
                       <input name="chkBebe" type="checkbox" id="chkBebe" <?php echo $bebe_status ?>>                           </td>
                     <td>Bebe</td>
                     <td>
                       <input name="chkTirarBarba" type="checkbox" id="chkTirarBarba" <?php echo $tirar_barba_status ?>>										 </td>
                     <td>Disposto a tirar a barba</td>
                   </tr>
                   <tr valign="middle">
                     <td height="20">
                       <input name="chkBrinco" type="checkbox" id="chkBrinco" <?php echo $brinco_status ?>>										 </td>
                     <td> Usa brinco</td>
                     <td><input name="chkTemFilho" type="checkbox" id="chkTemFilho" <?php echo $filho_status ?> /></td>
                     <td>Possui filhos</td>
                   </tr>
                   <tr valign="middle">
                     <td height="20"><input name="chkHoraExtra" type="checkbox" id="chkHoraExtra" <?php echo $hora_extra_status ?> /></td>
                     <td>Pode fazer hora extra</td>
                     <td><input name="chkTrabalharFds" type="checkbox" id="chkTrabalharFds" <?php echo $trabalha_fds_status ?> /></td>
                     <td>Pode trabalhar nos finais de semana</td>
                   </tr>
                   <tr valign="middle">
                     <td height="20"><input name="chkValeTransporte" type="checkbox" id="chkValeTransporte" <?php echo $vale_transporte_status ?> /></td>
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
                     <td height="14" colspan="4">
										 		<?php 
	
													if ($campos["foto"] != ""){
													
													
												?>
												<b><span style="color: #990000">Para alterar a imagem do colaborador, deve-se primeiro excluir a atual.</span>&nbsp;</b><input class="button" name="btnExcluir1" title="Exclui a foto do colaborador" onclick="wdCarregarFormulario('ColaboradorAltera.php?ExcluiFoto=1&Id=<?php echo $campos[id] ?>&headers=1','conteudo')" type="button" value="Excluir Foto Atual" />
											<?php
											
												} else {
													
											?>
										 <input type="file" size="100" name="foto" /></td>
										 <?php
										 }
										 ?>
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
                       <textarea name="edtDadosComplementares" wrap="virtual" class="datafield" id="edtDadosComplementares" style="width: 98%; height: 80px"><?php echo $campos[dados_complementares] ?></textarea>										 
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
             <td valign="top" class="dataLabel">Observações:</td>
             <td colspan="4" class="tabDetailViewDF">
						   <textarea name="edtObservacoes" wrap="virtual" class="datafield" id="edtObservacoes" style="width: 98%; height: 130px"><?php echo $campos[observacoes] ?></textarea>
						 </td>
           </tr>           	
	   		</table>
     </td>
   </tr>
</form>
</table>  	 

</td>
</tr>
</table>
