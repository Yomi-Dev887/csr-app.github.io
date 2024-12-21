<?php
# new category
if(isset($_POST['addCategory'])){
    $category = trim(stripslashes(mysqli_real_escape_string($db, $_POST['category'])));
    if(empty($category)){
        $errs[] = $categoryErr = "please enter a category name";
    }
    else{
        if(cntRows('project_cats', "*", "name='$category'") > 0){
            $errs[] = $categoryErr = "category already exists";
        }
    }

    if(count($errs) == 0){
        $q = dbInsert('project_cats', ['name'=>$category, 'dc'=>$now]);
        if($q){
            $sMsg = "category '$category' added successfully";
        }
        else{
            $eMsg = "something went wrong".mysqli_error($db);
        }
    }
}

if(isset($_GET['id'])){
    $id = $_GET['id'];
    if($id > 0){
        $categoryName = getDBVal('project_cats', $id, null);

        if(isset($_POST['updateCategory'])){
            $category = trim(mysqli_real_escape_string($db, $_POST['category']));
            if(empty($category)){
                $errs[] = $categoryErr = "please enter a category name";
            }
            else{
                if(cntRows('project_cats', "*", "name='$category' AND id=$id") > 0){
                    $errs[] = $categoryErr = "please modify the category to continue";
                }
                if(cntRows('project_cats', "*", "name='$category' AND id<>$id") > 0){
                    $errs[] = $categoryErr = "category already exists";
                }
            }

            if(count($errs) == 0){
                $q = dbUpdate('project_cats', 'id='.$id, ['name'=>$category, 'du'=>$now]);
                if($q){
                    $sMsg = "category '$categoryName' updated to '$category' successfully";
                }
                else{
                    $eMsg = "something went wrong".mysqli_error($db);
                }
            }
        }

        if(isset($_GET['activate'])){
            if(modifyStatus('project_cats', $id, 'active') == 'success'){
                $sMsg = "category '$categoryName' activated successfully";
                pageReload(3000, $pageURL);
            }
        }

        if(isset($_GET['deactivate'])){
            if(modifyStatus('project_cats', $id, 'inactive') == 'success'){
                $sMsg = "category '$categoryName' deactivated successfully";
                pageReload(3000, $pageURL);
            }
            else{
                $eMsg = "failed";
            }
        }
        if(isset($_GET['trash'])){
            if(modifyStatus('project_cats', $id, 'trashed') == 'success'){
                $sMsg = "category '$categoryName' trashed successfully";
                pageReload(3000, $pageURL);
            }
        }
        if(isset($_GET['restore'])){
            if(modifyStatus('project_cats', $id, 'active') == 'success'){
                $sMsg = "category '$categoryName' restored successfully";
                pageReload(3000, $pageURL);
            }
        }

        if(isset($_GET['delete'])){
            $app_config['promptMsg'] = "You're about to delete category '$categoryName'. Are you sure?";
            $app_config['prompt'] = true;
            $app_config['promptType'] = "dark";
            if(isset($_POST['doPrompt'])){
                $app_config['prompt'] = false;
                if(dbDelete('project_cats','id='.$id) == "success"){
                    $sMsg = "category '$categoryName' deleted successfully";
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
    $app_config['promptMsg'] = "You're about to truncate 'project categories' table. Are you sure?";
    $app_config['prompt'] = true;
    $app_config['promptType'] = "danger";
    if(isset($_POST['doPrompt'])) {
        $app_config['prompt'] = false;
        if (dbTruncate('project_cats') == 'success') {
            $sMsg = "Categories table truncated successfully";
            pageReload(5000, $pageURL);
        } else {
            $eMsg = "something went wrong";
        }
    }
}
