<?php
function getToken($client_id, $client_secret, $refresh_token)
{
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api-campaign-us-5.goacoustic.com/oauth/token",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYHOST =>false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "grant_type=refresh_token&client_id={$client_id}&client_secret={$client_secret}&refresh_token={$refresh_token}",
        CURLOPT_HTTPHEADER => array(
            "content-type: application/x-www-form-urlencoded"
        ),
    ));

    $response = curl_exec($curl);
    return json_decode($response);
}

function requestXMLAPI($token, $postField)
{
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api-campaign-us-5.goacoustic.com/XMLAPI',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => $postField,
    CURLOPT_HTTPHEADER => array(
        'Content-Type: text/xml',
        'Authorization: Bearer ' . $token
    ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}

function getRulesets($token, $mailingId) 
{

    $postField = "<Envelope>
    <Body>
      <ListDCRulesetsForMailing>
        <MAILING_ID>{$mailingId}</MAILING_ID>
      </ListDCRulesetsForMailing>
    </Body>
  </Envelope>";
  
  $response = requestXMLAPI($token, $postField);

    return $response;
}

function getRulesetIdIfExistsFromMailing($token, $mailingId, $ruleName) {
    $getRulesets = getRulesets($token, $mailingId);
    $response = new SimpleXMLElement($getRulesets);
    // print_r($ruleName);
    foreach ($response->Body->RESULT->RULESET as $ruleset) {
        if ($ruleName == $ruleset->RULESET_NAME) {
            return (string)$ruleset->RULESET_ID;
        }
    }
    return '';
}

function getPostField($site, $list_id, $mailing_id, $number)
{
    $fileName = "rulesets/{$site}/ruleset_{$list_id}_{$mailing_id}_{$number}.xml";
    
    $myfile = fopen($fileName, "r") or die("Unable to open file!");
    $content = fread($myfile,filesize($fileName));
    fclose($myfile);
    return $content;
}