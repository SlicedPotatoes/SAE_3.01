<?php
/*
 * Script pour ce déconnecter
 */
    session_start();
    session_destroy();
    header("Location: ../index.php");