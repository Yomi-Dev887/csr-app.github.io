<?php
if(!empty($_SESSION['csrAdminAuthMsg'])){
    $iMsg = $_SESSION['csrAdminAuthMsg'];
    unset($_SESSION['csrAdminAuthMsg']);
}

if(isset($_POST['login'])){
    $user = trim(stripslashes(mysqli_real_escape_string($db, $_POST['user'])));
    $password = trim(mysqli_real_escape_string($db, $_POST['password']));

    if(empty($user)){ $errs[] = $userErr = "required"; }
    elseif(empty($password)){ $errs[] = $passwordErr = "required"; }
    else{
        $q = dbSelect('users', "*","userID='$user' OR username='$user' OR email='$user'");
        if(mysqli_num_rows($q) > 0){
            $userData = mysqli_fetch_assoc($q);
            $dbPwd = $userData['password'];
            $status = strtolower($userData['status']);
            if(password_verify($password, $dbPwd)){
                if($status == 'active') {
                    $_SESSION['csrAdmin'] = $userData['id'];
                    $_SESSION['csrAdminLoginMsg'] = "You have been successfully logged in, kindly pick up from where you left off";
                    pageRedirect($adminURL . 'home');
                }
                else{
                    $eMsg = "Login failed, your account is inactive. Kindly contact site admin for further support";
                }
            }
            else{
                $eMsg = "invalid password encountered";
            }
        }
        else{
            $eMsg = "no such user here";
        }
    }
}