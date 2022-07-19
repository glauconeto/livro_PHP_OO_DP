<?php

class Repository
{
    private $activeRecord; // Classe manipulada pelo repositório

    function __construct($class)
    {
        $this->activeRecord = $class;
    }

    function load(Criteria $criteria)
    {
        // Instancia a instrução de SELECT
        $sql = 'SELECT * FROM '. constant($this->activeRecord. '::TABLENAME');

        // Obtém a clausula WHERE do objeto criteria
        if ($criteria) {
            $expression = $criteria->dump();
        }

        // Obém as propriedades do critério
        $order = $criteria->getProperty('order');
        $limit = $criteria->getProperty('limit');
        $offset = $criteria->getProperty('offset');

        // Obtém a ordenação do SELECT
        if ($order) {
            $sql .= ' ORDER BY '. $order;
        } if ($limit) {
            $sql .= ' LIMIT '. $limit;
        } if ($offset) {
            $sql .= ' OFFSET '. $offset;
        }

        // Obtém transação ativa
        if ($conn = Transaction::get()) {
            Transaction::log($sql); // Registra mensagem de log
            $result = $conn->Query($sql);
            $results = array();

            if ($result) {
                // Percorre os resultados da consulta, retornando um objeto
                while ($row = $result->fetchObject($this->activeRecord)) {
                    // Armazena no array $results
                    $results[] = $row;
                }
            }
            return $results;
        } else {
            throw new Exception('Não há transação ativa!');
        }
    }

    function delete(Criteria $criteria)
    {
        $expression = $criteria->dump();
        $sql = 'DELETE FROM '. constant($this->activeRecord. '::TABLENAME');

        if ($expression) {
            $sql .= ' WHERE '. $expression;
        }

        // Obtém transação ativa
        if ($conn = Transaction::get()) {
            Transaction::log($sql); // Registra mensagem de log
            $result = $conn->exec($sql); // Executa instrução de DELETE
            return $result;
        } else {
            throw new Exception('Não há transação ativa!');
        }
    }

    function count(Criteria $criteria)
    {
        $expression = $criteria->dump();
        $sql = "SELECT count(*) FROM " . constant($this->activeRecord.'::TABLENAME');
        if ($expression) {
            $sql .= ' WHERE ' . $expression;
        }
        
        // obtém transação ativa
        if ($conn = Transaction::get()) {
            Transaction::log($sql); // registra mensagem de log
            
            // executa instrução de SELECT
            $result= $conn->query($sql);
            if ($result) {
                $row = $result->fetch();
            }
            
            return $row[0]; // retorna o resultado
        }
        else {
            throw new Exception('Não há transação ativa!!');
        }
    }
}