<?php
$subject = 'My subject';
$description='this is very serious';
$Priority='low';
$appUrl='cloud.perfectomobile.com';
$suppliedName='ran';
$suppliedEmail='perfecto@perfectomobie.com';
$SuppliedPhone = '+111111111111';
$filename ='file.txt';
$path ='ATTACHMENT.txt';

$SfConfig = parse_ini_file("conf.ini");
define(SECURITY_TOKEN, $SfConfig['token']);
define(USERNAME, $SfConfig['user']);
define(PASSWORD, $SfConfig['password']);
require_once ('soapclient/SforceEnterpriseClient.php');

$mySforceConnection = new SforceEnterpriseClient();
$mySoapClient = $mySforceConnection->createConnection("soapclient/perfectomobile.xml");
$mylogin = $mySforceConnection->login(USERNAME,PASSWORD.SECURITY_TOKEN);

#case fields 
$sObject = new stdclass();
$sObject->Subject = $subject;
$sObject->Description =$description;
$sObject->Priority =$Priority; 
$sObject->AppURL__c =$appUrl;
$sObject->SuppliedName =$suppliedName;
$sObject->SuppliedEmail =$suppliedEmail;
$sObject->SuppliedPhone =$SuppliedPhone; 

#creating the case
try {
   $response = $mySforceConnection->create(array($sObject), 'Case');
   foreach ($response as $record)$parent=$record->id;
   $json['case'] = $response;
}
catch (Exception $e){ 

    $json['case'] = NULL;
    #create case failed 
}
if (file_exists ($path) ){
#uploading attachment
    try {
        $sfObj = new stdClass();
        $data= file_get_contents($path);
        $sfObj->Body = base64_encode($data);
        $sfObj->Name = $filename;
        $sfObj->ParentId = $parent;
        
        $response = $mySforceConnection->create(array($sfObj), 'Attachment');
        $json['attachment'] = $response;
    }
    catch (Exception $e){
        $json['attachment'] = NULL;
        }
    }
    $myJSON = json_encode($json);
    print_r($json);

 ?>
