<?php
if (!defined('APP_ENTRY') || !APP_ENTRY) die('Not A Valid Entry Point');

include __DIR__ . '/../../../layouts/header.php';

?>


<div style="text-align:left; margin:20px;">

    <p><b><?= $mod_lang['password_new']; ?> : </b><?= $dataView['newPassword']; ?></p>

    <?php

    if (!empty($dataView['jwtErrorMsg'])) {
        echo '<div class="error">' . $dataView['jwtErrorMsg'] . '</div>';
    }

    ?>

    <p><a href="<?= appHelperUrl_link($lang, 'auth', 'index'); ?>" class="button"><?= $mod_lang['login_submit']; ?></a></p>

</div>

<?php

include __DIR__ . '/../../../layouts/footer.php';

?>