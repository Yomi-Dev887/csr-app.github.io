<?php
# generate unique ID
$ID = "CSRA-".rand(00000,99999);
$genID = verifyUniqueID($ID, 'users', 'userID');

# new user
if(isset($_POST['addUser'])){
    $user = trim(stripslashes(mysqli_real_escape_string($db, $_POST['user'])));
    $userID = trim(stripslashes(mysqli_real_escape_string($db, $_POST['userID'])));
    $email = trim(stripslashes(mysqli_real_escape_string($db, $_POST['email'])));
    $userRole = intval($_POST['userRole']);
    if(empty($userID)){
        $errs[] = $userIDErr = "";
        $eMsg = "ID not provided";
    }
    if(empty($user)){
        $errs[] = $userErr = "please enter a user username";
    }
    else{
        if(cntRows('users', "*", "username='$user'") > 0){
            $errs[] = $userErr = "user already exists";
        }
    }
    if(empty($email)){
        $errs[] = $emailErr = "please enter a email address";
    }
    else{
        if(validateEmail($email)) {
            if (cntRows('users', "*", "email='$email'") > 0) {
                $errs[] = $emailErr = "email already exists";
            }
        }
    }

    if(count($errs) == 0){
        $password = password_hash($app_config['adminDefPwd'], PASSWORD_BCRYPT, ['cost'=>8]);
        $q = dbInsert('users', ['userID'=>$userID, 'username'=>$user, 'email'=>$email, 'role'=>$userRole, 'password'=>$password, 'dc'=>$now]);
        if($q){
            $sMsg = "user '$user' added successfully";
        }
        else{
            $eMsg = "something went wrong".mysqli_error($db);
        }
    }
}

if(isset($_GET['id'])){
    $id = $_GET['id'];
    if($id > 0){
        $userName = getDBVal('users', $id, null,'username');
        $dbEmail = getDBVal('users', $id, null,'email');
        $dbRole = getDBVal('users', $id, null,'role');
        $dbPwd = getDBVal('users', $id, null,'password');

        if(isset($_POST['updateUser'])){
            $user = trim(mysqli_real_escape_string($db, $_POST['user']));
            if(empty($user)){
                $errs[] = $userErr = "please enter a username";
            }
            else{
                if(cntRows('users', "*", "username='$user' AND id=$id") > 0){
                    $errs[] = $userErr = "please modify the username to continue";
                }
                if(cntRows('users', "*", "username='$user' AND id<>$id") > 0){
                    $errs[] = $userErr = "username already exists";
                }
            }

            if(count($errs) == 0){
                $q = dbUpdate('users', 'id='.$id, ['username'=>$user, 'du'=>$now]);
                if($q){
                    $sMsg = "user '$userName' updated to '$user' successfully";
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
                if(cntRows('users', "*", "email='$email' AND id=$id") > 0){
                    $errs[] = $emailErr = "please modify the email to continue";
                }
                if(cntRows('users', "*", "email='$email' AND id<>$id") > 0){
                    $errs[] = $emailErr = "email already exists";
                }
            }

            if(count($errs) == 0){
                $q = dbUpdate('users', 'id='.$id, ['email'=>$email, 'du'=>$now]);
                if($q){
                    $sMsg = "user email '$dbEmail' updated to '$email' successfully";
                }
                else{
                    $eMsg = "something went wrong".mysqli_error($db);
                }
            }
        }

        if(isset($_POST['updateRole'])){
            $userRole = intval($_POST['userRole']);
            if($userRole == 0){
                $errs[] = $userRoleErr = "please enter a valid email address";
            }
            else{
                if(cntRows('users', "*", "role=$userRole AND id=$id") > 0){
                    $errs[] = $userRoleErr = "please modify the role to continue";
                }
            }

            if(count($errs) == 0){
                $q = dbUpdate('users', 'id='.$id, ['role'=>$userRole, 'du'=>$now]);
                if($q){
                    $sMsg = "user role updated successfully";
                }
                else{
                    $eMsg = "something went wrong".mysqli_error($db);
                }
            }
        }

        if(isset($_GET['activate'])){
            if(modifyStatus('users', $id, 'active') == 'success'){
                $sMsg = "user '$userName' activated successfully";
                pageReload(3000, $pageURL);
            }
        }

        if(isset($_GET['deactivate'])){
            if(modifyStatus('users', $id, 'inactive') == 'success'){
                $sMsg = "user '$userName' deactivated successfully";
                pageReload(3000, $pageURL);
            }
            else{
                $eMsg = "failed";
            }
        }
        if(isset($_GET['trash'])){
            if(modifyStatus('users', $id, 'trashed') == 'success'){
                $sMsg = "user '$userName' trashed successfully";
                pageReload(3000, $pageURL);
            }
        }
        if(isset($_GET['restore'])){
            if(modifyStatus('users', $id, 'active') == 'success'){
                $sMsg = "user '$userName' restored successfully";
                pageReload(3000, $pageURL);
            }
        }

        if(isset($_GET['delete'])){
            $app_config['promptMsg'] = "You're about to delete user '$userName'. Are you sure?";
            $app_config['prompt'] = true;
            $app_config['promptType'] = "dark";
            if(isset($_POST['doPrompt'])){
                $app_config['prompt'] = false;
                if(dbDelete('users','id='.$id) == "success"){
                    $sMsg = "user '$userName' deleted successfully";
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
    $app_config['promptMsg'] = "You're about to truncate 'users' table. Are you sure?";
    $app_config['prompt'] = true;
    $app_config['promptType'] = "danger";
    if(isset($_POST['doPrompt'])) {
        $app_config['prompt'] = false;
        if (dbTruncate('users') == 'success') {
            $sMsg = "Users table truncated successfully";
            pageReload(5000, $pageURL);
        } else {
            $eMsg = "something went wrong";
        }
    }
}