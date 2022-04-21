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
      <th scope="col"><?= $mod_lang['list_reference'] ?></th>
      <th scope="col"><?= $mod_lang['list_title'] ?></th>
      <th scope="col"><?= $mod_lang['list_status'] ?></th>
      <th scope="col"><?= $mod_lang['list_type'] ?></th>
	  <th scope="col"><?= $mod_lang['list_modalite'] ?></th>
	  <th scope="col"><?= $mod_lang['list_start'] ?></th>
	  <th scope="col"><?= $mod_lang['list_end'] ?></th>
	  <th scope="col"><?= $mod_lang['list_duration'] ?></th>
    </tr>
  </thead>
  <tbody>
	  <?php

	  foreach($dataView['sessions'] as $session){
		echo '
			<tr class="table-primary">
			<td><a href='. appHelperUrl_link($lang, 'session', 'detail', $session->id) .'>'.$session->numero.'</a></td>
			<td>'.$session->name.'</td>
			<td>'.$session->status.'</td>
			<td>'.$session->type.'</td>
			<td>'.$session->modalite.'</td>
			<td>'.$session->debut.'</td>
			<td>'.$session->fin.'</td>
			<td>'.$session->nbhours.'</td>
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