<?php
# new type
if(isset($_POST['addType'])){
    $employmentType = trim(stripslashes(mysqli_real_escape_string($db, $_POST['employmentType'])));
    if(empty($employmentType)){
        $errs[] = $employmentTypeErr = "please enter a type name";
    }
    else{
        if(cntRows('employment_types', "*", "name='$employmentType'") > 0){
            $errs[] = $employmentTypeErr = "type already exists";
        }
    }

    if(count($errs) == 0){
        $q = dbInsert('employment_types', ['name'=>$employmentType, 'dc'=>$now]);
        if($q){
            $sMsg = "type '$employmentType' added successfully";
        }
        else{
            $eMsg = "something went wrong".mysqli_error($db);
        }
    }
}

if(isset($_GET['id'])){
    $id = $_GET['id'];
    if($id > 0){
        $employmentTypeName = getDBVal('employment_types', $id, null);

        if(isset($_POST['updateType'])){
            $employmentType = trim(mysqli_real_escape_string($db, $_POST['employmentType']));
            if(empty($employmentType)){
                $errs[] = $employmentTypeErr = "please enter a type name";
            }
            else{
                if(cntRows('employment_types', "*", "name='$employmentType' AND id=$id") > 0){
                    $errs[] = $employmentTypeErr = "please modify the type to continue";
                }
                if(cntRows('employment_types', "*", "name='$employmentType' AND id<>$id") > 0){
                    $errs[] = $employmentTypeErr = "type already exists";
                }
            }

            if(count($errs) == 0){
                $q = dbUpdate('employment_types', 'id='.$id, ['name'=>$employmentType, 'du'=>$now]);
                if($q){
                    $sMsg = "type '$employmentTypeName' updated to '$employmentType' successfully";
                }
                else{
                    $eMsg = "something went wrong".mysqli_error($db);
                }
            }
        }

        if(isset($_GET['activate'])){
            if(modifyStatus('employment_types', $id, 'active') == 'success'){
                $sMsg = "type '$employmentTypeName' activated successfully";
                pageReload(3000, $pageURL);
            }
        }

        if(isset($_GET['deactivate'])){
            if(modifyStatus('employment_types', $id, 'inactive') == 'success'){
                $sMsg = "type '$employmentTypeName' deactivated successfully";
                pageReload(3000, $pageURL);
            }
            else{
                $eMsg = "failed";
            }
        }
        if(isset($_GET['trash'])){
            if(modifyStatus('employment_types', $id, 'trashed') == 'success'){
                $sMsg = "type '$employmentTypeName' trashed successfully";
                pageReload(3000, $pageURL);
            }
        }
        if(isset($_GET['restore'])){
            if(modifyStatus('employment_types', $id, 'active') == 'success'){
                $sMsg = "type '$employmentTypeName' restored successfully";
                pageReload(3000, $pageURL);
            }
        }

        if(isset($_GET['delete'])){
            $app_config['promptMsg'] = "You're about to delete type '$employmentTypeName'. Are you sure?";
            $app_config['prompt'] = true;
            $app_config['promptType'] = "dark";
            if(isset($_POST['doPrompt'])){
                $app_config['prompt'] = false;
                if(dbDelete('employment_types','id='.$id) == "success"){
                    $sMsg = "type '$employmentTypeName' deleted successfully";
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
    $app_config['promptMsg'] = "You're about to truncate 'employment types' table. Are you sure?";
    $app_config['prompt'] = true;
    $app_config['promptType'] = "danger";
    if(isset($_POST['doPrompt'])) {
        $app_config['prompt'] = false;
        if (dbTruncate('employment_types') == 'success') {
            $sMsg = "Types table truncated successfully";
            pageReload(5000, $pageURL);
        } else {
            $eMsg = "something went wrong";
        }
    }
}