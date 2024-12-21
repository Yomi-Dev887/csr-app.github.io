<?php
# new official
if(isset($_POST['addOfficial'])){
    $fName = trim(stripslashes(mysqli_real_escape_string($db, $_POST['fName'])));
    $lName = trim(stripslashes(mysqli_real_escape_string($db, $_POST['lName'])));
    $email = trim(stripslashes(mysqli_real_escape_string($db, $_POST['email'])));
    $party = intval($_POST['party']);
    $lga = intval($_POST['lga']);
    if(empty($fName)){
        $errs[] = $fNameErr = "please enter first name";
    }
    elseif(empty($lName)){
        $errs[] = $lNameErr = "please enter last name";
    }
    elseif(empty($email)){
        $errs[] = $emailErr = "please enter a email address";
    }
    elseif($party == 0){
        $errs[] = $partyErr = "please select a party";
    }
    elseif($lga == 0){
        $errs[] = $lgaErr = "please select a LGA";
    }
    else{
        if(validateEmail($email)) {
            if (cntRows('officials', "*", "fName='$fName' AND lName='$lName' AND email='$email'") > 0) {
                $errs[] = $emailErr = "official already exists";
            }
        }
        if(validateEmail($email)) {
            if (cntRows('officials', "*", "email='$email'") > 0) {
                $errs[] = $emailErr = "email already exists";
            }
        }
        $partyName = getDBVal('parties',$party);
    }

    if(count($errs) == 0){
        $q = dbInsert('officials', ['fName'=>$fName, 'lName'=>$lName, 'email'=>$email, 'party'=>$party, 'lga'=>$lga, 'dc'=>$now]);
        if($q){
            $sMsg = "official '$fName $lName' under the party '$partyName' added successfully";
        }
        else{
            $eMsg = "something went wrong".mysqli_error($db);
        }
    }
}

if(isset($_GET['id'])){
    $id = $_GET['id'];
    if($id > 0){
        $officialFName = getDBVal('officials', $id, null,'fName');
        $officialLName = getDBVal('officials', $id, null,'lName');
        $dbEmail = getDBVal('officials', $id, null,'email');
        $dbLGA = getDBVal('officials', $id, null,'lga');
        $dbParty = getDBVal('officials', $id, null,'party');

        if(isset($_POST['updateOfficial'])){
            $official = trim(mysqli_real_escape_string($db, $_POST['official']));
            if(empty($official)){
                $errs[] = $officialErr = "please enter a officialname";
            }
            else{
                if(cntRows('officials', "*", "officialname='$official' AND id=$id") > 0){
                    $errs[] = $officialErr = "please modify the officialname to continue";
                }
                if(cntRows('officials', "*", "officialname='$official' AND id<>$id") > 0){
                    $errs[] = $officialErr = "officialname already exists";
                }
            }

            if(count($errs) == 0){
                $q = dbUpdate('officials', 'id='.$id, ['officialname'=>$official, 'du'=>$now]);
                if($q){
                    $sMsg = "official '$officialName' updated to '$official' successfully";
                }
                else{
                    $eMsg = "something went wrong".mysqli_error($db);
                }
            }
        }

        if(isset($_POST['updateEmail'])){
            $email = trim(mysqli_real_escape_string($db, $_POST['email']));
            if(empty($email)){
                $errs[] = $emailErr = "please enter a valid email address";
            }
            else{
                if(cntRows('officials', "*", "email='$email' AND id=$id") > 0){
                    $errs[] = $emailErr = "please modify the email to continue";
                }
                if(cntRows('officials', "*", "email='$email' AND id<>$id") > 0){
                    $errs[] = $emailErr = "email already exists";
                }
            }

            if(count($errs) == 0){
                $q = dbUpdate('officials', 'id='.$id, ['email'=>$email, 'du'=>$now]);
                if($q){
                    $sMsg = "official email '$dbEmail' updated to '$email' successfully";
                }
                else{
                    $eMsg = "something went wrong".mysqli_error($db);
                }
            }
        }

        if(isset($_POST['updateParty'])){
            $party = intval($_POST['party']);
            if($party == 0){
                $errs[] = $partyErr = "please enter a valid email address";
            }
            else{
                if(cntRows('officials', "*", "party=$party AND id=$id") > 0){
                    $errs[] = $partyErr = "please modify the party to continue";
                }
            }

            if(count($errs) == 0){
                $q = dbUpdate('officials', 'id='.$id, ['party'=>$party, 'du'=>$now]);
                if($q){
                    $sMsg = "official party updated successfully";
                }
                else{
                    $eMsg = "something went wrong".mysqli_error($db);
                }
            }
        }

        if(isset($_GET['activate'])){
            if(modifyStatus('officials', $id, 'active') == 'success'){
                $sMsg = "official '$officialName' activated successfully";
                pageReload(3000, $pageURL);
            }
        }

        if(isset($_GET['deactivate'])){
            if(modifyStatus('officials', $id, 'inactive') == 'success'){
                $sMsg = "official '$officialName' deactivated successfully";
                pageReload(3000, $pageURL);
            }
            else{
                $eMsg = "failed";
            }
        }
        if(isset($_GET['trash'])){
            if(modifyStatus('officials', $id, 'trashed') == 'success'){
                $sMsg = "official '$officialName' trashed successfully";
                pageReload(3000, $pageURL);
            }
        }
        if(isset($_GET['restore'])){
            if(modifyStatus('officials', $id, 'active') == 'success'){
                $sMsg = "official '$officialName' restored successfully";
                pageReload(3000, $pageURL);
            }
        }

        if(isset($_GET['delete'])){
            $app_config['promptMsg'] = "You're about to delete official '$officialName'. Are you sure?";
            $app_config['prompt'] = true;
            $app_config['promptType'] = "dark";
            if(isset($_POST['doPrompt'])){
                $app_config['prompt'] = false;
                if(dbDelete('officials','id='.$id) == "success"){
                    $sMsg = "official '$officialName' deleted successfully";
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
    $app_config['promptMsg'] = "You're about to truncate 'officials' table. Are you sure?";
    $app_config['prompt'] = true;
    $app_config['promptType'] = "danger";
    if(isset($_POST['doPrompt'])) {
        $app_config['prompt'] = false;
        if (dbTruncate('officials') == 'success') {
            $sMsg = "Officials table truncated successfully";
            pageReload(5000, $pageURL);
        } else {
            $eMsg = "something went wrong";
        }
    }
}