<?php
/*
 * Dans ce fichier, certaine variable global sont présente
 * Pour simplifié leurs modifications
 */
$TEST = false;// À mettre sûr true pendant un test unitaire
$PROD = false; // Permet de faire des actions différentes en prod, par exemple message d'erreur différent
$LIMIT_FILE_SIZE_UPLOAD = 5 * 1024 * 1024; // Taille maximale d'un fichier

$ALLOWED_MIME_TYPE = ['image/jpeg', 'image/png', 'application/pdf']; // MIME Type de fichier autorisé
$ALLOWED_EXTENSIONS_FILE = ['jpg','jpeg','png','pdf']; // Extension de fichier autorisé
