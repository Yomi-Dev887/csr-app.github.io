<?php
# new designation
if(isset($_POST['addDesignation'])){
    $designation = trim(stripslashes(mysqli_real_escape_string($db, $_POST['designation'])));
    if(empty($designation)){
        $errs[] = $designationErr = "please enter a designation name";
    }
    else{
        if(cntRows('designations', "*", "name='$designation'") > 0){
            $errs[] = $designationErr = "designation already exists";
        }
    }

    if(count($errs) == 0){
        $q = dbInsert('designations', ['name'=>$designation, 'dc'=>$now]);
        if($q){
            $sMsg = "designation '$designation' added successfully";
        }
        else{
            $eMsg = "something went wrong".mysqli_error($db);
        }
    }
}

if(isset($_GET['id'])){
    $id = $_GET['id'];
    if($id > 0){
        $designationName = getDBVal('designations', $id, null);

        if(isset($_POST['updateDesignation'])){
            $designation = trim(mysqli_real_escape_string($db, $_POST['designation']));
            if(empty($designation)){
                $errs[] = $designationErr = "please enter a designation name";
            }
            else{
                if(cntRows('designations', "*", "name='$designation' AND id=$id") > 0){
                    $errs[] = $designationErr = "please modify the designation to continue";
                }
                if(cntRows('designations', "*", "name='$designation' AND id<>$id") > 0){
                    $errs[] = $designationErr = "designation already exists";
                }
            }

            if(count($errs) == 0){
                $q = dbUpdate('designations', 'id='.$id, ['name'=>$designation, 'du'=>$now]);
                if($q){
                    $sMsg = "designation '$designationName' updated to '$designation' successfully";
                }
                else{
                    $eMsg = "something went wrong".mysqli_error($db);
                }
            }
        }

        if(isset($_GET['activate'])){
            if(modifyStatus('designations', $id, 'active') == 'success'){
                $sMsg = "designation '$designationName' activated successfully";
                pageReload(3000, $pageURL);
            }
        }

        if(isset($_GET['deactivate'])){
            if(modifyStatus('designations', $id, 'inactive') == 'success'){
                $sMsg = "designation '$designationName' deactivated successfully";
                pageReload(3000, $pageURL);
            }
            else{
                $eMsg = "failed";
            }
        }
        if(isset($_GET['trash'])){
            if(modifyStatus('designations', $id, 'trashed') == 'success'){
                $sMsg = "designation '$designationName' trashed successfully";
                pageReload(3000, $pageURL);
            }
        }
        if(isset($_GET['restore'])){
            if(modifyStatus('designations', $id, 'active') == 'success'){
                $sMsg = "designation '$designationName' restored successfully";
                pageReload(3000, $pageURL);
            }
        }

        if(isset($_GET['delete'])){
            $app_config['promptMsg'] = "You're about to delete designation '$designationName'. Are you sure?";
            $app_config['prompt'] = true;
            $app_config['promptType'] = "dark";
            if(isset($_POST['doPrompt'])){
                $app_config['prompt'] = false;
                if(dbDelete('designations','id='.$id) == "success"){
                    $sMsg = "designation '$designationName' deleted successfully";
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
    $app_config['promptMsg'] = "You're about to truncate 'designations' table. Are you sure?";
    $app_config['prompt'] = true;
    $app_config['promptType'] = "danger";
    if(isset($_POST['doPrompt'])) {
        $app_config['prompt'] = false;
        if (dbTruncate('designations') == 'success') {
            $sMsg = "Designations table truncated successfully";
            pageReload(5000, $pageURL);
        } else {
            $eMsg = "something went wrong";
        }
    }
}