<?php
if (!defined('APP_ENTRY') || !APP_ENTRY) die('Not A Valid Entry Point');

include __DIR__ . '/../../../layouts/header.php';
include __DIR__ . '/../../../layouts/menu.php';

// require_once(__DIR__ . '/../../../helpers/appHelperI18n.php');


$date_format = 'd/m/Y';
if ($lang == 'en') {
    $date_format = 'Y-m-d';
}
?>


<div style="text-align:left;">
    <?php

    echo '<p><b>' . $mod_lang['first_name'] . ':</b> ' . $_SESSION['user_first_name'] . '</p>';
    echo '<p><b>' . $mod_lang['last_name'] . ':</b> ' . $_SESSION['user_last_name'] . '</p>';
    echo '<p><b>' . $mod_lang['email'] . ':</b> ' . $_SESSION['user_email'] . '</p>';
    echo '<p><b>' . $mod_lang['phone_mobile'] . ':</b> ' . $_SESSION['user_phone_mobile'] . '</p>';
    echo '<p><b>' . $mod_lang['address_street'] . ':</b> ' . $_SESSION['user_address_street'] . '</p>';
    echo '<p><b>' . $mod_lang['address_postalcode'] . ':</b> ' . $_SESSION['user_address_postalcode'] . '</p>';
    echo '<p><b>' . $mod_lang['address_city'] . ':</b> ' . $_SESSION['user_address_city'] . '</p>';
    echo '<p><b>' . $mod_lang['address_country'] . ':</b> ' . strtoupper($_SESSION['user_address_country']) . '</p>';
    //echo '<p><b>' . $mod_lang['birthdate'] . ':</b> ' . appHelperI18n_convertDateFromTimezone($dataView['birthdate'], "GMT", "Europe/Paris", $date_format) . '</p>';
    //echo '<p><b>'.$mod_lang['lang'].':</b> '.$emoji_flags[strtoupper($dataView['lang'])].' '.strtoupper($dataView['lang']).'</p>';

    echo "<p><b>" . $mod_lang['calendars'] . ":</b>";
    echo "<ul>";

    foreach (json_decode($_SESSION['user_calendars'])->data as $calendar) {
        $calendar_name = '';

        if (strpos($calendar->url, "_validate.ics") != false) {
            $calendar_name = $mod_lang['trainings'];
        }

        if (strpos($calendar->url, "_meetings.ics") != false) {
            $calendar_name = $mod_lang['meetings'];
        }

        if (strpos($calendar->url, "_calls.ics") != false) {
            $calendar_name = $mod_lang['calls'];
        }

        if (!empty($calendar_name)) {
            echo '<li><a href="#" onclick="javascript:location.href=\'' . $calendar->url . '\';">';
            echo $calendar_name . '</a></li>';
        }
    }

    echo "</ul>
        </p>";

    ?>
</div>

<?php

include __DIR__ . '/../../../layouts/footer.php';

?>