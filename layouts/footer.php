 <?php

	global $startTime;
	$endTime =  microtime(true);
	$timeStr = $app_lang['execution_time'] . ': ' . number_format((($endTime - $startTime) * 1), '5', '.', ' ') . ' s - ' . $app_lang['memory_usage'] . ': ' . round((round(memory_get_usage() / 1024 * 100) / 100), 2) . " Kb";

	?>
 <!-- content end -->
 <p style="text-align: center;"><span style="font-size:smaller;"><?= $app_lang['powered_by'] ?> <a href="https://www.bluenote-systems.com" target="_bns">Blue note systems</a></span>
 	<br /><span style="font-size:10px"><i><?= $timeStr ?></i></span>
 <p>
 	</div>

 	<script type="text/javascript" src="assets/js/bootstrap.bundle.min.js"></script>
 	<script type="text/javascript" src="assets/js/jquery.min.js"></script>
 	<script type="text/javascript" src="assets/js/prism.js" data-manual></script>
 	<script type="text/javascript" src="assets/js/custom.js"></script>

 	<script type='text/javascript'>
 		$(document).ready(function() {
 			let hash = document.location.hash;
 			if (hash) {
 				$('.nav-tabs a[href=\"' + hash + '\"]').tab('show');
 			}
 		});
 	</script>

 	</body>

 	</html>