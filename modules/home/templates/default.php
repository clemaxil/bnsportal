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

<div style="text-align:center">
    <h1><?= $mod_lang['title']; ?></h1>
    <?= $mod_lang['content']; ?>


<div class="row">
    <div class="col-lg-4">

    <div class="card text-white bg-primary mb-3" style="max-width: 30rem;">
        <div class="card-header"><?= $mod_lang['session_planned'] ?></div>
        <div class="card-body">
            <?php 
                if(appHelperRole_isGranted("client")){
                    echo '<a href="'.appHelperUrl_link($lang, 'session', 'index', '', 'status=planned').'" style="color: white; text-decoration: none;">
                            <h1 class="card-title">'.$dataView['sessions']->planned.'</h1>
                        </a>';
                }
                else{
                    echo '<h1 class="card-title">'.$dataView['sessions']->planned.'</h1>';
                }
            ?>            
        </div>
    </div>

    </div>

    <div class="col-lg-4">

    <div class="card text-white bg-primary mb-3" style="max-width: 30rem;">
        <div class="card-header"><?= $mod_lang['session_inprogress'] ?></div>
        <div class="card-body">
            <?php 
                if(appHelperRole_isGranted("client")){
                    echo '<a href="'.appHelperUrl_link($lang, 'session', 'index', '', 'status=inprogress').'" style="color: white; text-decoration: none;">
                            <h1 class="card-title">'.$dataView['sessions']->inprogress.'</h1>
                        </a>';
                }
                else{
                    echo '<h1 class="card-title">'.$dataView['sessions']->inprogress.'</h1>';
                }
            ?>  
        </div>
    </div>

    </div>

    <div class="col-lg-4">

    <div class="card text-white bg-primary mb-3" style="max-width: 30rem;">
        <div class="card-header"><?= $mod_lang['session_closed'] ?></div>
        <div class="card-body">
            <?php 
                if(appHelperRole_isGranted("client")){
                    echo '<a href="'.appHelperUrl_link($lang, 'session', 'index', '', 'status=closed').'" style="color: white; text-decoration: none;">
                            <h1 class="card-title">'.$dataView['sessions']->closed.'</h1>
                        </a>';
                }
                else{
                    echo '<h1 class="card-title">'.$dataView['sessions']->closed.'</h1>';
                }
            ?>    
        </div>
    </div>

    </div>
</div>
</div>



<?php
}
//Content End
include __DIR__ . '/../../../layouts/footer.php';
?>