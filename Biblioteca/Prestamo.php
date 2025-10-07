<?php
class Prestamo {
    private $id;
    private $libro;
    private $fechaPrestamo;
    private $fechaDevolucion;
    private $devuelto;

    public function __construct($id, $libro, $fechaPrestamo) {
        $this->id = $id;
        $this->libro = $libro;
        $this->fechaPrestamo = $fechaPrestamo;
        $this->fechaDevolucion = null;
        $this->devuelto = false;
    }

    public function getId() { return $this->id; }
    public function getLibro() { return $this->libro; }
    public function getFechaPrestamo() { return $this->fechaPrestamo; }
    public function getFechaDevolucion() { return $this->fechaDevolucion; }
    public function estaDevuelto() { return $this->devuelto; }

    //funcion devolver
    public function devolver() {
        $this->devuelto = true;
        $this->fechaDevolucion = date('Y-m-d');
    }
}
?>
