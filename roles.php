<?php
# new role
if(isset($_POST['addRole'])){
    $role = trim(stripslashes(mysqli_real_escape_string($db, $_POST['role'])));
    if(empty($role)){
        $errs[] = $roleErr = "please enter a role name";
    }
    else{
        if(cntRows('roles', "*", "name='$role'") > 0){
            $errs[] = $roleErr = "role already exists";
        }
    }

    if(count($errs) == 0){
        $q = dbInsert('roles', ['name'=>$role, 'dc'=>$now]);
        if($q){
            $sMsg = "role '$role' added successfully";
        }
        else{
            $eMsg = "something went wrong".mysqli_error($db);
        }
    }
}

if(isset($_GET['id'])){
    $id = $_GET['id'];
    if($id > 0){
        $roleName = getDBVal('roles', $id, null);

        if(isset($_POST['updateRole'])){
            $role = trim(mysqli_real_escape_string($db, $_POST['role']));
            if(empty($role)){
                $errs[] = $roleErr = "please enter a role name";
            }
            else{
                if(cntRows('roles', "*", "name='$role' AND id=$id") > 0){
                    $errs[] = $roleErr = "please modify the role to continue";
                }
                if(cntRows('roles', "*", "name='$role' AND id<>$id") > 0){
                    $errs[] = $roleErr = "role already exists";
                }
            }

            if(count($errs) == 0){
                $q = dbUpdate('roles', 'id='.$id, ['name'=>$role, 'du'=>$now]);
                if($q){
                    $sMsg = "role '$roleName' updated to '$role' successfully";
                }
                else{
                    $eMsg = "something went wrong".mysqli_error($db);
                }
            }
        }

        if(isset($_GET['activate'])){
            if(modifyStatus('roles', $id, 'active') == 'success'){
                $sMsg = "role '$roleName' activated successfully";
                pageReload(3000, $pageURL);
            }
        }

        if(isset($_GET['deactivate'])){
            if(modifyStatus('roles', $id, 'inactive') == 'success'){
                $sMsg = "role '$roleName' deactivated successfully";
                pageReload(3000, $pageURL);
            }
            else{
                $eMsg = "failed";
            }
        }
        if(isset($_GET['trash'])){
            if(modifyStatus('roles', $id, 'trashed') == 'success'){
                $sMsg = "role '$roleName' trashed successfully";
                pageReload(3000, $pageURL);
            }
        }
        if(isset($_GET['restore'])){
            if(modifyStatus('roles', $id, 'active') == 'success'){
                $sMsg = "role '$roleName' restored successfully";
                pageReload(3000, $pageURL);
            }
        }

        if(isset($_GET['delete'])){
            $app_config['promptMsg'] = "You're about to delete role '$roleName'. Are you sure?";
            $app_config['prompt'] = true;
            $app_config['promptType'] = "dark";
            if(isset($_POST['doPrompt'])){
                $app_config['prompt'] = false;
                if(dbDelete('roles','id='.$id) == "success"){
                    $sMsg = "role '$roleName' deleted successfully";
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
    $app_config['promptMsg'] = "You're about to truncate 'roles' table. Are you sure?";
    $app_config['prompt'] = true;
    $app_config['promptType'] = "danger";
    if(isset($_POST['doPrompt'])) {
        $app_config['prompt'] = false;
        if (dbTruncate('roles') == 'success') {
            $sMsg = "Roles table truncated successfully";
            pageReload(5000, $pageURL);
        } else {
            $eMsg = "something went wrong";
        }
    }
}