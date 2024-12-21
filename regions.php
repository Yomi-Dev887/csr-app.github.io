<?php
# new region
if(isset($_POST['addRegion'])){
    $region = trim(stripslashes(mysqli_real_escape_string($db, $_POST['region'])));
    if(empty($region)){
        $errs[] = $regionErr = "please enter a region name";
    }
    else{
        if(cntRows('regions', "*", "name='$region'") > 0){
            $errs[] = $regionErr = "region already exists";
        }
    }

    if(count($errs) == 0){
        $q = dbInsert('regions', ['name'=>$region, 'dc'=>$now]);
        if($q){
            $sMsg = "region '$region' added successfully";
        }
        else{
            $eMsg = "something went wrong".mysqli_error($db);
        }
    }
}

if(isset($_GET['id'])){
    $id = $_GET['id'];
    if($id > 0){
        $regionName = getDBVal('regions', $id, null);

        if(isset($_POST['updateRegion'])){
            $region = trim(mysqli_real_escape_string($db, $_POST['region']));
            if(empty($region)){
                $errs[] = $regionErr = "please enter a region name";
            }
            else{
                if(cntRows('regions', "*", "name='$region' AND id=$id") > 0){
                    $errs[] = $regionErr = "please modify the region to continue";
                }
                if(cntRows('regions', "*", "name='$region' AND id<>$id") > 0){
                    $errs[] = $regionErr = "region already exists";
                }
            }

            if(count($errs) == 0){
                $q = dbUpdate('regions', 'id='.$id, ['name'=>$region, 'du'=>$now]);
                if($q){
                    $sMsg = "region '$regionName' updated to '$region' successfully";
                }
                else{
                    $eMsg = "something went wrong".mysqli_error($db);
                }
            }
        }

        if(isset($_GET['activate'])){
            if(modifyStatus('regions', $id, 'active') == 'success'){
                $sMsg = "region '$regionName' activated successfully";
                pageReload(3000, $pageURL);
            }
        }

        if(isset($_GET['deactivate'])){
            if(modifyStatus('regions', $id, 'inactive') == 'success'){
                $sMsg = "region '$regionName' deactivated successfully";
                pageReload(3000, $pageURL);
            }
            else{
                $eMsg = "failed";
            }
        }
        if(isset($_GET['trash'])){
            if(modifyStatus('regions', $id, 'trashed') == 'success'){
                $sMsg = "region '$regionName' trashed successfully";
                pageReload(3000, $pageURL);
            }
        }
        if(isset($_GET['restore'])){
            if(modifyStatus('regions', $id, 'active') == 'success'){
                $sMsg = "region '$regionName' restored successfully";
                pageReload(3000, $pageURL);
            }
        }

        if(isset($_GET['delete'])){
            $app_config['promptMsg'] = "You're about to delete region '$regionName'. Are you sure?";
            $app_config['prompt'] = true;
            $app_config['promptType'] = "dark";
            if(isset($_POST['doPrompt'])){
                $app_config['prompt'] = false;
                if(dbDelete('regions','id='.$id) == "success"){
                    $sMsg = "region '$regionName' deleted successfully";
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
    $app_config['promptMsg'] = "You're about to truncate 'regions' table. Are you sure?";
    $app_config['prompt'] = true;
    $app_config['promptType'] = "danger";
    if(isset($_POST['doPrompt'])) {
        $app_config['prompt'] = false;
        if (dbTruncate('regions') == 'success') {
            $sMsg = "Regions table truncated successfully";
            pageReload(5000, $pageURL);
        } else {
            $eMsg = "something went wrong";
        }
    }
}