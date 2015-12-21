<?php
class WDComponente
{
  public $strNome;
  //Define Tamanho do componente
  public $intSize;
  //Define numero de caracteres
  public $intMaxLength;
  //Define valor para o componente
  public $strValor;
  //define descrição para o componente
  public $strLabel;
  //Determina mas para o componente
  public $strMascara;
  //Define um ou varios eventos para o componente
  public $strEvento;
  //Determina o formulário onde os componentes se econtram
  public $strFormulario;
  //Determina tamanho do calendario
  public $intTamanhoTela;
  //Define o estilo do CSS
  public $strCSS;
  //Determna se deve ou não criar uma tabela para os campos
  public $bolCriarTabela;
  //Quebra a linha depois de definir o componente
  public $bolQuebraLinha;
  //Permite adicionar novos objetos ao componentes
  public $strComponente;
  //Determina a estrutura do componente internamente
  private $strComponenteClasse;
  //Determina a estrutura do evento internamente
  private $strEventoClasse;

  function WDComponente()
  {
    $this->strEventoClasse = "";
    $this->strComponenteClasse = "";
    $this->bolCriarTabela = true;
    $this->bolQuebraLinha = true;
  }

  //Cria o componente
  public function Criar()
  {
    $this->strEventoClasse .= $this->strEvento;

    //Define na variavel as propriedades para criação do componente
    $this->strComponenteClasse .= " <input type=\"text\" id=\"" . $this->strNome . "\" name=\"" .
	$this->strNome . "\" value=\"" . $this->strValor . "\" size=\"" . $this->
	intSize . "\" maxlength=\"" . $this->intMaxLength . "\" " . $this->strCSS . " " .
	$this->strEventoClasse . "style=\"text-align:right\"> " . $this->strComponente;

        //Monta o componente com contúdo seguinte na mesma linha do componente
        echo $this->strLabel . $this->strComponenteClasse;
    $this->strComponenteClasse = "";
    $this->strEventoClasse = "";
  }

}

?>