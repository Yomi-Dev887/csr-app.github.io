<?php

# new constituency
if(isset($_POST['addConstituency'])){
    $constituency = trim(stripslashes(mysqli_real_escape_string($db, $_POST['constituency'])));
    $state = intval($_POST['state']);
    if(empty($constituency)){
        $errs[] = $constituencyErr = "please enter a constituency name";
    }
    else{
        if(cntRows('constituencies', "*", "name='$constituency'") > 0){
            $errs[] = $constituencyErr = "constituency already exists";
        }
    }
    if($state == 0){
        $errs[] = $stateErr = "please select a state";
    }

    if(count($errs) == 0){
        $q = dbInsert('constituencies', ['name'=>$constituency, 'state'=>$state, 'dc'=>$now]);
        if($q){
            $sMsg = "constituency '$constituency' added successfully";
        }
        else{
            $eMsg = "something went wrong".mysqli_error($db);
        }
    }
}

if(isset($_GET['id'])){
    $id = $_GET['id'];
    if($id > 0){
        $constituencyName = getDBVal('constituencies', $id, null,'name');
        $dbState = getDBVal('constituencies', $id, null,'state');

        if(isset($_POST['updateConstituency'])){
            $constituency = trim(mysqli_real_escape_string($db, $_POST['constituency']));
            if(empty($constituency)){
                $errs[] = $constituencyErr = "please enter a name";
            }
            else{
                if(cntRows('constituencies', "*", "name='$constituency' AND id=$id") > 0){
                    $errs[] = $constituencyErr = "please modify the name to continue";
                }
                if(cntRows('constituencies', "*", "name='$constituency' AND id<>$id") > 0){
                    $errs[] = $constituencyErr = "name already exists";
                }
            }

            if(count($errs) == 0){
                $q = dbUpdate('constituencies', 'id='.$id, ['name'=>$constituency, 'du'=>$now]);
                if($q){
                    $sMsg = "constituency '$constituencyName' updated to '$constituency' successfully";
                }
                else{
                    $eMsg = "something went wrong".mysqli_error($db);
                }
            }
        }

        if(isset($_POST['updateState'])){
            $state = intval($_POST['state']);
            if($state == 0){
                $errs[] = $stateErr = "please enter a valid capital address";
            }
            else{
                if(cntRows('constituencies', "*", "state=$state AND id=$id") > 0){
                    $errs[] = $stateErr = "please modify the state to continue";
                }
            }

            if(count($errs) == 0){
                $q = dbUpdate('constituencies', 'id='.$id, ['state'=>$state, 'du'=>$now]);
                if($q){
                    $sMsg = "constituency state updated successfully";
                }
                else{
                    $eMsg = "something went wrong".mysqli_error($db);
                }
            }
        }

        if(isset($_GET['activate'])){
            if(modifyStatus('constituencies', $id, 'active') == 'success'){
                $sMsg = "constituency '$constituencyName' activated successfully";
                pageReload(3000, $pageURL);
            }
        }

        if(isset($_GET['deactivate'])){
            if(modifyStatus('constituencies', $id, 'inactive') == 'success'){
                $sMsg = "constituency '$constituencyName' deactivated successfully";
                pageReload(3000, $pageURL);
            }
            else{
                $eMsg = "failed";
            }
        }
        if(isset($_GET['trash'])){
            if(modifyStatus('constituencies', $id, 'trashed') == 'success'){
                $sMsg = "constituency '$constituencyName' trashed successfully";
                pageReload(3000, $pageURL);
            }
        }
        if(isset($_GET['restore'])){
            if(modifyStatus('constituencies', $id, 'active') == 'success'){
                $sMsg = "constituency '$constituencyName' restored successfully";
                pageReload(3000, $pageURL);
            }
        }

        if(isset($_GET['delete'])){
            $app_config['promptMsg'] = "You're about to delete constituency '$constituencyName'. Are you sure?";
            $app_config['prompt'] = true;
            $app_config['promptType'] = "dark";
            if(isset($_POST['doPrompt'])){
                $app_config['prompt'] = false;
                if(dbDelete('constituencies','id='.$id) == "success"){
                    $sMsg = "constituency '$constituencyName' deleted successfully";
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
    $app_config['promptMsg'] = "You're about to truncate 'constituencies' table. Are you sure?";
    $app_config['prompt'] = true;
    $app_config['promptType'] = "danger";
    if(isset($_POST['doPrompt'])) {
        $app_config['prompt'] = false;
        if (dbTruncate('constituencies') == 'success') {
            $sMsg = "Constituencies table truncated successfully";
            pageReload(5000, $pageURL);
        } else {
            $eMsg = "something went wrong";
        }
    }
}