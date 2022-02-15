<?php
if (!defined('APP_ENTRY') || !APP_ENTRY) die('Not A Valid Entry Point');

include __DIR__ . '/../../../layouts/header.php';
include __DIR__ . '/../../../layouts/menu.php';

if ($dataView['error'] === 1) {
  echo '<div class="alert alert-dismissible alert-danger">
				<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
				<strong>Error : </strong> ' . $dataView['error-message'] . '</div>';
} else {

?>

  <link rel='stylesheet' href='modules/calendar/includes/fullcalendar/lib/main.min.css'>
  <script src='modules/calendar/includes/fullcalendar/lib/main.min.js'></script>
  <script src='modules/calendar/includes/fullcalendar/lib/locales-all.min.js'></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var calendarEl = document.getElementById('calendar');
      var initialLocaleCode = 'en';
      var calendar = new FullCalendar.Calendar(calendarEl, {
        headerToolbar: {
          left: 'prev,next,today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth',
        },

        initialDate: '<?= date("Y-m-d"); ?>',
        locale: initialLocaleCode,
        navLinks: true,
        editable: true,
        eventStartEditable: false,
        dayMaxEvents: true,
        // weekNumbers: true,
        eventSources: [

          // your event source
          <?php
          foreach ($dataView['calendars'] as $calendar) {

            echo "{\n";
            echo "events: [\n";

            foreach ($calendar['events'] as $event) {
              $title = $event->summary;
              $start = $event->dtstart;
              $end = $event->dtend;
              $description = $event->description;
              $location = $event->location;
              $url = $event->uid;
              echo "{\n";
              echo "title: '" . str_replace("'", "", str_replace("\n", "", $title)) . "',\n";
              echo "start: '" . $start . "',\n";
              if (!empty($end)) {
                echo "end: '" . $end . "',\n";
              }
              echo "description: '" . str_replace("'", "", str_replace("\n", "", $description)) . "',\n";
              echo "location: '" . str_replace("'", "", str_replace("\n", "", $location)) . "',\n";
              if (!empty($event->uid)) {
                echo "url: '" . appHelperUrl_link($lang, 'calendar', 'detail', $event->uid) . "'\n";
              }
              echo "},\n";
            }

            echo "],\n";
            echo "color: '" . $calendar['config']['color'] . "',\n";
            echo "textColor: '" . $calendar['config']['textColor'] . "'\n";

            echo "},\n";
          }
          ?>
        ],


        /*eventClick: function(info,event) {
          if( info.event.extendedProps.description != "" || info.event.extendedProps.location != "" ){
            alert('Titre: '+info.event.title + '\nDescription: ' + info.event.extendedProps.description + '\nLocation: ' + info.event.extendedProps.location);
            info.el.style.borderColor = 'red';
          }
        }*/


      });


      calendar.render();
      calendar.setOption('aspectRatio', 1.8);
      calendar.setOption('locale', '<?= $lang; ?>');

    });
  </script>

  <style>
    #calendar {
      max-width: 100%;
      margin: 0 auto;
    }

    .fc-toolbar {
      margin-bottom: 0 !important;
    }

    .fc .fc-button {
      font-size: 14px;
      background-color: #3459E6;
      color: white;
    }

    .fc .fc-scrollgrid-section-body:not(.fc-scrollgrid-section-liquid) table {
      display: none;
    }
  </style>


  <div id='calendar'></div>

<?php

}

include __DIR__ . '/../../../layouts/footer.php';

?>