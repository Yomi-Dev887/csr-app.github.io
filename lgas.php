<?php

# new lga
if(isset($_POST['addLga'])){
    $lga = trim(stripslashes(mysqli_real_escape_string($db, $_POST['lga'])));
    $lgaConstituency = intval($_POST['lgaConstituency']);
    if(empty($lga)){
        $errs[] = $lgaErr = "please enter a lga name";
    }
    else{
        if(cntRows('lgas', "*", "name='$lga'") > 0){
            $errs[] = $lgaErr = "lga already exists";
        }
    }
    if($lgaConstituency == 0){
        $errs[] = $lgaConstituencyErr = "please select a constituency";
    }

    if(count($errs) == 0){
        $q = dbInsert('lgas', ['name'=>$lga, 'constituency'=>$lgaConstituency, 'dc'=>$now]);
        if($q){
            $sMsg = "lga '$lga' added successfully";
        }
        else{
            $eMsg = "something went wrong".mysqli_error($db);
        }
    }
}

if(isset($_GET['id'])){
    $id = $_GET['id'];
    if($id > 0){
        $lgaName = getDBVal('lgas', $id, null,'name');
        $dbConstituency = getDBVal('lgas', $id, null,'constituency');

        if(isset($_POST['updateLga'])){
            $lga = trim(mysqli_real_escape_string($db, $_POST['lga']));
            if(empty($lga)){
                $errs[] = $lgaErr = "please enter a name";
            }
            else{
                if(cntRows('lgas', "*", "name='$lga' AND id=$id") > 0){
                    $errs[] = $lgaErr = "please modify the name to continue";
                }
                if(cntRows('lgas', "*", "name='$lga' AND id<>$id") > 0){
                    $errs[] = $lgaErr = "name already exists";
                }
            }

            if(count($errs) == 0){
                $q = dbUpdate('lgas', 'id='.$id, ['name'=>$lga, 'du'=>$now]);
                if($q){
                    $sMsg = "lga '$lgaName' updated to '$lga' successfully";
                }
                else{
                    $eMsg = "something went wrong".mysqli_error($db);
                }
            }
        }

        if(isset($_POST['updateConstituency'])){
            $lgaConstituency = intval($_POST['lgaConstituency']);
            if($lgaConstituency == 0){
                $errs[] = $lgaConstituencyErr = "please enter a valid capital address";
            }
            else{
                if(cntRows('lgas', "*", "constituency=$lgaConstituency AND id=$id") > 0){
                    $errs[] = $lgaConstituencyErr = "please modify the constituency to continue";
                }
            }

            if(count($errs) == 0){
                $q = dbUpdate('lgas', 'id='.$id, ['constituency'=>$lgaConstituency, 'du'=>$now]);
                if($q){
                    $sMsg = "lga constituency updated successfully";
                }
                else{
                    $eMsg = "something went wrong".mysqli_error($db);
                }
            }
        }

        if(isset($_GET['activate'])){
            if(modifyStatus('lgas', $id, 'active') == 'success'){
                $sMsg = "lga '$lgaName' activated successfully";
                pageReload(3000, $pageURL);
            }
        }

        if(isset($_GET['deactivate'])){
            if(modifyStatus('lgas', $id, 'inactive') == 'success'){
                $sMsg = "lga '$lgaName' deactivated successfully";
                pageReload(3000, $pageURL);
            }
            else{
                $eMsg = "failed";
            }
        }
        if(isset($_GET['trash'])){
            if(modifyStatus('lgas', $id, 'trashed') == 'success'){
                $sMsg = "lga '$lgaName' trashed successfully";
                pageReload(3000, $pageURL);
            }
        }
        if(isset($_GET['restore'])){
            if(modifyStatus('lgas', $id, 'active') == 'success'){
                $sMsg = "lga '$lgaName' restored successfully";
                pageReload(3000, $pageURL);
            }
        }

        if(isset($_GET['delete'])){
            $app_config['promptMsg'] = "You're about to delete lga '$lgaName'. Are you sure?";
            $app_config['prompt'] = true;
            $app_config['promptType'] = "dark";
            if(isset($_POST['doPrompt'])){
                $app_config['prompt'] = false;
                if(dbDelete('lgas','id='.$id) == "success"){
                    $sMsg = "lga '$lgaName' deleted successfully";
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
    $app_config['promptMsg'] = "You're about to truncate 'lgas' table. Are you sure?";
    $app_config['prompt'] = true;
    $app_config['promptType'] = "danger";
    if(isset($_POST['doPrompt'])) {
        $app_config['prompt'] = false;
        if (dbTruncate('lgas') == 'success') {
            $sMsg = "Lgas table truncated successfully";
            pageReload(5000, $pageURL);
        } else {
            $eMsg = "something went wrong";
        }
    }
}