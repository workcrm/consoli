<?php 
###########
## Módulo para cadastro de Formandos do evento
## Criado: 14/10/2008 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) 
{
  header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

// Processa as diretivas de segurança 
require("Diretivas.php");

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Inclui o arquivo para manipulação de valor monetário
include "./include/ManipulaMoney.php";

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Adiciona o acesso a entidade de criação do componente data
include('CalendarioPopUp.php');  

//Cria um objeto do componente data
$objData = new tipData();

//Define que não deve exibir a hora no calendario
$objData->bolExibirHora = false;

//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript(); 

//Recupera o id do evento
if($_POST) 
{
	
  $EventoId = $_POST["EventoId"]; 

} 

else 

{
	
  $EventoId = $_GET["EventoId"]; 

}

//Converte uma data timestamp de mysql para normal
function TimestampMySQLRetornar($DATA)
{
  $ANO = 0000;
  $MES = 00;
  $DIA = 00;
  $HORA = "00:00:00";
  $data_array = split("[- ]",$DATA);

  if ($DATA <> "")
  {
    $ANO = $data_array[0];
    $MES = $data_array[1];
    $DIA = $data_array[2];
    $HORA = $data_array[3];
    return $DIA."/".$MES."/".$ANO. " - " . $HORA;

  }
  else 
  {
    $ANO = 0000;
    $MES = 00;
    $DIA = 00;
    return $DIA."/".$MES."/".$ANO;
  }
}

//pesquisa as diretivas do usuário
$sql_usuario = "SELECT * FROM usuarios WHERE usuario_id = $usuarioId";													  													  
							  
//Executa a query
$resultado_usuario = mysql_query($sql_usuario);

//Monta o array dos campos
$dados_usuario = mysql_fetch_array($resultado_usuario);


//Monta o lookup da tabela de cidades
//Monta o SQL
$lista_cidade = "SELECT * FROM cidades WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";

//Executa a query
$dados_cidade = mysql_query($lista_cidade);

//Monta o lookup da tabela de cursos
//Monta o SQL
$lista_curso = "SELECT * FROM cursos WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";

//Executa a query
$dados_curso = mysql_query($lista_curso);

//Recupera dos dados do evento
$sql_evento = mysql_query("SELECT 
                          eve.id,
                          eve.nome,
                          eve.descricao,
                          eve.status,
                          eve.cliente_id,
                          eve.grupo_id,
                          eve.responsavel,
                          eve.contato1,
                          eve.contato_obs1,
                          eve.contato_fone1,
                          eve.contato2,
                          eve.contato_obs2,
                          eve.contato_fone2,
                          eve.contato3,
                          eve.contato_obs3,
                          eve.contato_fone3,													
                          eve.data_realizacao,
                          eve.hora_realizacao,
                          eve.duracao,
                          eve.formandos_timestamp,
                          eve.formandos_operador_id,
                          concat(usu.nome , ' ', usu.sobrenome) as operador_nome,
                          cli.nome as cliente_nome,
                          gru.nome as grupo_nome
                          FROM eventos eve 
                          INNER JOIN clientes cli ON cli.id = eve.cliente_id
                          LEFT OUTER JOIN grupo_conta gru ON gru.id = eve.grupo_id
                          LEFT OUTER JOIN usuarios usu ON usu.usuario_id = eve.formandos_operador_id
                          WHERE eve.id = '$EventoId'");

//Cria o array dos dados
$dados_evento = mysql_fetch_array($sql_evento);

//Efetua o switch para o campo de status
switch ($dados_evento[status]) 
{
  case 0: $desc_status = "Em orçamento"; break;
  case 1: $desc_status = "Em aberto"; break;
  case 2: $desc_status = "Realizado"; break;
  case 3: $desc_status = "<span style='color: red'>Não-Realizado</span>"; break;
} 

?>

<script language="JavaScript">

function VerificaCPF() 
{
 
  cpf = document.cadastro.edtCpf.value;
  cpf = remove(cpf, ".");
  cpf = remove(cpf, "-");

  if (cpf.length > 0)
  {

    if (vercpf(cpf)) 
    {

      return true;

    } 
    else 
    {

      errors="1";

      if (errors) alert('O CPF informado é Inválido');

      document.retorno = (errors == '');

    }

  }
}

function vercpf(cpf) 
{
  
  if (cpf.length != 11 || cpf == "00000000000" || cpf == "11111111111" || cpf == "22222222222" || cpf == "33333333333" || cpf == "44444444444" || cpf == "55555555555" || cpf == "66666666666" || cpf == "77777777777" || cpf == "88888888888" || cpf == "99999999999")
  return false;
  add = 0;

  for (i=0; i < 9; i ++)
  add += parseInt(cpf.charAt(i)) * (10 - i);
  rev = 11 - (add % 11);

  if (rev == 10 || rev == 11)
  rev = 0;

  if (rev != parseInt(cpf.charAt(9)))
  return false;
  add = 0;
  for (i = 0; i < 10; i ++)
  add += parseInt(cpf.charAt(i)) * (11 - i);
  rev = 11 - (add % 11);
  if (rev == 10 || rev == 11)
  rev = 0;
  if (rev != parseInt(cpf.charAt(10)))
  return false;
  return true;
  
}

function remove(str, sub) 
{
  i = str.indexOf(sub);
  r = "";
  if (i == -1) return str;
  r += str.substring(0,i) + remove(str.substring(i + sub.length), sub);
  return r;
 }

//Função que alterna a visibilidade do painel especificado.
function oculta(id)
{
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function valida_form() 
{
  var Form;
  Form = document.cadastro;

  if (Form.edtCpf.value.length == 0) 
  {
    alert("É necessário informar o cpf do formando !");
    Form.edtCpf.focus();
    return false;
  }

  if (Form.edtNome.value.length == 0) 
  {
    alert("É necessário informar o nome do formando !");
    Form.edtNome.focus();
    return false;
  }

  if (Form.cmbCidadeId.value == 0) 
  {
    alert("É necessário selecionar a cidade do formando !");
    return false;
  }

  return true;

}
</script>
<link rel="stylesheet" type="text/css" href="include/workStyle.css">
</head>
<body>

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
  <tr>
    <td class="text" valign="top">
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Cadastramento de Formandos do Evento</span></td>
        </tr>
        <tr>
          <td colspan="5">
            <img src="image/bt_espacohoriz.gif" width="100%" height="12">
          </td>
        </tr>
      </table>

        <table width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="100%" class="text">

              <?php
              //Recupera os valores vindos do formulário e armazena nas variaveis
              if ($_POST["Submit"])
              {

                $edtEmpresaId = $empresaId;
                $edtEventoId = $_POST["EventoId"];
                $edtNome = $_POST["edtNome"];
                $cmbCursoId = $_POST["cmbCursoId"];
                $edtCpf = $_POST["edtCpf"];
                $edtDataNascimento = DataMySQLInserir($_POST["edtDataNascimento"]);
                $edtEndereco = $_POST["edtEndereco"];
                $edtComplemento = $_POST["edtComplemento"];
                $edtBairro = $_POST["edtBairro"];
                $cmbCidadeId = $_POST["cmbCidadeId"];
                $edtUf = $_POST["edtUf"];
                $edtCep = $_POST["edtCep"];
                $edtContato = $_POST["edtTelefone"];
                $edtOperadora = $_POST["edtOperadora"];
                $edtTelefoneComercial = $_POST["edtTelefoneComercial"];
                $edtTelefoneResidencial = $_POST["edtTelefoneResidencial"];
                $edtEmail = $_POST["edtEmail"];
                $edtStatus = $_POST["edtStatus"];
                $edtStatusFotoVideo = $_POST["edtStatusFotoVideo"];
                $edtObservacoes = $_POST["edtObservacoes"];

                //Parte financeira
                $edtSituacao = $_POST["edtSituacao"];
                $edtValorCapital = MoneyMySQLInserir($_POST["edtValorCapital"]);
                $edtValorContratado = MoneyMySQLInserir($_POST["edtValorContratado"]);
                $edtValorPago = MoneyMySQLInserir($_POST["edtValorPago"]);
                $edtValorPendente = MoneyMySQLInserir($_POST["edtValorPendente"]);
                $edtObsFinanceiro = $_POST["edtObsFinanceiro"];

                //Captura os 5 ultimos numeros do CPF
                $PegaDigitos = substr($edtCpf, (strlen($edtCpf) - 5), strlen($edtCpf));

                $senha_formando = str_replace("-", "", $PegaDigitos);

                //Participacoes
                $chkCulto = $_POST["chkCulto"];
                $chkColacao = $_POST["chkColacao"];
                $chkJantar = $_POST["chkJantar"];
                $chkBaile = $_POST["chkBaile"];

                $chkSpc = $_POST["chkSpc"];
                $chkAcaoCobranca = $_POST["chkAcaoCobranca"];
                $chkAssessoriaCobranca = $_POST["chkAssessoriaCobranca"];
                
                $edtAvalistaNome = $_POST["edtAvalistaNome"];
                $edtAvalistaCpf = $_POST["edtAvalistaCpf"];
                $edtAvalistaTelefone = $_POST["edtAvalistaTelefone"];
                $edtAvalistaEndereco = $_POST["edtAvalistaEndereco"];

                //Monta o sql e executa a query de inserção
                $sql = mysql_query("INSERT INTO eventos_formando (
                                    senha,
                                    empresa_id, 
                                    evento_id,
                                    nome,
                                    curso_id,
                                    cpf,
                                    endereco,
                                    complemento,
                                    bairro,
                                    cidade_id,
                                    uf,
                                    cep,
                                    contato,
                                    operadora,
                                    telefone_comercial,
                                    telefone_residencial,
                                    email,								
                                    observacoes,
                                    status,
                                    cadastro_timestamp,
                                    cadastro_operador_id,
                                    situacao,
                                    obs_financeiro,
                                    valor_capital,
                                    data_nascimento,
                                    chk_culto,
                                    chk_colacao,
                                    chk_jantar,
                                    chk_baile,
                                    chk_spc,
                                    chk_acao_cobranca,
                                    chk_assessoria_cobranca,
                                    status_fotovideo,
                                    avalista_nome,
                                    avalista_cpf,
                                    avalista_telefone,
                                    avalista_endereco

                                    ) VALUES (

                                    '$senha_formando',
                                    '$edtEmpresaId',
                                    '$edtEventoId',
                                    '$edtNome',
                                    '$cmbCursoId',
                                    '$edtCpf',
                                    '$edtEndereco',
                                    '$edtComplemento',
                                    '$edtBairro',
                                    '$cmbCidadeId',
                                    '$edtUf',
                                    '$edtCep',
                                    '$edtContato',
                                    '$edtOperadora',
                                    '$edtTelefoneComercial',
                                    '$edtTelefoneResidencial',
                                    '$edtEmail',
                                    '$edtObservacoes',
                                    '$edtStatus',
                                    now(),
                                    '$operadorId',
                                    '$edtSituacao',
                                    '$edtObsFinanceiro',
                                    '$edtValorCapital',
                                    '$edtDataNascimento',
                                    '$chkCulto',
                                    '$chkColacao',
                                    '$chkJantar',
                                    '$chkBaile',
                                    '$chkSpc',
                                    '$chkAcaoCobranca',
                                    '$chkAssessoriaCobranca',
                                    '$edtStatusFotoVideo',
                                    '$edtAvalistaNome',
                                    '$edtAvalistaCpf',
                                    '$edtAvalistaTelefone',
                                    '$edtAvalistaEndereco'
                                    );");

                //Configura a assinatura digital
                $sql = mysql_query("UPDATE eventos SET formandos_timestamp = now(), formandos_operador_id = $usuarioId WHERE id = $edtEventoId");

                //Exibe a mensagem de inclusão com sucesso
                echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Formando cadastrado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div>";
              
              }
              ?>
              <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
                <tr>
                  <td class="dataLabel" width="15%"> Nome do Evento:</td>
                  <td colspan="5" class="tabDetailViewDF">
                    <span style="font-size: 16px; color: #990000"><b><?php echo $dados_evento["nome"] ?></b></span>
                  </td>
                </tr>
                <tr>
                  <td valign="top" class="dataLabel">Descri&ccedil;&atilde;o:</td>
                  <td colspan="5" valign="middle" class="tabDetailViewDF">
                    <?php echo $dados_evento["descricao"] ?>
                  </td>
                </tr>
                <tr>
                  <td valign="top" class="dataLabel">Status:</td>
                  <td colspan="5" valign="middle" class="tabDetailViewDF">
                    <?php echo $desc_status ?>
                  </td>
                </tr>
                <tr>
                  <td valign="top" class="dataLabel">Data:</td>
                  <td valign="middle" class="tabDetailViewDF">
                    <?php echo DataMySQLRetornar($dados_evento["data_realizacao"]) ?>
                  </td>
                  <td valign="middle" class="dataLabel">Hora:</td>
                  <td width="19%" valign="middle" class="tabDetailViewDF">
                    <?php echo $dados_evento["hora_realizacao"] ?>								
                  </td>
                  <td width="12%" valign="middle" class="dataLabel">Dura&ccedil;&atilde;o:</td>
                  <td width="20%" valign="middle" class="tabDetailViewDF">
                    <?php echo $dados_evento["duracao"] ?>								
                  </td>
                </tr>
                <tr>
                  <td valign="top" class="dataLabel">Cliente:</td>
                  <td colspan="5" valign="middle" class="tabDetailViewDF">
                    <?php echo $dados_evento["cliente_nome"] ?>
                  </td>
                </tr>
                <tr>
                  <td valign="top" class="dataLabel">Grupo:</td>
                  <td colspan="5" valign="middle" class="tabDetailViewDF">
                    <?php echo $dados_evento["grupo_nome"] ?>
                  </td>
                </tr>              
                <tr>
                  <td class="dataLabel">Respons&aacute;vel:</td>
                  <td colspan="5" valign="middle" class="tabDetailViewDF">
                    <?php echo $dados_evento["responsavel"] ?>								
                  </td>
                </tr>
                <tr>
                  <td valign="top" class="dataLabel">Contatos:</td>
                  <td colspan="5" valign="middle" class="tabDetailViewDF">
                    <table width="100%" cellpadding="0" cellspacing="0">
                      <tr valign="middle">
                        <td width="300" height="20">
                          Nome:
                        </td>
                        <td width="260" height="20">
                          Observações:
                        </td>
                        <td height="20">
                          Telefone:
                        </td>
                      </tr>
                      <tr valign="middle">
                        <td height="20">
                          <span style="font-size: 12px"><?php echo $dados_evento[contato1] ?></span>
                        </td>
                        <td height="20">
                          <span style="font-size: 12px"><?php echo $dados_evento[contato_obs1] ?></span>
                        </td>
                        <td height="20">
                          <span style="font-size: 12px"><?php echo $dados_evento[contato_fone1] ?></span>
                        </td>
                      </tr>
                      <tr valign="middle">
                        <td height="20">
                          <span style="font-size: 12px"><?php echo $dados_evento[contato2] ?></span>                   
                        </td>
                        <td height="20">
                          <span style="font-size: 12px"><?php echo $dados_evento[contato_obs2] ?></span>
                        </td>
                        <td height="20">
                          <span style="font-size: 12px"><?php echo $dados_evento[contato_fone2] ?></span>
                        </td>
                      </tr>
                      <tr valign="middle">
                        <td height="20">
                          <span style="font-size: 12px"><?php echo $dados_evento[contato3] ?></span>
                        </td>
                        <td height="20">
                          <span style="font-size: 12px"><?php echo $dados_evento[contato_obs3] ?></span>
                        </td>
                        <td height="20">
                          <span style="font-size: 12px"><?php echo $dados_evento[contato_fone3] ?></span>
                        </td>
                      </tr>                 
                    </table>               							 
                  </td>
                </tr>
                <tr>
                  <td colspan="6" valign="middle" class="tabDetailViewDF">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0" >
                      <tr valign="middle">
                        <td colspan="8" style="padding-bottom: 4px">
                          <span style="font-size: 11px">Tarefas Adicionais:</span>
                        </td>
                      </tr>
                      <tr valign="middle">
                        <td width="30">
                          <img src="./image/bt_evento_gd.gif"/> 
                        </td>                			
                        <td width="85">
                          <a title="Clique para exibir o detalhamento deste evento" href="#" onclick="wdCarregarFormulario('EventoExibe.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Detalhamento</a>
                        </td>
                        <td width="30">
                          <img src="./image/bt_data_evento_gd.gif" /> 
                        </td>
                        <td width="85">
                          <a title="Clique para gerenciar as datas deste evento" href="#" onclick="wdCarregarFormulario('DataEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Datas</a>
                        </td>
                        <td width="30">
                          <img src="./image/bt_participante_gd.gif" /> 
                        </td>
                        <td width="85">
                          <a title="Clique para gerenciar os participantes deste evento" href="#" onclick="wdCarregarFormulario('ParticipanteEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Participantes</a>
                        </td>
                        <td width="30">
                          <img src="./image/bt_endereco_gd.gif" /> 
                        </td>
                        <td width="85">
                          <a title="Clique para gerenciar os endereços deste evento" href="#" onclick="wdCarregarFormulario('EnderecoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Endereços</a>
                        </td>
                        <td width="30">
                          <img src="./image/bt_item_gd.gif"/> 
                        </td>
                        <td width="85">
                          <a title="Clique para gerenciar os produtos deste evento" href="#" onclick="wdCarregarFormulario('ItemEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Produtos</a> 
                        </td>
                        <td width="30">
                          <img src="./image/bt_servico_gd.gif"/> 
                        </td>
                        <td width="85">
                          <a title="Clique para gerenciar os serviços deste evento" href="#" onclick="wdCarregarFormulario('ServicoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Serviços</a> 
                        </td>
                        <td width="30">
                          <img src="./image/bt_terceiro_gd.gif"/> 
                        </td>
                        <td width="85">
                          <a title="Clique para gerenciar os terceiros deste evento" href="#" onclick="wdCarregarFormulario('TerceiroEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Terceiros</a> 
                        </td>                			
                      </tr>
                      <tr>
                        <td colspan="2">
                          &nbsp;
                        </td>
                        <td width="30">
                          <img src="./image/bt_brinde_gd.gif"/> 
                        </td>
                        <td width="85">
                          <a title="Clique para gerenciar os brindes deste evento" href="#" onclick="wdCarregarFormulario('BrindeEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Brindes</a> 
                        </td>
                        <td width="30">
                          <img src="./image/bt_repertorio_gd.gif" /> 
                        </td>
                        <td width="85">
                          <a title="Clique para gerenciar o repertório deste evento" href="#" onclick="wdCarregarFormulario('RepertorioEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Repertório</a>
                        </td>				 
                        <td width="30">
                          <img src="./image/bt_fotovideo_gd.gif" /> 
                        </td>
                        <td>
                          <a title="Clique para gerenciar o foto e vídeo deste evento" href="#" onclick="wdCarregarFormulario('FotoVideoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Foto e Vídeo</a>
                        </td>
                        <td width="30">
                          <img src="./image/bt_documentos_gd.gif" /> 
                        </td>
                        <td colspan="4">
                          <a title="Clique para gerenciar os documentos deste evento" href="#" onclick="wdCarregarFormulario('DocumentosEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Documentos</a>
                        </td> 										
                      </tr>              			
                    </table>
                  </td>
                </tr>              
              </table>

              <?php
              //verifica se o usuário pode ver este submodulo
              if ($dados_usuario["evento_formando_inclui"] == 0)
              {

                die("<br/><b>Seu nível de acesso não permite efetuar modificações neste módulo !</b>");
              }

              //verifica os formandos já cadastrados para este evento e exibe na tela
              $sql_consulta = mysql_query("SELECT
                                          form.id,
                                          form.senha,
                                          form.cpf,
                                          form.status,
                                          form.situacao,
                                          form.nome,
                                          form.curso_id,
                                          form.email,
                                          form.contato,
                                          form.operadora,
                                          form.observacoes,
                                          form.chk_culto,
                                          form.chk_colacao,
                                          form.chk_jantar,
                                          form.chk_baile,
                                          form.status_fotovideo,
                                          curso.nome AS curso_nome,
                                          curso.id AS curso_id 
                                          FROM eventos_formando form
                                          LEFT OUTER JOIN cursos curso ON curso.id = form.curso_id
                                          WHERE form.evento_id = $EventoId
                                          ORDER BY form.nome");

              $registros = mysql_num_rows($sql_consulta);
              ?>

              <form id="form" name="cadastro" action="sistema.php?ModuloNome=FormandoEventoCadastra" method="post" onsubmit="return valida_form()">

                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                  <tr>  
                    <td>
                      <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                          <td width="440"><br/><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Formandos Cadastrados para o Evento:</span></td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <table id="4" width="100%" align="left" border="0" cellspacing="0" cellpadding="0" class="listView">
                        <?php

                          //Caso houverem registros
                          if ($registros > 0)
                          {

                            //Exibe o cabeçalho da tabela
                            echo "<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
                                    <td width='22'>&nbsp;&nbsp;S</td>
                                    <td width='50' align='center'>Part.</td>
                                    <td style='padding-left: 5px'>Formando</td>
                                    <td width='140'>Email</td>
                                    <td width='40' align='center'>Senha</td>	      
                                    <td width='70' align='center'>Ações</td>
                                  </tr>";
                            
                          }

                          //Caso não houverem registros
                          if ($registros == 0)
                          {

                            //Exibe uma linha dizendo que nao registros
                            echo "<tr height='24'>
                                    <td colspan='8' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1'>
                                      <font color='#33485C'><strong>Não há formandos cadastrados para este evento</strong></font>
                                    </td>
                                  </tr>";
                            
                          }

                          $string_email = "";

                          //Cria o array e o percorre para montar a listagem dinamicamente
                          while ($dados_consulta = mysql_fetch_array($sql_consulta))
                          {

                            //Efetua o switch para o campo de status
                            switch ($dados_consulta[status])
                            {
                              case 1: $desc_status = "<img src='image/bt_a_formar.png' alt='A se formar'>"; break;
                              case 2: $desc_status = "<img src='image/bt_formado.png' alt='Formado'>"; break;
                              case 3: $desc_status = "<img src='image/bt_desistente.png' alt='Desistente'>"; break;
                              case 4: $desc_status = "<img src='image/bt_pendencia.gif' alt='Aguardando Declaração de Rescisão'>"; break;
                            }


                            $desc_operadora = '';

                            //Efetua o switch para o campo de operadora
                            switch ($dados_consulta[operadora])
                            {
                              case 0: $desc_operadora = " - <span style='color: #990000'>(Operadora não Informada)</span>"; break;
                              case 1: $desc_operadora = " - <span style='color: #990000'>(VIVO)</span>"; break;
                              case 2: $desc_operadora = " - <span style='color: #990000'>(TIM)</span>"; break;
                              case 3: $desc_operadora = " - <span style='color: #990000'>(Claro)</span>"; break;
                              case 4: $desc_operadora = " - <span style='color: #990000'>(Oi)</span>"; break;
                            }

                            $desc_participante = "";

                            if ($dados_consulta["chk_culto"] == 1)
                            {

                              $desc_participante .= "<span title='Formando Participa do Culto'>M</span>&nbsp;";
                            }

                            if ($dados_consulta["chk_colacao"] == 1)
                            {

                              $desc_participante .= "<span title='Formando Participa da Colação'>C</span>&nbsp;";
                            }

                            if ($dados_consulta["chk_jantar"] == 1)
                            {

                              $desc_participante .= "<span title='Formando Participa do Jantar'>J</span>&nbsp;";
                            }

                            if ($dados_consulta["chk_baile"] == 1)
                            {

                              $desc_participante .= "<span title='Formando Participa do Baile'>B</span>";
                            }

                            //Se o formando estiver com restricoes financeiras, muda a cor da celula
                            if ($dados_consulta["situacao"] == 2)
                            {

                              $cor_celula = "#F0D9D9";
                            }
                            else
                            {

                              $cor_celula = "#FFFFFF";
                            }
  
                          ?>
                          <tr valign="middle">
                            <td bgcolor="<?php echo $cor_celula ?>" align="center"><?php echo $desc_status ?></td>
                            <td bgcolor="<?php echo $cor_celula ?>" align="center"><span style="color: #6666CC;"><b><?php echo $desc_participante ?></b></span></td>
                            <td bgcolor="<?php echo $cor_celula ?>" valign="middle" bgcolor="#fdfdfd" style="padding-left: 5px;">
                              <?php echo '(' . $dados_consulta['id'] . ') - ' ?><font color="#CC3300" size="2" face="Tahoma"><a title="Clique para alterar os dados deste formando" href="#" onclick="wdCarregarFormulario('FormandoEventoAltera.php?FormandoId=<?php echo $dados_consulta[id] ?>&EventoId=<?php echo $EventoId ?>&headers=1','conteudo')"><?php echo $dados_consulta["nome"]; ?></a></font><br/><b>Celular:</b> <?php echo $dados_consulta["contato"];
                               if ($dados_consulta["contato"] != '') echo $desc_operadora ?><br/><?php echo $dados_consulta["obs_financeiro"]; ?>        
                            </td>
                            <td bgcolor="<?php echo $cor_celula ?>">
                              <a href="mailto:<?php echo $dados_consulta["email"] ?>" title="Clique para enviar um email para o formando"><?php echo $dados_consulta[email] ?></a>&nbsp;
                            </td>			
                            <td bgcolor="<?php echo $cor_celula ?>" valign="middle" bgcolor="#fdfdfd" style="padding-top: 2px" align='center'>
                              <span style="color: #990000;"><?php echo $dados_consulta["senha"] ?></span>
                            </td>											
                            <td bgcolor="<?php echo $cor_celula ?>" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" style="padding-top: 3px;">
                              <img src="image/bt_boleto_avulso.png" alt="Clique para visualizar os boletos deste formando no site" onclick="abreJanela2('http://www.consolieventos.com.br/workeventos/WorkFinanceiro.php?user_login=<?php echo $dados_consulta[cpf] ?>')" style="cursor: pointer">
														  <?php
														  
															  //verifica se o usuário pode ver este menu
															  if ($dados_usuario["menu_financeiro"] == 1 AND $dados_consulta["email"] != "")
															  {

															    ?>	
	                                <img src="image/bt_recado_novo.gif"  alt="Enviar email de aviso de disponibilidade dos boletos para este formando" onclick="if(confirm('Confirma o envio do email para este Formando ?')) {abreJanela('FormandoEventoNotificaBoleto.php?TipoEnvio=2&FormandoId=<?php echo $dados_consulta[id] ?>')}" style="cursor: pointer">
	                                <?php

	                              }

	                              if ($usuarioNome == "Maycon" OR $usuarioNome == "Josiane")
	                              {

	                                ?>
	                                <img src="image/bt_exclui_3.gif" alt="Clique para excluir este formando" onclick="if(confirm('Confirma a exclusão deste Formando ?')) {wdCarregarFormulario('FormandoEventoExclui.php?FormandoId=<?php echo $dados_consulta[id] ?>&EventoId=<?php echo $EventoId ?>','conteudo')}" style="cursor: pointer">
	                                <?php
                              	
                              	}

                              ?>
                            </td>
                          </tr>
                          <tr>
                            <td bgcolor="<?php echo $cor_celula ?>" colspan="2" style="border-bottom: 1px solid;">
                              &nbsp;
                            </td>
                            <td bgcolor="<?php echo $cor_celula ?>" colspan="4" valign="middle" bgcolor="#fdfdfd" style="padding-left: 5px; border-bottom: 1px solid;">
														  <?php

															  //Verifica se existe um curso cadastrado
															  if ($dados_consulta["curso_id"] > 0)
															  {

															    echo "<b>CURSO:</b> <span style='color: #990000'>$dados_consulta[curso_nome]</span><br/>";

															  }

														  ?>
                              <b>OBS:</b> <?php echo $dados_consulta["observacoes"] ?>&nbsp;
                            </td>
                          </tr>
                          <?php

                            if ($dados_consulta["email"] != "")
                            {

                              $string_email .= "$dados_consulta[email];";
                              $conta_email++;

                            }

                          //Fecha o WHILE
                          }

                        ?>
                      </table>
                      <br/>
                    </td>
                  </tr>
                </table>
                <table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView" style="border-top: 0px">
                  <tr>
                    <td height="26" style="padding-left: 4px; padding-top: 4px; padding-bottom: 4px" valign="middle">
                      <img src="image/bt_recado_novo.gif">
                      <span style="font-size: 12px">
												<?php

													//Verifica o numero de emails dos formandos
													if ($conta_email <= 40)
													{

													  ?>
	                          <a href="mailto:<?php echo $string_email ?>" >Enviar email para todos os formandos</a>
													  <?php

													}

													else
													{

													  ?>
	                          <b>Atenção:</b><br/>O número de formandos deste evento é muito grande !<br/><a href="javascript:void(0);" onclick="abreJanela('FormandoEventoListaEmail.php?EventoId=<?php echo $EventoId ?>')">Clique aqui para exibir a relaçao de emails em uma janela separada.</a><br/>Copie e cole os emails em seu gerenciador de emails manualmente.
	                          <?php
	                        
	                        }

                        ?>
                      </span>
                    </td>
                  </tr>
                </table>
                <?php

                //verifica se o usuário pode ver este menu
                if ($dados_usuario["menu_financeiro"] == 1)
                {

                  ?>
                  <table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView" style="border-top: 0px">
                    <tr>
                      <td height="26" style="padding-left: 4px" valign="middle">
                        <img src="image/bt_recado_novo.gif"> <b>Notificação dos formandos - Departamento Financeiro</b>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2" style="padding-left: 4px; padding-bottom: 4px">
                        <input class="button" title="Envia os emails de notificacão de boletos no site" value="Notificar disponibilidade de boletos" name="btnNotifica" type="button" id="btnNotifica" onclick = "abreJanela('FormandoEventoNotificaBoleto.php?TipoEnvio=1&EventoId=<?php echo $EventoId ?>')" >
                      </td>
                    </tr>
                  </table>
                  <?php

                }

                ?>
                <br/>
                <span class="TituloModulo">Assinatura Digital:</span>
                <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">	
                  <tr>
                    <td valign="top" width="120" class="dataLabel">Última Alteração:</td>
                    <td class="tabDetailViewDF">
											<?php

												//Exibe o timestamp do cadastro da conta
												echo TimestampMySQLRetornar($dados_evento[formandos_timestamp])

											?>					
                    </td>
                    <td class="dataLabel">Operador:</td>
                    <td class="tabDetailViewDF" width="200">
											<?php echo $dados_evento["operador_nome"] ?>					
                    </td>
                  </tr>                 
                </table>

                <br/>

                <table cellspacing="0" cellpadding="0" width="520" border="0">
                  <tr>
                    <td style="PADDING-BOTTOM: 2px">
                      <input name="Submit" type="submit" class="button" id="Submit" title="Salva o registro atual" value="Salvar Formando" />
                      <input class="button" title="Limpa o conteúdo dos campos digitados" name="Reset" type="reset" id="Reset" value="Limpar Campos" />
                      <input name="EventoId" type="hidden" value="<?php echo $EventoId ?>" />
                    </td>
                  </tr>         
                </table>

                <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">         
                  <tr>
                    <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colSpan="20">
                      <table cellspacing="0" cellpadding="0" width="100%" border="0">
                        <tr>
                          <td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15" /> Informe os dados do formando do evento e clique em [Salvar Formando]</td>
                        </tr>		      
                      </table>             
                    </td>
                  </tr>
                  <tr>
                    <td class="dataLabel">CPF:</td>
                    <td class="tabDetailViewDF">
                      <input name="edtCpf" type="text" class="requerido" id="edtCpf" size="17" maxlength="14" onkeypress="return FormataCampo(document.cadastro, 'edtCpf', '999.999.999-99', event);" onblur="VerificaCPF();" />
                    </td>
                    <td class="tabDetailViewDF">
                      <input name="BuscaCpf" type="button" class="button" id="BuscaCpf" title="Consulta o CPF na base de formandos cadastrados" value="Consultar CPF" onclick="wdCarregarFormulario('FormandoEventoBuscaCpf.php?cpf=' + edtCpf.value,'busca_cpf',2)" />
                    </td>
                    <td class="tabDetailViewDF"><div id="busca_cpf">&nbsp;</div></td>
                  </tr>
                  <tr>
                    <td class="dataLabel" width="20%">
                      <span class="dataLabel">Nome do Formando :</span>             
                    </td>
                    <td colspan="3" class="tabDetailViewDF">           
                      <input name="edtNome" type="text" class="requerido" id="edtNome" style="width: 350px ;color: #6666CC; font-weight: bold" maxlength="50" />
                    </td>
                  </tr>
                  <tr>
                    <td valign="top" class="dataLabel">Status:</td>
                    <td colspan="5" valign="middle" class="tabDetailViewDF">
                      <table width="100%" cellpadding="0" cellspacing="0">
                        <tr valign="middle">
                          <td width="130" height="20">
                            <input name="edtStatus" type="radio" value="1" checked="checked" />&nbsp;&nbsp;<img src="image/bt_a_formar.png" alt="A se formar" />&nbsp;A se formar
                          </td>
                          <td width="130" height="20">
                            <input name="edtStatus" type="radio" value="2" />&nbsp;&nbsp;<img src="image/bt_formado.png" alt="Formado" />&nbsp;Formado
                          </td>
                          <td width="250" height="20">
                            <input name="edtStatus" type="radio" value="4" />&nbsp;&nbsp;<img src="image/bt_pendencia.gif" alt="Aguardando Declaração de Rescisão" />&nbsp;Aguardando Declaração de Rescisão
                          </td>
                          <td height="20">
                            <input name="edtStatus" type="radio" value="3" />&nbsp;&nbsp;<img src="image/bt_desistente.png" alt="Desistente" />&nbsp;Desistente
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td valign="top" class="dataLabel">Comercial Foto e Vídeo:</td>
                    <td colspan="5" valign="middle" class="tabDetailViewDF">
                      <table width="100%" cellpadding="0" cellspacing="0">
                        <tr valign="middle">
                          <td width="200" height="20">
                            <input name="edtStatusFotoVideo" type="radio" value="1" checked="checked" />&nbsp;&nbsp;Comercializada pela <b>CONSOLI</b>
                          </td>
                          <td width="220" height="20">
                            <input name="edtStatusFotoVideo" type="radio" value="0" />&nbsp;&nbsp;Comercializada por Outra Empresa
                          </td>
                          <td height="20">
                            <input name="edtStatusFotoVideo" type="radio" value="2" />&nbsp;&nbsp;Sem Interesse em FV
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td width="140" valign="top" class="dataLabel">Participante:</td>
                    <td colspan="3" valign="middle" class="tabDetailViewDF">
                      <input name="chkCulto" type="checkbox" id="chkCulto" value="1" checked> Culto<br/>
                      <input name="chkColacao" type="checkbox" id="chkColacao" value="1" checked> Colação<br/>
                      <input name="chkJantar" type="checkbox" id="chkJantar" value="1" checked> Jantar<br/>
                      <input name="chkBaile" type="checkbox" id="chkBaile" value="1" checked> Baile<br/>
                    </td>
                  </tr>
                  <tr>
                    <td class="dataLabel">Curso:</td>
                    <td colspan="3" class="tabDetailViewDF">
                      <select name="cmbCursoId" id="cmbCursoId" style="width:350px">
                        <option value="0">Selecione uma Opção</option>
												<?php

													//Monta o while para gerar o combo de escolha
													while ($lookup_curso = mysql_fetch_object($dados_curso))
													{

													  ?>
                          	<option value="<?php echo $lookup_curso->id ?>"><?php echo $lookup_curso->nome ?> </option>
													  <?php

													}

												?>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td class="dataLabel">Data Nascimento:</td>
                    <td colspan="3" class="tabDetailViewDF">
											<?php

												//Define a data do formulário
												$objData->strFormulario = "cadastro";
												//Nome do campo que deve ser criado
												$objData->strNome = "edtDataNascimento";
												//Valor a constar dentro do campo (p/ alteração)
												$objData->strValor = "";

												$objData->CriarData();

											?>
                    </td>
                  </tr>
                  <tr>
                    <td class="dataLabel">Endere&ccedil;o:</td>
                    <td colspan="3" valign="middle" class="tabDetailViewDF">
                      <input name="edtEndereco" type="text" class="datafield" id="edtEndereco" style="width: 470px" maxlength="80" />
                    </td>
                  </tr>
                  <tr>
                    <td class="dataLabel">
                      <span class="dataLabel">Complemento:</span>						
                    </td>
                    <td colspan="3" class="tabDetailViewDF">
                      <input name="edtComplemento" type="text" class="datafield" id="edtComplemento" style="width: 300" size="84" maxlength="50" />
                    </td>
                  </tr>
                  <tr>
                    <td class="dataLabel">Bairro:</td>
                    <td colspan="3" class="tabDetailViewDF">
                      <input name="edtBairro" type="text" class="datafield" id="edtBairro" style="width: 300" size="52" maxlength="50" />
                    </td>
                  </tr>
                  <tr>
                    <td class="dataLabel">Cidade:</td>
                    <td colspan="3" class="tabDetailViewDF">
                      <select name="cmbCidadeId" id="cmbCidadeId" style="width:350px">
                        <option value="0">Selecione uma Opção</option>
												<?php

													//Monta o while para gerar o combo de escolha
													while ($lookup_cidade = mysql_fetch_object($dados_cidade))
													{

													  ?>
	                          <option value="<?php echo $lookup_cidade->id ?>"><?php echo $lookup_cidade->nome ?> </option>
													  <?php

													}

												?>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td class="dataLabel">UF:</td>
                    <td width="25%" class="tabDetailViewDF">
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
                    <td width="18%" class="dataLabel">Cep:</td>
                    <td valign="top" class="tabDetailViewDF">
                      <input name="edtCep" type="text" class="datafield" id="edtCep" size="11" maxlength="9" onkeypress="return FormataCampo(document.cadastro, 'edtCep', '99999-999', event);">
                    </td>
                  </tr>
                  <tr>
                    <td valign="middle" class="dataLabel">Celular:</td>
                    <td colspan="3" class="tabDetailViewDF">
                      <table width="100%" cellpadding="0" cellspacing="0">
                        <tr valign="middle">
                          <td width="120">
                            <input name="edtTelefone" type="text" class="datafield" id="edtTelefone" size="16" maxlength="14" onkeypress="return FormataCampo(document.cadastro, 'edtTelefone', '(99) 9999-9999', event);" /> 
                          </td>
                          <td width="80" valign="middle" height="20">
                            <b>Operadora:</b>
                          </td>
                          <td width="120" height="20">
                            <input name="edtOperadora" type="radio" value="0" style="border: 0px" checked="checked" />&nbsp;Não Informado
                          </td>
                          <td height="20">
                            <input name="edtOperadora" type="radio" value="1" style="border: 0px" />&nbsp;Vivo
                          </td>
                          <td height="20">
                            <input name="edtOperadora" type="radio" style="border: 0px" value="2" />&nbsp;Tim
                          </td>
                          <td height="20">
                            <input name="edtOperadora" type="radio" style="border: 0px" value="3" />&nbsp;Claro
                          </td>
                          <td height="20">
                            <input name="edtOperadora" type="radio" style="border: 0px" value="4" />&nbsp;Oi
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td valign="middle" class="dataLabel">Telefone Comercial:</td>
                    <td colspan="3" class="tabDetailViewDF">
                      <input name="edtTelefoneComercial" type="text" class="datafield" id="edtTelefoneComercial" size="16" maxlength="14" onkeypress="return FormataCampo(document.cadastro, 'edtTelefoneComercial', '(99) 9999-9999', event);" />           
                    </td>
                  </tr> 
                  <tr>
                    <td valign="middle" class="dataLabel">Telefone Residencial:</td>
                    <td class="tabDetailViewDF">
                      <input name="edtTelefoneResidencial" type="text" class="datafield" id="edtTelefoneResidencial" size="16" maxlength="14" onkeypress="return FormataCampo(document.cadastro, 'edtTelefoneResidencial', '(99) 9999-9999', event);" />            
                    </td>           
                    <td valign="top" class="dataLabel">Email:</td>
                    <td class="tabDetailViewDF">
                      <input name="edtEmail" type="text" class="datafield" id="edtEmail" style="width: 300; text-transform:lowercase" maxlength="50" />            
                    </td>
                  </tr>       
                  <tr>
                    <td valign="top" class="dataLabel">Observações:</td>
                    <td colspan="3" class="tabDetailViewDF">
                      <textarea name="edtObservacoes" wrap="virtual" class="datafield" id="edtObservacoes" style="width: 100%; height: 60px"></textarea>
                    </td>
                  </tr>         
                </table>
                <br/>
                <span class="TituloModulo">Informações Financeiras:</span>
                <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
                  <tr>
                    <td width="140" valign="top" class="dataLabel">Situação:</td>
                    <td colspan="3" valign="middle" class="tabDetailViewDF">
                      <table width="500" cellpadding="0" cellspacing="0">
                        <tr valign="middle">
                          <td width="130" height="20">
                            <input name="edtSituacao" type="radio" value="1" checked="checked" />&nbsp;&nbsp;<img src="image/bt_receber.gif" alt="Formando em Dia" />&nbsp;Em dia
                          </td>
                          <td height="20">
                            <input name="edtSituacao" type="radio" value="2" />&nbsp;&nbsp;<img src="image/bt_pendente.gif" alt="Formando com Restrições Financeiras" />&nbsp;Restrições Financeiras
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td width="140" valign="top" class="dataLabel">Status Jurídico:</td>
                    <td colspan="3" valign="middle" class="tabDetailViewDF">
                      <input name="chkSpc" type="checkbox" id="chkSpc" value="1"> SPC<br/>
                      <input name="chkAcaoCobranca" type="checkbox" id="chkAcaoCobranca" value="1"> Ação de Cobrança<br/>
                      <input name="chkAssessoriaCobranca" type="checkbox" id="chkAsssessoriaCobranca" value="1"> Assessoria de Cobrança
                    </td>
                  </tr>
                <?php
                /*
                  <tr>
                  <td class="dataLabel">Valor Contratado:</td>
                  <td colspan="3" class="tabDetailViewDF">
                  <?php

                  //Cria um objeto do tipo WDEdit
                  $objWDComponente = new WDEditReal();

                  //Define nome do componente
                  $objWDComponente->strNome = "edtValorContratado";
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
                  <td class="dataLabel">Valor Pago:</td>
                  <td width="200" class="tabDetailViewDF">
                  <?php

                  //Cria um objeto do tipo WDEdit
                  $objWDComponente = new WDEditReal();

                  //Define nome do componente
                  $objWDComponente->strNome = "edtValorPago";
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
                  <td width="140" class="dataLabel">Valor Pendente:</td>
                  <td class="tabDetailViewDF">
                  <?php

                  //Cria um objeto do tipo WDEdit
                  $objWDComponente = new WDEditReal();

                  //Define nome do componente
                  $objWDComponente->strNome = "edtValorPendente";
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
                 */
                ?>
              <tr>
                <td class="dataLabel">
                  <span class="dataLabel">Observações:</span>						
                </td>
                <td colspan="3" class="tabDetailViewDF">
                  <input name="edtObsFinanceiro" type="text" class="datafield" id="edtObsFinanceiro" style="width: 600px" maxlength="150" />
                </td>
              </tr>
            </table>
            <br/>
            <span class="TituloModulo">Dados do Avalista:</span>
            <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
              <tr>
                <td width="140" valign="top" class="dataLabel">Nome:</td>
                <td valign="middle" class="tabDetailViewDF">
                  <input id="edtAvalistaNome" name="edtAvalistaNome" type="text" class="datafield" style="width: 300px" maxlength="50" />
                </td>
              </tr>
              <tr>
                <td width="140" valign="top" class="dataLabel">CPF:</td>
                <td valign="middle" class="tabDetailViewDF">
                  <input id="edtAvalistaCpf" name="edtAvalistaCpf" type="text" class="datafield" style="width: 130px" maxlength="15" />
                </td>
              </tr>
              <tr>
                <td width="140" valign="top" class="dataLabel">Telefone:</td>
                <td valign="middle" class="tabDetailViewDF">
                  <input id="edtAvalistaTelefone" name="edtAvalistaTelefone" type="text" class="datafield" style="width: 130px" maxlength="20" />
                </td>
              </tr>
              <tr>
                <td width="140" valign="top" class="dataLabel">Endereço:</td>
                <td valign="middle" class="tabDetailViewDF">
                  <input id="edtAvalistaEndereco" name="edtAvalistaEndereco" type="text" class="datafield" style="width: 600px" maxlength="150" />
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>  	 
    </tr>
  </table>
</form>