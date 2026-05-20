<?php
session_start();
session_unset();     // vide toutes les variables de session
session_destroy();   // détruit la session
header("Location: ../../html/page_login.php");
exit;
?>