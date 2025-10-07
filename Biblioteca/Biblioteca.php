<?php
require_once 'Libro.php';
require_once 'Prestamo.php';

class Biblioteca {
    private $libros = [];
    private $prestamos = [];

    public function agregarLibro($libro) {
        $this->libros[] = $libro;
    }

    public function eliminarLibro($id)
{
    foreach ($this->libros as $index => $libro) {
        if ($libro->getId() == $id) {
            $titulo = $libro->getTitulo();
          
            unset($this->libros[$index]);
            
            foreach ($this->prestamos as $key => $prestamo) {
                if ($prestamo->getLibro()->getTitulo() === $titulo) {
                    unset($this->prestamos[$key]);
                }
            }
            $this->libros = array_values($this->libros);
            $this->prestamos = array_values($this->prestamos);
            break;
        }
    }
}

    public function editarLibro($id, $titulo, $autor, $categoria) {
        foreach ($this->libros as $libro) {
            if ($libro->getId() == $id) {
                $libro->setTitulo($titulo);
                $libro->setAutor($autor);
                $libro->setCategoria($categoria);
            }
        }
    }

    public function buscarPorTitulo($titulo) {
        return array_filter($this->libros, fn($libro) => stripos($libro->getTitulo(), $titulo) !== false);
    }

    public function buscarPorAutor($autor) {
        return array_filter($this->libros, fn($libro) => stripos($libro->getAutor(), $autor) !== false);
    }

    public function buscarPorCategoria($categoria) {
        return array_filter($this->libros, fn($libro) => stripos($libro->getCategoria(), $categoria) !== false);
    }

    public function getLibros() {
        return $this->libros;
    }

    public function prestarLibro($id) {
        foreach ($this->libros as $libro) {
            if ($libro->getId() == $id && !$libro->estaPrestado()) {
                $libro->setPrestado(true);
                $prestamo = new Prestamo(rand(1000,9999), $libro, date('Y-m-d'));
                $this->prestamos[] = $prestamo;
                return "📚 Libro prestado exitosamente.";
            }
        }
        return "No se puede prestar este libro (ya está prestado o no existe).";
    }

    public function devolverLibro($idPrestamo) {
        foreach ($this->prestamos as $prestamo) {
            if ($prestamo->getId() == $idPrestamo && !$prestamo->estaDevuelto()) {
                $prestamo->devolver();
                $prestamo->getLibro()->setPrestado(false);
                return "Libro devuelto correctamente.";
            }
        }
        return "No se encontró el préstamo o ya fue devuelto.";
    }

    public function getPrestamos() {
        return $this->prestamos;
    }
}
?>
