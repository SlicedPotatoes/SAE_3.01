<?php
/**
 * Script de gestion de la deconnexion
 */
session_start();
session_destroy();
header("Location: ../index.php");