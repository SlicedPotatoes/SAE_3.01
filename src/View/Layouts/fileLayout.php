<?php
// Différent header pour que le navigateur sache comment afficher le fichier
header('Content-Type: ' . $mimeType);
header('Content-Disposition: inline; filename="' . $fileName . '"');
header('Content-Length: ' . filesize($filePath));
// Demander au navigateur de ne pas mettre en cache pour éviter le risque sur un poste partagé
// qu'un utilisateur accéde a la version en cache alors qu'il n'y est pas autorisé.
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

readfile($filePath);