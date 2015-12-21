<?php 
###########
## Módulo para alteração do documento do evento
## Criado: 10/01/2011 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) 
{
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Adiciona o acesso a entidade de criação do componente data
include("CalendarioPopUp.php");
//Cria um objeto do componente data
$objData = new tipData();
//Define que não deve exibir a hora no calendario
$objData->bolExibirHora = false;
//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript();


//Monta o lookup da tabela de tipos de documentos
//Monta o SQL
$lista_tipo = "SELECT id, nome FROM tipos_documento WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_tipo = mysql_query($lista_tipo);
?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<script language="JavaScript">
//Função que alterna a visibilidade do painel especificado.
function oculta(id){
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function wdSubmitDocumentoEventoAltera() {
   var Form;
   Form = document.frmDocumentoEventoAltera;
   
	 if (Form.edtData.value == 0) {
      alert("É necessário informar a data para o Evento !");
      Form.edtData.focus();
      return false;
   }
	 if (Form.edtDescricao.value == 0) {
      alert("É necessário informar a descricao da data do Evento !");
      Form.edtDescricao.focus();
      return false;
   }
   if (Form.edtAnexo.value == 0) {
      alert("É necessário selecionar um anexo a salvar para o Evento !");
      Form.edtAnexo.focus();
      return false;
   }
   return true;
}
</script>

<form name='frmDocumentoEventoAltera' enctype="multipart/form-data" action='sistema.php?ModuloNome=DocumentosEventoAltera' method='post' onsubmit='return wdSubmitDocumentoEventoAltera()'>

<table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'>
  <tr>
    <td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width='440'><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Alteração de Documento do Evento</span></td>
			  </tr>
			  <tr>
			    <td colspan='5'>
				    <img src="image/bt_espacohoriz.gif" width="100%" height="12" />
					</td>
			  </tr>
			</table>

      <table id='2' width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
        <tr>
          <td width='100%' class='text'>

          <?php
						//Verifica se a flag está vindo de uma postagem para liberar a alteração
            if($_POST['Submit'])
            {

						//Recupera os valores do formulario e alimenta as variáveis
						$id = $_POST["Id"];
	 					$edtOperadorId = $usuarioId;
	 					$edtEmpresaId = $empresaId;
	          $edtEventoId = $_POST["EventoId"];
            $cmbTipoDocumentoId = $_POST["cmbTipoDocumentoId"];
	          $edtData = DataMySQLInserir($_POST["edtData"]);
	          $edtDescricao = $_POST["edtDescricao"];
	          $edtObservacoes = $_POST["edtObservacoes"];	

						//Executa a query de alteração da conta
    	    	$sql = mysql_query("UPDATE eventos_documento SET 
																data = '$edtData',
                                tipo_documento_id = '$cmbTipoDocumentoId', 
																descricao = '$edtDescricao',  
																observacoes = '$edtObservacoes',
																alteracao_timestamp = now(),
																alteracao_operador_id = '$edtOperadorId'
																WHERE id = '$id' ");	
																
					//Configura a assinatura digital
    	    $sql = mysql_query("UPDATE eventos SET documentos_timestamp = now(), documentos_operador_id = $usuarioId WHERE id = $edtEventoId");		 

				//Exibe a mensagem de alteração com sucesso
        echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Documento do Evento alterado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 3500);</script>";
        	}

        //RECEBENDO OS VALORES PARA A ALTERAÇAO DO REGISTRO
				//Captura o id 
        if ($_GET["Id"]) 
        {
					
          $DataId = $_GET["Id"];
					$EventoId = $_GET["EventoId"];
				
        } 
        
        else 
        
        {
				  
          $DataId = $_POST["Id"];
				  $EventoId = $_POST["EventoId"];
				
        }
				
				//Monta o sql para busca do registro
        $sql = "SELECT * FROM eventos_documento WHERE id = $DataId";

        //Executa a query
				$resultado = mysql_query($sql);

				//Monta o array dos dados
        $campos = mysql_fetch_array($resultado);
					           					
			?>

        <table cellspacing='0' cellpadding='0' width='100%' border='0'>
          <tr>
            <td width="100%"> </td>
          </tr>
          <tr>
	        	<td style="PADDING-BOTTOM: 2px">
	        		<input name="Id" type="hidden" value="<?php echo $DataId ?>" />
	        		<input name="EventoId" type="hidden" value="<?php echo $EventoId ?>" />
            	<input name="Submit" type="submit" class="button" title="Salva o registro atual" value="Salvar Documento" onsubmit="return wdSubmitDocumentoEventoAltera()" />
            	<input class="button" title="Cancela as alterações efetuadas no registro" name='Reset' type='reset' id='Reset' value='Cancela Alterações' />
           	</td>
           	<td width="36" align="right">
							<input class="button" title="Retorna ao cadastro de documentos do evento" name='btnVoltar' type='button' id='btnVoltar' value='Retornar aos Documentos do Evento' onclick="wdCarregarFormulario('DocumentosEventoCadastra.php?EventoId=<?php echo $EventoId ?>&headers=1','conteudo')" />						
						</td>
	       	</tr>
        </table>
           
         <table class='tabDetailView' cellspacing='0' cellpadding='0' width='100%' border='0'>
           <tr>
             <td class='listViewPaginationTdS1' style='PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px' colspan='20'>
               <table cellspacing="0" cellpadding="0" width="100%" border="0" />
                 <tr>
                   <td class='tabDetailViewDL' style='TEXT-ALIGN: left'><img src="image/bt_cadastro.gif" width="16" height="15" /> Informe os dados da data do evento e clique em [Salvar Data] </td>
		     				 </tr>
       				 </table>             
			 				</td>
	       	 </tr>
	       	 </tr>
           <tr>
						 <td valign="middle" width="120" class="dataLabel">Data:</td>
             <td valign="middle" class="tabDetailViewDF">
							 <?php
							    //Define a data do formulário
							    $objData->strFormulario = "frmDataEventoAltera";  
							    //Nome do campo que deve ser criado
							    $objData->strNome = "edtData";
							    //Valor a constar dentro do campo (p/ alteração)
							    $objData->strValor = DataMySQLRetornar($campos[data]);
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
          <tr>
            <td class='dataLabel' width='20%'>
             <span class="dataLabel">Local:</span>             
            </td>
            <td class="tabDetailViewDF">
             <select name="cmbTipoDocumentoId" id="cmbTipoDocumentoId" style="width:350px">
               	<?php while ($lookup_tipo = mysql_fetch_object($dados_tipo)) { ?>
               <option <?php if ($lookup_tipo->id == $campos[tipo_documento_id]) {
                      echo " selected ";
                    } ?>
                   value="<?php echo $lookup_tipo->id ?>"><?php echo $lookup_tipo->nome ?>				 
            	 </option>
              <?php } ?>
             </select>						 
            </td>
          </tr>
          <tr>
            <td class="dataLabel">Descrição:</td>
            <td colspan="5" class='tabDetailViewDF'>
               <input name="edtDescricao" type="text" class='datafield' id="edtDescricao" style="width: 450px; color: #6666CC; font-weight: bold" maxlength="75" value="<?php echo $campos[descricao] ?>" />
            </td>
          </tr>          
          <tr>
            <td valign="top" class="dataLabel">Observações:</td>
            <td colspan="5" class="tabDetailViewDF">
						   <textarea name="edtObservacoes" wrap="virtual" class="datafield" id="edtObservacoes" style="width: 100%; height: 130px"><?php echo $campos[observacoes] ?></textarea>
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
