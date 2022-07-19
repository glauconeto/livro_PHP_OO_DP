<?php

use Livro\Control\Page;
use Livro\Widgets\Dialog\Message;
use Livro\Database\Transaction;
use Livro\Widgets\Container\Panel;

/**
 * Relatório de vendas
 */
class PessoasReport extends Page
{
    /**
     * método construtor
     */
    public function __construct()
    {
        parent::__construct();
        
        $loader = new Twig_Loader_Filesystem('App/Resources');
        $twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate('pessoas_report.html');
        
        $replaces = array();
        
        try {
            Transaction::open('livro');
            $replaces['pessoas'] = ViewSaldoPessoa::all();
            Transaction::close();
        } catch (Exception $e) {
            new Message('error', $e->getMessage());
            Transaction::rollback();
        }
        
        $content = $template->render($replaces);
        
        $panel = new Panel('Pessoas');
        $panel->add($content);
        
        parent::add($panel);
    }
}