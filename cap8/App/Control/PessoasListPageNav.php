<?php

use Livro\Control\Page;
use Livro\Control\Action;
use Livro\Widgets\Form\Form;
use Livro\Widgets\Form\Entry;
use Livro\Widgets\Form\Combo;
use Livro\Widgets\Container\Panel;
use Livro\Widgets\Container\VBox;
use Livro\Widgets\Datagrid\Datagrid;
use Livro\Widgets\Datagrid\DatagridColumn;
use Livro\Widgets\Datagrid\PageNavigation;
use Livro\Widgets\Dialog\Message;
use Livro\Widgets\Dialog\Question;
use Livro\Widgets\Wrapper\FormWrapper;
use Livro\Widgets\Wrapper\DatagridWrapper;
use Livro\Database\Transaction;
use Livro\Database\Repository;
use Livro\Database\Criteria;

/**
 * Listagem de Pessoas
 */
class PessoasListPageNav extends Page
{
    private $form;     // formulário de buscas
    private $datagrid; // listagem
    private $loaded;

    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new FormWrapper(new Form('form_busca_pessoas'));
        $this->form->setTitle('Pessoas');
        
        $nome = new Entry('nome');
        
        $this->form->addField('Nome', $nome, 300);
        $this->form->addAction('Buscar', new Action(array($this, 'onReload')));
        $this->form->addAction('Novo', new Action(array(new PessoasForm, 'onEdit')));
        
        $this->datagrid = new DatagridWrapper(new Datagrid);

        $codigo = new DatagridColumn('id', 'Código', 'right', 50);
        $nome = new DatagridColumn('nome', 'Nome', 'left', 200);
        $endereco = new DatagridColumn('endereco', 'Endereco','left', 200);
        $cidade = new DatagridColumn('nome_cidade','Cidade', 'left', 140);

        $this->datagrid->addColumn($codigo);
        $this->datagrid->addColumn($nome);
        $this->datagrid->addColumn($endereco);
        $this->datagrid->addColumn($cidade);

        $this->datagrid->addAction('Editar', new Action([new PessoasForm, 'onEdit']), 'id', 'fa fa-edit fa-lg blue');
        $this->datagrid->addAction('Excluir', new Action([$this, 'onDelete']), 'id', 'fa fa-trash fa-lg red');

        $this->pagenav = new PageNavigation;
        $this->pagenav->setAction(new Action(array($this, 'onReload')));
        
        $box = new VBox;
        $box->style = 'display:block';
        $box->add($this->form);
        $box->add($this->datagrid);
        $box->add($this->pagenav);
        
        parent::add($box);
    }

    /**
     * Carrega a Datagrid com os objetos do banco de dados
     */
    public function onReload($param)
    {
        Transaction::open('livro');
        $repository = new Repository('Pessoa');
        
        $dados = $this->form->getData();
        
        $criteria = new Criteria;
        
        if ($dados->nome) {
            $criteria->add('nome', 'like', "%{$dados->nome}%");
        }

        $count = $repository->count($criteria);
        
        $criteria->setProperty('order', 'id');
        $criteria->setProperty('limit', 10);
        $criteria->setProperty('offset', isset($param['offset']) ? (int) $param['offset'] : 0);
        $pessoas = $repository->load($criteria);
        
        $this->datagrid->clear();
        if ($pessoas) {
            foreach ($pessoas as $pessoa) {
                $this->datagrid->addItem($pessoa);
            }
        }

        $this->pagenav->setTotalRecords($count);
        $this->pagenav->setCurrentPage(isset($param['page']) ? (int) $param['page'] : 1);
        
        Transaction::close();
        $this->loaded = true;
    }

    /**
     * Pergunta sobre a exclusão de registro
     */
    public function onDelete($param)
    {
        $key = $param['key'];
        $action1 = new Action(array($this, 'Delete'));
        $action1->setParameter('key', $key);
        
        new Question('Deseja realmente excluir o registro?', $action1);
    }

    /**
     * Exclui um registro
     */
    public function Delete($param)
    {
        try {
            $key = $param['key'];
            Transaction::open('livro');
            $cidade = new Pessoa($key);
            $cidade->delete();
            Transaction::close();
            $this->onReload();
            new Message('info', "Registro excluído com sucesso");
        } catch (Exception $e) {
            new Message('error', $e->getMessage());
        }
    }

    /**
     * Exibe a página
     */
    public function show()
    {
        if (!$this->loaded) {
           $this->onReload( $_GET );
        }
        
        parent::show();
    }
}