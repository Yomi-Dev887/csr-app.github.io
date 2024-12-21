<?php
# new gender
if(isset($_POST['addGender'])){
    $gender = trim(stripslashes(mysqli_real_escape_string($db, $_POST['gender'])));
    if(empty($gender)){
        $errs[] = $genderErr = "please enter a gender name";
    }
    else{
        if(cntRows('genders', "*", "name='$gender'") > 0){
            $errs[] = $genderErr = "gender already exists";
        }
    }

    if(count($errs) == 0){
        $q = dbInsert('genders', ['name'=>$gender, 'dc'=>$now]);
        if($q){
            $sMsg = "gender '$gender' added successfully";
        }
        else{
            $eMsg = "something went wrong".mysqli_error($db);
        }
    }
}

if(isset($_GET['id'])){
    $id = $_GET['id'];
    if($id > 0){
        $genderName = getDBVal('genders', $id, null);

        if(isset($_POST['updateGender'])){
            $gender = trim(mysqli_real_escape_string($db, $_POST['gender']));
            if(empty($gender)){
                $errs[] = $genderErr = "please enter a gender name";
            }
            else{
                if(cntRows('genders', "*", "name='$gender' AND id=$id") > 0){
                    $errs[] = $genderErr = "please modify the gender to continue";
                }
                if(cntRows('genders', "*", "name='$gender' AND id<>$id") > 0){
                    $errs[] = $genderErr = "gender already exists";
                }
            }

            if(count($errs) == 0){
                $q = dbUpdate('genders', 'id='.$id, ['name'=>$gender, 'du'=>$now]);
                if($q){
                    $sMsg = "gender '$genderName' updated to '$gender' successfully";
                }
                else{
                    $eMsg = "something went wrong".mysqli_error($db);
                }
            }
        }

        if(isset($_GET['activate'])){
            if(modifyStatus('genders', $id, 'active') == 'success'){
                $sMsg = "gender '$genderName' activated successfully";
                pageReload(3000, $pageURL);
            }
        }

        if(isset($_GET['deactivate'])){
            if(modifyStatus('genders', $id, 'inactive') == 'success'){
                $sMsg = "gender '$genderName' deactivated successfully";
                pageReload(3000, $pageURL);
            }
            else{
                $eMsg = "failed";
            }
        }
        if(isset($_GET['trash'])){
            if(modifyStatus('genders', $id, 'trashed') == 'success'){
                $sMsg = "gender '$genderName' trashed successfully";
                pageReload(3000, $pageURL);
            }
        }
        if(isset($_GET['restore'])){
            if(modifyStatus('genders', $id, 'active') == 'success'){
                $sMsg = "gender '$genderName' restored successfully";
                pageReload(3000, $pageURL);
            }
        }

        if(isset($_GET['delete'])){
            $app_config['promptMsg'] = "You're about to delete gender '$genderName'. Are you sure?";
            $app_config['prompt'] = true;
            $app_config['promptType'] = "dark";
            if(isset($_POST['doPrompt'])){
                $app_config['prompt'] = false;
                if(dbDelete('genders','id='.$id) == "success"){
                    $sMsg = "gender '$genderName' deleted successfully";
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
    $app_config['promptMsg'] = "You're about to truncate 'genders' table. Are you sure?";
    $app_config['prompt'] = true;
    $app_config['promptType'] = "danger";
    if(isset($_POST['doPrompt'])) {
        $app_config['prompt'] = false;
        if (dbTruncate('genders') == 'success') {
            $sMsg = "Genders table truncated successfully";
            pageReload(5000, $pageURL);
        } else {
            $eMsg = "something went wrong";
        }
    }
}