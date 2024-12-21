<?php

# new employment
if(isset($_POST['addEmployment'])){
    $startDate = trim(stripslashes(mysqli_real_escape_string($db, $_POST['startDate'])));
    $official = intval($_POST['official']);
    $duration = intval($_POST['duration']);
    $empType = intval($_POST['empType']);
    $designation = intval($_POST['designation']);
    $arm = intval($_POST['arm']);
    $level = intval($_POST['level']);
    if(empty($startDate)){
        $errs[] = $startDateErr = "please enter a startDate";
    }
    elseif($official == 0){
        $errs[] = $officialErr = "please select a official";
    }
    elseif($duration == 0){
        $errs[] = $durationErr = "please enter a duration";
    }
    elseif($empType == 0){
        $errs[] = $empTypeErr = "please select a empType";
    }
    elseif($designation == 0){
        $errs[] = $designationErr = "please select a designation";
    }
    elseif($arm == 0){
        $errs[] = $armErr = "please select a arm";
    }
    elseif($level == 0){
        $errs[] = $levelErr = "please select a level";
    }
    else{
         if(cntRows('employments', "*", "official='$official' AND designation=$designation") > 0){
                    $errs[] = $employmentErr = "";
                    $eMsg = "employment already exists";
                }
    }

    if(count($errs) == 0){
        $q = dbInsert('employments', ['termDuration'=>$duration, 'startDate'=>$startDate, 'official'=>$official, 'arm'=>$arm, 'level'=>$level, 'designation'=>$designation, 'type'=>$empType, 'dc'=>$now]);
        if($q){
            $sMsg = "employment record added successfully";
        }
        else{
            $eMsg = "something went wrong".mysqli_error($db);
        }
    }
}

if(isset($_GET['id'])){
    $id = $_GET['id'];
    if($id > 0){
        $dbStartDate = getDBVal('employments', $id, null,'startDate');
        $dbEndDate = getDBVal('employments', $id, null,'endDate');
        $dbOfficial = getDBVal('employments', $id, null,'official');
        $dbDesignation = getDBVal('employments', $id, null,'designation');
        $dbType = getDBVal('employments', $id, null,'type');
        $dbArm = getDBVal('employments', $id, null,'arm');
        $dbLevel = getDBVal('employments', $id, null,'level');
        $dbTermDuration = getDBVal('employments', $id, null,'termDuration');
        $dbOfficialFName = getDBVal('officials', $dbOfficial, null,'fName');
        $dbOfficialLName = getDBVal('officials', $dbOfficial, null,'lName');
        $dbDesignationName = getDBVal('designations', $dbOfficial);

        if(isset($_POST['updateStartDate'])){
            $startDate = trim(mysqli_real_escape_string($db, $_POST['startDate']));
            if(empty($startDate)){
                $errs[] = $startDateErr = "please enter a valid startDate address";
            }
            else{
                if(cntRows('employments', "*", "startDate='$startDate' AND id=$id") > 0){
                    $errs[] = $startDateErr = "please modify the startDate to continue";
                }
                if(cntRows('employments', "*", "startDate='$startDate' AND id<>$id") > 0){
                    $errs[] = $startDateErr = "startDate already exists";
                }
            }

            if(count($errs) == 0){
                $q = dbUpdate('employments', 'id='.$id, ['startDate'=>$startDate, 'du'=>$now]);
                if($q){
                    $sMsg = "employment startDate '$dbStartDate' updated to '$startDate' successfully";
                }
                else{
                    $eMsg = "something went wrong".mysqli_error($db);
                }
            }
        }

        if(isset($_POST['updateOfficial'])){
            $official = intval($_POST['official']);
            if($official == 0){
                $errs[] = $officialErr = "please enter a valid startDate address";
            }
            else{
                if(cntRows('employments', "*", "official=$official AND id=$id") > 0){
                    $errs[] = $officialErr = "please modify the official to continue";
                }
            }

            if(count($errs) == 0){
                $q = dbUpdate('employments', 'id='.$id, ['official'=>$official, 'du'=>$now]);
                if($q){
                    $sMsg = "employment official updated successfully";
                }
                else{
                    $eMsg = "something went wrong".mysqli_error($db);
                }
            }
        }

        if(isset($_POST['updateArm'])){
            $arm = intval($_POST['arm']);
            if($arm == 0){
                $errs[] = $armErr = "please select";
            }
            else{
                if(cntRows('employments', "*", "arm=$arm AND id=$id") > 0){
                    $errs[] = $armErr = "please modify the arm to continue";
                }
            }

            if(count($errs) == 0){
                $q = dbUpdate('employments', 'id='.$id, ['arm'=>$arm, 'du'=>$now]);
                if($q){
                    $sMsg = "employment arm updated successfully";
                }
                else{
                    $eMsg = "something went wrong".mysqli_error($db);
                }
            }
        }

        if(isset($_POST['updateLevel'])){
            $level = intval($_POST['level']);
            if($level == 0){
                $errs[] = $levelErr = "please select";
            }
            else{
                if(cntRows('employments', "*", "level=$level AND id=$id") > 0){
                    $errs[] = $levelErr = "please modify the level to continue";
                }
            }

            if(count($errs) == 0){
                $q = dbUpdate('employments', 'id='.$id, ['level'=>$level, 'du'=>$now]);
                if($q){
                    $sMsg = "employment level updated successfully";
                }
                else{
                    $eMsg = "something went wrong".mysqli_error($db);
                }
            }
        }

        if(isset($_POST['updateType'])){
            $empType = intval($_POST['empType']);
            if($empType == 0){
                $errs[] = $empTypeErr = "please select";
            }
            else{
                if(cntRows('employments', "*", "empType=$empType AND id=$id") > 0){
                    $errs[] = $empTypeErr = "please modify the type to continue";
                }
            }

            if(count($errs) == 0){
                $q = dbUpdate('employments', 'id='.$id, ['type'=>$empType, 'du'=>$now]);
                if($q){
                    $sMsg = "employment type updated successfully";
                }
                else{
                    $eMsg = "something went wrong".mysqli_error($db);
                }
            }
        }

        if(isset($_POST['updateTerm'])){
            $termDuration = intval($_POST['termDuration']);
            if($termDuration == 0){
                $errs[] = $termDurationErr = "please select";
            }
            else{
                if(cntRows('employments', "*", "termDuration=$termDuration AND id=$id") > 0){
                    $errs[] = $termDurationErr = "please modify the duration to continue";
                }
            }

            if(count($errs) == 0){
                $q = dbUpdate('employments', 'id='.$id, ['termDuration'=>$termDuration, 'du'=>$now]);
                if($q){
                    $sMsg = "employment duration updated successfully";
                }
                else{
                    $eMsg = "something went wrong".mysqli_error($db);
                }
            }
        }

        if(isset($_GET['activate'])){
            if(modifyStatus('employments', $id, 'active') == 'success'){
                $sMsg = "employment '$employmentName' activated successfully";
                pageReload(3000, $pageURL);
            }
        }

        if(isset($_GET['deactivate'])){
            if(modifyStatus('employments', $id, 'inactive') == 'success'){
                $sMsg = "employment '$employmentName' deactivated successfully";
                pageReload(3000, $pageURL);
            }
            else{
                $eMsg = "failed";
            }
        }
        if(isset($_GET['trash'])){
            if(modifyStatus('employments', $id, 'trashed') == 'success'){
                $sMsg = "employment '$employmentName' trashed successfully";
                pageReload(3000, $pageURL);
            }
        }
        if(isset($_GET['restore'])){
            if(modifyStatus('employments', $id, 'active') == 'success'){
                $sMsg = "employment '$employmentName' restored successfully";
                pageReload(3000, $pageURL);
            }
        }

        if(isset($_GET['delete'])){
            $app_config['promptMsg'] = "You're about to delete employment '$employmentName'. Are you sure?";
            $app_config['prompt'] = true;
            $app_config['promptType'] = "dark";
            if(isset($_POST['doPrompt'])){
                $app_config['prompt'] = false;
                if(dbDelete('employments','id='.$id) == "success"){
                    $sMsg = "employment '$employmentName' deleted successfully";
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
    $app_config['promptMsg'] = "You're about to truncate 'employments' table. Are you sure?";
    $app_config['prompt'] = true;
    $app_config['promptType'] = "danger";
    if(isset($_POST['doPrompt'])) {
        $app_config['prompt'] = false;
        if (dbTruncate('employments') == 'success') {
            $sMsg = "Employments table truncated successfully";
            pageReload(5000, $pageURL);
        } else {
            $eMsg = "something went wrong";
        }
    }
}
