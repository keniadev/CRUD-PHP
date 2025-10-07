<?php
require_once 'Libro.php';
require_once 'Biblioteca.php';
require_once 'Prestamo.php';
session_start();

// Crear la biblioteca en sesión si no existe
if (!isset($_SESSION['biblioteca'])) {
    $_SESSION['biblioteca'] = new Biblioteca();
}
$biblioteca = $_SESSION['biblioteca'];

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'];

    // Agregar libro
    if ($accion === 'agregar') {
        $titulo = $_POST['titulo'];
        $autor = $_POST['autor'];
        $categoria = $_POST['categoria'];
        $nuevoLibro = new Libro(rand(1,9999), $titulo, $autor, $categoria);
        $biblioteca->agregarLibro($nuevoLibro);
    }

    // Eliminar libro (y sus préstamos relacionados)
    if ($accion === 'eliminar') {
        $biblioteca->eliminarLibro($_POST['id']);
    }

    // Editar libro
    if ($accion === 'editar') {
        $_SESSION['editarId'] = $_POST['id'];
    }

    // Guardar edición de libro
    if ($accion === 'guardarEdicion') {
        $biblioteca->editarLibro($_POST['id'], $_POST['titulo'], $_POST['autor'], $_POST['categoria']);
        unset($_SESSION['editarId']);
    }

    // Prestar libro
    if ($accion === 'prestar') {
        echo "<p class='mensaje'>".$biblioteca->prestarLibro($_POST['id'])."</p>";
    }

    // Devolver libro
    if ($accion === 'devolver') {
        $idLibro = $_POST['id'];
        foreach ($biblioteca->getPrestamos() as $prestamo) {
            if ($prestamo->getLibro()->getId() == $idLibro && !$prestamo->estaDevuelto()) {
                echo "<p class='mensaje'>" . $biblioteca->devolverLibro($prestamo->getId()) . "</p>";
                break;
            }
        }
    }
}


$_SESSION['biblioteca'] = $biblioteca;

// Buscar libros por título, autor o categoría
$resultadosBusqueda = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'buscar') {
    $termino = $_GET['busqueda'];
    $resultadosBusqueda = array_unique(array_merge(
        $biblioteca->buscarPorTitulo($termino),
        $biblioteca->buscarPorAutor($termino),
        $biblioteca->buscarPorCategoria($termino)
    ), SORT_REGULAR);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Biblioteca POO</title>
    <link rel="stylesheet" href="Style/style.css">
</head>
<body>
    <div class="container">
        <h1>📚 Sistema de Biblioteca</h1>

        <!-- Formulario para agregar libro -->
        <section>
            <h2>Agregar Libro</h2>
            <form method="POST">
                <input type="text" name="titulo" placeholder="Título" required>
                <input type="text" name="autor" placeholder="Autor" required>
                <input type="text" name="categoria" placeholder="Categoría" required>
                <button type="submit" name="accion" value="agregar">Agregar</button>
            </form>
        </section>

        <!-- Formulario para buscar libros -->
        <section>
            <h2>Buscar Libro</h2>
            <form method="GET">
                <input type="text" name="busqueda" placeholder="Título, Autor o Categoría" required>
                <button type="submit" name="accion" value="buscar">Buscar</button>
            </form>
        </section>

        <!-- Mostrar resultados de búsqueda -->
        <?php if (!empty($resultadosBusqueda)): ?>
            <h3>Resultados de la búsqueda</h3>
            <table>
                <tr><th>ID</th><th>Título</th><th>Autor</th><th>Categoría</th></tr>
                <?php foreach ($resultadosBusqueda as $libro): ?>
                    <tr>
                        <td><?= $libro->getId() ?></td>
                        <td><?= $libro->getTitulo() ?></td>
                        <td><?= $libro->getAutor() ?></td>
                        <td><?= $libro->getCategoria() ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <!-- Formulario para editar libro -->
        <?php
        if (isset($_SESSION['editarId'])) {
            $idEditar = $_SESSION['editarId'];
            foreach ($biblioteca->getLibros() as $libro) {
                if ($libro->getId() == $idEditar) { ?>
                    <h2>Editar Libro</h2>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= $libro->getId() ?>">
                        <input type="text" name="titulo" value="<?= $libro->getTitulo() ?>" required>
                        <input type="text" name="autor" value="<?= $libro->getAutor() ?>" required>
                        <input type="text" name="categoria" value="<?= $libro->getCategoria() ?>" required>
                        <button type="submit" name="accion" value="guardarEdicion">Guardar</button>
                    </form>
        <?php } } } ?>

        <!-- Mostrar libros registrados -->
        <h2>Libros Registrados</h2>
        <table>
            <tr><th>ID</th><th>Título</th><th>Autor</th><th>Categoría</th><th>Estado</th><th>Acciones</th></tr>
            <?php foreach ($biblioteca->getLibros() as $libro): ?>
                <tr>
                    <td><?= $libro->getId() ?></td>
                    <td><?= $libro->getTitulo() ?></td>
                    <td><?= $libro->getAutor() ?></td>
                    <td><?= $libro->getCategoria() ?></td>
                    <td><?= $libro->estaPrestado() ? 'Prestado' : 'Disponible' ?></td>
                    <td>
                   
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $libro->getId() ?>">
                            <button type="submit" name="accion" value="eliminar">Eliminar</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $libro->getId() ?>">
                            <button type="submit" name="accion" value="editar">Editar</button>
                        </form>
                        <?php if (!$libro->estaPrestado()): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $libro->getId() ?>">
                                <button type="submit" name="accion" value="prestar">Prestar</button>
                            </form>
                        <?php else: ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $libro->getId() ?>">
                                <button type="submit" name="accion" value="devolver">Devolver</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <!-- Mostrar préstamos registrados -->
        <h2>Préstamos Registrados</h2>
        <table>
            <tr><th>ID Préstamo</th><th>Título</th><th>Fecha Préstamo</th><th>Fecha Devolución</th><th>Estado</th></tr>
            <?php foreach ($biblioteca->getPrestamos() as $prestamo): ?>
                <tr>
                    <td><?= $prestamo->getId() ?></td>
                    <td><?= $prestamo->getLibro()->getTitulo() ?></td>
                    <td><?= $prestamo->getFechaPrestamo() ?></td>
                    <td><?= $prestamo->getFechaDevolucion() ?: '—' ?></td>
                    <td><?= $prestamo->estaDevuelto() ? 'Devuelto' : 'En préstamo' ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <footer>
        <p>© 2025 Kenia — Todos los derechos reservados.</p>
    </footer>
</body>
</html>
