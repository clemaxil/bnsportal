<?php
if (!defined('APP_ENTRY') || !APP_ENTRY) die('Not A Valid Entry Point');

include __DIR__ . '/../../../layouts/header.php';
include __DIR__ . '/../../../layouts/menu.php';

if ($dataView['error'] === 1) {
	echo '<div class="alert alert-dismissible alert-danger">
				  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
				  <strong>Error : </strong> ' . $dataView['error-message'] . '</div>';
} else {
//Content Start
?>


<table class="table table-hover">
  <thead>
    <tr>
      <th scope="col"><?= $mod_lang['list_number'] ?></th>
      <th scope="col"><?= $mod_lang['list_name'] ?></th>
      <th scope="col"><?= $mod_lang['list_amount_ht'] ?></th>
	  <th scope="col"><?= $mod_lang['list_amount_ttc'] ?></th>
    </tr>
  </thead>
  <tbody>
	  <?php

	  foreach($dataView['invoices'] as $invoice){
		echo '
			<tr class="table-primary">
			<td><a href='. appHelperUrl_link($lang, 'invoice', 'download', $invoice->session_id,'&invoice_id=' . $invoice->id) .'>'.$invoice->number.'</a></td>
			<td>'.$invoice->name.'</td>
			<td>'.$invoice->amountHT.'</td>
			<td>'.$invoice->amount.'</td>
			</tr>
		';
	  }

	  ?>
  </tbody>
</table>



<?php
}
//Content End
include __DIR__ . '/../../../layouts/footer.php';
?>