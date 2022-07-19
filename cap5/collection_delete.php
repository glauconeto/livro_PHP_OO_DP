<?php

// Carrega as classes necessárias
require_once 'classes/api/Connection.php';
require_once 'classes/api/Criteria.php';
require_once 'classes/api/Transaction.php';
require_once 'classes/api/Repository.php';
require_once 'classes/api/Record.php';
require_once 'classes/api/Logger.php';
require_once 'classes/api/LoggerTXT.php';
require_once 'classes/api/Produto.php';

try {
    // Inicia a transação com a base de dados
    Transaction::open('estoque');

    // Define o arquivo para log
    Transaction::setLogger(new LoggerTxt('/tmp/log_collection_delete.txt'));

    // Define o critério de seleção
    $criteria = new Criteria;
    $criteria->add('descricao', 'like', '%WEBC%');
    $criteria->add('descricao', 'like', '%FILMAD%', 'or');

    // Cria o repositório
    $repositorio = new Repository('Produto');

    // Exclui os objetos, conforme o critério
    $repository->delete($criteria);
    Transaction::close(); // Fecha a transação
} catch (Exception $e) {
    echo $e->getMessage();
    Transaction::rollback();
}