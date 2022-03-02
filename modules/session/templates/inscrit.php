<?php
if (!defined('APP_ENTRY') || !APP_ENTRY) die('Not A Valid Entry Point');

include __DIR__ . '/../../../layouts/header.php';
include __DIR__ . '/../../../layouts/menu.php';

require_once(__DIR__ . '/../../../helpers/appHelperI18n.php');


if ($dataView['save'] == "ok") {
    echo '<div class="alert alert-dismissible alert-success">
				<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
				<strong>Success: </strong> ' . $dataView['save-message'] . '</div>';
}


if ($dataView['save'] == "false") {
    echo '<div class="alert alert-dismissible alert-warning">
				<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
				<strong>Warning : </strong> ' . $dataView['save-message'] . '</div>';
}




if ($dataView['error'] === 1) {
    echo '<div class="alert alert-dismissible alert-danger">
				<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
				<strong>Error : </strong> ' . $dataView['error-message'] . '</div>';
} else {
?>

    <div style="text-align:center;">
        <h4>
            <?= $dataView['session']->numero . ", " . $dataView['session']->name; ?>
        </h4>
    </div>

    <div style="text-align:left;">




        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link" href="<?= appHelperUrl_link($dataView['lang'], 'calendar', 'detail', $dataView['id']); ?>"><i class="fas fa-clipboard"></i> <?= $mod_lang['submenu_detail']; ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= appHelperUrl_link($dataView['lang'], 'calendar', 'date', $dataView['id']); ?>"><i class="fas fa-clock"></i> <?= $mod_lang['submenu_date']; ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="<?= appHelperUrl_link($dataView['lang'], 'calendar', 'inscrit', $dataView['id']); ?>"><i class="fas fa-user-graduate"></i> <?= $mod_lang['submenu_registred']; ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= appHelperUrl_link($dataView['lang'], 'calendar', 'document', $dataView['id']); ?>"><i class="fas fa-folder-open"></i> <?= $mod_lang['submenu_document']; ?></a>
            </li>
        </ul>

        <div id="myTabContent" class="tab-content">


            <?php


            echo '<div class="tab-pane fade show active" id="registrations">';


            /*echo "<pre>";
    print_r($dataView['registration_fields']);
    echo "</pre>";*/

            echo '<table class="table table-hover">
        <thead>
            <tr>
            <th scope="col">' . $mod_lang['registred_name'] . '</th>
            <th scope="col">' . $mod_lang['registred_phone'] . '</th>
            <th scope="col">' . $mod_lang['registred_email'] . '</th>
            <th scope="col">' . $mod_lang['registred_status'] . '</th>
            </tr>
        </thead>
        <tbody>';

            if (is_array($dataView['registrations'])) {

                foreach ($dataView['registrations'] as $registration) {
                    $registrationId = $registration->id;
                    $contactId = $registration->contact_id_c;

                    if ($registration->status == "Inscription confirmee") {
                        echo '<tr class="table-success">';
                    } else {
                        echo '<tr class="table-info">';
                    }

                    echo '<td>' . $dataView['contacts']->$contactId->first_name . ' ' . $dataView['contacts']->$contactId->last_name . '</td>';
                    echo '<td>' . $dataView['contacts']->$contactId->bns_contact_phone_mobile_full_c . '</td>';
                    echo '<td><a href="mailto:' . $dataView['contacts']->$contactId->email1 . '" target="_blank">' . $dataView['contacts']->$contactId->email1 . '</a></td>';
                    echo '<td>' . $registration->status . '</td>';
                    echo '</tr>';

                    //registrations fields        
                    if (is_array($dataView['registration_fields'])) {
                        echo '<tr class="table-secondary"><td colspan="4">';
                        foreach ($dataView['registration_fields'] as $fieldContactId => $val) {
                            if ($fieldContactId == $contactId) {
                                echo '<form name="sessionupdate" method="post" action="index.php">';
                                echo '<input type="hidden" name="q" value="' . $dataView['lang'] . '/calendar/update/' . $dataView['id'] . '">';
                                echo '<input type="hidden" name="record" value="' . $dataView['id'] . '">';
                                echo '<input type="hidden" name="tabname" value="registrations">';
                                echo '<input type="hidden" name="registrationid" value="' . $registration->id . '">';

                                echo '<p><b><span class="text-primary"><i class="fas fa-comment-dots"></i> ' . $mod_lang['notes'] . '</span></b>';

                                foreach ($val as $registration_fields) {
                                    echo $registration_fields;
                                }

                                //notes
                                foreach ($dataView['notes']->$registrationId as $note) {
                                    $fieldName = $note->id;
                                    echo '<div class="col-sm-10">
                                    <label for="' . $fieldName . '">' . $note->name . '</label>
                                    <div class="col-sm-1">
                                    <input class="form-control" style="text-align: right;" id="' . $fieldName . '" name="note_' . $fieldName . '" type="text" maxlength="6" value="' . $note->noteeval . '">
                                    </div>
                                </div>';
                                }

                                echo '<br /><div align="right"><button type="submit" class="btn btn-primary btn-sm">' . $mod_lang['button_update_label'] . '</button></div>';
                                echo "</p></form>";
                            }
                        }
                        echo "</td></tr>";
                    }
                }
            }



            echo '</tbody>
    </table>
    </div>';

            ?>

        </div>
    </div>


<?php

}

include __DIR__ . '/../../../layouts/footer.php';

?>