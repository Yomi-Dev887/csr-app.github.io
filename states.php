<?php

# new state
if(isset($_POST['addState'])){
    $state = trim(stripslashes(mysqli_real_escape_string($db, $_POST['state'])));
    $capital = trim(stripslashes(mysqli_real_escape_string($db, $_POST['capital'])));
    $stateRegion = intval($_POST['stateRegion']);
    if(empty($state)){
        $errs[] = $stateErr = "please enter a state name";
    }
    else{
        if(cntRows('states', "*", "name='$state'") > 0){
            $errs[] = $stateErr = "state already exists";
        }
    }
    if(empty($capital)){
        $errs[] = $capitalErr = "please enter a capital";
    }
    if($stateRegion == 0){
        $errs[] = $stateRegionErr = "please select a region";
    }

    if(count($errs) == 0){
        $q = dbInsert('states', ['name'=>$state, 'capital'=>$capital, 'region'=>$stateRegion, 'dc'=>$now]);
        if($q){
            $sMsg = "state '$state' added successfully";
        }
        else{
            $eMsg = "something went wrong".mysqli_error($db);
        }
    }
}

if(isset($_GET['id'])){
    $id = $_GET['id'];
    if($id > 0){
        $stateName = getDBVal('states', $id, null,'name');
        $dbCapital = getDBVal('states', $id, null,'capital');
        $dbRegion = getDBVal('states', $id, null,'region');

        if(isset($_POST['updateState'])){
            $state = trim(mysqli_real_escape_string($db, $_POST['state']));
            if(empty($state)){
                $errs[] = $stateErr = "please enter a name";
            }
            else{
                if(cntRows('states', "*", "name='$state' AND id=$id") > 0){
                    $errs[] = $stateErr = "please modify the name to continue";
                }
                if(cntRows('states', "*", "name='$state' AND id<>$id") > 0){
                    $errs[] = $stateErr = "name already exists";
                }
            }

            if(count($errs) == 0){
                $q = dbUpdate('states', 'id='.$id, ['name'=>$state, 'du'=>$now]);
                if($q){
                    $sMsg = "state '$stateName' updated to '$state' successfully";
                }
                else{
                    $eMsg = "something went wrong".mysqli_error($db);
                }
            }
        }

        if(isset($_POST['updateCapital'])){
            $capital = trim(mysqli_real_escape_string($db, $_POST['capital']));
            if(empty($capital)){
                $errs[] = $capitalErr = "please enter a valid capital address";
            }
            else{
                if(cntRows('states', "*", "capital='$capital' AND id=$id") > 0){
                    $errs[] = $capitalErr = "please modify the capital to continue";
                }
                if(cntRows('states', "*", "capital='$capital' AND id<>$id") > 0){
                    $errs[] = $capitalErr = "capital already exists";
                }
            }

            if(count($errs) == 0){
                $q = dbUpdate('states', 'id='.$id, ['capital'=>$capital, 'du'=>$now]);
                if($q){
                    $sMsg = "state capital '$dbCapital' updated to '$capital' successfully";
                }
                else{
                    $eMsg = "something went wrong".mysqli_error($db);
                }
            }
        }

        if(isset($_POST['updateRegion'])){
            $stateRegion = intval($_POST['stateRegion']);
            if($stateRegion == 0){
                $errs[] = $stateRegionErr = "please enter a valid capital address";
            }
            else{
                if(cntRows('states', "*", "region=$stateRegion AND id=$id") > 0){
                    $errs[] = $stateRegionErr = "please modify the region to continue";
                }
            }

            if(count($errs) == 0){
                $q = dbUpdate('states', 'id='.$id, ['region'=>$stateRegion, 'du'=>$now]);
                if($q){
                    $sMsg = "state region updated successfully";
                }
                else{
                    $eMsg = "something went wrong".mysqli_error($db);
                }
            }
        }

        if(isset($_GET['activate'])){
            if(modifyStatus('states', $id, 'active') == 'success'){
                $sMsg = "state '$stateName' activated successfully";
                pageReload(3000, $pageURL);
            }
        }

        if(isset($_GET['deactivate'])){
            if(modifyStatus('states', $id, 'inactive') == 'success'){
                $sMsg = "state '$stateName' deactivated successfully";
                pageReload(3000, $pageURL);
            }
            else{
                $eMsg = "failed";
            }
        }
        if(isset($_GET['trash'])){
            if(modifyStatus('states', $id, 'trashed') == 'success'){
                $sMsg = "state '$stateName' trashed successfully";
                pageReload(3000, $pageURL);
            }
        }
        if(isset($_GET['restore'])){
            if(modifyStatus('states', $id, 'active') == 'success'){
                $sMsg = "state '$stateName' restored successfully";
                pageReload(3000, $pageURL);
            }
        }

        if(isset($_GET['delete'])){
            $app_config['promptMsg'] = "You're about to delete state '$stateName'. Are you sure?";
            $app_config['prompt'] = true;
            $app_config['promptType'] = "dark";
            if(isset($_POST['doPrompt'])){
                $app_config['prompt'] = false;
                if(dbDelete('states','id='.$id) == "success"){
                    $sMsg = "state '$stateName' deleted successfully";
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
    $app_config['promptMsg'] = "You're about to truncate 'states' table. Are you sure?";
    $app_config['prompt'] = true;
    $app_config['promptType'] = "danger";
    if(isset($_POST['doPrompt'])) {
        $app_config['prompt'] = false;
        if (dbTruncate('states') == 'success') {
            $sMsg = "States table truncated successfully";
            pageReload(5000, $pageURL);
        } else {
            $eMsg = "something went wrong";
        }
    }
}