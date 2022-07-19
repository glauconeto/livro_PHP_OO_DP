<?php

use Livro\Database\Record;

class ItemVenda extends Record
{
    const TABLENAME = 'item_venda';
    private $produto;

    public function set_produto(Produto $p)
    {
        $this->produto = $p;
        $this->id_produto = $p->id;
    }

    public function get_produto()
    {
        if (empty($this->produto)) {
            $this->produto = new Produto($this->id_produto);
        }

        return $this->produto;
    }
}