<?php
# new level
if(isset($_POST['addLevel'])){
    $govtLevel = trim(stripslashes(mysqli_real_escape_string($db, $_POST['govtLevel'])));
    if(empty($govtLevel)){
        $errs[] = $govtLevelErr = "please enter a level name";
    }
    else{
        if(cntRows('govt_levels', "*", "name='$govtLevel'") > 0){
            $errs[] = $govtLevelErr = "level already exists";
        }
    }

    if(count($errs) == 0){
        $q = dbInsert('govt_levels', ['name'=>$govtLevel, 'dc'=>$now]);
        if($q){
            $sMsg = "level '$govtLevel' added successfully";
        }
        else{
            $eMsg = "something went wrong".mysqli_error($db);
        }
    }
}

if(isset($_GET['id'])){
    $id = $_GET['id'];
    if($id > 0){
        $govtLevelName = getDBVal('govt_levels', $id, null);

        if(isset($_POST['updateLevel'])){
            $govtLevel = trim(mysqli_real_escape_string($db, $_POST['govtLevel']));
            if(empty($govtLevel)){
                $errs[] = $govtLevelErr = "please enter a level name";
            }
            else{
                if(cntRows('govt_levels', "*", "name='$govtLevel' AND id=$id") > 0){
                    $errs[] = $govtLevelErr = "please modify the level to continue";
                }
                if(cntRows('govt_levels', "*", "name='$govtLevel' AND id<>$id") > 0){
                    $errs[] = $govtLevelErr = "level already exists";
                }
            }

            if(count($errs) == 0){
                $q = dbUpdate('govt_levels', 'id='.$id, ['name'=>$govtLevel, 'du'=>$now]);
                if($q){
                    $sMsg = "level '$govtLevelName' updated to '$govtLevel' successfully";
                }
                else{
                    $eMsg = "something went wrong".mysqli_error($db);
                }
            }
        }

        if(isset($_GET['activate'])){
            if(modifyStatus('govt_levels', $id, 'active') == 'success'){
                $sMsg = "level '$govtLevelName' activated successfully";
                pageReload(3000, $pageURL);
            }
        }

        if(isset($_GET['deactivate'])){
            if(modifyStatus('govt_levels', $id, 'inactive') == 'success'){
                $sMsg = "level '$govtLevelName' deactivated successfully";
                pageReload(3000, $pageURL);
            }
            else{
                $eMsg = "failed";
            }
        }
        if(isset($_GET['trash'])){
            if(modifyStatus('govt_levels', $id, 'trashed') == 'success'){
                $sMsg = "level '$govtLevelName' trashed successfully";
                pageReload(3000, $pageURL);
            }
        }
        if(isset($_GET['restore'])){
            if(modifyStatus('govt_levels', $id, 'active') == 'success'){
                $sMsg = "level '$govtLevelName' restored successfully";
                pageReload(3000, $pageURL);
            }
        }

        if(isset($_GET['delete'])){
            $app_config['promptMsg'] = "You're about to delete level '$govtLevelName'. Are you sure?";
            $app_config['prompt'] = true;
            $app_config['promptType'] = "dark";
            if(isset($_POST['doPrompt'])){
                $app_config['prompt'] = false;
                if(dbDelete('govt_levels','id='.$id) == "success"){
                    $sMsg = "level '$govtLevelName' deleted successfully";
                    pageReload(5000, $pageURL);
                }
                else{
                    $eMsg = "something went wrong".mysqli_error($db);
                }
            }
        }
    }
    else{
        $eMsg = "invalid data reference encountered";
    }
}

if(isset($_GET['truncate'])){
    $app_config['promptMsg'] = "You're about to truncate 'government levels' table. Are you sure?";
    $app_config['prompt'] = true;
    $app_config['promptLevel'] = "danger";
    if(isset($_POST['doPrompt'])) {
        $app_config['prompt'] = false;
        if (dbTruncate('govt_levels') == 'success') {
            $sMsg = "Levels table truncated successfully";
            pageReload(5000, $pageURL);
        } else {
            $eMsg = "something went wrong";
        }
    }
}