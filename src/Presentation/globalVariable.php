<?php
/**
 * Dans ce fichier est contenue un certain nombre de variables global
 * Permet de les centralisés et de simplifier leurs modifications
 */

$PROD = false; // Permet de faire des actions différentes en prod, par exemple message d'erreur différent
$LIMIT_FILE_SIZE_UPLOAD = 5 * 1024 * 1024; // Taille maximale d'un fichier

$ALLOWED_MIME_TYPE = ['image/jpeg', 'image/png', 'application/pdf']; // MIME Type de fichier autorisé
$ALLOWED_EXTENSIONS_FILE = ['jpg','jpeg','png','pdf']; // Extension de fichier autorisé
