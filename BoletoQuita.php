<?php 
###########
## Módulo para quitação dos boletos emitidos
## Criado: 16/02/2010 - Maycon Edinger
## Alterado:
## Alterações:
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET['headers'] == 1) 
{
	header('Content-Type: text/html;  charset=ISO-8859-1',true);
}

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require('Diretivas.php');

//Estabelece a conexão com o banco de dados
include './conexao/ConexaoMySQL.php';

//Inclui o arquivo para manipulação de datas
include './include/ManipulaDatas.php';

//Inclui o arquivo para manipulação de valor monetário
include './include/ManipulaMoney.php';

//Recupera o id do boleto
if ($_GET['BoletoId']) {
	$BoletoId = $_GET['BoletoId'];
} else {
  $BoletoId = $_POST['Id'];
}
   
   
//Adiciona o acesso a entidade de criação do componente data
include('CalendarioPopUp.php');
//Cria um objeto do componente data
$objData = new tipData();
//Define que não deve exibir a hora no calendario
$objData->bolExibirHora = false;
//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript();

?>

<script language="JavaScript">

function wdSubmitBoletoQuita() {
   var Form;
   Form = document.frmBoletoQuita;
   if (Form.edtDataRecebimento.value.length == 0) {
      alert("É necessário Informar a Data de Recebimento !");
      Form.edtDataRecebimento.focus();
      return false;
   }

   if (Form.edtValorRecebido.value.length == 0) {
      alert("É necessário Informar o Valor Recebido !");
      Form.edtValorRecebido.focus();
      return false;
   }
	 	
   return true;
}

</script>

<form name="frmBoletoQuita" action="sistema.php?ModuloNome=BoletoQuita" method="post" onsubmit="return wdSubmitBoletoQuita()">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Quitação de Boleto</span></td>
			  </tr>
			  <tr>
			    <td>
				    <img src="image/bt_espacohoriz.gif" width="100%" height="12">
				  </td>
			  </tr>
			</table>

			<table width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
			  <tr>
			    <td width="100%" class="text">

          <?php
				
				//Verifica se a flag está vindo de uma postagem para liberar a alteração
				if($_POST['Submit'])
				{

  					//Recupera os valores do formulario e alimenta as variáveis
					$data_atualizacao = date("Y-m-d", mktime());
					
  					$id = $_POST['Id'];
					$ContaReceberId = $_POST['ContaReceberId'];
              
					$edtDataRecebimento = DataMySQLInserir($_POST['edtDataRecebimento']);
					$total_valor_recebido = MoneyMySQLInserir($_POST['edtValorRecebido']);												
					$edtObservacoes = $_POST['edtObservacoes'];
  							               
					//Faz o update do valor recebido da conta							
  					$sql = mysql_query("UPDATE boleto SET 
                                  boleto_recebido = 1,
                                  data_recebimento = '$edtDataRecebimento',
                                  valor_recebido = '$total_valor_recebido',
                                  obs_recebimento = '$edtObservacoes',
                                  usuario_recebimento_id = $usuarioId,
								  data_atualizacao = '$data_atualizacao'
                                  WHERE id = $id");
              
              
					//Gera um lançamento de recebimento para a conta a receber vinculada
					$sql = mysql_query("INSERT INTO contas_receber_recebimento (
																conta_receber_id,
																data_recebimento, 
																tipo_recebimento,
																total_recebido,
																obs 
																) values (				
																'$ContaReceberId',
																'$edtDataRecebimento',
																'4',
																'$total_valor_recebido',
																'$edtObservacoes'
																);");
              
              //Efetua a baixa da conta a receber vinculada
  						$sql = mysql_query("UPDATE contas_receber SET 
                                  situacao = 2, 
                                  valor_recebido = '$total_valor_recebido'
                                  WHERE id = $ContaReceberId");
  								
                
              //Exibe a mensagem de alteração com sucesso
  		        echo "<table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Boleto baixado com sucesso !<br/>A conta a receber foi marcada como quitada.</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td>";																																															
							
									
           }
           
           //Monta o sql para recuperar os dados do boleto
          $sql="SELECT * FROM boleto		
          	  	WHERE id = $BoletoId";		
          			   
          //Executa a query
          $resultado = mysql_query($sql);
          
          //Monta o array dos dados
          $campos = mysql_fetch_array($resultado);
          
          //Efetua o switch para o campo de situacao
          switch ($campos['boleto_recebido']) 
          {
            
            case 0: $desc_situacao = 'Em aberto'; break;
          	case 1: $desc_situacao = 'Recebido'; break;
          
          } 
          
          $user_boleto_id = $campos['usuario_recebimento_id'];
          
          //Pega os dados do usuario que baixou o boleto
          //Poderia ter feito com join, mas dava muito trabalho
          $sql_usuario = "SELECT CONCAT(nome, ' ', sobrenome) AS usuario_nome FROM usuarios	WHERE usuario_id = $user_boleto_id";		
          			   
          //Executa a query
          $resultado_usuario = mysql_query($sql_usuario);
          
          //Monta o array dos dados
          $campos_usuario = mysql_fetch_array($resultado_usuario);
					
        ?>
      <table cellspacing="0" cellpadding="0" width="100%" border="0">
        <tr>
      		<td style="padding-bottom: 2px">
						<input class="button" title="Retorna a exibição dos detalhes da conta a receber" name="btnVoltar" type="button" id="btnVoltar" value="Retornar a Exibição da Conta a Receber" onclick="wdCarregarFormulario('ContaReceberExibe.php?ContaId=<?php echo $campos['conta_receber_id'] ?>','conteudo')" />					
					</td>
	  		</tr>
    	</table>	

    	<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
         <tr>
           <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="22">
             <table cellspacing="0" cellpadding="0" width="100%" border="0">
               <tr>
                 <td class="tabDetailViewDL" style="TEXT-ALIGN: left">
                   <img src="image/bt_cadastro.gif" width="16" height="15" />
                   &nbsp;Informe os dados para a baixa do boleto [Efetuar Baixa do Boleto] 
                   <br />
                   <span style="color: #990000;"><b>ATENÇÃO: </b>Ao efetuar a quitação do boleto, a conta a receber geradora deste boleto também será marcada como recebida.</span>
                 </td>
		           </tr>
	           </table>             
		       </td>
         </tr>
         <tr>
           <td width="140" class="dataLabel">
             <span class="dataLabel">Data Documento:</span>             
           </td>
           <td colspan="4" class="tabDetailViewDF">
							<?php echo DataMySQLRetornar($campos['data_documento']) ?>
				   </td>
         </tr>
         <tr>
           <td width="140" class="dataLabel">
             <span class="dataLabel">Data Processamento:</span>             
           </td>
           <td colspan="4" class="tabDetailViewDF">
							<?php echo DataMySQLRetornar($campos['data_processamento']) ?>
				   </td>
         </tr>
         <tr>
           <td width="140" class="dataLabel">Nº do Documento:</td>
           <td colspan="4" valign="middle" class="tabDetailViewDF">
             <strong><?php echo $campos['numero_documento'] ?></strong>
					 </td>
        </tr>
				           
         <tr>
           <td width="140" class="dataLabel">Nosso Número:</td>
           <td colspan="4" valign="middle" class="tabDetailViewDF">
             <strong><?php echo $campos['nosso_numero'] ?></strong>
					 </td>
         </tr>           
				 <tr>
           <td width="140" class="dataLabel">Sacado:</td>
           <td colspan="4" valign="middle" class="tabDetailViewDF">
             <?php echo $campos['sacado'] ?>
					 </td>
         </tr>           
				 <tr>
           <td width="140" class="dataLabel">Demonstrativo:</td>
           <td colspan="4" valign="middle" class="tabDetailViewDF">
             <?php echo $campos['demonstrativo1'] . '<br/>' . $campos['demonstrativo2'] . '<br/>' . $campos['demonstrativo3'] ?>
					 </td>
         </tr>           
				 <tr>
           <td width="140" class="dataLabel">Instruçoes:</td>
           <td colspan="4" valign="middle" class="tabDetailViewDF">
             <?php echo $campos['instrucoes1'] . '<br/>' . $campos['instrucoes2'] . '<br/>' . $campos['instrucoes3'] . '<br/>' . $campos['instrucoes4'] ?>
					 </td>
         </tr>

        <tr>
          <td width="140" valign="top" class="dataLabel">Valor a Receber: </td>
          <td width="173" class="tabDetailViewDF">
             <?php echo 'R$ ' . number_format($campos['valor_boleto'], 2, ',', '.') ?>
					</td>
          <td width="146" class="dataLabel">Data Vencimento:</td>
          <td colspan="2" class="tabDetailViewDF">
             <?php echo DataMySQLRetornar($campos['data_vencimento']) ?>
					</td>
        </tr>
      <table>
	  </td>
  </tr>
</table>

<?php 

//Verifica se o boleto já não foi pago
if ($campos['boleto_recebido'] == 0)
{
  
?>

<br/>			
<table cellspacing="0" cellpadding="0" width="100%" border="0">
  <tr>
		<td style="padding-bottom: 2px">
			<input name="Id" type="hidden" value="<?php echo $BoletoId ?>" />
      <input name="ContaReceberId" type="hidden" value="<?php echo $campos['conta_receber_id'] ?>" />
  		<input name="Submit" type="submit" class="button" title="Efetua o recebimento e dá baixa neste boleto" value="Efetuar Baixa do Boleto" />
  	</td>						
	</tr>
</table>				
<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
  <tr>
    <td width="140" class="dataLabel">Data Recebimento:</td>
    <td class="tabDetailViewDF">
      <?php
			    
          $dataRecebe = DataMySQLRetornar($campos['data_vencimento']);
          
          //Define a data do formul&aacute;rio
			    $objData->strFormulario = 'frmBoletoQuita';  
			    //Nome do campo que deve ser criado
			    $objData->strNome = 'edtDataRecebimento';
          $objData->strRequerido = true;
			    //Valor a constar dentro do campo (p/ altera&ccedil;&atilde;o)
			    $objData->strValor = $dataRecebe;
			    //Define o tamanho do campo 
			    //$objData->intTamanho = 15;
			    //Define o n&uacute;mero maximo de caracteres
			    //$objData->intMaximoCaracter = 20;
			    //define o tamanho da tela do calendario
			    //$objData->intTamanhoCalendario = 200;
			    //Cria o componente com seu calendario para escolha da data
			    $objData->CriarData();
			?>
		</td>
  </tr>
  <tr>
    <td class="dataLabel">Valor Recebido: </td>
    <td class="tabDetailViewDF">
			<?php
			//Acerta a variável com o valor a alterar
			$valor_alterar = str_replace('.',',',$campos['valor_boleto']);
			
			//Cria um objeto do tipo WDEdit 
			$objWDComponente = new WDEditReal();
			
			//Define nome do componente
			$objWDComponente->strNome = 'edtValorRecebido';
			//Define o tamanho do componente
			$objWDComponente->intSize = 16;
			//Busca valor definido no XML para o componente
			$objWDComponente->strValor = "$valor_alterar";
			//Busca a descrição do XML para o componente
			$objWDComponente->strLabel = '';
			//Determina um ou mais eventos para o componente
			$objWDComponente->strEvento = '';
			//Define numero de caracteres no componente
			$objWDComponente->intMaxLength = 14;
			
			//Cria o componente edit
			$objWDComponente->Criar();  
			?>               
		</td>
  </tr>
  <tr>
    <td valign="top" class="dataLabel">Observações:</td>
    <td valign="middle" class="tabDetailViewDF">
      <textarea name="edtObservacoes" wrap="virtual" class="datafield" id="edtObservacoes" style="width: 100%; height: 80px"></textarea>
    </td>
  </tr>       				                           	
</table>

<?php

}

else

{

?>
<br/>
<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
  <tr>
    <td height="22" width="20" valign="middle" bgcolor="#FFFFCD" style="border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px">
      <img src="./image/bt_informacao.gif" border="0" />
    </td>
    <td valign="middle" bgcolor="#FFFFCD" style="border: solid 1px; padding-left: 4px; border-left: 0px">
      <strong>Este boleto já foi baixado e a conta receber quitada !.</strong>
    </td>
  </tr>
  <tr>
    <td colspan="2">&nbsp</td>
  </tr>
</table>
</td></tr><tr><td>

<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">  
  <tr>
    <td width="140" valign="top" class="dataLabel">Data Baixa:</td>
    <td class="tabDetailViewDF">
       <?php echo DataMySQLRetornar($campos['data_recebimento']) ?>
		</td>          
  </tr>
  <tr>
    <td valign="top" class="dataLabel">Usuário:</td>
    <td class="tabDetailViewDF">
       <?php echo $campos_usuario['usuario_nome'] ?>
		</td>          
  </tr>
   <tr>
    <td class="dataLabel">Valor:</td>
    <td class="tabDetailViewDF">       
       <?php echo 'R$ ' . number_format($campos['valor_recebido'], 2, ',', '.') ?>
		</td>          
  </tr>  
  <tr>
    <td valign="top" class="dataLabel">Obs. Recebimento: </td>
    <td class="tabDetailViewDF">
       <?php echo nl2br($campos['obs_recebimento']) ?>
		</td>          
  </tr>
</table> 

<?php
  
}

?>

	  	</td>
	  </tr>	  
	</table>
	</td>
  </tr>
</table>
</form>