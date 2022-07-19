<?php

use Livro\Database\Record;

class Produto extends Record
{
    const TABLENAME = 'produto';
    private $fabricante;

    public function get_nome_fabricante()
    {
        if (empty($this->fabricante)) {
            $this->fabricante = new Fabricante($this->id_fabricante);
        }

        return $this->fabricante->nome;
    }
}