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

<h5><?php

if (empty($dataView['status'])) {
	$dataView['status'] = 'all';
}

echo $mod_lang['status_'.$dataView['status']];

?>
</h5>

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
			<td>'.$mod_lang['list_status_'.$session->status].'</td>
			<td>'.$session->type.'</td>
			<td>'.$mod_lang['list_modalite_'.$session->modalite].'</td>
			<td>'.appHelperI18n_dateCreateFromFormat($session->debut, $_SESSION['user_lang']).'</td>
			<td>'.appHelperI18n_dateCreateFromFormat($session->fin, $_SESSION['user_lang']).'</td>
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