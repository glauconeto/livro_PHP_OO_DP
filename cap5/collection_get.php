<?php

require_once 'classes/api/Connection.php';
require_once 'classes/api/Criteria.php';
require_once 'classes/api/Transaction.php';
require_once 'classes/api/Repository.php';
require_once 'classes/api/Record.php';
require_once 'classes/api/Logger.php';
require_once 'classes/api/LoggerTXT.php';
require_once 'classes/api/Produto.php';

try {
    // Inicia a transação
    Transaction::open('estoque');

    // Define o arquivo para LOG
    Transaction::setLogger(new LoggerTxt('/tmp/log_collection_update.txt'));

    // Define o critério de seleção
    $criteria = new Criteria;
    $criteria->add('preco_venda', '<=', 35);
    $criteria->add('origem', '=', 'N');

    // Cria o repositório
    $repository = new Repository('Produto');

    // Carrega os objetos, conforme o critério
    $produtos = $repository->load($criteria);
    if ($produtos) {
        // Percorre todos os objetos
        foreach ($produtos as $produto) {
            $produto->preco_venda *= 1.3;
            $produto->store();
        }
    }

    Transaction::close(); // Fecha a transação
} catch (Exception $e) {
    echo $e->getMessage();
    Transaction::rollback();
}