<?php
// index.php 1226

// Incluir configuración y funciones
require_once "config.php";
require_once "includes/funciones.php";

// Incluir cabecera
include "includes/header.php";

// Control de páginas simples
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

if ($page === 'home') {
    echo "<h2>Bienvenido a " . SITE_NAME . "</h2>";
    echo "<p>Este es el inicio de nuestro mini sitio.</p>";
} elseif ($page === 'about') {
    echo "<h2>Acerca de</h2>";
    echo "<p>Este sitio es un ejemplo de estructura básica en PHP.</p>";
} elseif ($page === 'services') {
    echo "<h2>Services</h2>";
    echo "<p>Elige nuestro mejores servicios del muncho tech.</p>";
} else {
    echo "<h2>Página no encontrada</h2>";
}

// Incluir pie de página
include "includes/footer.php";
?>
