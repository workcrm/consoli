<?php 
###########
## Módulo para Resposta de recados
## Criado: 25/04/2007 - Maycon Edinger
## Alterado:
## Alterações: 
###########
/**
* @package workeventos
* @abstract Módulo para Resposta de recados
* @author Maycon Edinger
* @copyright 2007 - Maycon Edinger
*/

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
//header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

// Processa as diretivas de segurança 
require("Diretivas.php");

//Instancia o FCKEditor
include("./FCKEditor/fckeditor.php");

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";
?>

<script language="JavaScript">
//Função que alterna a visibilidade do painel especificado.
function oculta(id){
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function valida_form() {   
	updateRTEs();
	 
	 var Form;
     Form = document.cadastro;

	 if (Form.edtData.value.length == 0) {
        alert("É necessário informar a data !");
        Form.edtData.focus();
        return false;
     }
     if (Form.edtAssunto.value.length == 0) {
        alert("É necessário informar o assunto !");
        Form.edtAssunto.focus();
        return false;
     }
     return true;
}
</script>
</head>

<body>

<?php 

	//Recebe o ID do recado a exibir               
	$RecadoId = $_GET["RecadoId"];

	//Monta a query de consulta do recado
	$sql = "SELECT 
					rec.id, 
					rec.empresa_id, 
					rec.remetente_id, 
					rec.destinatario_id, 
					rec.data, 
					rec.assunto, 
					rec.mensagem,
					rec.lido, 
					usu.nome as remetente_nome, 
					usu.sobrenome as remetente_sobrenome 
					FROM recados rec 
					LEFT OUTER JOIN usuarios usu ON rec.remetente_id = usu.usuario_id 
					WHERE rec.id = '$RecadoId'";

	//Executa a query			
	$resultado = mysql_query($sql);
	//Monta o array associativo dos dados retornados
	$campos = mysql_fetch_array($resultado);
  
  
  
  //Adiciona o acesso a entidade de criação do componente data
  include("CalendarioPopUp.php");
  //Cria um objeto do componente data
  $objData = new tipData();
  //Define que não deve exibir a hora no calendario
  $objData->bolExibirHora = false;
  //Monta javaScript do calendario uma unica vez para todos os campos do tipo data
  $objData->MontarJavaScript();   
?>

<table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'>
  <tr>
    <td class="text" valign="top">
		  <table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width='440'><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Envio de Recados - Responder Recado</span></td>
			  </tr>
			  <tr>
			    <td colspan='5'>
				    <img src="image/bt_espacohoriz.gif" width="100%" height="12">
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
	
					//Exibe a mensagem de inclusão com sucesso
        	echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Recado respondido com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500); wdCarregarFormulario('MeuPortal.php','conteudo')</script>";
        	}
        ?>

        <TABLE cellSpacing='0' cellPadding='0' width='520' border='0'>
          <tr>
            <td width="484">
              <FORM id='form' name='cadastro' action='sistema.php?ModuloNome=RecadoRespondePosta' method='post' onSubmit='return valida_form()'>
			  </td>
          </TR>
          <tr>
	        <TD style="PADDING-BOTTOM: 2px">
	        <INPUT name='Submit' type='submit' class=button id="Submit" accessKey='S' title="Salva o registro atual [Alt+S]" value='Responder Recado'>
            <INPUT class=button title="Limpa o conteúdo dos campos digitados [Alt+L]" accessKey='L' name='Reset' type='reset' id='Reset' value='Limpar Campos'>

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
				        <input name="cmbDestinatario" type="hidden" value=<?php echo $campos[remetente_id] ?> />
								<?php echo $campos[remetente_nome] . " " . $campos[remetente_sobrenome] ?>
             </TD>
           </TR>
           <TR>
             <TD class='dataLabel'>Data:</TD>
             <TD colspan="3" valign="middle" class=tabDetailViewDF>
							 <?php
							    //Define a data do formulário
							    $objData->strFormulario = "cadastro";  
							    //Nome do campo que deve ser criado
							    $objData->strNome = "edtData";
							    //Valor a constar dentro do campo (p/ alteração)
							    $objData->strValor = Date('d/m/Y', mktime());
							    //Define o tamanho do campo 
							    //$objData->intTamanho = 15;
							    //Define o número maximo de caracteres
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
               <input name="edtAssunto" type="text" class='datafield' id="edtAssunto" style="width: 300" size="84" maxlength="80" value="Re: <?php echo $campos[assunto]?>">
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
						    			 $oFCKeditor -> Value = "</br></br><font color='#ff0000'><em>Mensagem Original:</br>=========================================</br></em></font>" . $campos[mensagem];
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
