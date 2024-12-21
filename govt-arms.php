<?php
# new arm
if(isset($_POST['addArm'])){
    $govtArm = trim(stripslashes(mysqli_real_escape_string($db, $_POST['govtArm'])));
    if(empty($govtArm)){
        $errs[] = $govtArmErr = "please enter a arm name";
    }
    else{
        if(cntRows('govt_arms', "*", "name='$govtArm'") > 0){
            $errs[] = $govtArmErr = "arm already exists";
        }
    }

    if(count($errs) == 0){
        $q = dbInsert('govt_arms', ['name'=>$govtArm, 'dc'=>$now]);
        if($q){
            $sMsg = "arm '$govtArm' added successfully";
        }
        else{
            $eMsg = "something went wrong".mysqli_error($db);
        }
    }
}

if(isset($_GET['id'])){
    $id = $_GET['id'];
    if($id > 0){
        $govtArmName = getDBVal('govt_arms', $id, null);

        if(isset($_POST['updateArm'])){
            $govtArm = trim(mysqli_real_escape_string($db, $_POST['govtArm']));
            if(empty($govtArm)){
                $errs[] = $govtArmErr = "please enter a arm name";
            }
            else{
                if(cntRows('govt_arms', "*", "name='$govtArm' AND id=$id") > 0){
                    $errs[] = $govtArmErr = "please modify the arm to continue";
                }
                if(cntRows('govt_arms', "*", "name='$govtArm' AND id<>$id") > 0){
                    $errs[] = $govtArmErr = "arm already exists";
                }
            }

            if(count($errs) == 0){
                $q = dbUpdate('govt_arms', 'id='.$id, ['name'=>$govtArm, 'du'=>$now]);
                if($q){
                    $sMsg = "arm '$govtArmName' updated to '$govtArm' successfully";
                }
                else{
                    $eMsg = "something went wrong".mysqli_error($db);
                }
            }
        }

        if(isset($_GET['activate'])){
            if(modifyStatus('govt_arms', $id, 'active') == 'success'){
                $sMsg = "arm '$govtArmName' activated successfully";
                pageReload(3000, $pageURL);
            }
        }

        if(isset($_GET['deactivate'])){
            if(modifyStatus('govt_arms', $id, 'inactive') == 'success'){
                $sMsg = "arm '$govtArmName' deactivated successfully";
                pageReload(3000, $pageURL);
            }
            else{
                $eMsg = "failed";
            }
        }
        if(isset($_GET['trash'])){
            if(modifyStatus('govt_arms', $id, 'trashed') == 'success'){
                $sMsg = "arm '$govtArmName' trashed successfully";
                pageReload(3000, $pageURL);
            }
        }
        if(isset($_GET['restore'])){
            if(modifyStatus('govt_arms', $id, 'active') == 'success'){
                $sMsg = "arm '$govtArmName' restored successfully";
                pageReload(3000, $pageURL);
            }
        }

        if(isset($_GET['delete'])){
            $app_config['promptMsg'] = "You're about to delete arm '$govtArmName'. Are you sure?";
            $app_config['prompt'] = true;
            $app_config['promptType'] = "dark";
            if(isset($_POST['doPrompt'])){
                $app_config['prompt'] = false;
                if(dbDelete('govt_arms','id='.$id) == "success"){
                    $sMsg = "arm '$govtArmName' deleted successfully";
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
    $app_config['promptMsg'] = "You're about to truncate 'government arms' table. Are you sure?";
    $app_config['prompt'] = true;
    $app_config['promptArm'] = "danger";
    if(isset($_POST['doPrompt'])) {
        $app_config['prompt'] = false;
        if (dbTruncate('govt_arms') == 'success') {
            $sMsg = "Arms table truncated successfully";
            pageReload(5000, $pageURL);
        } else {
            $eMsg = "something went wrong";
        }
    }
}