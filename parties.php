<?php
# new party
if(isset($_POST['addParty'])){
    $party = trim(stripslashes(mysqli_real_escape_string($db, $_POST['party'])));
    if(empty($party)){
        $errs[] = $partyErr = "please enter a party name";
    }
    else{
        if(cntRows('parties', "*", "name='$party'") > 0){
            $errs[] = $partyErr = "party already exists";
        }
    }

    if(count($errs) == 0){
        $q = dbInsert('parties', ['name'=>$party, 'dc'=>$now]);
        if($q){
            $sMsg = "party '$party' added successfully";
        }
        else{
            $eMsg = "something went wrong".mysqli_error($db);
        }
    }
}

if(isset($_GET['id'])){
    $id = $_GET['id'];
    if($id > 0){
        $partyName = getDBVal('parties', $id, null);

        if(isset($_POST['updateParty'])){
            $party = trim(mysqli_real_escape_string($db, $_POST['party']));
            if(empty($party)){
                $errs[] = $partyErr = "please enter a party name";
            }
            else{
                if(cntRows('parties', "*", "name='$party' AND id=$id") > 0){
                    $errs[] = $partyErr = "please modify the party to continue";
                }
                if(cntRows('parties', "*", "name='$party' AND id<>$id") > 0){
                    $errs[] = $partyErr = "party already exists";
                }
            }

            if(count($errs) == 0){
                $q = dbUpdate('parties', 'id='.$id, ['name'=>$party, 'du'=>$now]);
                if($q){
                    $sMsg = "party '$partyName' updated to '$party' successfully";
                }
                else{
                    $eMsg = "something went wrong".mysqli_error($db);
                }
            }
        }

        if(isset($_GET['activate'])){
            if(modifyStatus('parties', $id, 'active') == 'success'){
                $sMsg = "party '$partyName' activated successfully";
                pageReload(3000, $pageURL);
            }
        }

        if(isset($_GET['deactivate'])){
            if(modifyStatus('parties', $id, 'inactive') == 'success'){
                $sMsg = "party '$partyName' deactivated successfully";
                pageReload(3000, $pageURL);
            }
            else{
                $eMsg = "failed";
            }
        }
        if(isset($_GET['trash'])){
            if(modifyStatus('parties', $id, 'trashed') == 'success'){
                $sMsg = "party '$partyName' trashed successfully";
                pageReload(3000, $pageURL);
            }
        }
        if(isset($_GET['restore'])){
            if(modifyStatus('parties', $id, 'active') == 'success'){
                $sMsg = "party '$partyName' restored successfully";
                pageReload(3000, $pageURL);
            }
        }

        if(isset($_GET['delete'])){
            $app_config['promptMsg'] = "You're about to delete party '$partyName'. Are you sure?";
            $app_config['prompt'] = true;
            $app_config['promptType'] = "dark";
            if(isset($_POST['doPrompt'])){
                $app_config['prompt'] = false;
                if(dbDelete('parties','id='.$id) == "success"){
                    $sMsg = "party '$partyName' deleted successfully";
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
    $app_config['promptMsg'] = "You're about to truncate 'parties' table. Are you sure?";
    $app_config['prompt'] = true;
    $app_config['promptType'] = "danger";
    if(isset($_POST['doPrompt'])) {
        $app_config['prompt'] = false;
        if (dbTruncate('parties') == 'success') {
            $sMsg = "Parties table truncated successfully";
            pageReload(5000, $pageURL);
        } else {
            $eMsg = "something went wrong";
        }
    }
}