<?php

use Livro\Control\Page;
use Livro\Control\Action;
use Livro\Widgets\Form\Form;
use Livro\Widgets\Form\Entry;
use Livro\Widgets\Form\Date;
use Livro\Widgets\Dialog\Message;
use Livro\Database\Transaction;
use Livro\Database\Repository;
use Livro\Database\Criteria;

use Livro\Widgets\Wrapper\FormWrapper;
use Livro\Widgets\Container\Panel;

/**
 * Relatório de vendas
 */
class VendasReport extends Page
{
    private $form;

    /**
     * método construtor
     */
    public function __construct()
    {
        parent::__construct();

        $this->form = new FormWrapper(new Form('form_relat_vendas'));
        $this->form->setTitle('Relatório de vendas');
        
        $data_ini = new Date('data_ini');
        $data_fim = new Date('data_fim');
        
        $this->form->addField('Data Inicial', $data_ini, '50%');
        $this->form->addField('Data Final', $data_fim, '50%');
        $this->form->addAction('Gerar', new Action(array($this, 'onGera')));
        
        parent::add($this->form);
    }

    /**
     * Gera o relatório, baseado nos parâmetros do formulário
     */
    public function onGera()
    {
        $loader = new Twig_Loader_Filesystem('App/Resources');
        $twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate('vendas_report.html');
        
        $dados = $this->form->getData();

        $this->form->setData($dados);
        
        $data_ini = $dados->data_ini;
        $data_fim = $dados->data_fim;
        
        $replaces = array();
        $replaces['data_ini'] = $dados->data_ini;
        $replaces['data_fim'] = $dados->data_fim;
        
        try {
            Transaction::open('livro');

            $repositorio = new Repository('Venda');

            $criterio = new Criteria;
            $criterio->setProperty('order', 'data_venda');
            
            if ($dados->data_ini)
                $criterio->add('data_venda', '>=', $data_ini);
            if ($dados->data_fim)
                $criterio->add('data_venda', '<=', $data_fim);
            
            $vendas = $repositorio->load($criterio);
            
            if ($vendas) {
                foreach ($vendas as $venda) {
                    $venda_array = $venda->toArray();
                    $venda_array['nome_cliente'] = $venda->cliente->nome;
                    $itens = $venda->itens;
                    if ($itens) {
                        foreach ($itens as $item) {
                            $item_array = $item->toArray();
                            $item_array['descricao'] = $item->produto->descricao;
                            $venda_array['itens'][] = $item_array;
                        }
                    }
                    $replaces['vendas'][] = $venda_array;
                }
            }
            
            Transaction::close();
        } catch (Exception $e) {
            new Message('error', $e->getMessage());
            Transaction::rollback();
        }

        $content = $template->render($replaces);
        
        $title = 'Vendas';
        $title.= (!empty($dados->data_ini)) ? ' de '. $dados->data_ini : '';
        $title.= (!empty($dados->data_fim)) ? ' até '. $dados->data_fim : '';
        
        $panel = new Panel($title);
        $panel->add($content);
        
        parent::add($panel);
    }
}