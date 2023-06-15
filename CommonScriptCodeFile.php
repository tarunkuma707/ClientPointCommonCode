<!--- Common ascensus_script_phase_2 Starts here --->
<link href="https://ascensus.clientpoint.net/asset/get-assets/MGlmLTMxNi0xMTAzLWJ6aQ%3D%3D/AscensusPhase_2_CommonStyle.css" rel="stylesheet"/>
<?php
function getNumberValue( $value ) {
    $ret = 0;
    $ret = floatval( preg_replace( "/[^-0-9\.]/", "", $value ) );
    
    return $ret;
}

$fields = array();
$blocks = array();
$i      = 0;
// take form fields and put it into a more usable format
// simple fields that are not part of a block of rows are in the root
// row entry arrays are in an associated array with the row block name
foreach ( $this->formFields as $fieldEntry ) {
    if ( $fieldEntry['type'] == 'block' ) {
        foreach ( $fieldEntry['value'] as $blockEntry ) {
            $blocks[ $fieldEntry['cleanName'] ][ $i ][ $blockEntry['name'] ] = $blockEntry['value'];
        }
        $i ++;
    } else {
        //$fields[$fieldEntry['name']] = $fieldEntry['value'];
        $fields[ $fieldEntry['name'] ] = htmlspecialchars( $fieldEntry['value'] );
    }
}
function getCustomFieldsValues( $customFieldName, $customFieldValue ) {
    if ( $customFieldValue != "" ) {
        $companyCustomFields = new CompanyCustomFields;
        $query               = $companyCustomFields->select()->from( array( 'p' => 'CompanyCustomFields' ) )->setIntegrityCheck( false )
            ->joinLeft( array( 'c' => 'CustomSelectOptions' ), 'p.id = c.customFieldId', array( 'optionid' => 'id', 'customFieldId' => 'customFieldId', 'value' => 'value', 'label' => 'label' ) )
            ->where( 'p.deleted = 0' )
            ->where( 'p.tagName = ?', $customFieldName )
            ->where( 'c.value = ?', $customFieldValue )
            ->where( 'p.companyId = ?', Zend_Registry::get( 'session' )->identity->companyId )
            ->where( 'p.groupId = ?', Zend_Registry::get( 'session' )->defaultGroupId );
        
        return $customFieldValue = $companyCustomFields->fetchRow( $query )->toArray();
    }
}
$companyID			= Zend_Registry::get( 'session' )->companyId;
$proposalId 		= $fields['proposal_id'];
/////////////////////////  Azure id 10594 ///////////////
$proposalTable      = new Proposals();
$proposal           = $proposalTable->find( $proposalId )->current();
$session            = Zend_Registry::get( 'session' );
//////// TODO : Fetch the domain name dynamically ///////
$hostname			= "clientpoint.co";
///////////////// Get Proposal Divsion and url ////////
$proposalDivisions         = new ProposalDivisions();
$proposalDivisionselect = $proposalDivisions->select()->from(array('pd' => 'ProposalDivisions'))->setIntegrityCheck(false)
					->joinInner(array('d' => 'Divisions'), 'd.id = pd.divisionId', array('url' => 'url'))
					->where('pd.proposalId = ?', $proposalId);
					$divisionUrl = $proposalDivisions->fetchRow($proposalDivisionselect)->toArray();
if(!empty($divisionUrl["url"]) && isset($divisionUrl)){
	$siteUrl	=	'https://' .$divisionUrl["url"].'.'.$hostname;
}
else{
	$siteUrl            = 'https://' . array_shift((explode('.', $_SERVER['HTTP_HOST']))).'.'.$hostname;
}
$asc_clientpointurl = $siteUrl . "/v/$proposalId";
if ( ! empty( $proposal->PIN ) ) {
    $asc_clientpointurl = $siteUrl . "/v/$proposalId/" . $proposal->PIN;
}
/////////////////////////  Azure id 10594 ///////////////
function getValueLabelCustomFieldValue($value){
    $CompanyCustomFieldsTable = new CompanyCustomFields();
    $CustomSelectOptionsTable = new CustomSelectOptions();
    $queryOption = $CustomSelectOptionsTable->select()->from($CustomSelectOptionsTable)
    ->where('id in (?)', $value);
    $selectedOptions  = $CustomSelectOptionsTable->fetchRow($queryOption)->toArray();
    return $selectedOptions;
}
$selectOptions            = array();
$CompanyCustomFieldsTable = new CompanyCustomFields();
$CustomSelectOptionsTable = new CustomSelectOptions();
$query = $CompanyCustomFieldsTable->select()->from(array('p'=>'CompanyCustomFields'))->setIntegrityCheck(false)
	->joinLeft(array('pc' => 'ProposalCustomFields'),'p.id = pc.customFieldId',array('customFieldValues'))
	->where( 'deleted = 0' )->where('status = 1')
	->where( 'proposalId = ?',$proposalId )
	->where( 'companyId = ?', Zend_Registry::get('session')->identity->companyId );
$result                   = $CompanyCustomFieldsTable->fetchAll( $query )->toArray();
if (!empty($result)) {
	foreach ($result as $k => $v) {
		if(is_numeric($v['customFieldValues'])){
            $values[] = $v['customFieldValues'];
        }
        if($v["tagName"]=="PartnerInvestLineup"){
           	$partnerInvestLineup = getValueLabelCustomFieldValue($v["customFieldValues"]);
            $fields["PartnerInvestLineup"] = $partnerInvestLineup["value"];
        }
        if($v["tagName"]=="AdminType"){
			$adminType = getValueLabelCustomFieldValue($v["customFieldValues"]);
           $fields["AdminType"] = $adminType["value"];
        }
        if($v["tagName"]=="FiduciaryServices"){
			$fiduciaryServices = getValueLabelCustomFieldValue($v["customFieldValues"]);;
            $fields["FiduciaryServices"] = $fiduciaryServices["value"];
        }
		//////////////////// User Story 35291: Ascensus | 3(16) Fiduciary Services ///////////////
        if($v["tagName"]=="3(16) Fiduciary Services"){
			$fiduciaryServices316 = getValueLabelCustomFieldValue($v["customFieldValues"]);;
			$fields['FiduciaryServices316'] = $fiduciaryServices316["value"];
		}
        if($v["tagName"]=="InstallType"){
			$installType = getValueLabelCustomFieldValue($v["customFieldValues"]);;
            $fields["InstallType"] = $installType["value"];
            $fields["InstallTypeLabel"] = $installType["label"];
        }
        if($v["tagName"]=="ManagedAccounts"){
			$managedAccounts = getValueLabelCustomFieldValue($v["customFieldValues"]);;
            $fields["ManagedAccounts"] = $managedAccounts["value"];
        }
        if($v["tagName"]=="GoldmanBaseFeeDiscount"){
			$GoldmanBaseFeeDiscount = getValueLabelCustomFieldValue($v["customFieldValues"]);;
            $fields["GoldmanBaseFeeDiscount"] = $GoldmanBaseFeeDiscount["value"];
        }
        if($v["tagName"]=="ClientPointAccountCode"){
			$ClientPointAccountCode = getValueLabelCustomFieldValue($v["customFieldValues"]);;
            $fields["ClientPointAccountCode"] = $ClientPointAccountCode["value"];
        }
        if($v["tagName"]=="PlanType"){
			$PlanType = getValueLabelCustomFieldValue($v["customFieldValues"]);;
            $fields["PlanType"] = $PlanType["value"];
        }
    }
	if(!empty($values)) {
        $queryOption   = $CustomSelectOptionsTable->select()->from($CustomSelectOptionsTable)->where('id in (?)',$values);
        $selectOptions = $CustomSelectOptionsTable->fetchAll($queryOption)->toArray();
        foreach($selectOptions as $kk => $vv){
            $optionArray[$vv['customFieldId']][$vv['id']] = $vv['value'];
        }
    }
	foreach($result as $key => $val) {
        if(array_key_exists($val['id'],$optionArray)) {
            $value = implode(",",$optionArray[$val['id']]);
        } else {
            $value = $val['customFieldValues'];
        }
        $templateData[$key]['type']        = $val['type'];
        $templateData[$key]['name']        = $val['customId'];
        $templateData[$key]['encodedName'] = $val['customId'];
        $templateData[$key]['value']       = $value; //$val['customFieldValues'];
        $templateData[$key]['id']          = '';
    }
}
$customRep = array();
foreach( $templateData as $customField ){
    $customRep[$customField['name']] 	= $customField['value'];
}
/////////////////// Check for Additional Services Checked from Info page //////////////////////
$selAdditionalServices	=	"";
if(!empty($customRep)){
	foreach($customRep as $ky=>$values){
		if($ky=="AdditionalServices"){
			$selAdditionalServices	=	$values;
		}
	}
}
$proposalHelper        = new Proposal( $proposalId );
$division              = $proposalHelper->getDivision( $proposalId );
$service               = $proposalHelper->getService( $proposalId );
$productFamily         = $division->name;
$serviceName           = $service->name;
$customFieldLabelArray = [];
array_push( $customFieldLabelArray, $productFamily );
if ( $productFamily == "Goldman" ) {
    array_push( $customFieldLabelArray, $serviceName );
    if ( $fields['AdminType'] ) {
        $adminType = getCustomFieldsValues( 'AdminType', $fields['AdminType'] );
        if ( $adminType['label'] ) {
            array_push( $customFieldLabelArray, $adminType['label'] );
        }
    }
    $ClientPointAccountCode = getCustomFieldsValues( 'ClientPointAccountCode', $fields['ClientPointAccountCode'] );
    if ( $ClientPointAccountCode['label'] ) {
        array_push( $customFieldLabelArray, $ClientPointAccountCode['label'] );
    }
    /////// Goldman Product Family Conditions Ends below ////
} /////////////////// Get all the block of Ascensus condition to fix 20003 Ascensus consolidated tool level comp 403b ////////////////
elseif ( $productFamily == "Ascensus" ) {
    if ( $serviceName == "Fee Based" ) {
        if ( isset( $fields["PartnerInvestLineup"] ) && trim( $fields["PartnerInvestLineup"] ) != "AscLPLNone" && trim( $fields["PartnerInvestLineup"] ) != "AscFidelity" && $fields["PartnerInvestLineup"] != "AscCLA" ) {  ///////////// LPL Tools If they select Partner Invest Lineup ////////////
            $partnerInvestLineup = getCustomFieldsValues( 'PartnerInvestLineup', $fields['PartnerInvestLineup'] );
            if ( $partnerInvestLineup['label'] ) {
                array_push( $customFieldLabelArray, $partnerInvestLineup['label'] );
            }
            if ( isset( $fields['PlanType'] ) && ( $fields["PartnerInvestLineup"] == "AscLPLCore"  || $fields["PartnerInvestLineup"] == "AscLPLExpanded" || $fields["PartnerInvestLineup"] == "AscLPLPassive" )) {
                $PlanType = getCustomFieldsValues( 'PlanType', $fields['PlanType'] );
                if ( $PlanType['label'] == "403(b)" ) {
                    array_push( $customFieldLabelArray, $PlanType['label'] );
                }
            }
        }
    }
    //////////////// User Story 35257: Ascensus | Consolidated Ascensus Tools | Update the tool code to show Franklin Templeton Personal Fund List when Managed Account is Franklin Templeton Personal Retirement Path selected on the info page /////////////////////
	if($fields["PartnerInvestLineup"] != "AscLPLCore" && $fields["PartnerInvestLineup"] != "AscLPLExpanded" && $fields["PartnerInvestLineup"] != "AscLPLPassive"){
		if ( isset( $fields['ManagedAccounts'] ) ) {
			if ( $fields['ManagedAccounts'] == "AscRussellMA" || $fields['ManagedAccounts'] == "AscFTMA") {
				$managedAccounts = getCustomFieldsValues( 'ManagedAccounts', $fields['ManagedAccounts'] );
				if ( $managedAccounts['label'] ) {
					array_push( $customFieldLabelArray, $managedAccounts['label'] );
				}
			}
		}
	}
    array_push( $customFieldLabelArray, $serviceName );
    if ( $fields['InstallType'] ) {
        $InstallType = getCustomFieldsValues( 'InstallType', $fields['InstallType'] );
        if ( $InstallType['label'] == "Startup" ) {
            array_push( $customFieldLabelArray, $InstallType['label'] );
        }
    }
    if ( $fields['AdminType'] ) {
        $adminType = getCustomFieldsValues( 'AdminType', $fields['AdminType'] );
        if ( $adminType['label'] ) {
            array_push( $customFieldLabelArray, $adminType['label'] );
        }
    }
    /////// Ascensus Product Family Conditions Ends below ////
} elseif ( $productFamily == "ML" ) { /////// Merryl Lynch Family Conditions Starts ////
    str_replace( $serviceName, "", $customFieldLabelArray );
    if ( $fields['FiduciaryServices'] ) {
        $FiduciaryServices = getCustomFieldsValues( 'FiduciaryServices', $fields['FiduciaryServices'] );
        if ( $FiduciaryServices['label'] == "3(38)" ) {
            array_push( $customFieldLabelArray, $FiduciaryServices['label'] );
        }
    }
    if ( $fields['AdminType'] ) {
        $adminType = getCustomFieldsValues( 'AdminType', $fields['AdminType'] );
        if ( $adminType['label'] ) {
            array_push( $customFieldLabelArray, $adminType['label'] );
        }
    }
    if ( $fields['InstallType'] ) {
        $InstallType = getCustomFieldsValues( 'InstallType', $fields['InstallType'] );
        if ( $InstallType['label'] == "Startup" || $InstallType['label'] == "Conversion" || $InstallType['label'] == "Merger" || $InstallType['label'] == "Product Change" ) {
            array_push( $customFieldLabelArray, $InstallType['label'] );
        }
    }
} elseif ( $productFamily == "Morgan Stanley" ) { ////////// Morgan Stanley Starts //////
    str_replace( $serviceName, "", $customFieldLabelArray );
    if ( $fields['AdminType'] ) {
        $adminType = getCustomFieldsValues( 'AdminType', $fields['AdminType'] );
        if ( $adminType['label'] ) {
            array_push( $customFieldLabelArray, $adminType['label'] );
        }
    }
    if ( $fields['InstallType'] ) {
        $InstallType = getCustomFieldsValues( 'InstallType', $fields['InstallType'] );
        if ( $InstallType['label'] == "Startup" ) {
            array_push( $customFieldLabelArray, $InstallType['label'] );
        }
    }
} elseif ( $productFamily == "Raymond James" ) { ////////// Raymond James Conditions Starts //////
    str_replace( $serviceName, "", $customFieldLabelArray );
    if ( $fields['AdminType'] ) {
        $adminType = getCustomFieldsValues( 'AdminType', $fields['AdminType'] );
        if ( $adminType['label'] ) {
            array_push( $customFieldLabelArray, $adminType['label'] );
        }
    }
    if ( $fields['InstallType'] ) {
        $InstallType = getCustomFieldsValues( 'InstallType', $fields['InstallType'] );
        if ( $InstallType['label'] == "Startup" ) {
            array_push( $customFieldLabelArray, $InstallType['label'] );
        }
    }
}
elseif ( $productFamily == "State Farm" ) { ////////// State Farm Conditions Starts //////
    if ( $fields['AdminType'] ) {
        $adminType = getCustomFieldsValues( 'AdminType', $fields['AdminType'] );
        if ( $adminType['label'] ) {
            array_push( $customFieldLabelArray, $adminType['label'] );
        }
    }
    if ( $fields['InstallType'] ) {
        $InstallType = getCustomFieldsValues( 'InstallType', $fields['InstallType'] );
        if ( $InstallType['label'] == "Startup" ) {
            array_push( $customFieldLabelArray, $InstallType['label'] );
        }
    }
}else{
    if ( $fields['AdminType'] ) {
        $adminType = getCustomFieldsValues( 'AdminType', $fields['AdminType'] );
        if ( $adminType['label'] ) {
            array_push( $customFieldLabelArray, $adminType['label'] );
        }
    }
    if ( $fields['InstallType'] ) {
        $InstallType = getCustomFieldsValues( 'InstallType', $fields['InstallType'] );
        if ( $InstallType['label'] == "Startup" ) {
            array_push( $customFieldLabelArray, $InstallType['label'] );
        }
    }
}
$customFieldLabelText                   = implode( " ", $customFieldLabelArray );
$fields["PricingTools"]                 = array_search( $customFieldLabelText, $keyArr );
$productsTable                          = new Products();
if(empty($fields["PricingTools"])){
	echo "<h1>There is something went wrong. Please review the selection of custom fields on the info page.</h1>";
	die();
}
$productSelect                          = $productsTable->select()->where( 'custom6 = ?', $fields["PricingTools"] )->where( 'deleted = 0' )->where('companyId = ?', $companyID);
$productsArr                            = $productsTable->fetchAll( $productSelect )->toArray();
$products								= $productsArr[0];
$fundListArray                          = explode( ",", $products['custom2'] );
$requestData['asc_psgpricingschedules'] = explode( ",", $products['custom3'] );
$fieldsShow                             = explode( ",", $products['custom4'] );
///////// Additional Services Azure id 21835 /////////
if(isset($fields["AdditionalServices"]) && !empty($fields["AdditionalServices"])){
    if(strpos($fields["AdditionalServices"],",")!==false){
        $additionalServices	=	explode(",",$fields["AdditionalServices"]);
    }
    else{
        $additionalServices	=	array($fields["AdditionalServices"]);
    }
}
/////////////////// Remove Ancillary Fee Azure Id 21835 //////////
if(in_array("AncillaryFee", $fieldsShow)){
    $ancillary_key = array_search("AncillaryFee",$fieldsShow);
    unset($fieldsShow[$ancillary_key]);
}
$disableFields                          = explode( ",", $products['custom5'] );
$category                               = $products['category'];
if ( count( $blocks ) == 0 ) {
    unset( $fields["FLN"] );
}
if ( ! empty( $serviceName ) ) {
    $requestData['asc_name'] = $productFamily; // Product Name from info page
}
$dynamicsCrm = new Dynamicscrm();
$crmDataJson = $dynamicsCrm->pullCRMData( $proposalId, $requestData );
$crmData     = json_decode( $crmDataJson );
// Opportunity Data
if ( ! empty( $crmData->opportunityData ) ) {
    $oppdata         = $crmData->opportunityData->value[0];
    $opportunityId   = $oppdata->opportunityid;
    $opportunityName = $oppdata->name;
    // Section 1 Data
    $fields['PlanName'] = $oppdata->name; // = Company name = Opprtunity name
    //if ($startUp) {
    //$fields['PB']                     = $oppdata->asc_eligibleemployees;
    //} else {
    $fields['PB'] = $oppdata->asc_activeparticipants;
    //}
    $fields['PA'] = "$" . number_format( $oppdata->asc_assets, 2 );
    $fields['AN'] = $oppdata->parentcontactid->fullname;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             // Opportunity Contact
    //$fields['Client']             = $oppdata->customerid_account->name;   // Organization = Account Name
    $fields['SN']   = $oppdata->parentcontactid->address1_line1;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    // Contact address1_line1
    $streetAddress2 = $oppdata->parentcontactid->address2_line2; // Contact address2_line2
    $address1City   = $oppdata->parentcontactid->address1_city; // Contact address1_city
    $address1State  = $oppdata->parentcontactid->{'asc_state@OData.Community.Display.V1.FormattedValue'}; // Contact's State
    $address1Zip    = $oppdata->parentcontactid->address1_postalcode; // Contact address1_postalcode
    $fields['CSZ']  = $address1City . ', ' . $address1State . ', ' . $address1Zip;
    // Section 2 Data
    $fields['PreparedFor'] = $oppdata->name;       // = Company name = Opprtunity name
    $fields['PresentedBy'] = $oppdata->parentcontactid->fullname;    // Associated contact's fullname
    $oppPlantype           = $oppdata->{'asc_rsplantype@OData.Community.Display.V1.FormattedValue'};
    $planType              = $oppdata->asc_rsplantype;   // Plan type code
    $nextfollowupdate      = $oppdata->asc_nextfollowupdate;
    $oppOwnerId            = $oppdata->_ownerid_value;
    $ascUserId             = $oppdata->_asc_isc_value;
    // Pull deatils of ISC Contact
    if ( ! empty( $ascUserId ) ) {
        $filter       = "%24filter=systemuserid%20eq%20%27$ascUserId%27";
        $dynamicsCrm  = new Dynamicscrm();
        $userDataJson = $dynamicsCrm->runDynamicsQuery( "GET", "systemusers", $filter );
        $userData     = json_decode( $userDataJson, true );
        if ( ! array_key_exists( 'error', $userData ) ) {
            $oppUserEmailId = $userData['value'][0]['internalemailaddress'];
        }
    }
    // Update assigned to in database
    if ( ! empty( $oppUserEmailId ) ) {
        //Get userID by email ID
        $usersTable = new Users();
        $select     = $usersTable->select()->where( 'username = ?', $oppUserEmailId );
        $users      = $usersTable->fetchRow( $select );
        $userID     = $users->id;
        //update propsoal table assign functionality
        if ( isset( $userID ) && isset( $proposalId ) ) {
            $proposalsTable = new Proposals();
            $data           = array( 'userId' => $userID );
            $where          = array( 'id = (?)' => $proposalId );
            $proposalsTable->update( $data, $where );
        }
    }
}

# Opportunity Account data
if ( ! empty( $crmData->opportunityAccount ) ) {
    $oppAccount       = $crmData->opportunityAccount->value[0];
    $fields['Client'] = $oppAccount->name;  // Organization = Account Name
}

# Additional Services 21835
$additionalServicesJSON = $dynamicsCrm->runDynamicsQuery("GET", "asc_additionalservices", "", "");
$additionalServicesData = json_decode($additionalServicesJSON);
$easeFee	=	"";
$enrollSupportFee		=	"";
$annual_min				=	"";
$annual_max				=	"";
$cal_annualNtcDel		=	"";
$cal_FWE				=	"";
$cal_investmentNDC		=	0;
$cal_FWP				=	"";
$cal_payrollInt			=	"";
if(!empty($additionalServicesData->value)){
	foreach($additionalServicesData->value as $additionalServicesValue){
		if($additionalServicesValue->asc_additionalservicename =="EASE"){
			if($additionalServicesValue->asc_minppt <= $fields['PB'] && $additionalServicesValue->asc_maxppt >= $fields['PB']){
				$easeFee	=	$additionalServicesValue->asc_flatdollarrate_base;
			}
			else{
				$easeFee	=	0;
			}			
		}
		elseif($additionalServicesValue->asc_additionalservicename=="Enrollment Support"){
			$enrollSupportFee	=	$additionalServicesValue->asc_flatdollarrate_base;
		}
        elseif($additionalServicesValue->asc_additionalservicename=="Ascensus 3(16)"){
			$fiduciaryServiceFee	=	$additionalServicesValue->asc_flatdollarrate_base;
		}
		/////////////// Task Id 31173 Azure Id 30947 New Comparability ////////////////
		elseif($additionalServicesValue->asc_additionalservicename=="Non-Vanguard New Comparability"){
			$NewComparability	=	$additionalServicesValue->asc_flatdollarrate_base;
		}
		elseif($additionalServicesValue->asc_additionalservicename=="Annual Notice Delivery"){
			////////////////// Annual Notice Delivery User Story 30947 Task Id 31169 ///////////////////////////
			$basepricerate	=	$oppdata->asc_eligibleemployees * $additionalServicesValue->asc_priceperrate;
			if(!empty($additionalServicesValue->asc_minimum_base)){
				$annual_min	=	$additionalServicesValue->asc_minimum_base;
			}
			if(!empty($additionalServicesValue->asc_maximum_base)){
				$annual_max	=	$additionalServicesValue->asc_maximum_base;
			}
			if(!empty($annual_min) && !empty($annual_max)){
				if($basepricerate>$annual_min && $annual_max>$basepricerate ){
					$cal_annualNtcDel	=	$basepricerate;
				}
				else{
					if($annual_max<$basepricerate){
						$cal_annualNtcDel	=	$annual_max;
					}
					if($annual_min>$basepricerate){
						$cal_annualNtcDel	=	$annual_min;
					}
				}
			}
			else{
				if(!empty($annual_min)){
					if($annual_min > $basepricerate){
						$cal_annualNtcDel	= 	$annual_min;
					}
					else{
						$cal_annualNtcDel	=	$basepricerate;
					}
				}
				elseif(!empty($annual_max)){
					if($annual_max > $basepricerate){
						$cal_annualNtcDel	= 	$basepricerate;
					}
					else{
						$cal_annualNtcDel	=	$annual_max;
					}
				}
				else{
					$cal_annualNtcDel	=	$basepricerate;
				}
			}
		}
		///////////////////Task Id 31172 and Azure Id 30947 Investment Change Notice Delivery /////////////////////
		elseif($additionalServicesValue->asc_additionalservicename=="Investment Change Notice Delivery"){
			$investmentChangeNoticeDel	=	getNumberValue($oppdata->asc_eligibleemployees) * getNumberValue($additionalServicesValue->asc_priceperrate);
			if(!empty($additionalServicesValue->asc_minimum)){
				$investmentChangeNoticeDel_min	=	getNumberValue($additionalServicesValue->asc_minimum);
			}
			if(!empty($additionalServicesValue->asc_maximum)){
				$investmentChangeNoticeDel_max	=	getNumberValue($additionalServicesValue->asc_maximum);
			}
			if(!empty($investmentChangeNoticeDel_min) && !empty($investmentChangeNoticeDel_max)){
				if(getNumberValue($investmentChangeNoticeDel) > getNumberValue($investmentChangeNoticeDel_min) && getNumberValue($investmentChangeNoticeDel_max) < getNumberValue($investmentChangeNoticeDel)){
					$cal_investmentNDC	=	getNumberValue($investmentChangeNoticeDel);
				}
				else{
					if(getNumberValue($investmentChangeNoticeDel_max) <getNumberValue($investmentChangeNoticeDel)){
						$cal_investmentNDC	=	getNumberValue($investmentChangeNoticeDel_max);
					}
					if(getNumberValue($investmentChangeNoticeDel_min) > getNumberValue($investmentChangeNoticeDel)){
						$cal_investmentNDC	=	$investmentChangeNoticeDel_min;
					}
				}
			}
			else{
				if(!empty($investmentChangeNoticeDel_min)){
					if(getNumberValue($investmentChangeNoticeDel_min) > getNumberValue($investmentChangeNoticeDel)){
						$cal_investmentNDC	= 	getNumberValue($investmentChangeNoticeDel_min);
					}
					else{
						$cal_investmentNDC	=	getNumberValue($investmentChangeNoticeDel);
					}
				}
				elseif(!empty($investmentChangeNoticeDel_max)){
					if($investmentChangeNoticeDel_max > $investmentChangeNoticeDel){
						$cal_investmentNDC	= 	getNumberValue($investmentChangeNoticeDel);
					}
					else{
						$cal_investmentNDC	=	getNumberValue($investmentChangeNoticeDel_max);
					}
				}
				else{
					$cal_investmentNDC	=	getNumberValue($investmentChangeNoticeDel);
				}
			}
		}
		/////////////// Financial Wellness Essentials /////////////
		elseif($additionalServicesValue->asc_additionalservicename=="Financial Wellness Essentials"){
			$FWEbasepricerate	=	$fields['PB'] * $additionalServicesValue->asc_priceperrate;
			if(!empty($additionalServicesValue->asc_minimum)){
				$FWE_min	=	$additionalServicesValue->asc_minimum;
			}
			if(!empty($additionalServicesValue->asc_maximum)){
				$FWE_max	=	$additionalServicesValue->asc_maximum;
			}
			if(!empty($FWE_min) && !empty($FWE_max)){
				if($FWEbasepricerate>$FWE_min && $FWE_max>$FWEbasepricerate ){
					$cal_FWE	=	$FWEbasepricerate;
				}
				else{
					if($FWE_max<$FWEbasepricerate){
						$cal_FWE	=	$FWE_max;
					}
					if($FWE_min>$FWEbasepricerate){
						$cal_FWE	=	$FWE_min;
					}
				}
			}
			else{
				if(!empty($FWE_min)){
					if($FWE_min > $FWEbasepricerate){
						$cal_FWE	= 	$FWE_min;
					}
					else{
						$cal_FWE	=	$FWEbasepricerate;
					}
				}
				elseif(!empty($FWE_max)){
					if($FWE_max > $FWEbasepricerate){
						$cal_FWE	= 	$FWEbasepricerate;
					}
					else{
						$cal_FWE	=	$FWE_max;
					}
				}
				else{
					$cal_FWE	=	$FWEbasepricerate;
				}
			}
		}
		/////////////// Financial Wellness Plus /////////////
		elseif($additionalServicesValue->asc_additionalservicename=="Financial Wellness Plus"){
			$FWPbasepricerate	=	$fields['PB'] * $additionalServicesValue->asc_priceperrate;
			if(!empty($additionalServicesValue->asc_minimum)){
				$FWP_min	=	$additionalServicesValue->asc_minimum;
			}
			if(!empty($additionalServicesValue->asc_maximum)){
				$FWP_max	=	$additionalServicesValue->asc_maximum;
			}
			if(!empty($FWP_min) && !empty($FWP_max)){
				if($FWPbasepricerate>$FWP_min && $FWP_max>$FWPbasepricerate ){
					$cal_FWP	=	$FWPbasepricerate;
				}
				else{
					if($FWP_max<$FWPbasepricerate){
						$cal_FWP	=	$FWP_max;
					}
					if($FWP_min>$FWPbasepricerate){
						$cal_FWP	=	$FWP_min;
					}
				}
			}
			else{
				if(!empty($FWP_min)){
					if($FWP_min > $FWPbasepricerate){
						$cal_FWP	= 	$FWP_min;
					}
					else{
						$cal_FWP	=	$FWPbasepricerate;
					}
				}
				elseif(!empty($FWP_max)){
					if($FWP_max > $FWPbasepricerate){
						$cal_FWP	= 	$FWPbasepricerate;
					}
					else{
						$cal_FWP	=	$FWP_max;
					}
				}
				else{
					$cal_FWP	=	$FWPbasepricerate;
				}
			}
		}
		/////////////// Payroll Integration /////////////
		elseif($additionalServicesValue->asc_additionalservicename=="Payroll integration"){
			$payrollIntbasepricerate	=	$fields['PB'] * $additionalServicesValue->asc_priceperrate;
			if(!empty($additionalServicesValue->asc_minimum)){
				$payrollInt_min	=	$additionalServicesValue->asc_minimum;
			}
			if(!empty($additionalServicesValue->asc_maximum)){
				$payrollInt_max	=	$additionalServicesValue->asc_maximum;
			}
			if(!empty($payrollInt_min) && !empty($payrollInt_max)){
				if($payrollIntbasepricerate>$payrollInt_min && $payrollInt_max>$payrollIntbasepricerate ){
					$cal_payrollInt	=	$payrollIntbasepricerate;
				}
				else{
					if($payrollInt_max<$payrollIntbasepricerate){
						$cal_payrollInt	=	$payrollInt_max;
					}
					if($payrollInt_min>$payrollIntbasepricerate){
						$cal_payrollInt	=	$payrollInt_min;
					}
				}
			}
			else{
				if(!empty($payrollInt_min)){
					if($payrollInt_min > $payrollIntbasepricerate){
						$cal_payrollInt	= 	$payrollInt_min;
					}
					else{
						$cal_payrollInt	=	$payrollIntbasepricerate;
					}
				}
				elseif(!empty($payrollInt_max)){
					if($payrollInt_max > $payrollIntbasepricerate){
						$cal_payrollInt	= 	$payrollIntbasepricerate;
					}
					else{
						$cal_payrollInt	=	$payrollInt_max;
					}
				}
				else{
					$cal_payrollInt	=	$payrollIntbasepricerate;
				}
			}
		}
	}
}
// Section 2: PSG Proposal Entity Data (This data will pull once the tool opened after initially submited)
if ( ! empty( $crmData->psgProposalsData ) ) {
    //pr($crmData->psgProposalsData);
    $proposalEntity                              = $crmData->psgProposalsData->value[0];
    $fields['ReferencedProposal']                = $proposalEntity->{'asc_isreferencedproposal@OData.Community.Display.V1.FormattedValue'};
    $fields['VanguardRep']                       = $proposalEntity->{'asc_vanguardrep@OData.Community.Display.V1.FormattedValue'};
    $fields['EstAdminFeeCredit']                 = $proposalEntity->asc_estadminfeecredit;
    $fields['InvestmentFiduciaryServicesAnnual'] = $proposalEntity->asc_invmtfiduciarysvrcyrlyfee;
    $fields['estAdditionalFeesDue']              = $proposalEntity->asc_estadditionalfeesdue;
    $fields['AdvisorBPSTier2']                   = $proposalEntity->asc_advisorbpstier2;
    $fields['WhoReferencedProposal']             = $proposalEntity->asc_whoreferencedtheproposal;
    $fields['VanguardOpportunityID']             = $proposalEntity->asc_vanguardopportunityid;
    //$fields['AdvisorBSP']                         = $proposalEntity->asc_advisorbps;
    //$fields['AF']                                 = $proposalEntity->asc_advisorbps;
    $fields['EstAnnualInvestmentAdvisorFee'] = $proposalEntity->asc_estanninvestmentadvsfee;
    $fields['MesirowRate']                   = $proposalEntity->asc_mesirowrate;
    $fields['VanguardPropsoalID']            = $proposalEntity->asc_vanguardproposalid;
    $fields['asc_clientstate']               = $proposalEntity->asc_clientstate;
    $fields['asc_setupfeewaivedcode']        = $proposalEntity->asc_setupfeewaivedcode;
    $fields['asc_proposalduedate']           = $proposalEntity->asc_proposalduedate;
    $fields['ClientCity']                    = $proposalEntity->asc_clientcity;
    $fields['asc_numproposalsrequested']     = (int) $proposalEntity->asc_numproposalsrequested;
}

$pricingSchedules = [];
if ( ! empty( $crmData->psgPricingSchedules ) ) {
    foreach ( $crmData->psgPricingSchedules as $pricingScheduleName => $pricingScheduleData ) {
        $pricingSchedule                                       = $pricingScheduleData->value[0];
        $pricingSchedules[ $pricingScheduleName ]["rangetype"] = $pricingSchedule->{'asc_schedulerangetype@OData.Community.Display.V1.FormattedValue'};
        $pricingSchedules[ $pricingScheduleName ]["basefee"]   = $pricingSchedule->{'asc_basefee@OData.Community.Display.V1.FormattedValue'};
        $pricingSchedules[ $pricingScheduleName ]["name"]      = $pricingSchedule->{'asc_name'};
        if ( ! empty( $pricingSchedule->asc_psgpricingschedule_asc_psgpricingscheduleitem_pricingschedule ) ) {
            foreach ( $pricingSchedule->asc_psgpricingschedule_asc_psgpricingscheduleitem_pricingschedule as $key => $pricingScheduleItem ) {
                if ( $pricingSchedules[ $pricingScheduleName ]["rangetype"] == "PPT" ) {
                    $pricingScheduleItemData['min'] = $pricingScheduleItem->asc_minpptvalue;
                    $pricingScheduleItemData['max'] = $pricingScheduleItem->asc_maxpptvalue;
                    if ( $key == 0 ) {
                        $pricingScheduleItemData['rate'] = '$' . number_format( getNumberValue( $pricingScheduleItem->{'asc_rate@OData.Community.Display.V1.FormattedValue'} ) + getNumberValue( $pricingSchedules[ $pricingScheduleName ]["basefee"] ), 2 );
                    } else {
                        $pricingScheduleItemData['rate'] = $pricingScheduleItem->{'asc_rate@OData.Community.Display.V1.FormattedValue'};
                    }
                    
                    $pricingScheduleItemData['base_min'] = $pricingScheduleItem->{'asc_minassetvalue_base@OData.Community.Display.V1.FormattedValue'};
                    $pricingScheduleItemData['base_max'] = $pricingScheduleItem->{'asc_maxassetvalue_base@OData.Community.Display.V1.FormattedValue'};
                    //$pricingScheduleItemData['base_rate']=$pricingScheduleItem->{'asc_rate_base@OData.Community.Display.V1.FormattedValue'};
                    $pricingSchedules[ $pricingScheduleName ]["items"][] = $pricingScheduleItemData;
                } else {
                    $pricingScheduleItemData['min'] = $pricingScheduleItem->{'asc_minassetvalue@OData.Community.Display.V1.FormattedValue'};
                    $pricingScheduleItemData['max'] = $pricingScheduleItem->{'asc_maxassetvalue@OData.Community.Display.V1.FormattedValue'};
                    if ( $key == 0 ) {
                        $pricingScheduleItemData['rate'] = '$' . number_format( getNumberValue( $pricingScheduleItem->{'asc_rate@OData.Community.Display.V1.FormattedValue'} ) + getNumberValue( $pricingSchedules[ $pricingScheduleName ]["basefee"] ), 2 );
                    } else {
                        $pricingScheduleItemData['rate'] = $pricingScheduleItem->{'asc_rate@OData.Community.Display.V1.FormattedValue'};
                    }
                    $pricingScheduleItemData['base_min'] = $pricingScheduleItem->{'asc_minassetvalue_base@OData.Community.Display.V1.FormattedValue'};
                    $pricingScheduleItemData['base_max'] = $pricingScheduleItem->{'asc_maxassetvalue_base@OData.Community.Display.V1.FormattedValue'};
                    //$pricingScheduleItemData['base_rate']=$pricingScheduleItem->{'asc_rate_base@OData.Community.Display.V1.FormattedValue'};
                    $pricingSchedules[ $pricingScheduleName ]["items"][] = $pricingScheduleItemData;
                }
            }
        }
    }
}

# Prposal Entity Hidden fields
$asc_plantypecode = $planType;

/** For Azure ID 13321: Special Characters taken care of in the Company and Plan name. */
$asc_name = htmlentities( $fields['PlanName'] ); // Opportunity name
$asc_ParentOpportunity = $opportunityId;
$isProductFound        = 0;
if ( ! empty( $crmData->ascPsgProduct ) ) {
    if ( ! empty( $crmData->ascPsgProduct->value ) ) {
        $isProductFound = 1;
        $productInfo    = $crmData->ascPsgProduct->value[0];
        $asc_Productid  = $productInfo->asc_psg_productid;
    }
}
$asc_businessunitname = "Retirement Sales"; // The attribute has to default to “Retirement Sales”
$asc_nextfollowupdate = $nextfollowupdate;
?>
<script>
    require(["dojo/number"]);

    function round_format(val, dec) {
        var abc;
        require(["dojo/number"], function (number) {
            abc = number.format(val, {
                places: dec,
                locale: 'en-us'
            });
        });
        return abc;
    }

    function NoDecimal(val) {
        var abc;
        if (typeof val === "undefined" || val === null || val === "" || val === " ") {
            val = 0;
        }
        require(["dojo/number"], function (number) {
            abc = number.format(val, {
                places: 0,
                locale: 'en-us'
            });
        });
        if (val < 0) {
            var st = Math.abs(abc);
            abc = "($" + st + ")";
        } else {
            abc = "$" + abc;
        }
        return abc;
    }

    var countBlocks = <?= count( $blocks ); ?>;
    if (countBlocks > 0) {
        formOnLoads();
    }
    ///// Calculate Enhanced Administrative Services Experience Fee (Optional) //////
    var NumberParticipants = Number($("input[name='PB']").val().replace(/[^0-9\.]+/g, ""));
    ///////////// Sales Sprint Yes No/////
    var SalesSprint = getAjaxrequest('Sales Sprint');
    var ShowSalessprint = SalesSprint[0]["custom1"];
    if ($("input[name='ShowSalessprint']").length > 0) {
        $("input[name='ShowSalessprint']").val(ShowSalessprint);
    }
    ////////////////////////// Create TD For Sub TA PP AMOUNT /////////////////////
    var createTD = '<td><input type="text" name="subTAPPAmt-#p#-fdEntry[]" value="" onblur="num_format(this);"></td>';
    $("tr#fdEntryTr").find("input").each(function () {
        var inputname = $(this).attr("name");
        if (inputname == "Minimum-#p#-fdEntry[]") {
            $(this).closest("td").before(createTD);
        }
    });
    var EditFundsFields = ["fund_name-#p#-fdEntry[]", "net_ratio-#p#-fdEntry[]", "12b_1C-#p#-fdEntry[]", "subTARate-#p#-fdEntry[]", "Minimum-#p#-fdEntry[]"];
    var dollarFields = ["Minimum-#p#-fdEntry[]"]
    $("tr#fdEntryTr").find("input").each(function () {
        var inputname = $(this).attr("name");
        if (EditFundsFields.indexOf(inputname) >= 0) {
            $(this).removeAttr("readonly");
            $(this).attr("onchange", "updateCurrentTicker(this);");
            $(this).closest("td").removeClass("hide");

        }
        if (dollarFields.indexOf(inputname) >= 0) {
            $(this).attr("onblur", "num_format(this);");

        }
    });
    ///////////////////// When Duplicating Ticker //////
    var table;
    var tableTR;
    var tableTickerObj;
    var myDialog;
    require(["dijit/Dialog", "dojo/domReady!"], function (Dialog) {
        myDialog = new Dialog({
            title: "Delete",
            style: "width: 300px"
        });
    });

    var trObj = {};

    function checkDuplicate(obj) {
        $("div.main_pink span.errorMsg").html("");
        $("div.showerrormsg").hide();
        var countTicker = 0;
        trObj = obj;
        var CurrentTicker = $(obj).closest("tr").find("input[name='ticker-#p#-fdEntry[]']").val();
        $("table#FundEntry-tab-table tbody").find("tr.templates").each(function () {
            var RowTicker = $(this).find("input[name='ticker-#p#-fdEntry[]']").val();
            if (RowTicker == CurrentTicker) {
                countTicker++;
            }
        });
        if (countTicker > 1) {
            tableTR = $(obj).closest("tr");
            var content = "<div style='font-weight:bold;text-align:center;font-size: 14px;'>This fund already exists. Do you still want to add?</div>";
            content += "<div><button style='margin-top:15px;float:right;' onclick = 'CancelDuplicate();' type='button' dojoType='dijit.form.Button' value='Cancel'>No</button></div>",
                content += "<div><button style='margin-top:15px;float:right;' onclick = 'deletethis();'  type='button' dojoType='dijit.form.Button' value='Delete'>Yes</button></div>",
                myDialog.set("content", content);
            myDialog.show();
        } else {
            $(obj).closest("tr").find("input[name='tickerHidden-#p#-fdEntry[]']").val(CurrentTicker);
            createTicker(obj);
        }
    }

    function updateChangeComments(obj) {
        var tr_index = $(obj).closest("tr").index();
        var Comments_Display = $(obj).closest("tr").find("textarea[name='Comments-#p#-fdEntry[]']").val();
        //$("table#FundList-tab-table tbody tr:eq("+tr_index+")").find("input[name='Comments-#p#-fundListTable[]']").val(Comments_Display);
        $("table#display-tab-table tbody tr:eq(" + tr_index + ")").find("input[name='comments-#p#-displayTable[]']").val(Comments_Display);
        //$(obj).closest("tr").find("input[name='CommentsChange-#p#-fdEntry[]']").val("yes");
    }

    /////////// Check If any Negative Percentage Value Entered //////
    function checkIfNegative(value) {
        var newValue;
        if (value.indexOf("-") >= 0) {
            newValue = -Math.abs(Number(value.replace(/[^0-9\.]+/g, "")));
        } else {
            newValue = Number(value.replace(/[^0-9\.]+/g, ""));
        }
        return newValue;
    }

    // Post Ajax request
    function searchTicker(obj) {
        var TickerInput = $(obj).val();
        if (TickerInput.length > 0) {
            var gProduct = [];
            dojo.xhrPost({
                url: '/dynamicscrm/search-fund-by-ticker/ticker/' + TickerInput,
                sync: true,
                content: {
                    'csrfToken': '<?= Zend_Registry::get( 'session' )->csrfToken ?>'
                },
                load: function (data) {
                    if (data) {
                        gProduct = data;
                    }
                }
            });
            return JSON.parse(gProduct);
        }
    }

    function searchTickerInDefaultList(obj) {
        var TickerInput = $(obj).val();
        var AjaxUrl = encodeURIComponent('<?= $category; ?>');
        var getProduct1 = getAjaxrequest(AjaxUrl);
        var defaultFundList = "";
        var selectedFundList = $("select[name='FLN']").val();
        var gProduct = [];
        if (selectedFundList != "Custom" && selectedFundList.trim() != "") {
            var productDescription = selectedFundList;
            defaultFundList = productDescription.split("\n");
            if (TickerInput.length > 0 && defaultFundList.length > 0) {
                dojo.xhrPost({
                    url: '/dynamicscrm/search-ticker-in-fundlist/ticker/' + TickerInput,
                    sync: true,
                    content: {
                        'csrfToken': '<?= Zend_Registry::get( 'session' )->csrfToken ?>',
                        'defaultFundList': JSON.stringify(defaultFundList)
                    },
                    load: function (data) {
                        if (data) {
                            gProduct = data;
                        }
                    }
                });
                return JSON.parse(gProduct);
            }
        } else {
            dojo.xhrPost({
                url: '/dynamicscrm/search-ticker-in-fundlist/ticker/' + TickerInput,
                sync: true,
                content: {
                    'csrfToken': '<?= Zend_Registry::get( 'session' )->csrfToken ?>'
                },
                load: function (data) {
                    if (data) {
                        gProduct = data;
                    }
                }
            });
        }
        return JSON.parse(gProduct);
    }

    function createTicker(obj) {
        $("div.main_pink span.errorMsg").html("");
        $("div.showerrormsg").hide();
        var TickerInput = $(obj).val();
        if (TickerInput.length > 0) {
            // Search tickers in default fund list
            var tickerObj = searchTickerInDefaultList(obj);
            tableTickerObj = tickerObj;
            tableTR = $(obj).closest("tr");
            var TickerValue = $(tableTR).find("input[name='ticker-#p#-fdEntry[]']").val();
            if (tickerObj.asc_fund_by_ticker) { // Ticker found
                if (tickerObj.isfundlistfound == 1) { // check if ticker found in default fund list
                    populateTicker(tickerObj, obj); // Add and populate ticker values
                    //check if Corporate Action Type Closed to All
                    var tickerObjvalue = tickerObj.asc_fund_by_ticker;
                    var corporateActionType = "";
                    if (tickerObjvalue["asc_corporateactiontype@OData.Community.Display.V1.FormattedValue"]) {
                        corporateActionType = tickerObjvalue["asc_corporateactiontype@OData.Community.Display.V1.FormattedValue"];
                    }
                    if (corporateActionType == "Closed to New" || corporateActionType == "Closed to All") {
                        var content = "<div style='font-weight:bold;text-align:center;font-size: 14px;'>This fund " + TickerValue + " is Closed to New/Closed to ALL. Do you still want to add?</div>";
                        content += "<div><button style='margin-top:15px;float:right;' onclick = 'deleteTickerRow();' type='button' dojoType='dijit.form.Button' value='Cancel'>No</button></div>",
                            content += "<div><button style='margin-top:15px;float:right;' onclick = 'hideTickerRow();'  type='button' dojoType='dijit.form.Button' value='Delete'>Yes</button></div>",
                            myDialog.set("content", content);
                        myDialog.show();
                        return false;
                    }

                } else { // Fund Ticker found but not in default fund list
                    tableTR = $(obj).closest("tr");
                    populateTicker(tickerObj, obj); //Populate CRM
                    var selectedFundList = $("select[name='FLN']").val();
                    if (selectedFundList != "Custom" && selectedFundList.trim() != "") {
                        var content = "<div style='font-weight:bold;text-align:center;font-size: 14px;'>This fund does not exist in Default Fund list. Do you still want to add?</div>";
                        content += "<div><button style='margin-top:15px;float:right;' onclick = 'deleteRow();' type='button' dojoType='dijit.form.Button' value='Cancel'>No</button></div>",
                            content += "<div><button style='margin-top:15px;float:right;' onclick = 'deletethis();'  type='button' dojoType='dijit.form.Button' value='Delete'>Yes</button></div>",
                            myDialog.set("content", content);
                        myDialog.show();
                    } else {
                        var tickerObjvalue = tickerObj.asc_fund_by_ticker;
                        var corporateActionType = "";
                        if (tickerObjvalue["asc_corporateactiontype@OData.Community.Display.V1.FormattedValue"]) {
                            corporateActionType = tickerObjvalue["asc_corporateactiontype@OData.Community.Display.V1.FormattedValue"];
                        }
                        if (corporateActionType == "Closed to New" || corporateActionType == "Closed to All") {
                            var content = "<div style='font-weight:bold;text-align:center;font-size: 14px;'>This fund " + TickerValue + " is Closed to New/Closed to ALL. Do you still want to add?</div>";
                            content += "<div><button style='margin-top:15px;float:right;' onclick = 'deleteTickerRow();' type='button' dojoType='dijit.form.Button' value='Cancel'>No</button></div>",
                                content += "<div><button style='margin-top:15px;float:right;' onclick = 'hideTickerRow();'  type='button' dojoType='dijit.form.Button' value='Delete'>Yes</button></div>",
                                myDialog.set("content", content);
                            myDialog.show();
                            return false;
                        }
                    }
                    return false;
                }
            } else {
                tableTR = $(obj).closest("tr");
                var content = "<div style='font-weight:bold;text-align:center;font-size: 14px;'>Fund ticker could not be found in CRM, do you want to add this fund?</div>";
                content += "<div><button style='margin-top:15px;float:right;' onclick = 'deleteRow();' type='button' dojoType='dijit.form.Button' value='Cancel'>No</button></div>",
                    content += "<div><button style='margin-top:15px;float:right;' onclick = 'deletethis();'  type='button' dojoType='dijit.form.Button' value='Delete'>Yes</button></div>",
                    myDialog.set("content", content);
                myDialog.show();
                return false;
            }
        }
        updateTickerFD();
    }

    //Multile
    var multipleCount = 0;

    function ShowInput(obj) {
        ////////////// Azure Id 10594 //////////////////
        $("#myForm").find("input[name='asc_defaultfundlist']").val(obj.value);
        ////////////// Azure Id 10594 //////////////////
        if (obj.value == 'Custom' || obj.value == '') {
            changeFundList();
        } else {
            $(".overlay-loader").css("display", "block");
            //$("div.CustomFundList").hide();
            changeFundList();
        }
        multipleCount = 0;
        $("#dialogSubmit").parent().parent().show();
    }

    function Add_Multiple_Ticker_Tool() {
        if ($("#myForm").find("textarea[name='multiple_tickers']").val().trim() != "") {
            $(".overlay-loader").css("display", "block");
            setTimeout(function () {
                var multiple_tickers = $("#myForm").find("textarea[name='multiple_tickers']").val();
                if (multiple_tickers) {
                    delete_blank_row("FundList-tab-table", "templates");
                    delete_blank_row("FundEntry-tab-table", "templates");
                    ///////////// For Multiple Ticker With Assets Azure Id 9389 Starts //////
                    var CurrencyExists = false;
                    if (multiple_tickers.indexOf("\n") >= 0) {
                        var multiple_tickersArr = multiple_tickers.split(/\n/);
                        for (var i = 0; i < multiple_tickersArr.length; i++) {
                            multiple_tickersArr[i] = multiple_tickersArr[i].trim();
                            if (multiple_tickersArr[i]) {
                                if (multiple_tickersArr[i].indexOf("\t") > 0) {
                                    CurrencyExists = true;
                                    var TickerValueWithAsset = (multiple_tickersArr[i].replace(/\t\t+/g, '\t')).split("\t");
                                    var TickerValueOnly = TickerValueWithAsset[0].trim();
                                    var TickerAssetOnly = Number((TickerValueWithAsset[1].trim()).replace(/[^0-9\.]+/g, ""));
                                    var trCloneFDentry = $('#displayTableClone tr#fdEntryTr').clone();
                                    $(trCloneFDentry).find('input[name="ticker-#p#-fdEntry[]"]').val(TickerValueOnly);
                                    $(trCloneFDentry).find('input[name="assestPerFund-#p#-fdEntry[]"]').val(format(TickerAssetOnly));
                                    checkDuplicateMultiple($(trCloneFDentry).find('input[name="ticker-#p#-fdEntry[]"]'));
                                    $("#myForm").find("#FundEntry-tab-table tbody").append(trCloneFDentry);
                                } else if (multiple_tickersArr[i].indexOf(" ") > 0) {
                                    CurrencyExists = true;
                                    var TickerValueWithAsset = (multiple_tickersArr[i].replace(/\s\s+/g, ' ')).split(" ");
                                    var TickerValueOnly = TickerValueWithAsset[0].trim();
                                    var TickerAssetOnly = Number(TickerValueWithAsset[1].trim().replace(/[^0-9\.]+/g, ""));
                                    var trCloneFDentry = $('#displayTableClone tr#fdEntryTr').clone();
                                    $(trCloneFDentry).find('input[name="ticker-#p#-fdEntry[]"]').val(TickerValueOnly);
                                    $(trCloneFDentry).find('input[name="assestPerFund-#p#-fdEntry[]"]').val(format(TickerAssetOnly));
                                    checkDuplicateMultiple($(trCloneFDentry).find('input[name="ticker-#p#-fdEntry[]"]'));
                                    $("#myForm").find("#FundEntry-tab-table tbody").append(trCloneFDentry);
                                } else {
                                    var trCloneFDentry = $('#displayTableClone tr#fdEntryTr').clone();
                                    $(trCloneFDentry).find('input[name="ticker-#p#-fdEntry[]"]').val(multiple_tickersArr[i]);
                                    checkDuplicateMultiple($(trCloneFDentry).find('input[name="ticker-#p#-fdEntry[]"]'));
                                    $("#myForm").find("#FundEntry-tab-table tbody").append(trCloneFDentry);
                                }
                            }
                        }
                    } else if (multiple_tickers.indexOf(" ") > 0) {
                        CurrencyExists = true;
                        var TickerValueWithAsset = (multiple_tickers.replace(/\s\s+/g, ' ')).split(" ");
                        var TickerValueOnly = TickerValueWithAsset[0].trim();
                        var TickerAssetOnly = Number(TickerValueWithAsset[1].trim().replace(/[^0-9\.]+/g, ""));
                        var trCloneFDentry = $('#displayTableClone tr#fdEntryTr').clone();
                        $(trCloneFDentry).find('input[name="ticker-#p#-fdEntry[]"]').val(TickerValueOnly);
                        $(trCloneFDentry).find('input[name="assestPerFund-#p#-fdEntry[]"]').val(format(TickerAssetOnly));
                        checkDuplicateMultiple($(trCloneFDentry).find('input[name="ticker-#p#-fdEntry[]"]'));
                        $("#myForm").find("#FundEntry-tab-table tbody").append(trCloneFDentry);
                    } else if (multiple_tickers.indexOf(",") >= 0) {
                        var multiple_tickersArr = multiple_tickers.split(",");
                        for (var i = 0; i < multiple_tickersArr.length; i++) {
                            var trCloneFDentry = $('#displayTableClone tr#fdEntryTr').clone();
                            $(trCloneFDentry).find('input[name="ticker-#p#-fdEntry[]"]').val(multiple_tickersArr[i]);
                            checkDuplicateMultiple($(trCloneFDentry).find('input[name="ticker-#p#-fdEntry[]"]'));
                            $("#myForm").find("#FundEntry-tab-table tbody").append(trCloneFDentry);
                        }
                    } else {
                        var trCloneFDentry = $('#displayTableClone tr#fdEntryTr').clone();
                        $(trCloneFDentry).find('input[name="ticker-#p#-fdEntry[]"]').val(multiple_tickers);
                        checkDuplicateMultiple($(trCloneFDentry).find('input[name="ticker-#p#-fdEntry[]"]'));
                        $("#myForm").find("#FundEntry-tab-table tbody").append(trCloneFDentry);
                    }
                    if (!CurrencyExists) {
                        equalDistributeAssets();
                    } else {
                        updateTickerFD();
                    }
                }
                ///////////// For Multiple Ticker With Assets Azure Id 9389 Ends //////
                $("textarea[name=multiple_tickers]").val("");
                $(".overlay-loader").css("display", "none");
                $.toast({
                    heading: '',
                    text: 'Congrats. Your fund tickers have been added to the fund table. Please review the funds added and accept or decline any tickers that are duplicates or could not be found in CRM',
                    icon: 'success',
                    hideAfter: 10000,
                    loader: false,
                    position: {
                        right: 20,
                        bottom: 20
                    }
                });
            }, 100);
        }
    }

    function checkDuplicateMultiple(obj) {
        $("div.main_pink span.errorMsg").html("");
        $("div.showerrormsg").hide();
        var countTicker = 0;
        trObj = obj;
        var CurrentTicker = $(obj).closest("tr").find("input[name='ticker-#p#-fdEntry[]']").val();
        $("table#FundEntry-tab-table tbody").find("tr.templates").each(function () {
            var RowTicker = $(this).find("input[name='ticker-#p#-fdEntry[]']").val();
            if (RowTicker == CurrentTicker) {
                countTicker++;
            }
        });
        if (countTicker > 0) {
            tableTR = $(obj).closest("tr");
            var content = "<div><div style='font-weight: bold;text-align: left;font-size: 12px;margin-top: 12px;float:left;'>This fund already exists. Do you still want to add?</div>";
            content += "<button style='margin-top:15px;float: right;min-width: 65px;min-height: 20px;padding: 9px;' onclick = 'CancelDuplicateMultiple(this);' type='button' class='form-button main_green tickerBtnGreen' dojoType='dijit.form.Button' value='Cancel'>No</button>",
                content += "<button style='margin-top:15px;float: left;min-width: 65px;min-height: 20px;padding: 9px;' onclick = 'deletethisMultiple(this, 1);' class='form-button main_green tickerBtnGreen' type='button' dojoType='dijit.form.Button' value='Delete'>Yes</button></div>",
                $(obj).after(content);
            $(obj).closest("tr").find("input[name='tickerHidden-#p#-fdEntry[]']").val(CurrentTicker);
            createTickerMultiple(obj);
            multipleCount++;
            setSubmit(multipleCount);
            return false;
        } else {
            $(obj).closest("tr").find("input[name='tickerHidden-#p#-fdEntry[]']").val(CurrentTicker);
            createTickerMultiple(obj);
        }
    }

    function createTickerMultiple(obj, d) {
        $("div.main_pink span.errorMsg").html("");
        $("div.showerrormsg").hide();
        var TickerInput = $(obj).val();
        if (TickerInput.length > 0) {
            // Search tickers in default fund list
            var tickerObj = searchTickerInDefaultList(obj);
            tableTickerObj = tickerObj;
            tableTR = $(obj).closest("tr");
            var TickerValue = $(tableTR).find("input[name='ticker-#p#-fdEntry[]']").val();
            if (tickerObj.asc_fund_by_ticker) { // Ticker found
                if (tickerObj.isfundlistfound == 1) { // check if ticker found in default fund list
                    populateTicker(tickerObj, obj); // Add and populate ticker values
                    //check if Corporate Action Type Closed to All
                    var tickerObjvalue = tickerObj.asc_fund_by_ticker;
                    var corporateActionType = "";
                    if (tickerObjvalue["asc_corporateactiontype@OData.Community.Display.V1.FormattedValue"]) {
                        corporateActionType = tickerObjvalue["asc_corporateactiontype@OData.Community.Display.V1.FormattedValue"];
                    }
                    if (corporateActionType == "Closed to New" || corporateActionType == "Closed to All") {
                        if (d != 1) {
                            var content = "<div><div style='font-weight:bold;text-align:center;font-size: 14px;'>This fund " + TickerValue + " is Closed to New/Closed to ALL. Do you still want to add?</div>";
                            content += "<button style='margin-top:15px;float: right;min-width: 65px;min-height: 20px;padding: 9px;' class='form-button main_green' onclick = 'deleteTickerRowMultiple(this);' type='button' dojoType='dijit.form.Button' value='Cancel'>No</button>",
                                content += "<button style='margin-top:15px;float: left;min-width: 65px;min-height: 20px;padding: 9px;' class='form-button main_green' onclick = 'hideTickerRowMultiple(this);'  type='button' dojoType='dijit.form.Button' value='Delete'>Yes</button></div>",
                                $(obj).after(content);
                            multipleCount++;
                            setSubmit(multipleCount);
                            return false;
                        }
                    }
                } else { // Fund Ticker found but not in default fund list
                    tableTR = $(obj).closest("tr");
                    populateTicker(tickerObj, obj); //Populate CRM
                    if (d != 1) {
                        var selectedFundList = $("select[name='FLN']").val();
                        if (selectedFundList != "Custom" && selectedFundList.trim() != "") {
                            if ($(obj).closest("td").find("div").length == 0) {
                                var content = "<div><div style='font-weight: bold;text-align: left;font-size: 12px;margin-top: 12px;'>This fund does not exist in Default Fund list. Do you still want to add?</div>";
                                content += "<button style='margin-top:15px;float: right;min-width: 65px;min-height: 20px;padding: 9px;' class='form-button main_green tickerBtnGreen' onclick = 'deleteRowMultiple(this);' type='button' dojoType='dijit.form.Button' value='Cancel'>No</button>",
                                    content += "<button style='margin-top:15px;float: left;min-width: 65px;min-height: 20px;padding: 9px;' class='form-button main_green tickerBtnGreen' onclick = 'deletethisMultiple(this);'  type='button' dojoType='dijit.form.Button' value='Delete'>Yes</button></div>",
                                    $(obj).after(content);
                                //     myDialog.set("content", content);
                                // myDialog.show();
                                multipleCount++;
                                setSubmit(multipleCount);
                            }
                        } else {
                            var tickerObjvalue = tickerObj.asc_fund_by_ticker;
                            var corporateActionType = "";
                            if (tickerObjvalue["asc_corporateactiontype@OData.Community.Display.V1.FormattedValue"]) {
                                corporateActionType = tickerObjvalue["asc_corporateactiontype@OData.Community.Display.V1.FormattedValue"];
                            }
                            if (corporateActionType == "Closed to New" || corporateActionType == "Closed to All") {
                                if (d != 1) {
                                    var content = "<div><div style='font-weight:bold;text-align:center;font-size: 14px;'>This fund " + TickerValue + " is Closed to New/Closed to ALL. Do you still want to add?</div>";
                                    content += "<button style='margin-top:15px;float: right;min-width: 65px;min-height: 20px;padding: 9px;' class='form-button main_green' onclick = 'deleteTickerRowMultiple(this);' type='button' dojoType='dijit.form.Button' value='Cancel'>No</button>",
                                        content += "<button style='margin-top:15px;float: left;min-width: 65px;min-height: 20px;padding: 9px;' class='form-button main_green' onclick = 'hideTickerRowMultiple(this);'  type='button' dojoType='dijit.form.Button' value='Delete'>Yes</button></div>",
                                        $(obj).after(content);
                                    multipleCount++;
                                    setSubmit(multipleCount);
                                    return false;
                                }
                            }
                        }
                    }
                }
            } else {
                if (d != 1) {
                    tableTR = $(obj).closest("tr");
                    var content = "<div><div style='font-weight: bold;text-align: left;font-size: 12px;margin-top: 12px;'>Fund ticker could not be found in CRM, do you want to add this fund?</div>";
                    content += "<button class='form-button main_green tickerBtnGreen' style='margin-top:15px;float: right;min-width: 65px;min-height: 20px;padding: 9px;' onclick = 'deleteRowMultiple(this);' type='button' dojoType='dijit.form.Button' value='Cancel'>No</button>",
                        content += "<button class='form-button main_green tickerBtnGreen' style='margin-top:15px;float: left;min-width: 65px;min-height: 20px;padding: 9px;' onclick = 'deletethisMultiple(this);'  type='button' dojoType='dijit.form.Button' value='Delete'>Yes</button></div>",
                        $(obj).after(content);
                    multipleCount++;
                    setSubmit(multipleCount);
                    return false;
                }

            }
        }
        updateTickerFD();
    }

    function deleteTickerRowMultiple(element) {
        multipleCount--;
        setSubmit(multipleCount);
        if ($(".deleteThisElement").length > 0) {
            var tableTR = $(".deleteThisElement");
        } else {
            var tableTR = $(element).closest("tr");
        }
        $(tableTR).find("input[name='ticker-#p#-fdEntry[]']").val('');
        $(tableTR).find("input[name='fund_name-#p#-fdEntry[]']").val('');
        //Add code for empty all the input and textarea
        $(tableTR).find("input").val('');
        $(tableTR).find("textarea").val('');
        myDialog.hide();
        setTimeout(function () {
            if ($(tableTR).prev("tr").length > 0) {
                $(tableTR).prev("tr").find("input[name='tickerHidden-#p#-fdEntry']").focus();
            } else {
                $("textarea[name='multiple_tickers']").focus();
            }
            $(".overlay-loader").css("display", "none");
            $(tableTR).remove();
        }, 300);
        $(".overlay-loader").css("display", "block");
        updateTickerFD();
        return false;
    }

    $('body').on('click', '.dijitDialogCloseIcon', function () {
        if ($(".deleteThisElement").length > 0) {
            $(".deleteThisElement").removeClass("deleteThisElement");
        }
    });

    function deletethisMultiple(element, d) {
        ///// Get This function to fix All deleted when Click for Closed to New Azure Id 6892 //////
        multipleCount--;
        setSubmit(multipleCount);
        var tableTR = $(element).closest("tr");
        $(tableTR).addClass("deleteThisElement");
        var TickerValue = $(tableTR).find("input[name='ticker-#p#-fdEntry[]']").val();
        $(tableTR).find("input[name='tickerHidden-#p#-fdEntry[]']").val(TickerValue);
        $(element).parent().hide();
        var tickerObj = searchTickerCRM(TickerValue);
        var corporateActionType = "";
        if (tickerObj) {
            if (tickerObj.asc_fund_by_ticker) {
                var tickerObjvalue = tickerObj.asc_fund_by_ticker;
                if (tickerObjvalue["asc_corporateactiontype@OData.Community.Display.V1.FormattedValue"]) {
                    corporateActionType = tickerObjvalue["asc_corporateactiontype@OData.Community.Display.V1.FormattedValue"];
                }
                if (corporateActionType == "Closed to New" || corporateActionType == "Closed to All") {
                    var content = "<div><div style='font-weight:bold;text-align:center;font-size: 14px;'>This fund " + TickerValue + " is Closed to New/Closed to ALL. Do you still want to add?</div>";
                    content += "<button style='margin-top:15px;float:right;' onclick = 'deleteTickerRowMultiple();' type='button' dojoType='dijit.form.Button' value='Cancel'>No</button>",
                        content += "<button style='margin-top:15px;float:right;' onclick = 'addTickerRowMultiple(this);'  type='button' dojoType='dijit.form.Button' value='Delete'>Yes</button></div>",
                        $(element).after(content);
                    //createTickerMultiple($(element).closest("tr").find('input[name="ticker-#p#-fdEntry[]"]'), d);
                    myDialog.set("content", content);
                    myDialog.show();
                    multipleCount++;
                    setSubmit(multipleCount);
                    return false;
                } else {
                    if ($(tableTR).hasClass("deleteThisElement")) {
                        $(tableTR).removeClass("deleteThisElement");
                    }
                    if (d == 1) {
                        createTickerMultiple($(element).closest("tr").find('input[name="ticker-#p#-fdEntry[]"]'), d);
                    }
                    updateTickerFD();
                }
            } else {
                if (d == 1) {
                    createTickerMultiple($(element).closest("tr").find('input[name="ticker-#p#-fdEntry[]"]'), d);
                }
                updateTickerFD();
            }
        } else {
            if (d == 1) {
                createTickerMultiple($(element).closest("tr").find('input[name="ticker-#p#-fdEntry[]"]'), d);
            }
            updateTickerFD();
        }
    }

    function searchTickerCRM(TickerValue) {
        var defaultFundList = "";
        var selectedFundList = $("select[name='FLN']").val();
        var gProduct = [];
        var productDescription = selectedFundList;
        defaultFundList = productDescription.split("\n");
        if (TickerValue.length > 0 && defaultFundList.length > 0) {
            dojo.xhrPost({
                url: '/dynamicscrm/search-ticker-in-fundlist/ticker/' + TickerValue,
                sync: true,
                content: {
                    'csrfToken': '<?= Zend_Registry::get( 'session' )->csrfToken ?>',
                    'defaultFundList': JSON.stringify(defaultFundList)
                },
                load: function (data) {
                    if (data) {
                        gProduct = data;
                    }
                }
            });
        }
        return JSON.parse(gProduct);
    }

    function deleteRowMultiple(element) {
        multipleCount--;
        setSubmit(multipleCount);
        var tableTR = $(element).closest("tr");
        $(tableTR).find("input[name='ticker-#p#-fdEntry[]']").val('');
        $(tableTR).find("input[name='fund_name-#p#-fdEntry[]']").val('');
        //Add code for empty all the input and textarea
        $(tableTR).find("input").val('');
        $(tableTR).find("textarea").val('');
        // myDialog.hide();
        $(element).parent().hide();
        $(element).closest("tr").remove();
        ////////// To Fix the Display Table Remove Azure Id 9389 /////
        updateTickerFD();
        return false;
    }

    function CancelDuplicateMultiple(element) {
        multipleCount--;
        setSubmit(multipleCount);
        var tableTR = $(element).closest("tr");
        var HiddenTicker = $(tableTR).find("input[name='tickerHidden-#p#-fdEntry[]']").val();
        $(tableTR).find("input[name='ticker-#p#-fdEntry[]']").val(HiddenTicker);
        $(tableTR).find("input[name='assestPerFund-#p#-fdEntry[]']").val(''); //MS
        // myDialog.hide();
        $(element).parent().hide();
        $(element).closest("tr").remove();
        return false;
    }

    function addTickerRowMultiple(element) {
        if ($(".deleteThisElement").length > 0) {
            $(".deleteThisElement").removeClass("deleteThisElement");
        }
        multipleCount--;
        setSubmit(multipleCount);
        myDialog.hide();
        $(element).parent().hide();
        updateTickerFD();
    }

    function hideTickerRowMultiple(element) {
        multipleCount--;
        setSubmit(multipleCount);
        // myDialog.hide();
        $(element).parent().hide();
        updateTickerFD();
    }

    function setSubmit(multipleCount) {
        if (multipleCount >= 1) {
            $("#dialogSubmit").parent().parent().hide();
        } else {
            $("#dialogSubmit").parent().parent().show();
        }
    }

    function checkShowSubmit() {
        var error = 0;
        $("table#FundEntry-tab-table tbody tr.templates").each(function () {
            if ($(this).find("td:eq(1)").find("div").length > 0) {
                var div_display_attr = $(this).find("td:eq(1)").find("div").css("display");
                if (div_display_attr == "block") {
                    error++;
                }
            }
        });
        if (error == 0) {
            multipleCount = 0;
            $("#dialogSubmit").parent().parent().show();
        }
    }

    function delete_blank_row(table, trClass) {
        $("#myForm").find("#" + table + " tbody tr." + trClass).each(function () {
            var ticker = $(this).find('input[name^="ticker-#p#-"]').val();
            if (ticker.trim() == "") {
                $(this).remove();
            }
        });
    }

    //Multile
    function populateTicker(tickerObj, obj) {
        var TickerInput = $(obj).val();
        if (tickerObj.asc_fund_by_ticker) {
            var tickerObjvalue = tickerObj.asc_fund_by_ticker;
            var tr = $(obj).closest('tr');
            if (tickerObjvalue.asc_fund_legal_name) {
                $(tr).find('input[name="fund_name-#p#-fdEntry[]"]').val(tickerObjvalue.asc_fund_legal_name);
            }
            if (tickerObjvalue.asc_net_operating_expense_pct != null) {
                $(tr).find('input[name="net_ratio-#p#-fdEntry[]"]').val((tickerObjvalue.asc_net_operating_expense_pct).toFixed(5));
            }
            /*else{
				$(tr).find('input[name="net_ratio-#p#-fdEntry[]"]').val("0.0000");
			}*/
            if (tickerObjvalue.asc_actual_12b1 != null) {
                $(tr).find('input[name="12b_1C-#p#-fdEntry[]"]').val((tickerObjvalue.asc_actual_12b1).toFixed(4));
            }
            /*else{
				$(tr).find('input[name="12b_1C-#p#-fdEntry[]"]').val("0.0000");
			}*/
            if (tickerObjvalue.asc_subtarate != null) {
                $(tr).find('input[name="subTARate-#p#-fdEntry[]"]').val((tickerObjvalue.asc_subtarate).toFixed(4));
            }
            /*else{
				$(tr).find('input[name="subTARate-#p#-fdEntry[]"]').val("0.0000");
			}*/
            if (tickerObjvalue.asc_subtappamt != null) {
                $(tr).find('input[name="subTARateDollar-#p#-fdEntry[]"]').val(format(tickerObjvalue.asc_subtappamt));
            }
            if (tickerObjvalue.asc_subtappamt != null) {
                $(tr).find('input[name="subTAPPAmt-#p#-fdEntry[]"]').val(format(tickerObjvalue.asc_subtappamt));
            } else {
                $(tr).find('input[name="subTAPPAmt-#p#-fdEntry[]"]').val(format("0"));
            }
            if (tickerObjvalue.asc_fundminimuminvestment) {
                $(tr).find('input[name="Minimum-#p#-fdEntry[]"]').val(format(tickerObjvalue.asc_fundminimuminvestment));
            }

            var comments = "";
            if (tickerObjvalue.asc_comments) {
                comments = tickerObjvalue.asc_comments;
            }
            var corporateActionType = "";
            if (tickerObjvalue["asc_corporateactiontype@OData.Community.Display.V1.FormattedValue"] == "Closed to New" || tickerObjvalue["asc_corporateactiontype@OData.Community.Display.V1.FormattedValue"] == "Closed to All") {
                corporateActionType = tickerObjvalue["asc_corporateactiontype@OData.Community.Display.V1.FormattedValue"];
            }
            //cmnt = (corporateActionType) ? corporateActionType + " - " + comments : comments;
            if (corporateActionType != "" && comments != "") {
                cmnt = corporateActionType + " - " + comments;
            } else {
                if (comments != "") {
                    cmnt = comments;
                } else {
                    cmnt = corporateActionType;
                }
            }
            if (cmnt) {
                $(tr).find('textarea[name="Comments-#p#-fdEntry[]"]').val(cmnt);
            }
            var trCloneFundList = $('#displayTableClone tr#fundListTableTr').clone();
            $(trCloneFundList).find('input[name="ticker-#p#-fundListTable[]"]').val(TickerInput);
            if (tickerObjvalue.asc_fund_legal_name) {
                $(trCloneFundList).find('input[name="fund_name-#p#-fundListTable[]"]').val(tickerObjvalue.asc_fund_legal_name);
            }
            if (tickerObjvalue.asc_morningstar_category_name) {
                $(trCloneFundList).find('input[name="morningstart_category-#p#-fundListTable[]"]').val(tickerObjvalue.asc_morningstar_category_name);
            }
            if (tickerObjvalue.asc_fund_family_name) {
                $(trCloneFundList).find('input[name="family_name-#p#-fundListTable[]"]').val(tickerObjvalue.asc_fund_family_name);
            }
            if (tickerObjvalue.asc_fund_legal_name) {
                $(trCloneFundList).find('input[name="Share_Class_Type-#p#-fundListTable[]"]').val(tickerObjvalue.asc_share_class_type);
            }
            if (tickerObjvalue.asc_fundminimuminvestment) {
                $(trCloneFundList).find('input[name="Minimum-#p#-fundListTable[]"]').val(format(tickerObjvalue.asc_fundminimuminvestment));
            }

            if (cmnt) {
                $(trCloneFundList).find('input[name="Comments-#p#-fundListTable[]"]').val(cmnt);
            }

            if (tickerObjvalue.asc_morningstarrating) {
                $(trCloneFundList).find('input[name="Master_Rating-#p#-fundListTable[]"]').val(tickerObjvalue.asc_morningstarrating);
            }
            if (tickerObjvalue.asc_securitytype) {
                $(trCloneFundList).find('input[name="Security_Type-#p#-fundListTable[]"]').val(tickerObjvalue.asc_securitytype);
            }
            if (tickerObjvalue.asc_net_operating_expense_pct) {
                $(trCloneFundList).find('input[name="net_ratio-#p#-fundListTable[]"]').val((tickerObjvalue.asc_net_operating_expense_pct).toFixed(5));
            }
            if (tickerObjvalue.asc_actual_12b1) {
                $(trCloneFundList).find('input[name="12b_1C-#p#-fundListTable[]"]').val((tickerObjvalue.asc_actual_12b1).toFixed(4));
            }
            if (tickerObjvalue.asc_subtarate) {
                $(trCloneFundList).find('input[name="subTARate-#p#-fundListTable[]"]').val(tickerObjvalue.asc_subtarate);
            }
            if (tickerObjvalue.asc_subtappamt) {
                $(trCloneFundList).find('input[name="subTAPPRate-#p#-fundListTable[]"]').val(tickerObjvalue.asc_subtappamt);
            }
            if (tickerObjvalue.asc_subtappamt != null) {
                $(trCloneFundList).find('input[name="subTAPPAmt-#p#-fundListTable[]"]').val(format(tickerObjvalue.asc_subtappamt));
            } else {
                $(trCloneFundList).find('input[name="subTAPPAmt-#p#-fundListTable[]"]').val(format("0"));
            }
            $(trCloneFundList).removeAttr("id");
            $("#myForm").find("#FundList-tab-table tbody").append(trCloneFundList);
        } else {
            $(obj).val("");
            $(obj).css('border', '1px solid #b70909');
        }
    }


    function deleteTickerRow() {
        $(tableTR).find("input[name='ticker-#p#-fdEntry[]']").val('');
        $(tableTR).find("input[name='fund_name-#p#-fdEntry[]']").val('');
        //Add code for empty all the input and textarea
        $(tableTR).find("input").val('');
        $(tableTR).find("textarea").val('');
        myDialog.hide();
        updateTickerFD();
        return false;
    }

    function addTickerRow() {
        myDialog.hide();
        updateTickerFD();
    }

    function hideTickerRow() {
        myDialog.hide();
        updateTickerFD();
    }

    function CancelDuplicate() {
        var HiddenTicker = $(tableTR).find("input[name='tickerHidden-#p#-fdEntry[]']").val();
        $(tableTR).find("input[name='ticker-#p#-fdEntry[]']").val(HiddenTicker);
        myDialog.hide();
        return false;
    }

    function deletethis() {
        var TickerValue = $(tableTR).find("input[name='ticker-#p#-fdEntry[]']").val();
        $(tableTR).find("input[name='tickerHidden-#p#-fdEntry[]']").val(TickerValue);
        myDialog.hide();
        var tickerObj = tableTickerObj;
        var corporateActionType = "";
        if (tickerObj) {
            if (tickerObj.asc_fund_by_ticker) {
                var tickerObjvalue = tickerObj.asc_fund_by_ticker;
                if (tickerObjvalue["asc_corporateactiontype@OData.Community.Display.V1.FormattedValue"]) {
                    corporateActionType = tickerObjvalue["asc_corporateactiontype@OData.Community.Display.V1.FormattedValue"];
                }
                if (corporateActionType == "Closed to New" || corporateActionType == "Closed to All") {
                    var content = "<div style='font-weight:bold;text-align:center;font-size: 14px;'>This fund " + TickerValue + " is Closed to New/Closed to ALL. Do you still want to add?</div>";
                    content += "<div><button style='margin-top:15px;float:right;' onclick = 'deleteTickerRow();' type='button' dojoType='dijit.form.Button' value='Cancel'>No</button></div>",
                        content += "<div><button style='margin-top:15px;float:right;' onclick = 'addTickerRow();'  type='button' dojoType='dijit.form.Button' value='Delete'>Yes</button></div>",
                        myDialog.set("content", content);
                    myDialog.show();
                    createTicker(tableTR);
                } else {
                    updateTickerFD();
                }
            } else {
                updateTickerFD();
            }
        } else {
            updateTickerFD();
        }
    }

    function deleteRow() {
        $(tableTR).find("input[name='ticker-#p#-fdEntry[]']").val('');
        $(tableTR).find("input[name='fund_name-#p#-fdEntry[]']").val('');
        //Add code for empty all the input and textarea
        $(tableTR).find("input").val('');
        $(tableTR).find("textarea").val('');
        myDialog.hide();
        ////////// To Fix the Display Table Remove Azure Id 9389 /////
        updateTickerFD();
        return false;
    }

    ///////////////// Will Not allowed to enter string or character for IE ////
    function handleNumKey(e) {
        let newValue = e.target.value + e.key;
        if (
            // It is not a number nor a control key?
            isNaN(newValue) &&
            e.which != 8 && // backspace
            e.which != 17 && // ctrl
            newValue[0] != '-' || // minus
            // It is not a negative value?
            newValue[0] == '-' &&
            isNaN(newValue.slice(1)))
            e.preventDefault(); // Then don't write it!
    }

    // Check Default Fund Line up
    function fun_DefaultFundLineUp(DefaultFundLineup, fundListName) {
        var AllTickerGroup = [];
        for (var key in DefaultFundLineup) {
            if (DefaultFundLineup[key]['name'].toUpperCase() == fundListName.toUpperCase()) {
                if (DefaultFundLineup[key]['description']) {
                    var partsOfStr = DefaultFundLineup[key]['description'].split(',');
                    for (var i = 0; i < partsOfStr.length; i++) {
                        AllTickerGroup += partsOfStr[i].replace(/(\r\n|\n|\r)/gm, "") + ",";
                    }
                }
            }
        }
        return AllTickerGroup.slice(0, -1);
    }

    // Function with Ajax request for Ticker and Dynamic CRM
    function create_and_populateCRM_fields(AllTickerGroup, DefaultFundLineup) {
        $(".overlay-loader").css("display", "block");
        var FundData = [];
        var fundsNotFound = [];
        dojo.xhrPost({
            url: '/dynamicscrm/search-fund-by-ticker-group1',
            sync: false,
            content: {
                'tickerGroup': AllTickerGroup
            },
            load: function (data) {
                $(".overlay-loader").css("display", "none");
                if (data) {
                    FundData = JSON.parse(data);
                    if (FundData) {
                        for (var key2 in FundData.asc_funds_by_ticker_group) {
                            if (key2) {
                                if (FundData.asc_funds_by_ticker_group.hasOwnProperty(key2)) {
                                    if (DefaultFundLineup.indexOf(key2)) {
                                        var trCloneFundList = $('#displayTableClone tr#fundListTableTr').clone();
                                        var trCloneFDentry = $('#displayTableClone tr#fdEntryTr').clone();
                                        // Fund List
                                        var tickerObj = FundData.asc_funds_by_ticker_group[key2];
                                        if (tickerObj == "not_found") {
                                            if (key2) {
                                                fundsNotFound.push(key2);
                                            }

                                        } else {
                                            $(trCloneFDentry).find('input[name="ticker-#p#-fdEntry[]"]').val(key2);
                                            $(trCloneFDentry).find('input[name="tickerHidden-#p#-fdEntry[]"]').val(key2);
                                            if (tickerObj.asc_fund_legal_name) {
                                                $(trCloneFDentry).find('input[name="fund_name-#p#-fdEntry[]"]').val(tickerObj.asc_fund_legal_name);
                                                $(trCloneFDentry).find('input[name="fund_name-#p#-fdEntry[]"]').removeClass("hide");
                                            }

                                            if (tickerObj.asc_net_operating_expense_pct != null) {
                                                $(trCloneFDentry).find('input[name="net_ratio-#p#-fdEntry[]"]').val((tickerObj.asc_net_operating_expense_pct).toFixed(5));
                                                $(trCloneFDentry).find('input[name="net_ratio-#p#-fdEntry[]"]').removeClass("hide");
                                            }
                                            /* else{
												$(trCloneFDentry).find('input[name="net_ratio-#p#-fdEntry[]"]').val("0.000");
												$(trCloneFDentry).find('input[name="net_ratio-#p#-fdEntry[]"]').removeClass("hide");
											} */
                                            if (tickerObj.asc_actual_12b1 != null) {
                                                $(trCloneFDentry).find('input[name="12b_1C-#p#-fdEntry[]"]').val((tickerObj.asc_actual_12b1).toFixed(4));
                                                $(trCloneFDentry).find('input[name="12b_1C-#p#-fdEntry[]"]').removeClass("hide");
                                            }
                                            /* else{
												$(trCloneFDentry).find('input[name="12b_1C-#p#-fdEntry[]"]').val("0.000");
												$(trCloneFDentry).find('input[name="12b_1C-#p#-fdEntry[]"]').removeClass("hide");
											} */
                                            if (tickerObj.asc_subtarate != null) {
                                                $(trCloneFDentry).find('input[name="subTARate-#p#-fdEntry[]"]').val((tickerObj.asc_subtarate).toFixed(4));
                                                $(trCloneFDentry).find('input[name="subTARate-#p#-fdEntry[]"]').removeClass("hide");
                                            }
                                            /* else{
												$(trCloneFDentry).find('input[name="subTARate-#p#-fdEntry[]"]').val("0.000");
												$(trCloneFDentry).find('input[name="subTARate-#p#-fdEntry[]"]').removeClass("hide");
											} */
                                            if (tickerObj.asc_subtappamt) {
                                                $(trCloneFDentry).find('input[name="subTARateDollar-#p#-fdEntry[]"]').val(format(tickerObj.asc_subtappamt));
                                            }
                                            if (tickerObj.asc_subtappamt != null) {
                                                $(trCloneFDentry).find('input[name="subTAPPAmt-#p#-fdEntry[]"]').val(format(tickerObj.asc_subtappamt));
                                            } else {
                                                $(trCloneFDentry).find('input[name="subTAPPAmt-#p#-fdEntry[]"]').val(format("0"));
                                            }
                                            if (tickerObj.asc_fundminimuminvestment) {
                                                $(trCloneFDentry).find('input[name="Minimum-#p#-fdEntry[]"]').val(format(tickerObj.asc_fundminimuminvestment));
                                            }

                                            var comments = "";
                                            if (tickerObj.asc_comments) {
                                                comments = tickerObj.asc_comments;
                                            }
                                            var corporateActionType = "";
                                            if (tickerObj["asc_corporateactiontype@OData.Community.Display.V1.FormattedValue"] == "Closed to New" || tickerObj["asc_corporateactiontype@OData.Community.Display.V1.FormattedValue"] == "Closed to All") {
                                                corporateActionType = tickerObj["asc_corporateactiontype@OData.Community.Display.V1.FormattedValue"];
                                            }
                                            //cmnt = (corporateActionType) ? corporateActionType + " - " + comments : comments;
                                            if (corporateActionType != "" && comments != "") {
                                                cmnt = corporateActionType + " - " + comments;
                                            } else {
                                                if (comments != "") {
                                                    cmnt = comments;
                                                } else {
                                                    cmnt = corporateActionType;
                                                }
                                            }
                                            if (cmnt) {
                                                $(trCloneFDentry).find('textarea[name="Comments-#p#-fdEntry[]"]').val(cmnt);
                                            }

                                            $(trCloneFDentry).removeAttr('id');
                                            $("#myForm").find("#FundEntry-tab-table tbody").append(trCloneFDentry);
                                            //updateTickerFD();
                                            // Fd Entry
                                            $(trCloneFundList).find('input[name="ticker-#p#-fundListTable[]"]').val(key2);
                                            if (tickerObj.asc_fund_legal_name) {
                                                $(trCloneFundList).find('input[name="fund_name-#p#-fundListTable[]"]').val(tickerObj.asc_fund_legal_name);
                                            }
                                            if (tickerObj.asc_morningstar_category_name) {
                                                $(trCloneFundList).find('input[name="morningstart_category-#p#-fundListTable[]"]').val(tickerObj.asc_morningstar_category_name);
                                            }
                                            if (tickerObj.asc_fund_family_name) {
                                                $(trCloneFundList).find('input[name="family_name-#p#-fundListTable[]"]').val(tickerObj.asc_fund_family_name);
                                            }
                                            if (tickerObj.asc_share_class_type) {
                                                $(trCloneFundList).find('input[name="Share_Class_Type-#p#-fundListTable[]"]').val(tickerObj.asc_share_class_type);
                                            }
                                            if (tickerObj.asc_fundminimuminvestment) {
                                                $(trCloneFundList).find('input[name="Minimum-#p#-fundListTable[]"]').val(format(tickerObj.asc_fundminimuminvestment));
                                            }

                                            if (cmnt) {
                                                $(trCloneFundList).find('input[name="Comments-#p#-fundListTable[]"]').val(cmnt);
                                            }

                                            if (tickerObj.asc_morningstarrating) {
                                                $(trCloneFundList).find('input[name="Master_Rating-#p#-fundListTable[]"]').val(tickerObj.asc_morningstarrating);
                                            }
                                            if (tickerObj.asc_securitytype) {
                                                $(trCloneFundList).find('input[name="Security_Type-#p#-fundListTable[]"]').val(tickerObj.asc_securitytype);
                                            }
                                            if (tickerObj.asc_net_operating_expense_pct != null) {
                                                $(trCloneFundList).find('input[name="net_ratio-#p#-fundListTable[]"]').val((tickerObj.asc_net_operating_expense_pct).toFixed(5));
                                            }
                                            /* else{
												$(trCloneFundList).find('input[name="net_ratio-#p#-fundListTable[]"]').val("0.0000");
											} */
                                            if (tickerObj.asc_actual_12b1 != null) {
                                                $(trCloneFundList).find('input[name="12b_1C-#p#-fundListTable[]"]').val((tickerObj.asc_actual_12b1).toFixed(4));
                                            }
                                            /* else{
												$(trCloneFundList).find('input[name="12b_1C-#p#-fundListTable[]"]').val("0.0000");
											} */
                                            if (tickerObj.asc_subtarate != null) {
                                                $(trCloneFundList).find('input[name="subTARate-#p#-fundListTable[]"]').val((tickerObj.asc_subtarate).toFixed(4));
                                            }
                                            /* else{
												$(trCloneFundList).find('input[name="subTARate-#p#-fundListTable[]"]').val("0.0000");
											} */
                                            if (tickerObj.asc_subtappamt) {
                                                $(trCloneFundList).find('input[name="subTAPPRate-#p#-fundListTable[]"]').val(tickerObj.asc_subtappamt);
                                            }
                                            if (tickerObj.asc_subtappamt != null) {
                                                $(trCloneFundList).find('input[name="subTAPPAmt-#p#-fundListTable[]"]').val(format(tickerObj.asc_subtappamt));
                                            } else {
                                                $(trCloneFundList).find('input[name="subTAPPAmt-#p#-fundListTable[]"]').val(format("0"));
                                            }
                                            $(trCloneFundList).removeAttr('id');
                                            $("#myForm").find("#FundList-tab-table tbody").append(trCloneFundList);
                                        }
                                    }
                                }
                            } else {
                                fundsNotFound = 'Not Found';
                            }
                        }
                        equalDistributeAssets();
                    }
                    // Display not found funds
                    $("div.fund_not_found_error").hide();
                    if (fundsNotFound == 'Not Found') {
                        $("div.fund_not_found_error").html("funds not available");
                        $("div.fund_not_found_error").show();
                    } else {
                        if (typeof fundsNotFound !== 'undefined' && fundsNotFound.length > 0) {
                            $("div.fund_not_found_error").html("Following funds are not found: " + fundsNotFound);
                            $("div.fund_not_found_error").show();
                        }
                    }
                }
            }
        });

    }

    function changeFundList() {
        $("#myForm").find('select[name="FLN"]').removeClass("error");
        $("div.fund_not_found_error").hide(); // hide error on initial load
        var DefaultFundLineup = getAjaxrequest('Default Fund Lineup');
        var fundListName = $("#myForm").find('select[name="FLN"]').val();
        $("#myForm").find("#FundEntry-tab-table tbody tr.templates").remove();
        $("#myForm").find("#FundList-tab-table tbody tr.templates").remove();
        $("#myForm").find("#display-tab-table tbody tr.template").remove();
        // Check if fund list name is Other
        if (fundListName == 'Custom' || fundListName == '') {
            var trCloneFundList = $('#displayTableClone tr#fundListTableTr').clone();
            var trCloneFDentry = $('#displayTableClone tr#fdEntryTr').clone();
            // Fund List
            $(trCloneFDentry).find('input[name="12b_1C-#p#-fdEntry[]"]').val("");
            $(trCloneFDentry).find('input[name="subTARate-#p#-fdEntry[]"]').val("");
            $(trCloneFDentry).find('input[name="subTARateDollar-#p#-fdEntry[]"]').val("");

            $(trCloneFDentry).removeAttr('id');
            $("#myForm").find("#FundEntry-tab-table tbody").append(trCloneFDentry);
            //updateTickerFD();
            // Fd Entry
            $(trCloneFundList).find('input[name="net_ratio-#p#-fundListTable[]"]').val(0);
            $(trCloneFundList).find('input[name="12b_1C-#p#-fundListTable[]"]').val(0);
            $(trCloneFundList).find('input[name="subTARate-#p#-fundListTable[]"]').val(0);
            $(trCloneFundList).find('input[name="subTAPPRate-#p#-fundListTable[]"]').val(0);
            $(trCloneFundList).removeAttr('id');
            $("#myForm").find("#FundList-tab-table tbody").append(trCloneFundList);
            equalDistributeAssets();
        } else {
            if (fundListName) {
                var AllTickerGroup = fun_DefaultFundLineUp(DefaultFundLineup, fundListName);
                create_and_populateCRM_fields(AllTickerGroup, DefaultFundLineup);
            }
        }

    }

    function checkReferProposalValue() {
        var Reference_Proposal = $("select[name='ReferencedProposal']").val();
        if (Reference_Proposal == "0") {
            $("input[name='WhoReferencedProposal']").val("");
            $("input[name='WhoReferencedProposal']").css("background-color", "#808080");
            $("input[name='WhoReferencedProposal']").attr("readonly", "readonly");
        } else {
            $("input[name='WhoReferencedProposal']").css("background-color", "#ffffff");
            $("input[name='WhoReferencedProposal']").removeAttr("readonly");
        }
    }

    function formOnLoads() {
        var CountBlocks = <?= count( $blocks ); ?>;
        ////////////////// Fixed the Bug ID 20526 that pricing values doesnot changes when change the dropdown values from the info page  User Story 20003 ///
        if (CountBlocks == 0) {
            checkReferProposalValue();
            //equalDistributeAssets();
            isCrmProductExist();
            // Default Fund Lineup
            changeFundList();
        }
        ////////////////// Fixed end here making the calculation out of the conditions so it should also work in the edit mode /////////////
        var AjaxUrl = encodeURIComponent('<?= $category; ?>');
        var getProduct1 = getAjaxrequest(AjaxUrl);
        ////////////// For Comm Base Bunled Table ////////
        var CommBaseBundled = "";
        var getAvgPPT = '';
        var getSSRange = '';
        var getSSLink = '';
        if (getProduct1[0]['custom1'] == 'test' || getProduct1[0]['custom1'] == '') {
        } else if (JSON.parse(getProduct1[0]['custom1'])['AveregeSST']) {
            getAvgPPT = JSON.parse(getProduct1[0]['custom1'])['AveregeSST']['AvgPPT'];
            getSSRange = JSON.parse(getProduct1[0]['custom1'])['AveregeSST']['SSRange'];
            getSSLink = JSON.parse(getProduct1[0]['custom1'])['AveregeSST']['SSLink'];
        }
        // Psg Schedules
        var $pricingSchedules = <?= json_encode( $pricingSchedules ) ?>;
        var $c = 0;
        var ScheduleItemHtml = "";
        var $pricingSchedule;
        if (getProduct1.length > 0) {
            $.each($pricingSchedules, function ($i, $pricingSchedule) {
                $pricingScheduleItems = $pricingSchedule["items"];
                pricingSchedule = $pricingSchedule;
                var $tr_class = "";
                var $tr_class = "";
                var $range_index = "";
                ScheduleItemHtml = "";
                if ($c == 0) {
                    $("table#CommBaseBundled tbody").find("tr").remove();
                    $tr_class = "templateRange";
                }
                if ($pricingScheduleItems.length > 0) {
                    for (gp = 0; gp < $pricingScheduleItems.length; gp++) {
                        var $base_rate = "$0.00";
                        if ($pricingScheduleItems[gp]["rate"] !== undefined && $pricingScheduleItems[gp]["rate"] !== null) {
                            $base_rate = $pricingScheduleItems[gp]["rate"];
                        }
                        if ($pricingScheduleItems[gp]["max"]) {
                            var $rangeText = $pricingScheduleItems[gp]["min"] + " - " + $pricingScheduleItems[gp]["max"];
                        } else {
                            var $rangeText = "Over " + $pricingScheduleItems[gp]["min"];
                        }

                        ScheduleItemHtml += '<tr class="' + $tr_class + '">';
                        ScheduleItemHtml += '<td class="td-color" style="display:none;">';
                        ScheduleItemHtml += '</td>';
                        ScheduleItemHtml += '<td>';
                        ScheduleItemHtml += '<input type="text" name="RangeValue-#p#-ModelRange' + $range_index + '[]" value="' + $rangeText + '" readonly="readonly" />';
                        ScheduleItemHtml += '</td>';
                        ScheduleItemHtml += '<td>';
                        ScheduleItemHtml += '<input type="text" name="Pricing-#p#-ModelRange' + $range_index + '[]" value="' + $base_rate + '" readonly="readonly" />';
                        ScheduleItemHtml += '</td>';
                        ScheduleItemHtml += '</tr>';

                    }
                }

                if ($c == 0) {
                    $("table#CommBaseBundled tbody").append(ScheduleItemHtml);
                }
            });
            $("#CommBaseBundledSchedules").find("input[name='BaseCommBundled']").val(pricingSchedule["basefee"]);

            //////////// AVG Per PPT, SS and SS Link Tables ////////////////
            $("table#AveregeSST tbody").find("tr").remove();
            var AveregeSST = "";
            for (gp = 0; gp < getAvgPPT.length; gp++) {
                AveregeSST += '<tr class="templateAveregeSST">';
                AveregeSST += '<td class="td-color" style="display:none;">';
                AveregeSST += '<div class="add-remove-sign">';
                AveregeSST += '<div class="add-arrow" onclick="addNewRow(this)"><i class="material-icons">add_circle</i></div>';
                AveregeSST += '<div class="remove-arrow" onclick="removeRow(this, \'templateAveregeSST\');ModelTabCalculation();"><i class="material-icons">remove_circle</i></div>';
                AveregeSST += '</div>';
                AveregeSST += '</td>';
                AveregeSST += '<td>';
                AveregeSST += '<input type="text" name="RangeValue-#p#-AveregeSST[]" value="' + getAvgPPT[gp] + '" readonly="readonly" />';
                AveregeSST += '</td>';
                AveregeSST += '<td>';
                AveregeSST += '<input type="text" name="SSValue-#p#-AveregeSST[]" value="' + getSSRange[gp] + '" readonly="readonly" />';
                AveregeSST += '</td>';
                AveregeSST += '<td>';
                AveregeSST += '<input type="text" name="SSLink-#p#-AveregeSST[]" value="' + getSSLink[gp] + '" readonly="readonly" />';
                AveregeSST += '</td>';
                AveregeSST += '</tr>';
            }
            $("table#AveregeSST tbody").append(AveregeSST);
        }
        ModelTabCalculation();
    }

    function equalDistributeAssets() {
        var TotalPlanAssets = Number($("input[name='PA']").val().replace(/[^0-9\.]+/g, ""));
        //////////////////PRO RATA ASSET /////////////
        var TotalTicker = 0;
        ///////////// For Multiple Ticker With Assets Azure Id 9389 Starts //////
        var TotalFundEntryTR = $("table#FundEntry-tab-table tbody").find("tr.templates").length;
        if (TotalFundEntryTR > 0) {
            $("table#FundEntry-tab-table tbody").find("tr.templates").each(function () {
                if ($(this).find("input[name='ticker-#p#-fdEntry[]']").val() != "") {
                    TotalTicker++;
                }
            });
            var PRAS = TotalPlanAssets / TotalTicker;
            if (isFinite(PRAS) && !isNaN(PRAS)) {
                $("input[name='PRAS']").val(format(PRAS));
            } else {
                $("input[name='PRAS']").val("$0.00");
            }
            $("table#FundEntry-tab-table tbody").find("tr.templates").each(function () {
                if ($(this).find("input[name='ticker-#p#-fdEntry[]']").val() != "") {
                    $(this).find("input[name='assestPerFund-#p#-fdEntry[]']").val(format(PRAS));
                }
            });
            //////////////////// To match with GrandTotal update Last Asset Per Fund Value //////////////
            var GrandTotalNotRound = CalgrandTotalFDTab();
            var Difference = GrandTotalNotRound["AssetTotal"] - TotalPlanAssets;
            if (GrandTotalNotRound["AssetTotal"] > 0) {
                if (Difference != 0) {
                    var LastfundAssetValue = (PRAS.toFixed(2) - Difference.toFixed(2)) * 100;
                    $("table#FundEntry-tab-table tbody").find("tr.templates:last").find("input[name='assestPerFund-#p#-fdEntry[]']").val(format((Math.round(LastfundAssetValue) / 100).toFixed(2)));
                }
            }
            updateTickerFD();
        }
        ///////////// For Multiple Ticker With Assets Azure Id 9389 Ends //////
    }

    // Get Ajax request
    function getAjaxrequest(url) {
        var getProduct = [];
        dojo.xhrGet({
            url: '/Products/getproducts/?&category=' + url,
            handleAs: "json",
            sync: true,
            load: function (data, ioArgs) {
                for (var i = 0; i < data.length; i++) {
                    getProduct.push(data[i]);
                }
            }
        });
        return getProduct;

    }

    $(document).ready(function () {
        $('.tabs .tab-links a').on('click', function (e) {
            var currentAttrValue = $(this).attr('href');
            // Show/Hide Tabs
            $('.tabs ' + currentAttrValue).show().siblings().hide();
            // Change/remove current tab to active
            $(this).parent('li').addClass('active').siblings().removeClass('active');
            e.preventDefault();
        });
    });

    // function for tab Click
    function addActiveClass(obj) {
        var currentAttrValue = $(obj).attr('href');
        // Show/Hide Tabs
        $('.tabs ' + currentAttrValue).show().siblings().hide();
        // Change/remove current tab to active
        $(obj).parent('li').addClass('active').siblings().removeClass('active');
    }

    function addNewRow(obj) {
        var tr_close = $(obj).closest('tr');
        var trClone = $(obj).closest('tr').clone();
        $(trClone).find("input").val("");
        $(trClone).find("textarea").val("");
        if ($(trClone).find("td:eq(1)").find("div").length > 0) {
            $(trClone).find("td:eq(1)").find("div").css("display", "none");
        }
        $(tr_close).after(trClone);
    }

    function removeRow(obj, trClass) {
        var table = $(obj).closest('table');
        var tr = $(obj).closest('tr');
        //var trIndex     = $(tr).index();
        if ($(table).find('tbody tr.' + trClass).length > 1) {
            $(obj).closest('tr').remove();
            //$("#tab2 #display-tab-table").find("tbody tr").eq(trIndex).remove();
        } else {
            $(obj).closest('tr').find('input[type="text"], textarea').val();
        }
        checkShowSubmit();
    }

    /********************** get Number from String  ***********/
    function getNumbers(vals) {
        var value = 0;
        if (vals) {
            value = Number(vals.replace(/[^0-9\.]+/g, ""));
        }
        return value;
    }

    require(["dojo/number"]);

    function format(val) {
        var abc;
        if (typeof val === "undefined" || val === null || val === "" || val === " ") {
            val = 0;
            return "$0.00";
        }
        require(["dojo/number"], function (number) {
            abc = number.format(val, {
                places: 2,
                locale: 'en-us'
            });
        });
        if (val < 0) {
            var st = "$" + abc;
            abc = st.replace("$-", "-$");
        } else {
            abc = "$" + abc;
        }
        return abc;
    }

    function format1(val) {
        var abc;
        require(["dojo/number"], function (number) {
            abc = number.format(val, {
                places: 2,
                locale: 'en-us'
            });
        });
        return abc;
    }

    // Number format onblur
    function num_format(obj) {
        var value = Number($(obj).val().replace(/[^0-9\-.]+/g, ""));
        if (value) {
            obj.value = format(value);
        } else {
            if ($(obj).attr("name") != "AdvF2") {
                obj.value = format(0);
            }
        }
        ModelTabCalculation();
    }

    ////// When the Ticker Value change in the FD Entry Tab //////
    function updateTickerFD() {
        var GrandTotal = CalgrandTotalFDTab();
        var NumberParticipants = Number($("input[name='PB']").val().replace(/[^0-9\.]+/g, ""));
        $("table#FundEntry-tab-table tbody").find("tr.templates").each(function () {
            var Closest_Tr = $(this);
            var FundEntyTicker = $(Closest_Tr).find("input[name='ticker-#p#-fdEntry[]']").val();
            var AssetPerFund = Number($(Closest_Tr).find("input[name='assestPerFund-#p#-fdEntry[]']").val().replace(/[^0-9\.]+/g, ""));
            if (FundEntyTicker.length > 0) {
                $("table#FundList-tab-table tbody").find("tr.templates").each(function () {
                    var FundListTicker = $(this).find("input[name='ticker-#p#-fundListTable[]']").val();
                    if (FundListTicker == FundEntyTicker) {
                        if ($(Closest_Tr).find("input[name='fund_name-#p#-fdEntry[]']").val().trim() == "") {
                            var FundListFundName = $(this).find("input[name='fund_name-#p#-fundListTable[]']").val();
                            $(Closest_Tr).find("input[name='fund_name-#p#-fdEntry[]']").val(FundListFundName);

                            var expRatio = $(this).find("input[name='net_ratio-#p#-fundListTable[]']").val();
                            $(Closest_Tr).find("input[name='net_ratio-#p#-fdEntry[]']").val(expRatio);

                            var fundExpense = AssetPerFund * Number(expRatio.replace(/[^0-9\.]+/g, ""));
                            $(Closest_Tr).find("input[name='fund_expense-#p#-fdEntry[]']").val(fundExpense.toFixed(4));

                            var FundListTwelveBCr = $(this).find("input[name='12b_1C-#p#-fundListTable[]']").val();
                            $(Closest_Tr).find("input[name='12b_1C-#p#-fdEntry[]").val(Number(FundListTwelveBCr.replace(/[^0-9\.]+/g, "")).toFixed(4));

                            var subTARate = $(this).find("input[name='subTARate-#p#-fundListTable[]']").val();
                            $(Closest_Tr).find("input[name='subTARate-#p#-fdEntry[]']").val(Number(subTARate.replace(/[^0-9\.]+/g, "")).toFixed(4));


                            var subTARateDollar = Number($(this).find("input[name='subTARate-#p#-fundListTable[]']").val().replace(/[^0-9\.]+/g, "")) * AssetPerFund;
                            $(Closest_Tr).find("input[name='subTARateDollar-#p#-fdEntry[]']").val(format(subTARateDollar));

                            var FundListTwelveBCrDoollar = Number(FundListTwelveBCr.replace(/[^0-9\.]+/g, "")) * AssetPerFund;
                            $(Closest_Tr).find("input[name='12b1-#p#-fdEntry[]']").val(format(FundListTwelveBCrDoollar));

                            var expRationCost = AssetPerFund * expRatio;
                            $(Closest_Tr).find("input[name='expRationCost-#p#-fdEntry[]']").val(format(expRationCost));

                            var FundListMinimum = $(this).find("input[name='Minimum-#p#-fundListTable[]']").val();
                            $(Closest_Tr).find("input[name='Minimum-#p#-fdEntry[]']").val(format(FundListMinimum));

                            var FundListComments = $(this).find("input[name='Comments-#p#-fundListTable[]']").val();
                            $(Closest_Tr).find("input[name='Comments-#p#-fdEntry[]']").val(FundListComments);

                            var FundListSpecialCreteria = $(this).find("input[name='specialCreterial-#p#-fundListTable[]']").val();
                            $(Closest_Tr).find("input[name='specialCreterial-#p#-fdEntry[]']").val(FundListSpecialCreteria);

                            var FundListPPAmount = Number($(this).find("input[name='subTAPPRate-#p#-fundListTable[]']").val().replace(/[^0-9\.]+/g, ""));
                            $(Closest_Tr).find("input[name='perPostAmount-#p#-fdEntry[]']").val(format(FundListPPAmount));

                        }
                        var ExpenseRatioCost = AssetPerFund * Number($(Closest_Tr).find("input[name='net_ratio-#p#-fdEntry[]']").val().replace(/[^0-9\.]+/g, ""));
                        $(Closest_Tr).find("input[name='expRationCost-#p#-fdEntry[]']").val(format(ExpenseRatioCost));
                        var TwelveB1Dolloar = AssetPerFund * Number($(Closest_Tr).find("input[name='12b_1C-#p#-fdEntry[]']").val().replace(/[^0-9\.]+/g, ""));
                        $(Closest_Tr).find("input[name='12b1-#p#-fdEntry[]']").val(format(TwelveB1Dolloar));
                        var SubTADollar = AssetPerFund * Number($(Closest_Tr).find("input[name='subTARate-#p#-fdEntry[]']").val().replace(/[^0-9\.]+/g, ""));
                        $(Closest_Tr).find("input[name='subTARateDollar-#p#-fdEntry[]']").val(format(SubTADollar));
                        var DefaultPPExpense = (((AssetPerFund / GrandTotal["AssetTotal"]) * NumberParticipants) * Number($(this).find("input[name='subTAPPRate-#p#-fundListTable[]']").val().replace(/[^0-9\.]+/g, "")));
                        if (!isNaN(DefaultPPExpense)) {
                            $(Closest_Tr).find("input[name='defaultPPExpense-#p#-fdEntry[]']").val(format(DefaultPPExpense));
                        } else {
                            $(Closest_Tr).find("input[name='defaultPPExpense-#p#-fdEntry[]']").val(format(0));
                        }
                    }
                });
            }
        });
        calculateFooterTotal()
        ModelTabCalculation();
    }

    function updateCurrentTicker(obj) {
        var NumberParticipants = Number($("input[name='PB']").val().replace(/[^0-9\.]+/g, ""));
        var GrandTotal = CalgrandTotalFDTab();
        var AssetPerFund = Number($(obj).closest("tr").find("input[name='assestPerFund-#p#-fdEntry[]']").val().replace(/[^0-9\.]+/g, ""));
        var TwelveB1Dolloar = AssetPerFund * Number($(obj).closest("tr").find("input[name='12b_1C-#p#-fdEntry[]']").val().replace(/[^0-9\.]+/g, ""));
        $(obj).closest("tr").find("input[name='12b1-#p#-fdEntry[]']").val(format(TwelveB1Dolloar));
        var SubTADollar = AssetPerFund * Number($(obj).closest("tr").find("input[name='subTARate-#p#-fdEntry[]']").val().replace(/[^0-9\.]+/g, ""));
        $(obj).closest("tr").find("input[name='subTARateDollar-#p#-fdEntry[]']").val(format(SubTADollar));
        var ExpenseRatioCost = AssetPerFund * Number($(obj).closest("tr").find("input[name='net_ratio-#p#-fdEntry[]']").val().replace(/[^0-9\.]+/g, ""));
        $(obj).closest("tr").find("input[name='expRationCost-#p#-fdEntry[]']").val(format(ExpenseRatioCost));
        var TRIndex = $(obj).closest("tr").index();
        var FundListPPAmount = Number($("table#FundList-tab-table").find("tbody tr").find("input[name='subTAPPRate-#p#-fundListTable[]']").val().replace(/[^0-9\.]+/g, ""));
        var DefaultPPExpense = (((AssetPerFund / GrandTotal["AssetTotal"]) * NumberParticipants) * FundListPPAmount);
        if (!isNaN(DefaultPPExpense)) {
            $(obj).closest("tr").find("input[name='defaultPPExpense-#p#-fdEntry[]']").val(format(DefaultPPExpense));
        } else {
            $(obj).closest("tr").find("input[name='defaultPPExpense-#p#-fdEntry[]']").val(format(0));
        }
        calculateFooterTotal();
        ModelTabCalculation();
    }

    function CalgrandTotalFDTab() {
        var AssetFundTotal = [];
        AssetFundTotal["AssetTotal"] = 0;
        $("table#FundEntry-tab-table tbody").find("tr.templates").each(function () {
            AssetFundTotal["AssetTotal"] += Number($(this).find("input[name='assestPerFund-#p#-fdEntry[]']").val().replace(/[^0-9\.]+/g, ""));
        });
        $("table#FundEntry-tab-table tfoot").find("input[name='GrandTotalAssets']").val(format(AssetFundTotal["AssetTotal"]));
        return AssetFundTotal;
    }

    function calculateFooterTotal() {
        var GrandTotalfund = 0;
        var GrandTotalTwelBD = 0;
        var GrandTotalsubTARateDollar = 0;
        var GrandTotalexpenseDollar = 0
        $("table#FundEntry-tab-table tbody").find("tr.templates").each(function () {
            GrandTotalfund += Number($(this).find("input[name='fund_expense-#p#-fdEntry[]']").val().replace(/[^0-9\.]+/g, ""));
            GrandTotalTwelBD += Number($(this).find("input[name='12b1-#p#-fdEntry[]']").val().replace(/[^0-9\.]+/g, ""));
            GrandTotalsubTARateDollar += Number($(this).find("input[name='subTARateDollar-#p#-fdEntry[]']").val().replace(/[^0-9\.]+/g, ""));
            GrandTotalexpenseDollar += Number($(this).find("input[name='expRationCost-#p#-fdEntry[]']").val().replace(/[^0-9\.]+/g, ""));
        });
        $("table#FundEntry-tab-table tfoot").find("input[name='GrandTotalfund']").val(format(GrandTotalfund));
        $("table#FundEntry-tab-table tfoot").find("input[name='GrandTotalTwelBD']").val(format(GrandTotalTwelBD));
        $("table#FundEntry-tab-table tfoot").find("input[name='GrandTotalsubTARateDollar']").val(format(GrandTotalsubTARateDollar));
        $("table#FundEntry-tab-table tfoot").find("input[name='GrandTotalexpenseDollar']").val(format(GrandTotalexpenseDollar));
    }

    function displayTabCal() {
        $("table#display-tab-table tbody").find("tr").remove();
        var FDEntryTRLen = $("table#FundEntry-tab-table tbody").find("tr").length;
        if (FDEntryTRLen >= 1) {
            $("table#FundEntry-tab-table tbody").find("tr").each(function () {
                var fundTickerDisplay = $(this).find("input[name='ticker-#p#-fdEntry[]']").val();
                var fundNameDisplay = $(this).find("input[name='fund_name-#p#-fdEntry[]']").val();
                var assestPerFundDisplay = $(this).find("input[name='assestPerFund-#p#-fdEntry[]']").val();
                var net_ratioDisplay = (Number($(this).find("input[name='net_ratio-#p#-fdEntry[]']").val().replace(/[^0-9\.]+/g, "")) * 100).toFixed(4) + "%";
                var RecordkeepingFeesSubTADisplay = Number($(this).find("input[name='perPostAmount-#p#-fdEntry[]']").val().replace(/[^0-9\.]+/g, "")) > 0 ? (Number($(this).find("input[name='perPostAmount-#p#-fdEntry[]']").val().replace(/[^0-9\.]+/g, "")) * 100).toFixed(4) + "%" : (Number($(this).find("input[name='subTARate-#p#-fdEntry[]']").val().replace(/[^0-9\.]+/g, "")) * 100).toFixed(4) + "%";
                var AdditionAssFundFeesDisplay = Number($(this).find("input[name='subTARate-#p#-fdEntry[]']").val().replace(/[^0-9\.]+/g, "")) < 0.0025 ? ((0.0025 - Number($(this).find("input[name='subTARate-#p#-fdEntry[]']").val().replace(/[^0-9\.]+/g, ""))) * 100).toFixed(4) + "%" : "0.000%";
                var EsitmatedAddRKFEE = (Number(assestPerFundDisplay.replace(/[^0-9\.]+/g, "")) * Number(AdditionAssFundFeesDisplay.replace(/[^0-9\.]+/g, ""))) / 100;
                var AdvisoryCompensationDisplay = (Number($(this).find("input[name='12b_1C-#p#-fdEntry[]']").val().replace(/[^0-9\.]+/g, "")) * 100).toFixed(4) + "%";
                var MinimumDisplay = $(this).find("input[name='Minimum-#p#-fdEntry[]']").val();
                var CommentsDisplay = $(this).find("textarea[name='Comments-#p#-fdEntry[]']").val();
                var TRClone = $("table#displayTableClone").find("tr#displayTableCloneTr").clone();
                $(TRClone).find("input[name='fundName-#p#-displayTable[]']").val(fundNameDisplay);
                $(TRClone).find("input[name='fundTicker-#p#-displayTable[]']").val(fundTickerDisplay);
                $(TRClone).find("input[name='balanceByFund-#p#-displayTable[]']").val(assestPerFundDisplay);
                $(TRClone).find("input[name='fundExpenseRatio-#p#-displayTable[]']").val(net_ratioDisplay);
                $(TRClone).find("input[name='RecordkeepingFees-#p#-displayTable[]']").val(RecordkeepingFeesSubTADisplay);
                $(TRClone).find("input[name='additionalAscensus-#p#-displayTable[]']").val(AdditionAssFundFeesDisplay);
                $(TRClone).find("input[name='EsitmatedAddRKFEE-#p#-displayTable[]']").val(EsitmatedAddRKFEE.toFixed(4));
                $(TRClone).find("input[name='addvisorCompensation-#p#-displayTable[]']").val(AdvisoryCompensationDisplay);
                $(TRClone).find("input[name='minimum-#p#-displayTable[]']").val(format(MinimumDisplay));
                $(TRClone).find("input[name='comments-#p#-displayTable[]']").val(CommentsDisplay);
                $(TRClone).removeAttr('id');
                $("table#display-tab-table tbody").append(TRClone);
            });
        }
        calculateGrandTotalDisplayTable();
        AdditionalServiceCalculation();
    }

    //// Calculation for FD Entry Tab when Fund List Changes ////
    function updateFDEntry(obj) {
        var GrandTotal = CalgrandTotalFDTab();
        var Closest_Tr = $(obj).closest("tr");
        var NumberParticipants = Number($("input[name='PB']").val().replace(/[^0-9\.]+/g, ""));
        var FundListTicker = $(obj).closest("tr").find("input[name='ticker-#p#-fundListTable[]']").val();
        var FundListFundName = $(obj).closest("tr").find("input[name='fund_name-#p#-fundListTable[]']").val();
        var FundListMinimum = $(obj).closest("tr").find("input[name='Minimum-#p#-fundListTable[]']").val();
        var FundListNetRatio = $(obj).closest("tr").find("input[name='net_ratio-#p#-fundListTable[]']").val();
        var FundListTwelveBCr = $(obj).closest("tr").find("input[name='12b_1C-#p#-fundListTable[]']").val();
        var FundListsubTARate = $(obj).closest("tr").find("input[name='subTARate-#p#-fundListTable[]']").val();
        var FundListComments = $(obj).closest("tr").find("input[name='Comments-#p#-fundListTable[]']").val();
        var FundListsubTAPPRate = Number($(obj).closest("tr").find("input[name='subTAPPRate-#p#-fundListTable[]']").val().replace(/[^0-9\.]+/g, ""));
        var FundListspecialCr = $(obj).closest("tr").find("input[name='specialCreterial-#p#-fundListTable[]']").val();
        if (FundListTicker.length > 0) {
            $("table#FundEntry-tab-table tbody").find("tr.templates").each(function () {
                var FDEntryTicker = $(this).find("input[name='ticker-#p#-fdEntry[]']").val();
                ////////// IF Match with ticker /////////////
                if (FundListTicker == FDEntryTicker) {
                    var AssetsPerFund = Number($(this).find("input[name='assestPerFund-#p#-fdEntry[]']").val().replace(/[^0-9\.]+/g, ""));
                    var fundexpense = AssetsPerFund * Number(FundListNetRatio.replace(/[^0-9\.]+/g, ""));
                    var FundListTwelveBDol = AssetsPerFund * Number(FundListTwelveBCr.replace(/[^0-9\.]+/g, ""));
                    var FundListsubTARateDol = AssetsPerFund * Number(FundListsubTARate.replace(/[^0-9\.]+/g, ""));
                    var ExpenseRatioCost = AssetsPerFund * Number(FundListNetRatio.replace(/[^0-9\.]+/g, ""));
                    var DefaultPPExpense = (((AssetsPerFund / GrandTotal["AssetTotal"]) * NumberParticipants) * FundListsubTAPPRate);
                    $(this).find("input[name='fund_name-#p#-fdEntry[]']").val(FundListFundName);
                    $(this).find("input[name='fund_expense-#p#-fdEntry[]']").val(fundexpense.toFixed(4));
                    $(this).find("input[name='net_ratio-#p#-fdEntry[]']").val(FundListNetRatio);
                    $(this).find("input[name='12b_1ModelRangeC-#p#-fdEntry[]']").val(Number(FundListTwelveBCr.replace(/[^0-9\.]+/g, "")).toFixed(4));
                    $(this).find("input[name='subTARate-#p#-fdEntry[]']").val(FundListsubTARate);
                    $(this).find("input[name='12b1-#p#-fdEntry[]']").val(format(FundListTwelveBDol));
                    $(this).find("input[name='subTARateDollar-#p#-fdEntry[]']").val(format(FundListsubTARateDol));
                    $(this).find("input[name='expRationCost-#p#-fdEntry[]']").val(format(ExpenseRatioCost));
                    $(this).find("input[name='Minimum-#p#-fdEntry[]']").val(format(FundListMinimum));
                    $(this).find("textarea[name='Comments-#p#-fdEntry[]']").val(FundListComments);
                    $(this).find("input[name='perPostAmount-#p#-fdEntry[]']").val(format(FundListsubTAPPRate));
                    $(this).find("input[name='specialCreterial-#p#-fdEntry[]']").val(FundListspecialCr);
                    if (!isNaN(DefaultPPExpense)) {
                        $(this).find("input[name='defaultPPExpense-#p#-fdEntry[]']").val(format(DefaultPPExpense));
                    } else {
                        $(this).find("input[name='defaultPPExpense-#p#-fdEntry[]']").val(format(0));
                    }
                }
            });
        }
        updateTickerFD();
    }

    function formvalidation() { 
		var numproposalsrequested = $("input[name='asc_numproposalsrequested']").val();
        var errorMsg = "";
        var totalError = 0;
        var ProposalDueDate = new Date($("input[name='asc_proposalduedate']").val());
        var ProposalDueDate_UTC = Date.UTC(ProposalDueDate.getUTCFullYear(), ProposalDueDate.getUTCMonth(), ProposalDueDate.getUTCDate(), ProposalDueDate.getUTCHours(), ProposalDueDate.getUTCMinutes(), ProposalDueDate.getUTCSeconds());
        var CurrentDate = new Date($("input[name='DatePrepared']").val());
        var CurrentDate_UTC = Date.UTC(CurrentDate.getUTCFullYear(), CurrentDate.getUTCMonth(), CurrentDate.getUTCDate(), CurrentDate.getUTCHours(), CurrentDate.getUTCMinutes(), CurrentDate.getUTCSeconds());
        $("table#FundEntry-tab-table tbody tr").each(function () {
            if ($(this).find("input[name='fund_name-#p#-fdEntry[]']").val().trim() == "") {
                totalError++;
                $(this).find("input[name='fund_name-#p#-fdEntry[]']").css("border-color", "red");
            }

            if ($(this).find("input[name='assestPerFund-#p#-fdEntry[]']").val().trim() == "") {
                totalError++;
                $(this).find("input[name='assestPerFund-#p#-fdEntry[]']").css("border-color", "red");
            }

            if ($(this).find("input[name='net_ratio-#p#-fdEntry[]']").val().trim() == "") {
                totalError++;
                $(this).find("input[name='net_ratio-#p#-fdEntry[]']").css("border-color", "red");
            }

            if ($(this).find("input[name='12b_1C-#p#-fdEntry[]']").val().trim() == "") {
                totalError++;
                $(this).find("input[name='12b_1C-#p#-fdEntry[]']").css("border-color", "red");
            }


            if ($(this).find("input[name='subTARate-#p#-fdEntry[]']").val().trim() == "") {
                totalError++;
                $(this).find("input[name='subTARate-#p#-fdEntry[]']").css("border-color", "red");
            }

            if ($(this).find("input[name='subTAPPAmt-#p#-fdEntry[]']").val().trim() == "") {
                totalError++;
                $(this).find("input[name='subTAPPAmt-#p#-fdEntry[]']").css("border-color", "red");
            }
        });
        if (errorMsg != "") {
            errorMsg += " and ";
        }
        if (totalError > 0) {
            errorMsg += "Highlighed fields of fund(s) are not valid";
        }
        ///////////// Check Number of Proposal//////////////////
        if (numproposalsrequested != "") {
            if (isNaN(numproposalsrequested)) {
                if (errorMsg != "") {
                    errorMsg += " and ";
                }
                errorMsg += "Num Of Proposals Requested is not valid";
            }
        } else {
            if (errorMsg != "") {
                errorMsg += " and ";
            }
            errorMsg += "Num Of Proposals Requested is not valid";
        }
        var proposalDueDate = $("input[name='asc_proposalduedate']").val();
        if (proposalDueDate == "") {
            if (errorMsg != "") {
                errorMsg += " and ";
                $("input[name='asc_proposalduedate']").closest("td").find(".dijitDateTextBox ").css("border-color", "red");
            }
            errorMsg += "Proposal Due Date is not valid";
        } else {
            if (CurrentDate_UTC > ProposalDueDate_UTC) {
                if (errorMsg != "") {
                    errorMsg += " and ";
                }
                errorMsg += "Proposal due date is not valid";
                $("input[name='asc_proposalduedate']").closest("td").find(".dijitDateTextBox ").css("border-color", "red");
            } else {
                $("input[name='asc_proposalduedate']").closest("td").find(".dijitDateTextBox ").css("border-color", "#ddd");
            }
        }
        var totalPlanAsset = Number($("input[name='PA']").val().replace(/[^0-9\.]+/g, ""));
        var GrandTotalAssets = Number($("input[name='GrandTotalAssets']").val().replace(/[^0-9\.]+/g, ""));
        if (totalPlanAsset != GrandTotalAssets) {
            if (errorMsg != "") {
                errorMsg += " and ";
            }
            errorMsg += "Plan Asset Value is not equal to the Grand Total Assets Value";
        }

        var fundListName = $("#myForm").find('select[name="FLN"]').val();
        if (fundListName == "") {
            if (errorMsg != "") {
                errorMsg += " and ";
            }
            errorMsg += "Please Select any option from fund list";
            $("#myForm").find('select[name="FLN"]').addClass("error");
        } else {
            $("#myForm").find('select[name="FLN"]').removeClass("error");
        }
        var NumberParticipants = Number($("input[name='PB']").val().replace(/[^0-9\.]+/g, ""));
        var productFamily = $("input[name='productFamily']").val();
        /////////////////////////////////// Change for Azure Task Id 17883 //////////////////////////
        if (NumberParticipants > 1000 && productFamily.trim() == "Goldman" && $("#myForm").find("input[name='RateOneThousandPartic']").length > 0) {
            if ($("#myForm").find("input[name='RateOneThousandPartic']").val() == "") {
                if (errorMsg != "") {
                    errorMsg += " and ";
                }
                errorMsg += "Please enter valid value for 1001+ Participants";
                $("#myForm").find("input[name='RateOneThousandPartic']").addClass("error");
            }
            /* else{
				var RateOneThousandPartic	=	Number($("#myForm").find("input[name='RateOneThousandPartic']").val());
				if(isNaN(RateOneThousandPartic)){
					if (errorMsg != "") {
						errorMsg += " and ";
					}
					errorMsg += "Please enter valid value for 1001+ Participants";
					$("#myForm").find("input[name='RateOneThousandPartic']").addClass("error");
				}
			} */
        }
        if (errorMsg != "") {
            $("div.main_pink span.errorMsg").html(errorMsg);
            $("div.showerrormsg").show();
            return false;
        } else {
			//// User Story 35291: Ascensus | 3(16) Fiduciary Services //////////
            var SelectedOldFiduciaryServices	=	"<?= $fields["FiduciaryServices316"] ?>";
			$("#myForm").find("input[name='SelectedOldFiduciaryServices']").val(SelectedOldFiduciaryServices);
            $("div.showerrormsg").hide();
            var theform = dijit.byId('myForm'); // get form
            theform.submit();
        }
    }

    function isCrmProductExist() {
        var isProductExist = <?= $isProductFound ?>;
        var errMsg = "";
        if (isProductExist == "0") {
            errMsg = "Product not found at Dynamics CRM.";
            $("div.main_pink span.errorMsg").html(errMsg);
            $("div.showerrormsg").show();
        }
    }

    function converToPercentage(obj) {
        var val1 = $(obj).val();
        var val2 = Number($(obj).val().replace(/[^-0-9\-.]+/g, ""));
        if (val1.indexOf("%") < 0) {
            obj.value = (val2 * 100).toFixed(2) + "%";
        } else {
            obj.value = (val2).toFixed(2) + "%";
        }
    }

    function disableInputFieldsML(obj) {
        var InstallType = "<?php echo $fields['InstallType']; ?>";
        if (InstallType != "860530002") {
            var FiduciaryServices = "<?php echo $fields['FiduciaryServices']; ?>";
            var TotalPlanAssets = Number($("input[name='PA']").val().replace(/[^0-9\.]+/g, ""));
            if (FiduciaryServices == "FidServiceML338") {
                if ($(obj).attr("name") == "FASDiscretionaryAmount") {
                    if (Number($("input[name='FASDiscretionaryAmount']").val().replace(/[^0-9\.]+/g, "")) > 0) {
                        $("input[name='FASDiscretionaryPercentage']").attr("readonly", "readonly");
                        $("input[name='FASDiscretionaryPercentage']").attr("tabindex", "-1");
                        $("input[name='FASDisableValue']").val("FASDiscretionaryPercentage");
                        $("input[name='FASDiscretionaryAmount']").removeAttr("readonly");
                        $("input[name='FASDiscretionaryAmount']").removeAttr("tabindex");
                        /////////// Calculate Percentage //////////
                        var FASDiscretionaryAmount = Number($("input[name='FASDiscretionaryAmount']").val().replace(/[^0-9\.]+/g, ""));
                        var FASDiscretionaryPercentage = FASDiscretionaryAmount / TotalPlanAssets;
                        if (isFinite(FASDiscretionaryPercentage) && !isNaN(FASDiscretionaryPercentage) && FASDiscretionaryPercentage > 0) {
                            $("input[name='FASDiscretionaryPercentage']").val((FASDiscretionaryPercentage * 100).toFixed(2) + "%");
                        } else {
                            $("input[name='FASDiscretionaryPercentage']").val("0.00%");
                        }
                    } else {
                        $("input[name='FASDiscretionaryPercentage']").removeAttr("readonly", "readonly");
                        $("input[name='FASDiscretionaryPercentage']").removeAttr("tabindex");
                        $("input[name='FASDiscretionaryPercentage']").val("0.00%");
                        $("input[name='FASDisableValue']").val("");
                    }

                } else if ($(obj).attr("name") == "FASDiscretionaryPercentage") {
                    if (Number($("input[name='FASDiscretionaryPercentage']").val().replace(/[^0-9\.]+/g, "")) > 0) {
                        $("input[name='FASDiscretionaryAmount']").attr("readonly", "readonly");
                        $("input[name='FASDiscretionaryAmount']").attr("tabindex", "-1");
                        $("input[name='FASDisableValue']").val("FASDiscretionaryAmount");
                        $("input[name='FASDiscretionaryPercentage']").removeAttr("readonly");
                        $("input[name='FASDiscretionaryPercentage']").removeAttr("tabindex");
                        if ($("input[name='FASDiscretionaryPercentage']").val().indexOf("%") > 0) {
                            var FASDiscretionaryPercentage = Number($("input[name='FASDiscretionaryPercentage']").val().replace(/[^0-9\.]+/g, "")) / 100;
                        } else {
                            var FASDiscretionaryPercentage = Number($("input[name='FASDiscretionaryPercentage']").val().replace(/[^0-9\.]+/g, ""));
                        }
                        var FASDiscretionaryAmount = TotalPlanAssets * FASDiscretionaryPercentage;
                        if (isFinite(FASDiscretionaryAmount) && !isNaN(FASDiscretionaryAmount) && FASDiscretionaryAmount > 0) {
                            $("input[name='FASDiscretionaryAmount']").val(format(FASDiscretionaryAmount));
                        } else {
                            $("input[name='FASDiscretionaryAmount']").val("$0.00");
                        }
                    } else {
                        $("input[name='FASDiscretionaryAmount']").removeAttr("readonly", "readonly");
                        $("input[name='FASDiscretionaryAmount']").removeAttr("tabindex");
                        $("input[name='FASDisableValue']").val("");
                        $("input[name='FASDiscretionaryAmount']").val("$0.00");
                    }
                }
            } else {
                if ($(obj).attr("name") == "FiduciaryAdvSF") {
                    if (Number($("input[name='FiduciaryAdvSF']").val().replace(/[^0-9\.]+/g, "")) > 0) {
                        $("input[name='FiduciaryAdvSFPercentage']").attr("readonly", "readonly");
                        $("input[name='FiduciaryAdvSFPercentage']").attr("tabindex", "-1");
                        $("input[name='FiduciaryDisableValue']").val("FiduciaryAdvSFPercentage");
                        $("input[name='FiduciaryAdvSF']").removeAttr("readonly");
                        $("input[name='FiduciaryAdvSF']").removeAttr("tabindex");
                        var FiduciaryAdvSF = Number($("input[name='FiduciaryAdvSF']").val().replace(/[^0-9\.]+/g, ""));
                        var FiduciaryAdvSFPercentage = FiduciaryAdvSF / TotalPlanAssets;
                        if (isFinite(FiduciaryAdvSFPercentage) && !isNaN(FiduciaryAdvSFPercentage) && FiduciaryAdvSFPercentage > 0) {
                            $("input[name='FiduciaryAdvSFPercentage']").val((FiduciaryAdvSFPercentage * 100).toFixed(2) + "%");
                        } else {
                            $("input[name='FiduciaryAdvSFPercentage']").val("0.00%");
                        }
                    } else {
                        $("input[name='FiduciaryAdvSFPercentage']").removeAttr("readonly", "readonly");
                        $("input[name='FiduciaryAdvSFPercentage']").removeAttr("tabindex");
                        $("input[name='FiduciaryDisableValue']").val("");
                        $("input[name='FiduciaryAdvSFPercentage']").val("0.00%");
                    }
                } else if ($(obj).attr("name") == "FiduciaryAdvSFPercentage") {
                    if (Number($("input[name='FiduciaryAdvSFPercentage']").val().replace(/[^0-9\.]+/g, "")) > 0) {
                        $("input[name='FiduciaryAdvSF']").attr("readonly", "readonly");
                        $("input[name='FiduciaryAdvSF']").attr("tabindex", "-1");
                        $("input[name='FiduciaryDisableValue']").val("FiduciaryAdvSF");
                        $("input[name='FiduciaryAdvSFPercentage']").removeAttr("readonly");
                        $("input[name='FiduciaryAdvSFPercentage']").removeAttr("tabindex");
                        if ($("input[name='FiduciaryAdvSFPercentage']").val().indexOf("%") > 0) {
                            var FiduciaryAdvSFPercentage = Number($("input[name='FiduciaryAdvSFPercentage']").val().replace(/[^0-9\.]+/g, "")) / 100;
                        } else {
                            var FiduciaryAdvSFPercentage = Number($("input[name='FiduciaryAdvSFPercentage']").val().replace(/[^0-9\.]+/g, ""));
                        }
                        var FiduciaryAdvSF = TotalPlanAssets * FiduciaryAdvSFPercentage;
                        if (isFinite(FiduciaryAdvSF) && !isNaN(FiduciaryAdvSF) && FiduciaryAdvSF > 0) {
                            $("input[name='FiduciaryAdvSF']").val(format(FiduciaryAdvSF));
                        } else {
                            $("input[name='FiduciaryAdvSF']").val("$0.00");
                        }
                    } else {
                        $("input[name='FiduciaryAdvSF']").removeAttr("readonly", "readonly");
                        $("input[name='FiduciaryAdvSF']").removeAttr("tabindex");
                        $("input[name='FiduciaryAdvSF']").val("$0.00");
                        $("input[name='FiduciaryDisableValue']").val("");
                    }
                }
            }
            /////////////////// Education And Plan Services ////////////
            if ($(obj).attr("name") == "EduPlanServiceAmount") {
                if (Number($("input[name='EduPlanServiceAmount']").val().replace(/[^0-9\.]+/g, "")) > 0) {
                    $("input[name='EduPlanServicePercentage']").attr("readonly", "readonly");
                    $("input[name='EduPlanServicePercentage']").attr("tabindex", "-1");
                    $("input[name='EducationPlanDisableValue']").val("EduPlanServicePercentage");
                    $("input[name='EduPlanServiceAmount']").removeAttr("readonly");
                    $("input[name='EduPlanServiceAmount']").removeAttr("tabindex");
                    /////////// Calculate Percentage /////
                    var EduPlanServiceAmount = Number($("input[name='EduPlanServiceAmount']").val().replace(/[^0-9\.]+/g, ""));
                    var EduPlanServicePercentage = EduPlanServiceAmount / TotalPlanAssets;
                    if (isFinite(EduPlanServicePercentage) && !isNaN(EduPlanServicePercentage) && EduPlanServicePercentage > 0) {
                        $("input[name='EduPlanServicePercentage']").val((EduPlanServicePercentage * 100).toFixed(2) + "%");
                    } else {
                        $("input[name='EduPlanServicePercentage']").val("0.00%");
                    }
                } else {
                    $("input[name='EduPlanServicePercentage']").removeAttr("readonly", "readonly");
                    $("input[name='EduPlanServicePercentage']").removeAttr("tabindex");
                    $("input[name='EducationPlanDisableValue']").val("");
                    $("input[name='EduPlanServicePercentage']").val("0.00%");
                }
            } else if ($(obj).attr("name") == "EduPlanServicePercentage") {
                if (Number($("input[name='EduPlanServicePercentage']").val().replace(/[^0-9\.]+/g, "")) > 0) {
                    $("input[name='EduPlanServiceAmount']").attr("readonly", "readonly");
                    $("input[name='EduPlanServiceAmount']").attr("tabindex", "-1");
                    $("input[name='EducationPlanDisableValue']").val("EduPlanServiceAmount");
                    $("input[name='EduPlanServicePercentage']").removeAttr("readonly");
                    $("input[name='EduPlanServicePercentage']").removeAttr("tabindex");
                    if ($("input[name='EduPlanServicePercentage']").val().indexOf("%") > 0) {
                        var EduPlanServicePercentage = Number($("input[name='EduPlanServicePercentage']").val().replace(/[^0-9\.]+/g, "")) / 100;
                    } else {
                        var EduPlanServicePercentage = Number($("input[name='EduPlanServicePercentage']").val().replace(/[^0-9\.]+/g, ""));
                    }
                    var EduPlanServiceAmount = TotalPlanAssets * EduPlanServicePercentage;
                    if (isFinite(EduPlanServiceAmount) && !isNaN(EduPlanServiceAmount) && EduPlanServiceAmount > 0) {
                        $("input[name='EduPlanServiceAmount']").val(format(EduPlanServiceAmount));
                    } else {
                        $("input[name='EduPlanServiceAmount']").val("$0.00");
                    }
                } else {
                    $("input[name='EduPlanServiceAmount']").removeAttr("readonly", "readonly");
                    $("input[name='EduPlanServiceAmount']").removeAttr("tabindex");
                    $("input[name='EducationPlanDisableValue']").val("");
                }
            }
        }
        ModelTabCalculation();
    }
	
	////////////////// This is for User Story 21835 and 2484554 /////////////
	function disableInvFiduciaryServices(selectObject){
		var TotalPlanAssets = Number($("input[name='PA']").val().replace(/[^0-9\.]+/g, ""));
		var disableInvFidServInput = "";
		if((Number($("input[name='InvFiduciaryServices']").val().replace(/[^0-9\.]+/g, ""))==0 || $("input[name='InvFiduciaryServices']").val().trim()=="") && (Number($("input[name='InvFiduciaryServicesPercentage']").val().replace(/[^0-9\.]+/g, ""))==0 || $("input[name='InvFiduciaryServicesPercentage']").val().trim()=="")){				
			$("input[name='InvFiduciaryServices']").removeAttr("tabindex");
			$("input[name='InvFiduciaryServicesPercentage']").removeAttr("tabindex");
			$("input[name='InvFiduciaryServices']").removeAttr("readonly");
			$("input[name='InvFiduciaryServicesPercentage']").removeAttr("readonly");				
			$("#disableInvFidServInputName").val("");
		}else{
			if(selectObject.name == "InvFiduciaryServices"){
				var InvFiduciaryServices		=	Number($("input[name='InvFiduciaryServices']").val().replace(/[^0-9\.]+/g, ""));
				var InvFiduciaryServicesPercentage	=	Math.round(InvFiduciaryServices / TotalPlanAssets*10000)/10000;
				if(TotalPlanAssets>0){
					$("#myForm").find("input[name='InvFiduciaryServicesPercentage']").val(round_format(InvFiduciaryServicesPercentage*100,2)+"%");
				}else{
					$("#myForm").find("input[name='InvFiduciaryServicesPercentage']").val("");
				}
				$("input[name='InvFiduciaryServicesPercentage']").attr("readonly","readonly");
				$("input[name='InvFiduciaryServicesPercentage']").attr("tabindex","-1");
				$("input[name='InvFiduciaryServices']").removeAttr("readonly");
				$("input[name='InvFiduciaryServices']").removeAttr("tabindex");
				disableInvFidServInput = "InvFiduciaryServicesPercentage";
			} else if (selectObject.name == "InvFiduciaryServicesPercentage") {
				if($("input[name='InvFiduciaryServicesPercentage']").val().indexOf("%")>0){
					var InvFiduciaryServicesPercentage	=	Number($("input[name='InvFiduciaryServicesPercentage']").val().replace(/[^0-9\.]+/g, ""))/100;
				}
				else{
					var InvFiduciaryServicesPercentage	=	Number($("input[name='InvFiduciaryServicesPercentage']").val().replace(/[^0-9\.]+/g, ""));
				}
				var InvFiduciaryServices		=		InvFiduciaryServicesPercentage*TotalPlanAssets;
				if(InvFiduciaryServices>0){
					$("#myForm").find("input[name='InvFiduciaryServices']").val(format(InvFiduciaryServices));
				}else{
					$("#myForm").find("input[name='InvFiduciaryServices']").val("");
				}
				disableInvFidServInput = "InvFiduciaryServices";
				$("input[name='InvFiduciaryServicesPercentage']").removeAttr("readonly");
				$("input[name='InvFiduciaryServicesPercentage']").removeAttr("tabindex");
				$("input[name='InvFiduciaryServices']").attr("readonly","readonly");
				$("input[name='InvFiduciaryServices']").attr("tabindex","-1");
			}
			$("#disableInvFidServInputName").val(disableInvFidServInput);
		}
		if((Number($("input[name='InvFiduciaryServices']").val().replace(/[^0-9\.]+/g, ""))==0 || $("input[name='InvFiduciaryServices']").val().trim()=="") && (Number($("input[name='InvFiduciaryServicesPercentage']").val().replace(/[^0-9\.]+/g, ""))==0 || $("input[name='InvFiduciaryServicesPercentage']").val().trim()=="")){
			$("input[name='InvFiduciaryServices']").removeAttr("tabindex");
			$("input[name='InvFiduciaryServicesPercentage']").removeAttr("tabindex");
			$("input[name='InvFiduciaryServices']").removeAttr("readonly");
			$("input[name='InvFiduciaryServicesPercentage']").removeAttr("readonly");				
			$("#disableInvFidServInputName").val("");
		}
		ModelTabCalculation();
	}
	
	////////////////// This is for User Story 21835 and 2484554 /////////////
	function disableTPAFee(selectObject){
		var TotalPlanAssets = Number($("input[name='PA']").val().replace(/[^0-9\.]+/g, ""));
		var disableTPAFeeInput = "";
		if((Number($("input[name='TPAFees']").val().replace(/[^0-9\.]+/g, ""))==0 || $("input[name='TPAFees']").val().trim()=="") && (Number($("input[name='TPAFeesPerc']").val().replace(/[^0-9\.]+/g, ""))==0 || $("input[name='TPAFeesPerc']").val().trim()=="")){				
			$("input[name='TPAFees']").removeAttr("tabindex");
			$("input[name='TPAFeesPerc']").removeAttr("tabindex");
			$("input[name='TPAFees']").removeAttr("readonly");
			$("input[name='TPAFeesPerc']").removeAttr("readonly");				
			$("#disableTPAFeeInputName").val("");
		}else{
			if(selectObject.name == "TPAFees"){
				var TPAFees		=	Number($("input[name='TPAFees']").val().replace(/[^0-9\.]+/g, ""));
				var TPAFeesPerc	=	Math.round(TPAFees/TotalPlanAssets*10000)/10000;
				if(TotalPlanAssets>0){
					$("#myForm").find("input[name='TPAFeesPerc']").val(round_format(TPAFeesPerc*100,2)+"%");
				}else{
					$("#myForm").find("input[name='TPAFeesPerc']").val("");
				}
				$("input[name='TPAFeesPerc']").attr("readonly","readonly");
				$("input[name='TPAFeesPerc']").attr("tabindex","-1");
				$("input[name='TPAFees']").removeAttr("readonly");
				$("input[name='TPAFees']").removeAttr("tabindex");
				disableTPAFeeInput = "TPAFeesPerc";
			} else if (selectObject.name == "TPAFeesPerc") {
				if($("input[name='TPAFeesPerc']").val().indexOf("%")>0){
					var TPAFeesPerc	=	Number($("input[name='TPAFeesPerc']").val().replace(/[^0-9\.]+/g, ""))/100;
				}
				else{
					var TPAFeesPerc	=	Number($("input[name='TPAFeesPerc']").val().replace(/[^0-9\.]+/g, ""));
				}
				var TPAFees		=		TPAFeesPerc*TotalPlanAssets;
				if(TPAFees>0){
					$("#myForm").find("input[name='TPAFees']").val(format(TPAFees));
				}else{
					$("#myForm").find("input[name='TPAFees']").val("");
				}
				disableTPAFeeInput = "TPAFees";
				$("input[name='TPAFeesPerc']").removeAttr("readonly");
				$("input[name='TPAFeesPerc']").removeAttr("tabindex");
				$("input[name='TPAFees']").attr("readonly","readonly");
				$("input[name='TPAFees']").attr("tabindex","-1");
			}
			$("#disableTPAFeeInputName").val(disableTPAFeeInput);
		}
		if((Number($("input[name='TPAFees']").val().replace(/[^0-9\.]+/g, ""))==0 || $("input[name='TPAFees']").val().trim()=="") && (Number($("input[name='TPAFeesPerc']").val().replace(/[^0-9\.]+/g, ""))==0 || $("input[name='TPAFeesPerc']").val().trim()=="")){
			$("input[name='TPAFees']").removeAttr("tabindex");
			$("input[name='TPAFeesPerc']").removeAttr("tabindex");
			$("input[name='TPAFees']").removeAttr("readonly");
			$("input[name='TPAFeesPerc']").removeAttr("readonly");				
			$("#disableTPAFeeInputName").val("");
		}
		ModelTabCalculation();
	}
	
</script>
<!--- Common ascensus_script_phase_2 Ends here --->