<?php

use Livro\Database\Record;
use Livro\Database\Criteria;
use Livro\Database\Repository;

class Pessoa extends Record
{
    const TABLENAME = 'pessoa';
    private $cidade;

    public function get_cidade()
    {
        if (empty($this->cidade)) {
            $this->cidade = new Cidade($this->id_cidade);
        }
        return $this->cidade;
    }

    public function get_nome_cidade()
    {
        if (empty($this->cidade))
            $this->cidade = new Cidade($this->id_cidade);
        return $this->cidade->nome;
    }

    public function addGroup(Grupo $grupo)
    {
        $pg = new PessoaGrupo();
        $pg->id_grupo = $grupo->id;
        $pg->id_pessoa = $this->id;
        $pg->store();
    }

    public function delGrupos()
    {
        $criteria = new Criteria();
        $criteria->add('id_pessoa', '=', $this->id);
        $repo = new Repository('PessoaGroup');
        return $repo->delete($criteria);
    }

    public function getGrupos()
    {
        $grupos = array();
        $criteria = new Criteria();
        $criteria->add('id_pessoa', '=', $this->id);
        $repo = new Repository('PessoaGroup');
        $vinculos = $repo->load($criteria);

        if ($vinculos) {
            foreach ($vinculos as $vinculo) {
                $grupos[] = new Grupo($vinculo->id_group);
            }
        }

        return $grupos;
    }

    public function getIdsGrupos()
    {
        $grupos_id = array();
        $grupos = $this->getGrupos();

        if ($grupos) {
            foreach ($grupos as $grupo) {
                $grupos_ids[] = $grupo->id;
            }
        }

        return $grupos_id;
    }

    public function getContasEmAberto()
    {
        return Conta::getByPessoa($this->id);
    }

    public function totalDebitos()
    {
        return Conta::debitosPorPessoa($this->id);
    }
}