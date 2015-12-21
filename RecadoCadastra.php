<?php 
###########
## M�dulo para Cadastro de recados
## Criado: 17/04/2007 - Maycon Edinger
## Alterado: 19/07/2007 - Maycon Edinger
## Altera��es: 
## 19/07/2007 - Implementado rotina para envio com c�pia-carbono (2 usu�rios)
###########
/**
* @package workeventos
* @abstract M�dulo para Cadastro de recados
* @author Maycon Edinger
* @copyright 2007 - Maycon Edinger
*/

//Seta o header do retorno para efetuar a acentua��o correta usando o AJAX
//header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

//Com a utiliza��o do AJAX, deve-se efetuar nova conex�o e novo processamento de diretivas
// Processa as diretivas de seguran�a 
//require("Diretivas.php");
//Estabelece a conex�o com o banco de dados
//include "./conexao/ConexaoMySQL.php";

//Instancia o FCKEditor
include("./FCKEditor/fckeditor.php");

//Inclui o arquivo para manipula��o de datas
include "./include/ManipulaDatas.php";
?>

<script language="JavaScript">
//Fun��o que alterna a visibilidade do painel especificado.
function oculta(id){
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function valida_form() {   

	var Form;
    Form = document.cadastro;

	 	if (Form.cmbDestinatario.value == 0) {
      alert("� necess�rio selecionar um Destinat�rio !");
      Form.cmbDestinatario.focus();
      return false;
   	}

	 	if (Form.cmbCopia.value > 0) {
		 	if (Form.cmbDestinatario.value == Form.cmbCopia.value) {
	      alert("Os usu�rios de Destinat�rio e C�pia-carbono (C.c.) s�o iguais !");
	      Form.cmbDestinatario.focus();
	      return false;
	   	}      
   	}
   
		if (Form.edtData.value.length == 0) {
        alert("� necess�rio informar a data !");
        Form.edtData.focus();
        return false;
     }
     if (Form.edtAssunto.value.length == 0) {
        alert("� necess�rio informar o assunto !");
        Form.edtAssunto.focus();
        return false;
     }
     
		 updateRTEs();
		 return true;
}
</script>
</head>

<body>

<?php 
	//Efetua o lookup na tabela de usuarios
  //Monta a sele��o de usu�rios na combo, sem considerar o usuario atual.
  $lista_usuarios = "SELECT * FROM usuarios WHERE empresa_id = $empresaId";
  //Para nao incluir o usuario atual:  [AND usuario_id <> $usuarioId]
  $dados_usuarios = mysql_query($lista_usuarios);
  
  //Monta a sele��o de usu�rios na combo, sem considerar o usuario atual.
  $lista_copia = "SELECT * FROM usuarios WHERE empresa_id = $empresaId";
  //Para nao incluir o usuario atual:  [AND usuario_id <> $usuarioId]
  $dados_copia = mysql_query($lista_copia);
  
  //Adiciona o acesso a entidade de cria��o do componente data
  include("CalendarioPopUp.php");
  //Cria um objeto do componente data
  $objData = new tipData();
  //Define que n�o deve exibir a hora no calendario
  $objData->bolExibirHora = false;
  //Monta javaScript do calendario uma unica vez para todos os campos do tipo data
  $objData->MontarJavaScript();   
?>

<FORM id='form' name='cadastro' action='sistema.php?ModuloNome=RecadoCadastra' method='post' onSubmit='return valida_form()'>

<table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'>
  <tr>
    <td class="text" valign="top">
		  <table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width='440'><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Envio de Recados</span></td>
			  </tr>
			  <tr>
			    <td colspan='5'>
				    <img src="image/bt_espacohoriz.gif" width="100%" height="12">
				</td>
			  </tr>
		      <tr>
		        <td colspan='5'>
			      <table id='1' style="display: none" width="100%" cellpadding="0" cellspacing="0" border="0">
		            <tr>
		              <td valign='midle'><img src="image/bt_ajuda.gif" width="13" height="16" /></td>
		              <td>  Este painel est� oculto. Clique em [Exibir/Ocultar Painel] para alternar sua visibilidade.</td>
		            </tr>
			      </table>
			    </td>
			  </tr>
			</table>

      <table id='2' width='750' align='left' border='0' cellspacing='0' cellpadding='0'>
        <tr>
          <td width='750' class='text'>

          <?php
            if($_POST['Submit']){

            $edtEmpresaId = $empresaId;
						$edtRemetenteId = $usuarioId;
            $edtDestinatarioId = $_POST["cmbDestinatario"];
            $edtCopiaId = $_POST["cmbCopia"];
            $edtData = DataMySQLInserir($_POST["edtData"]);
            $edtAssunto = $_POST["edtAssunto"];
            $edtMensagem = $_POST["edtMensagem"];

    	    	$sql = mysql_query("
                INSERT INTO recados (
								empresa_id, 
								remetente_id, 
								destinatario_id, 
								data, 
								assunto, 
								mensagem
								
								) VALUES (
				
								'$edtEmpresaId',
								'$edtRemetenteId',
								'$edtDestinatarioId',
								'$edtData',
								'$edtAssunto',
								'$edtMensagem'
								);");

						//Verifica se deve enviar um recado para mais de um usu�rio
						if ($edtCopiaId > 0) {
							//Insere novamente o recado, por�m para o usu�rio de copia
							$sql = mysql_query("
                INSERT INTO recados (
								empresa_id, 
								remetente_id, 
								destinatario_id, 
								data, 
								assunto, 
								mensagem
								
								) VALUES (
				
								'$edtEmpresaId',
								'$edtRemetenteId',
								'$edtCopiaId',
								'$edtData',
								'$edtAssunto',
								'$edtMensagem'
								);");
						}
	
					//Exibe a mensagem de inclus�o com sucesso
        	echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Recado cadastrado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
        	}
        ?>

        <TABLE cellSpacing='0' cellPadding='0' width='520' border='0'>
          <tr>
            <td width="484">
			  </td>
          </TR>
          <tr>
	        <TD style="PADDING-BOTTOM: 2px">
	        <INPUT name='Submit' type='submit' class=button id="Submit" accessKey='S' title="Salva o registro atual [Alt+S]" value='Enviar Recado'>
            <INPUT class=button title="Limpa o conte�do dos campos digitados [Alt+L]" accessKey='L' name='Reset' type='reset' id='Reset' value='Limpar Campos'>

             </TD>
             <TD width="36" align=right>	  </TD>
	       </TR>
         </TBODY>
         </TABLE>
           
         <TABLE class='tabDetailView' cellSpacing='0' cellPadding='0' width='100%' border='0'>
         <TBODY>
           <TR>
             <TD class='listViewPaginationTdS1' style='PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px' colSpan='20'>
               <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
               <TBODY>
                 <TR>
                   <TD class='tabDetailViewDL' style='TEXT-ALIGN: left'><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados do recado e clique em [Enviar Recado] </TD>
			     			 </TR>
		       		 </TBODY>
		       	   </TABLE>             
				 		 </TD>
	       	 </TR>
           <TR>
             <TD class='dataLabel' width='15%'>
						   <span class="dataLabel">Remetente:</span>
             </TD>
             <TD width="85%" colspan='3' class=tabDetailViewDF>
			   			 <?php echo $usuarioNome . " " . $usuarioSobrenome ?>
             </TD>
           </TR>
           <TR>
             <TD class='dataLabel'>Destinat&aacute;rio:</TD>
             <TD colspan="3" valign="middle" class=tabDetailViewDF>
               <select name="cmbDestinatario" id="cmbDestinatario" style="width: 300px">
                 <option value="0">Selecione uma Op��o</option>
								 <?php while ($lookup_usuarios = mysql_fetch_object($dados_usuarios)) { ?>
                 <option value="<?php echo $lookup_usuarios->usuario_id ?>"><?php echo $lookup_usuarios->nome . " " . $lookup_usuarios->sobrenome ?> </option>
                 <?php } ?>
               </select>
             </TD>
           </TR>
           <TR>
             <TD class='dataLabel'>C.c.:</TD>
             <TD colspan="3" valign="middle" class=tabDetailViewDF>
               <select name="cmbCopia" id="cmbCopia" style="width: 300px">
                 <option value="0">Selecione uma Op��o</option>
								 <?php while ($lookup_copia = mysql_fetch_object($dados_copia)) { ?>
                 <option value="<?php echo $lookup_copia->usuario_id ?>"><?php echo $lookup_copia->nome . " " . $lookup_copia->sobrenome ?> </option>
                 <?php } ?>
               </select>
             </TD>
           </TR>
           <TR>
             <TD class='dataLabel'>Data:</TD>
             <TD colspan="3" valign="middle" class=tabDetailViewDF>
							 <?php
							    //Define a data do formul�rio
							    $objData->strFormulario = "cadastro";  
							    //Nome do campo que deve ser criado
							    $objData->strNome = "edtData";
							    //Valor a constar dentro do campo (p/ altera��o)
							    $objData->strValor = Date('d/m/Y', mktime());
							    //Define o tamanho do campo 
							    //$objData->intTamanho = 15;
							    //Define o n�mero maximo de caracteres
							    //$objData->intMaximoCaracter = 20;
							    //define o tamanho da tela do calendario
							    //$objData->intTamanhoCalendario = 200;
							    //Cria o componente com seu calendario para escolha da data
							    $objData->CriarData();
							 ?>
             </TD>
           </TR>
           <TR>
             <TD class='dataLabel'>Assunto:</TD>
             <TD colspan='3' valign="middle" class=tabDetailViewDF>
               <input name="edtAssunto" type="text" class='datafield' id="edtAssunto" style="width: 380px;color: #6666CC; font-weight: bold" size="84" maxlength="60">
             </TD>
           </TR>
           <TR>
             <TD valign="top" class=dataLabel>Mensagem:</TD>
             <TD valign="top" colspan="3" class=tabDetailViewDF>
				  		 <table width="100%" border="0" cellspacing="0" cellpadding="0">
                 <tr>
                   <td valign="top">
										 <?php									
											 //Cria um novo objeto do FCKEditor
						    			 $oFCKeditor = new FCKeditor('edtMensagem');
						    			 $oFCKeditor -> BasePath = "./FCKEditor/";
						    			 $oFCKeditor -> Value = "";
						    			 $oFCKeditor -> Width = "100%";
						    			 $oFCKeditor -> Height = "300";
						    			 $oFCKeditor -> Create();
						    		 ?>
					  			 </td>
                 </tr>
               </table>		 
			  		 </TD>
           </TR>
         </TBODY>
	     </TABLE>
     </td>
   </tr>
  </FORM>
</table>  	 

</tr>
</table>
