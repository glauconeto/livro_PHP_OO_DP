<?php

use Livro\Database\Record;
use Livro\Database\Criteria;
use Livro\Database\Repository;

class Venda extends Record
{
    const TABLENAME = 'venda';
    private $itens;
    private $cliente;

    public function set_cliente(Pessoa $c)
    {
        $this->cliente = $c;
        $this->id_cliente = $c->id;
    }

    public function get_cliente()
    {
        if (empty($this->cliente)) {
            $this->cliente = new Pessoa($this->id_cliente);
        }

        return $this->cliente;
    }

    public function addItem(Produto $p, $quantidade) {
        $item = new ItemVenda;
        $item->produto = $p;
        $item->preco = $p->preco_venda;
        $item->quantidade = $quantidade;
        $this->itens[] = $item;
        $this->valor_venda += ($item->preco * $quantidade);
    }

    public function store()
    {
        parent::store();

        foreach ($this->itens as $item) {
            $this->id_venda = $this->id;
            $item->store();
        }
    }

    public function get_itens()
    {
        $repositorio = new Repository('ItemVenda');

        $criterio = new Criteria;
        $criterio->add('id_venda', '=', $this->id);
        $this->itens = $repositorio->load($criterio);
        
        return $this->itens;
    }
}