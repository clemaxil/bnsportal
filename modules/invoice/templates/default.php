<?php
if (!defined('APP_ENTRY') || !APP_ENTRY) die('Not A Valid Entry Point');

include __DIR__ . '/../../../layouts/header.php';
include __DIR__ . '/../../../layouts/menu.php';

require_once(__DIR__ . '/../../../helpers/appHelperI18n.php');

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
	  <th scope="col"><?= $mod_lang['list_date'] ?></th>
	  <th scope="col"><?= $mod_lang['list_due_date'] ?></th>
      <th scope="col"><?= $mod_lang['list_amount_ht'] ?></th>
	  <th scope="col"><?= $mod_lang['list_amount_ttc'] ?></th>
	  <th scope="col"><?= $mod_lang['list_amount_due'] ?></th>
    </tr>
  </thead>
  <tbody>
	  <?php

	  foreach($dataView['invoices'] as $invoice){
		echo '
			<tr class="table-primary">
			<td><a href='. appHelperUrl_link($lang, 'invoice', 'download', $invoice->session_id,'&invoice_id=' . $invoice->id.'&document_name='.$invoice->name) .'>'.$invoice->number.'</a></td>
			<td>'.str_replace('_',' ',$invoice->name).'</td>
			<td>'.appHelperI18n_dateCreateFromFormat($invoice->invoice_date, $_SESSION['user_lang']).'</td>
			<td>'.appHelperI18n_dateCreateFromFormat($invoice->due_date, $_SESSION['user_lang']).'</td>
			<td>'.sprintf("%01.2f",$invoice->amountHT).' '.$invoice->currency.'</td>
			<td>'.sprintf("%01.2f",$invoice->amount).' '.$invoice->currency.'</td>
			<td>'.sprintf("%01.2f",$invoice->amount_due).' '.$invoice->currency.'</td>
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