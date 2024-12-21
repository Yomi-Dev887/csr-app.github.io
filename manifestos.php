<?php

# new manifesto
if(isset($_POST['addManifesto'])){
    $manifesto = trim(stripslashes(mysqli_real_escape_string($db, $_POST['manifesto'])));
    $description = trim(stripslashes(mysqli_real_escape_string($db, $_POST['description'])));
    if(empty($manifesto)){
        $errs[] = $manifestoErr = "please enter a manifesto title";
    }
    else{
        if(cntRows('manifestos', "*", "title='$manifesto'") > 0){
            $errs[] = $manifestoErr = "manifesto already exists";
        }
    }
    if(empty($description)){
        $errs[] = $descriptionErr = "please enter a description";
    }

    if(count($errs) == 0){
        $q = dbInsert('manifestos', ['title'=>$manifesto, 'description'=>$description, 'dc'=>$now]);
        if($q){
            $sMsg = "manifesto '$manifesto' added successfully";
            $app_config['preventResubmission'] = true;
        }
        else{
            $eMsg = "something went wrong".mysqli_error($db);
        }
    }
}

if(isset($_GET['id'])){
    $id = $_GET['id'];
    if($id > 0){
        $manifestoTitle = getDBVal('manifestos', $id, null,'title');
        $dbDescription = getDBVal('manifestos', $id, null,'description');

        if(isset($_POST['updateManifesto'])){
            $manifesto = trim(mysqli_real_escape_string($db, $_POST['manifesto']));
            if(empty($manifesto)){
                $errs[] = $manifestoErr = "please enter a title";
            }
            else{
                if(cntRows('manifestos', "*", "title='$manifesto' AND id=$id") > 0){
                    $errs[] = $manifestoErr = "please modify the title to continue";
                }
                if(cntRows('manifestos', "*", "title='$manifesto' AND id<>$id") > 0){
                    $errs[] = $manifestoErr = "title already exists";
                }
            }

            if(count($errs) == 0){
                $q = dbUpdate('manifestos', 'id='.$id, ['title'=>$manifesto, 'du'=>$now]);
                if($q){
                    $sMsg = "manifesto '$manifestoTitle' updated to '$manifesto' successfully";
                }
                else{
                    $eMsg = "something went wrong".mysqli_error($db);
                }
            }
        }

        if(isset($_POST['updateDescription'])){
            $description = trim(mysqli_real_escape_string($db, $_POST['description']));
            if(empty($description)){
                $errs[] = $descriptionErr = "please enter a valid description address";
            }
            else{
                if(cntRows('manifestos', "*", "description='$description' AND id=$id") > 0){
                    $errs[] = $descriptionErr = "please modify the description to continue";
                }
                if(cntRows('manifestos', "*", "description='$description' AND id<>$id") > 0){
                    $errs[] = $descriptionErr = "description already exists";
                }
            }

            if(count($errs) == 0){
                $q = dbUpdate('manifestos', 'id='.$id, ['description'=>$description, 'du'=>$now]);
                if($q){
                    $sMsg = "manifesto description '$dbDescription' updated to '$description' successfully";
                }
                else{
                    $eMsg = "something went wrong".mysqli_error($db);
                }
            }
        }

        if(isset($_POST['updateRegion'])){
            $manifestoRegion = intval($_POST['manifestoRegion']);
            if($manifestoRegion == 0){
                $errs[] = $manifestoRegionErr = "please enter a valid description address";
            }
            else{
                if(cntRows('manifestos', "*", "region=$manifestoRegion AND id=$id") > 0){
                    $errs[] = $manifestoRegionErr = "please modify the region to continue";
                }
            }

            if(count($errs) == 0){
                $q = dbUpdate('manifestos', 'id='.$id, ['region'=>$manifestoRegion, 'du'=>$now]);
                if($q){
                    $sMsg = "manifesto region updated successfully";
                }
                else{
                    $eMsg = "something went wrong".mysqli_error($db);
                }
            }
        }

        if(isset($_GET['activate'])){
            if(modifyStatus('manifestos', $id, 'active') == 'success'){
                $sMsg = "manifesto '$manifestoTitle' activated successfully";
                pageReload(3000, $pageURL);
            }
        }

        if(isset($_GET['deactivate'])){
            if(modifyStatus('manifestos', $id, 'inactive') == 'success'){
                $sMsg = "manifesto '$manifestoTitle' deactivated successfully";
                pageReload(3000, $pageURL);
            }
            else{
                $eMsg = "failed";
            }
        }
        if(isset($_GET['trash'])){
            if(modifyStatus('manifestos', $id, 'trashed') == 'success'){
                $sMsg = "manifesto '$manifestoTitle' trashed successfully";
                pageReload(3000, $pageURL);
            }
        }
        if(isset($_GET['restore'])){
            if(modifyStatus('manifestos', $id, 'active') == 'success'){
                $sMsg = "manifesto '$manifestoTitle' restored successfully";
                pageReload(3000, $pageURL);
            }
        }

        if(isset($_GET['delete'])){
            $app_config['promptMsg'] = "You're about to delete manifesto '$manifestoTitle'. Are you sure?";
            $app_config['prompt'] = true;
            $app_config['promptType'] = "dark";
            if(isset($_POST['doPrompt'])){
                $app_config['prompt'] = false;
                if(dbDelete('manifestos','id='.$id) == "success"){
                    $sMsg = "manifesto '$manifestoTitle' deleted successfully";
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
    $app_config['promptMsg'] = "You're about to truncate 'manifestos' table. Are you sure?";
    $app_config['prompt'] = true;
    $app_config['promptType'] = "danger";
    if(isset($_POST['doPrompt'])) {
        $app_config['prompt'] = false;
        if (dbTruncate('manifestos') == 'success') {
            $sMsg = "Manifestos table truncated successfully";
            pageReload(5000, $pageURL);
        } else {
            $eMsg = "something went wrong";
        }
    }
}