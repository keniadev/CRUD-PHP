<?php
class Libro {
    private $id;
    private $titulo;
    private $autor;
    private $categoria;
    private $prestado;

    public function __construct($id, $titulo, $autor, $categoria) {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->autor = $autor;
        $this->categoria = $categoria;
        $this->prestado = false;
    }

    public function getId() { return $this->id; }
    public function getTitulo() { return $this->titulo; }
    public function getAutor() { return $this->autor; }
    public function getCategoria() { return $this->categoria; }
    public function estaPrestado() { return $this->prestado; }

    public function setTitulo($titulo) { $this->titulo = $titulo; }
    public function setAutor($autor) { $this->autor = $autor; }
    public function setCategoria($categoria) { $this->categoria = $categoria; }
    public function setPrestado($prestado) { $this->prestado = $prestado; }
}
?>
