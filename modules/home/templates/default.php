<?php
if (!defined('APP_ENTRY') || !APP_ENTRY) die('Not A Valid Entry Point');

include __DIR__ . '/../../../layouts/header.php';
include __DIR__ . '/../../../layouts/menu.php';

?>

<div style="text-align:center">
    <h1><?= $mod_lang['title']; ?></h1>
    <?= $mod_lang['content']; ?>
</div>

<?php

include __DIR__ . '/../../../layouts/footer.php';

?>