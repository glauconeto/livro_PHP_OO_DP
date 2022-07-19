<?php

namespace Livro\Widgets\Base;

class Element
{
    protected $tagname;
    protected $properties;
    protected $children;

    public function __construct($name)
    {
        $this->tagname = $name; // Define o nome do elemento
    }

    public function __set($name, $value)
    {
        // Armazena os valores atribuidos ao array properties
        $this->properties[$name] = $value;
    }

    public function __get($name)
    {
        // Retorna os valores atribuídos ao array properties
        return isset($this->properties[$name]) ? $this->properties[$name] : NULL;
    }

    public function add($child)
    {
        $this->children[] = $child;
    }

    public function show()
    {
        $this->open(); // Abre a tag
        echo "\n";
        if ($this->children) {
            foreach ($this->children as $child) {
                if (is_object($child)) { // Se for objeto
                    $child->show();
                } else if (is_string($child) OR (is_numeric($child))) {
                    // Se for texto
                    echo $child;
                }
            }
        }

        $this->close(); // Fecha a tag
    }

    public function open()
    {
        // Exibe a tag de abertura
        echo "<{$this->tagname}>";
        if ($this->properties) {
            // Percorre as propriedades
            foreach ($this->properties as $name => $value) {
                if (is_scalar($value)) {
                    echo "{$name}=\"{$value}\"";
                }
            }
        }
        echo '>';
    }

    public function close()
    {
        echo "</{$this->tagname}>\n";
    }

    public function __toString()
    {
        ob_start();
        $this->show();
        $content = ob_get_clean();
        return $content;
    }
}