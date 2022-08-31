<?php

include_once('functions.php');
include_once('settings.php');
include_once('acousticAPI.php');

// foreach ($settings as $setting) {
//     // $outfile = $setting['site'] . '_similar_listings_email_day5.html';
//     // writeToFile($outfile, buildSimilarAdsHtml(1, $setting['colorCode']));
//     // $outfile = $setting['site'] . '_similar_listings_email_day8.html';
//     // writeToFile($outfile, buildSimilarAdsHtml(7, $setting['colorCode']));
//     // $outfile = $setting['site'] . '_similar_listings_email_day11.html';
//     // writeToFile($outfile, buildSimilarAdsHtml(13, $setting['colorCode']));
// }

function generateRulesets($settings) {
    foreach ($settings as $setting) {
        $list_id = $setting['listId'];
        $color_code = $setting['colorCode'];
        $site = $setting['site'];
        foreach ($setting['Mailings'] as $mailing) {
            $startIndex = $mailing['startIndex'];
            $mailing_id = $mailing['mailingId'];
    
            foreach (range($startIndex, $startIndex + 5) as $index) {
                $formatedIndex = sprintf('%02d', $index);
                $rulesetPayload = ruleset($list_id, $mailing_id, $formatedIndex, $color_code);
                $outfile = 'ruleset_' . $list_id . '_' . $mailing_id . '_' . $formatedIndex . '.xml';
                writeToFile( 'rulesets/' . $site . '/' . $outfile, $rulesetPayload);
            }
        }
    }
}



$settings = getSettings();
generateRulesets($settings);

foreach ($settings as $setting) {
    $list_id = $setting['listId'];
    $color_code = $setting['colorCode'];
    $site = $setting['site'];
    
    foreach ($setting['Mailings'] as $mailing) {
        $startIndex = $mailing['startIndex'];
        $mailing_id = $mailing['mailingId'];

        foreach (range($startIndex, $startIndex + 5) as $index) {
            $formatedIndex = sprintf('%02d', $index);
            $ruleName = "similar_listing_{$formatedIndex}";
            $postField = getPostField($site, $list_id, $mailing_id, $formatedIndex);
            $token = getToken($setting['client_id'], $setting['secret'], $setting['refresh_token'])->access_token;
            $rulesetId = getRulesetIdIfExistsFromMailing($token, $mailing_id, $ruleName);
            if (!empty($rulesetId)) {
                $postField = getRulesetUpdate($rulesetId, $formatedIndex, $color_code);
            }
            $response = requestXMLAPI($token, $postField);
            print_r($response);
            //exit;
            //print_r($ruleName);
        }
    }    
}






