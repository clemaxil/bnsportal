<?php

if (!defined('APP_ENTRY') || !APP_ENTRY) die('Not A Valid Entry Point');

include __DIR__ . '/../../../layouts/header.php';
include __DIR__ . '/../../../layouts/menu.php';

?>

<div style="text-align:center;">
  <h4>
    <?= $mod_lang['document_title'] . ' ' . ucfirst(str_replace('_',' ',$_GET['document_name'])); ?>
  </h4>
</div>

<br /><br />

<div id="myTabContent" class="tab-content">
  <div style="text-align: center">
    <iframe src="<?php echo 'upload/session/' . $dataView['id'] . '/' . $_SESSION['user_id_ext'] . '/' . $dataView['document_name']; ?>" type="application/pdf" width="100%" height="800px" frameborder="0"></iframe>
  </div>
</div>


<?php

include __DIR__ . '/../../../layouts/footer.php';

?>