<?php

abstract class Logger
{
    protected $filename; // Local do arquivo de LOG

    public function __construct($filename)
    {
        $this->filename = $filename;
        file_put_contents($filename, ""); // Limpa o conteúdo do arquivo
    }

    // Define o método write como obrigatório
    abstract function write($message);
}