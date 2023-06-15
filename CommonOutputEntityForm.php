<?php
//////////////////////////////// Common ascensus_entity.php starts ///////////////
opcache_reset();
// Convert date time to ISO date time format
function convertToISO($datetime)
{
    $isoDateTime = "";
    if (!empty($datetime)) {
        // Convert from to 'America/Los_Angeles' to 'UTC'
        $date = new DateTime($datetime, new DateTimeZone('America/Los_Angeles'));
        $date->setTimezone(new DateTimeZone('UTC'));
        // Convert to ISO date time format
        $isoDateTime = $date->format('c');
    }
    
    return $isoDateTime;
}

function getCustomFieldsValues($customFieldName, $customFieldValue)
{
    if ($customFieldName && $customFieldValue) {
        $companyCustomFields = new CompanyCustomFields;
        $query               = $companyCustomFields->select()->from(array('p' => 'CompanyCustomFields'))->setIntegrityCheck(false)
            ->joinLeft(
                array('c' => 'CustomSelectOptions'),
                'p.id = c.customFieldId',
                array('optionid' => 'id', 'customFieldId' => 'customFieldId', 'value' => 'value', 'label' => 'label')
            )
            ->where('p.deleted = 0')
            ->where('p.tagName = ?', $customFieldName)
            ->where('c.value = ?', $customFieldValue)
            ->where('p.companyId = ?', Zend_Registry::get('session')->identity->companyId)
            ->where('p.groupId = ?', Zend_Registry::get('session')->defaultGroupId);
        
        return $customFieldValue = $companyCustomFields->fetchRow($query)->toArray();
    }
}


// Get proposal entity values from templateDataArray:

// Mandatory fields
$asc_plantypecode = $templateDataArray["asc_plantypecode"];

/** For Azure ID 13321: Special Characters taken care of in the Company and Plan name. */
$asc_name = html_entity_decode($templateDataArray["asc_name"]);
/////////// FT Opportunity ID User Story 30947 Task Id 31170 ///////
$asc_ftopportunityid 		= $templateDataArray["FTOpportunityIDCRM"];
$asc_ParentOpportunity     	= $templateDataArray["asc_ParentOpportunity"];
$asc_Productid             	= $templateDataArray["asc_Productid"];
$asc_plantypecode          	= $templateDataArray["asc_plantypecode"];
$asc_businessunitname      	= $templateDataArray["asc_businessunitname"];
$asc_nextfollowupdate      	= $templateDataArray["asc_nextfollowupdate"];
$asc_numproposalsrequested 	= $templateDataArray["asc_numproposalsrequested"];
$asc_proposalduedate       	= convertToISO($templateDataArray["asc_proposalduedate"]);


$flag = false;
if (!empty($asc_plantypecode) && !empty($asc_name) && !empty($asc_ParentOpportunity) && !empty($asc_Productid) && !empty($asc_plantypecode)
    && !empty($asc_businessunitname)
    && !empty($asc_nextfollowupdate)
    && (strlen($asc_numproposalsrequested) > 0)
    && !empty($asc_proposalduedate)
) {
    $flag = true;
}

// Other Fields
$asc_isreferencedproposal      = $templateDataArray["ReferencedProposal"];
$asc_estadminfeecredit         = $templateDataArray["EstAdminFeeCredit"];
$asc_invmtfiduciarysvrcyrlyfee = $templateDataArray["InvestmentFiduciaryServicesAnnual"];
$asc_estadditionalfeesdue      = $templateDataArray["EstAdditionalFeesDue"];
$asc_conversionsetupfee        = $templateDataArray["asc_conversionsetupfee"];
$asc_whoreferencedtheproposal  = $templateDataArray["WhoReferencedProposal"];
$asc_advisorbps                = $templateDataArray["AdvisorBSP"];
$asc_estanninvestmentadvsfee   = (!empty($templateDataArray["EstAnnualInvestmentAdvisorFee"])) ? $templateDataArray["EstAnnualInvestmentAdvisorFee"]
    : $templateDataArray["AdvF2"];
/////////////// Task Id 31172 Azure Id 30947 Investment Change Notice Delivery ////////////////
$asc_investnoticedeliveryfee	=	$templateDataArray["InvestmentChangeNoticeDelivery"];
/////////////// Task Id 31173 Azure Id 30947 New Comparability ////////////////
$asc_eccfee						=	$templateDataArray["EmployerContributionFee"];
$asc_mesirowrate           		= 	$templateDataArray["MesirowRate"];
$asc_clientstate           		= 	$templateDataArray["asc_clientstate"];
$asc_setupfeewaivedcode    		= 	$templateDataArray["asc_setupfeewaivedcode"];
$asc_clientcity            		= 	$templateDataArray["ClientCity"];
$asc_fiduciaryservices316       =   $templateDataArray["asc_fiduciaryservices316Label"];
$asc_advisorbpstier2       		= 	$templateDataArray["AdvisorBPSTier2"];
$AF                        		= 	$templateDataArray["AF"];
$asc_annualrkfee           		= 	$templateDataArray['RKFEE'];
$asc_tpafees               		=	(!empty($templateDataArray["TPAFees"])) ? $templateDataArray["TPAFees"] : $templateDataArray["TPAFees1"];
$asc_vanguardrep           		= 	$templateDataArray['VanguardRep'];
$asc_vanguardproposalid    		= 	$templateDataArray['VanguardProposalID'];
$asc_vanguardopportunityid 		= 	$templateDataArray['VanguardOpportunityID'];
$asc_ownerid               		= 	$templateDataArray['asc_ownerid'];
///////////////////// Azure Id 10594 /////////////////////
$asc_clientpointurl  = $templateDataArray['asc_clientpointurl'];
$asc_defaultfundlist = $templateDataArray['asc_defaultfundlist'];
///////////////////// Azure Id 10594 /////////////////////
//////////// Push back Additonal Service Azure Id 21835 ///////
$asc_annualnoticedeliveryfee		= !isset($templateDataArray['AnnualNoticeDelivery']) ? "$0.00" :$templateDataArray['AnnualNoticeDelivery'];
$asc_easefee						= !isset($templateDataArray['EnhancedAdminServiceExpFee']) ? "$0.00" :$templateDataArray['EnhancedAdminServiceExpFee'];
$asc_enrollmentsupportfee			= !isset($templateDataArray['EnrollSupport']) ? "$0.00" :$templateDataArray['EnrollSupport'];
$asc_financialwellnessfee			= !isset($templateDataArray['FWPlus']) ? (isset($templateDataArray['FWellnessPlus']) ? $templateDataArray['FWellnessPlus'] : "$0.00") : (isset($templateDataArray['FWPlus']) ? $templateDataArray['FWPlus'] : "$0.00" );
$asc_investmentfiduciaryservicesfee	= !isset($templateDataArray['InvFiduciaryServices']) ? "$0.00" :$templateDataArray['InvFiduciaryServices'];
$asc_payrollintegrationfee			= !isset($templateDataArray['PayrollIntegration']) ? "$0.00" : $templateDataArray['PayrollIntegration'];
$asc_fiduciaryservices316fee		= !isset($templateDataArray['FiduciaryServices316Value']) ? "$0.00" : $templateDataArray['FiduciaryServices316Value'];
//////////// Push back Additonal Service Azure Id 21835 ///////
//One Time Installation Fee amount from Tool needs to feed into CRM
if (isset($templateDataArray["OneTimeInstallationFee"])) {
    $asc_conversionsetupfee = $templateDataArray["OneTimeInstallationFee"];
}
// Dynamics API to create/update proposal entity
if (isset($templateDataArray["proposal_id"]) && !empty($templateDataArray["proposal_id"]) && $flag == true) {
    $proposalId       = $templateDataArray['proposal_id'];
    $proposalsTable   = new Proposals();
    $proposal         = $proposalsTable->find($proposalId)->current();
    $externalType     = $proposal->externalType;
    $opportunityId    = $proposal->externalId;
    $externalDataJson = $proposal->externalData;
    if (!empty($externalDataJson)) {
        $externalData = json_decode($externalDataJson, true);
        $entityId     = $externalData['entityId'];
    }
    
    
    if (!empty($opportunityId) && $externalType == "dynamicscrm") {
        // Create a new entity at Dynamics CRM
        $entityData = array(
            "asc_ParentOpportunity@odata.bind" => "/opportunities($opportunityId)",
            "asc_Productid@odata.bind"         => "/asc_psg_products($asc_Productid)",
        );
        if ($asc_proposalduedate != '') {
            $entityData['asc_proposalduedate'] = $asc_proposalduedate;
        }
        if ($asc_plantypecode != '') {
            $entityData['asc_plantypecode'] = $asc_plantypecode;
        }
        if ($asc_name != '') {
            $entityData['asc_name'] = $asc_name;
        }
        if ($asc_businessunitname != '') {
            $entityData['asc_businessunitname'] = $asc_businessunitname;
        }
		/////////// FT Opportunity ID User Story 30947 Task Id 31170 ///////
		if($templateDataArray["productFamily"]=="Ascensus"){
			$entityData['asc_ftopportunityid'] = $asc_ftopportunityid;
		}else{
			$entityData['asc_ftopportunityid'] = "";
		}
        if ($asc_nextfollowupdate != '') {
            $entityData['asc_nextfollowupdate'] = $asc_nextfollowupdate;
        }
        if ($asc_numproposalsrequested != '') {
            $entityData['asc_numproposalsrequested'] = $asc_numproposalsrequested;
        }
        if ($asc_isreferencedproposal != '') {
            $entityData['asc_isreferencedproposal'] = ($asc_isreferencedproposal == "1" ? true : false);
        }
        if ($asc_estadminfeecredit != '') {
            $entityData['asc_estadminfeecredit'] = (float)str_replace("$", "", str_replace(",", "", $asc_estadminfeecredit));
        }
        if ($asc_invmtfiduciarysvrcyrlyfee != '') {
            $entityData['asc_invmtfiduciarysvrcyrlyfee'] = (float)str_replace("$", "", str_replace(",", "", $asc_invmtfiduciarysvrcyrlyfee));
        }
        if ($asc_estadditionalfeesdue != '') {
            $entityData['asc_estadditionalfeesdue'] = (float)str_replace("$", "", str_replace(",", "", $asc_estadditionalfeesdue));
        }
        if ($asc_conversionsetupfee != '') {
            $entityData['asc_conversionsetupfee'] = (float)str_replace("$", "", str_replace(",", "", $asc_conversionsetupfee));
        }
        if ($asc_whoreferencedtheproposal != '') {
            $entityData['asc_whoreferencedtheproposal'] = $asc_whoreferencedtheproposal;
        }
        if ($asc_advisorbps != '') {
            $entityData['asc_advisorbps'] = $asc_advisorbps;
        }
        if ($asc_estanninvestmentadvsfee != '') {
            $entityData['asc_estanninvestmentadvsfee'] = (float)str_replace("$", "", str_replace(",", "", $asc_estanninvestmentadvsfee));
        }
        if ($asc_mesirowrate != '') {
            $entityData['asc_mesirowrate'] = (int)$asc_mesirowrate;
        }
        if ($asc_clientstate != '') {
            $entityData['asc_clientstate'] = $asc_clientstate;
        }
        if ($asc_setupfeewaivedcode != '') {
            $entityData['asc_setupfeewaivedcode'] = (int)$asc_setupfeewaivedcode;
        }
        if ($asc_clientcity != '') {
            $entityData['asc_clientcity'] = $asc_clientcity;
        }
		/////////////// Task Id 31172 Azure Id 30947 New Comparability ////////////////
		if(!empty($asc_investnoticedeliveryfee) && isset($asc_investnoticedeliveryfee)){
			$entityData['asc_investnoticedeliveryfee'] = (float)str_replace("$", "", str_replace(",", "", $asc_investnoticedeliveryfee));
		}
		else{
			$entityData['asc_investnoticedeliveryfee'] = 0;
		}
		/////////////// Task Id 31173 Azure Id 30947 New Comparability ////////////////
		if(!empty($asc_eccfee) && isset($asc_eccfee)){
			$entityData['asc_eccfee'] = (float)str_replace("$", "", str_replace(",", "", $asc_eccfee));
		}
		else{
			$entityData['asc_eccfee'] = 0;
		}
        if ($asc_advisorbpstier2 != '') {
            $entityData['asc_advisorbpstier2'] = $asc_advisorbpstier2;
        }
        if ($AF != '') {
            $entityData['asc_advisorbps'] = $AF;
        }
        if ($asc_annualrkfee != '') {
            $entityData['asc_annualrkfee'] = (float)str_replace("$", "", str_replace(",", "", $asc_annualrkfee));
        }
        if ($asc_tpafees != '') {
            $entityData['asc_tpafees'] = (float)str_replace("$", "", str_replace(",", "", $asc_tpafees));
        }
        if ($asc_vanguardrep != '') {
            $entityData['asc_vanguardrep'] = (int)$asc_vanguardrep;
        }
        if ($asc_vanguardproposalid != '') {
            $entityData['asc_vanguardproposalid'] = $asc_vanguardproposalid;
        }
        if ($asc_vanguardopportunityid != '') {
            $entityData['asc_vanguardopportunityid'] = $asc_vanguardopportunityid;
        }
        if ($asc_ownerid != '') {
            $entityData["ownerid@odata.bind"] = "/systemusers($asc_ownerid)";
        }
        ///////////////////// AZure Id 10594 /////////////////////
        if ($asc_clientpointurl != '') {
            $entityData['asc_clientpointurl'] = $asc_clientpointurl;
        }
        if ($asc_defaultfundlist != '') {
            $entityData['asc_defaultfundlist'] = $asc_defaultfundlist;
        }
        ///////////////////// AZure Id 10594 /////////////////////
		//////////// Push back Additonal Service Azure Id 21835 /////////////////
		if ($asc_annualnoticedeliveryfee != '') {
            $entityData['asc_annualnoticedeliveryfee'] = (float)str_replace("$", "", str_replace(",", "", $asc_annualnoticedeliveryfee));
        }
        if ($asc_easefee != '') {
            $entityData['asc_easefee'] = (float)str_replace("$", "", str_replace(",", "", $asc_easefee));
        }
		if ($asc_enrollmentsupportfee != '') {
            $entityData['asc_enrollmentsupportfee'] = (float)str_replace("$", "", str_replace(",", "", $asc_enrollmentsupportfee));
        }
        if ($asc_financialwellnessfee != '') {
            $entityData['asc_financialwellnessfee'] = (float)str_replace("$", "", str_replace(",", "", $asc_financialwellnessfee));
        }
		if ($asc_investmentfiduciaryservicesfee != '') {
            $entityData['asc_investmentfiduciaryservicesfee'] = (float)str_replace("$", "", str_replace(",", "", $asc_investmentfiduciaryservicesfee));
        }
        if ($asc_payrollintegrationfee != '') {
            $entityData['asc_payrollintegrationfee'] = (float)str_replace("$", "", str_replace(",", "", $asc_payrollintegrationfee));
        }
        if ($asc_fiduciaryservices316fee != '') {
            $entityData['asc_fiduciaryservices316fee'] = (float)str_replace("$", "", str_replace(",", "", $asc_fiduciaryservices316fee));
        }
        else{
            $entityData['asc_fiduciaryservices316fee'] =    0;
        }
        if ($asc_fiduciaryservices316 != '') {
            $entityData['asc_fiduciaryservices316'] = $asc_fiduciaryservices316;
        }
        else{
            $entityData['asc_fiduciaryservices316'] = "";
        }
		//////////// Push back Additonal Service Azure Id 21835 /////////////////
        if (isset($templateDataArray["AdminType"]) && $templateDataArray["AdminType"] == "FS") {
            $AdminTypeValPost = "860530000";
        } else {
            if (isset($templateDataArray["AdminType"]) && $templateDataArray["AdminType"] == "RKO") {
                $AdminTypeValPost = "860530001";
            } else {
                $AdminTypeValPost = "";
            }
        }
        
        if ($templateDataArray["Service"] == "Fee Based") {
            $ServiceValPost = "860530000";
        } else {
            if ($templateDataArray["Service"] == "Level Compensation") {
                $ServiceValPost = "860530001";
            } else {
                $ServiceValPost = "";
            }
        }
        // $templateDataArray["Service"] ="";
        
        if (isset($templateDataArray["InstallType"]) && $templateDataArray["InstallType"] != '') {
            $entityData['asc_rsinstallationtype'] = $templateDataArray["InstallType"];
        }
        if (isset($templateDataArray["FiduciaryServices"]) && $templateDataArray["FiduciaryServices"] != '') {
            $getFiduciaryServices = getCustomFieldsValues('FiduciaryServices', $templateDataArray["FiduciaryServices"]);
            if (isset($getFiduciaryServices['label']) && $getFiduciaryServices['label']) {
            }
            $entityData['asc_fiduciaryservices'] = $getFiduciaryServices['label'];
        }
        if ($AdminTypeValPost != '') {
            $entityData['asc_administrationtype'] = $AdminTypeValPost;
        }
        if ($ServiceValPost != '') {
            $entityData['asc_compensationtype'] = $ServiceValPost;
        }
        if (isset($templateDataArray["ManagedAccountsLabel"]) && $templateDataArray["ManagedAccountsLabel"] != '') {
            $entityData['asc_managedaccounts'] = $templateDataArray["ManagedAccountsLabel"];
        }
        
        if (isset($templateDataArray["PartnerInvestLineup"]) && $templateDataArray["PartnerInvestLineup"] != '') {
            $partnerInvestLineup = getCustomFieldsValues('PartnerInvestLineup', $templateDataArray["PartnerInvestLineup"]);
            if (isset($partnerInvestLineup['label']) && $partnerInvestLineup['label']) {
                $entityData['asc_partnerinvestmentlineup'] = $partnerInvestLineup['label'];
            }
        }
        
        if (isset($templateDataArray["proposal_id"]) && $templateDataArray["proposal_id"] != '') {
            $entityData['asc_clientpointid'] = $templateDataArray["proposal_id"];
        }
        /////////// Azure Task Id 12839 ////////////////
        if (isset($templateDataArray["Channel"]) && $templateDataArray["Channel"] != '') {
            $entityData['asc_channel'] = $templateDataArray["Channel"];
        } else {
            $entityData['asc_channel'] = "860530001";
        }
        /////////// Azure Task Id 12839 ////////////////
        $entityData  = json_encode($entityData);
        $dynamicsCrm = new Dynamicscrm();
        if (!empty($entityId)) {  // Update existing entity
            $resp = $dynamicsCrm->runDynamicsQuery("PATCH", "asc_psgopportunityproducts", "", "", $entityData, $entityId);
            fwrite($fp, $resp.PHP_EOL);
        } else {  // Create a new entity at CRM
            $jsonResp = $dynamicsCrm->runDynamicsQuery("POST", "asc_psgopportunityproducts", "", "", $entityData);
            fwrite($fp, $jsonResp.PHP_EOL);
            //pr($jsonResp);
            $resource = json_decode($jsonResp, true);
            if (!array_key_exists('error', $resource)) {
                $resourceId = $resource['asc_psgopportunityproductid'];
                //Save resourceId wrt. proposalId in db
                if (!empty($resourceId)) {
                    $db = Zend_Registry::get("db");
                    if (!empty($resourceId)) {
                        $insertData = json_encode(array("entityId" => $resourceId));
                        $query      = "update Proposals set externalData = '".$insertData."' where id = '".$proposalId."'";
                        $db->query($query);
                    }
                }
            }
        }
        
        # Uncomment to record debug logs (under uploads dir)   
        $fp = fopen('/mnt/vol/var/www/gmi.paperlessproposal.com/htdocs/uploads/workspaceUserlog_1.9.2020.txt', 'a');
        fwrite($fp, "v1".PHP_EOL);
        fwrite($fp, $entityData.PHP_EOL);
        fwrite($fp, $resp.PHP_EOL);
        fwrite($fp, $jsonResp.PHP_EOL);
        fclose($fp);
    }
}
?>