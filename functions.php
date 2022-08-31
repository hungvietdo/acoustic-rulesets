<?php
function writeToFile($outFile, $content)
{
    $myfile = fopen($outFile, "w") or die("Unable to open file!");
    fwrite($myfile, $content);
    fclose($myfile);
}


function composeATd($number, $colorCode)
{
    return <<<HTML
                <td>
                    <div style="font-family:'Arial';border:1px solid #E7E7E7;font-size:16px;border-radius:5px;padding:10px;">
                        <div>
                            <img style="width:248px;height:150px;padding:4px;border:1px solid #E7E7E7;"
                                src="%%Photo_{$number}%%">
                            <div style="font-style:normal;font-weight:700;font-size:16px;color:#575757;">
                                <p style="width:250px;height:25px;font-size:16px;line-height:143.74%;color:#575757;">%%Year_{$number}%% %%Make_{$number}%% %%Model_{$number}%%</p>
                                <p style="width:150px;height:26px;font-size:18px;color:#575757;padding-bottom:10px;">%%price_{$number}%%
                                </p>
                            </div>
                            <p style="border:1px solid;padding:10px;color:{$colorCode};text-align:center;width:100px;">
                                <a href="%%Ad_URL_{$number}%%?%%CMP_Code%%" target="_blank"
                                    style="color:{$colorCode};font-style:normal;font-weight:700;font-size:14px;line-height:16px;text-align:center;">View Details</a>
                            </p>
                        </div>
                    </div>
                </td>
    HTML;
}

function composeHtmlContentRuleset($number, $colorCode)
{
return <<<HTML
<div style="font-family:'Arial';border:1px solid #E7E7E7;font-size:16px;border-radius:5px;padding:10px;">
            <div>
                <img style="width:248px;height:150px;padding:4px;border:1px solid #E7E7E7;"
                    src="%%Photo_{$number}%%">
                <div style="font-style:normal;font-weight:700;font-size:16px;color:#575757;">
                    <p style="width:250px;height:25px;font-size:16px;line-height:143.74%;color:#575757;">%%Year_{$number}%% %%Make_{$number}%% %%Model_{$number}%%</p>
                    <p style="width:150px;height:26px;font-size:18px;color:#575757;padding-bottom:10px;">%%price_{$number}%%
                    </p>
                </div>
                <p style="border:1px solid;padding:10px;color:{$colorCode};text-align:center;width:100px;">
                    <a href="%%Ad_URL_{$number}%%?cmp=%%CMP_Code%%" target="_blank"
                        style="color:{$colorCode};font-style:normal;font-weight:700;font-size:14px;line-height:16px;text-align:center;">View Details</a>
                </p>
            </div>
        </div>
HTML;
}

// function buildSimilarAdsHtml($startIndex = 1, $color) : string
//     {        
//         $similarAdsHtml = '<table style="border-spacing: .5em .5em;">' . "\n" . '<tr>' . "\n";
//         foreach (range($startIndex, $startIndex + 5) as $index) {
//             $formatedIndex = sprintf('%02d', $index);
//             $similarAdsHtml .= composeATd($formatedIndex, $color) . "\n";

//             // Check even index and close/open table row
//             if ((($index) % 2 == 0)) {
//                 $similarAdsHtml .= '</tr>' . "\n" . '<tr>' . "\n";
//             }
//         }

//         return $similarAdsHtml . '</tr> ' . "\n" . '</table>';
//     }

function ruleset($list_id, $mailing_id, $number, $colorCode)
{
    $html_listing_content = composeHtmlContentRuleset($number, $colorCode);

    return <<<XML
<Envelope>
  <Body>
    <AddDCRuleset>
      <LIST_ID>$list_id</LIST_ID>
      <MAILING_ID>{$mailing_id}</MAILING_ID>
      <RULESET_NAME>similar_listing_{$number}</RULESET_NAME>
      <CONTENT_AREAS>
        <CONTENT_AREA name="listing_content_{$number}" type="Body-HTML">
          <DEFAULT_CONTENT name="default"><![CDATA[{$html_listing_content}]]></DEFAULT_CONTENT>
        </CONTENT_AREA>
      </CONTENT_AREAS>
      <RULES>
        <RULE>
          <RULE_NAME>No listing data</RULE_NAME>
          <PRIORITY>1</PRIORITY>
          <CRITERIA>
            <EXPRESSION criteria_type="profile">
              <COLUMN>Ad_Id_{$number}</COLUMN>
              <OPERATOR>is blank</OPERATOR>
            </EXPRESSION>
          </CRITERIA>
        </RULE>
      </RULES>
    </AddDCRuleset>
  </Body>
</Envelope>
XML;
}

function getRulesetUpdate($rulesetId, $number, $colorCode)
{
    $html_listing_content = composeHtmlContentRuleset($number, $colorCode);

    return <<<XML
<Envelope>
  <Body>
    <ReplaceDCRuleset>
      <RULESET_ID>{$rulesetId}</RULESET_ID>
      <RULESET_NAME>similar_listing_{$number}</RULESET_NAME>
      <CONTENT_AREAS>
        <CONTENT_AREA name="listing_content_{$number}" type="Body-HTML">
          <DEFAULT_CONTENT name="default"><![CDATA[{$html_listing_content}]]></DEFAULT_CONTENT>
        </CONTENT_AREA>
      </CONTENT_AREAS>
      <RULES>
        <RULE>
          <RULE_NAME>No listing data</RULE_NAME>
          <PRIORITY>1</PRIORITY>
          <CRITERIA>
            <EXPRESSION criteria_type="profile">
              <COLUMN>Ad_Id_{$number}</COLUMN>
              <OPERATOR>is blank</OPERATOR>
            </EXPRESSION>
          </CRITERIA>
        </RULE>
      </RULES>
    </ReplaceDCRuleset>
  </Body>
</Envelope>
XML;
}