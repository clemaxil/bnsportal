<?php
if (!defined('APP_ENTRY') || !APP_ENTRY) die('Not A Valid Entry Point');

include __DIR__ . '/../../../layouts/header.php';
include __DIR__ . '/../../../layouts/menu.php';
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
    <tr class="table-primary">
		<td><a href="<?= appHelperUrl_link($lang, 'session', 'detail', '9eb9c014-85cd-11ec-9ccf-0050569c3446') ?>">Column content</a></td>
		<td>Column content</td>
		<td>Column content</td>
		<td>Column content</td>
		<td>Column content</td>
		<td>Column content</td>
		<td>Column content</td>
		<td>Column content</td>
	</tr>
	<tr class="table-primary">
		<td>Column content</td>
		<td>Column content</td>
		<td>Column content</td>
		<td>Column content</td>
		<td>Column content</td>
		<td>Column content</td>
		<td>Column content</td>
		<td>Column content</td>
	</tr>
	<tr class="table-primary">
		<td>Column content</td>
		<td>Column content</td>
		<td>Column content</td>
		<td>Column content</td>
		<td>Column content</td>
		<td>Column content</td>
		<td>Column content</td>
		<td>Column content</td>
	</tr>
	<tr class="table-primary">
		<td>Column content</td>
		<td>Column content</td>
		<td>Column content</td>
		<td>Column content</td>
		<td>Column content</td>
		<td>Column content</td>
		<td>Column content</td>
		<td>Column content</td>
	</tr>
  </tbody>
</table>



<?php
//Content End
include __DIR__ . '/../../../layouts/footer.php';
?>