<div id="FDEntrytab" class="tab active">
	<div class="customer-table">
		<table id="FDentry-price-table" class="table-lightblue-head">
			<tbody>
				<tr>
					<td style="width:20%;">Plan Name:</td>
					<td style="width:30%;" class="info-td">
						<!-- For Azure ID 13321: Special Characters taken care of in the Company and Plan name -->
						<input type="text" name="PlanName" value="<?= htmlentities($fields["PlanName"]) ?>">
					</td>
					<td style="width:20%;">Advisor Name: </td>
					<td style="width:30%;" class="info-td">
						<input type="text" name="AN" value="<?= html_entity_decode(htmlentities($fields["AN"])) ?>">
					</td>
				</tr>
				<tr>
					<td>Date Prepared:</td>
					<td class="info-td">
						<input type="text" name="DatePrepared" data-dojo-type="dijit.form.DateTextBox" value="<?= count($blocks) == 0 ? date("Y-m-d") : date('Y-m-d', strtotime($fields['DatePrepared'])); ?>">
					</td>
					<td>Company Name:</td>
					<td class="info-td">
						<!-- For Azure ID 13321: Special Characters taken care of in the Company and Plan name -->
						<input type="text" name="Client" value="<?= html_entity_decode(htmlentities($fields["Client"])) ?>">
					</td>
				</tr>
				<tr>
					<td>Participants w/ Balance: </td>
					<td class="info-td">
						<input type="text" name="PB" value="<?= htmlentities($fields['PB']); ?>" onblur="updateTickerFD();" onchange="updateTickerFD()" readonly="readonly">
					</td>
					<td>Street Address: </td>
					<td class="info-td">
						<input type="text" name="SN" value="<?= htmlentities($fields['SN']); ?>">
					</td>
				</tr>
				<tr>
					<td>Plan Assets: </td>
					<td class="info-td">
						<input type="text" name="PA" value="<?= htmlentities($fields['PA']); ?>" onblur="updateTickerFD();num_format(this);" onchange="updateTickerFD()" readonly="reaodnly" />
					</td>
					<td>City, State, Zip:</td>
					<td class="info-td">
						<input type="text" name="CSZ" value="<?= htmlentities($fields['CSZ']); ?>" />
					</td>
				</tr>
				<tr>
					<td>Fund List Name: </td>
					<td class="info-td">
						<select name="FLN" onchange="ShowInput(this);">
							<option value=""> Please Select </option>
							<?php
							foreach ($fundListArray as $val) {
								$sel = '';
								if ($fields['FLN'] == $val) {
									$sel = 'selected';
								}
								echo '<option value="' . $val . '" ' . $sel . '>' . $val . '</option>';
							}
							?>
						</select>
					</td>
					<!-----------21835 EASE -------->
					<?php if ($easeFee>0 && in_array("EASE", $additionalServices)) { ?>
						<td>Enhanced Administrative Service Experience Fee (Optional):</td>
							<td class="info-td"><input type="text" name="EnhancedAdminServiceExpFee" readonly="readonly" value="<?= "$" . number_format($easeFee,2) ?>" onchange="ModelTabCalculation();" onblur="ModelTabCalculation();num_format(this);">
						</td>
					<?php } else { ?>
						<td></td>
						<td>
							<input type="hidden" name="EnhancedAdminServiceExpFee" value="0" onchange="ModelTabCalculation();" onblur="ModelTabCalculation();num_format(this);">
						</td>
					<?php } ?>
				</tr>
			</tbody>
		</table>
		<br />
		<table class="table-lightblue-head">
			<thead>
				<tr>
					<th class="align-left" colspan="4">General</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="width:20%;">Referenced Proposal</td>
					<td style="width:30%;" class="info-td">
						<select name="ReferencedProposal" onchange="checkReferProposalValue();">
							<option value="1" <?= ($fields['ReferencedProposal'] == 'Yes' ? "selected" : ''); ?>>Yes</option>
							<option value="0" <?= ($fields['ReferencedProposal'] == 'No' ? "selected" : ''); ?>>No</option>
						</select>
					</td>
					<td style="width:20%;">Who Referenced the Proposal?</td>
					<td style="width:30%;" class="info-td input-required">
						<input type="text" name="WhoReferencedProposal" style="<?= $fields["ReferencedProposal"] == 'No' ? 'background-color: gray;' : '' ?>" <?= $fields["ReferencedProposal"] == "No" ? "readonly='readonly'" : "" ?> value="<?= $fields["ReferencedProposal"] == "No" ? "" : $fields['WhoReferencedProposal']; ?>" placeholder="Who Referenced the Proposal?" />
					</td>
				</tr>
				<tr>
					<td style="width:20%;">Num of Proposals Requested:</td>
					<td style="width:30%;" class="info-td">
						<input type="text" name="asc_numproposalsrequested" value="<?= ($fields["asc_numproposalsrequested"] > 0 ? html_entity_decode(htmlentities($fields["asc_numproposalsrequested"])) : 0); ?>">
					</td>
					<td style="width:20%;">Setup Fee Waived:</td>
					<td style="width:30%;" class="info-td">
						<select name="asc_setupfeewaivedcode">
							<option value="1" <?= ($fields['asc_setupfeewaivedcode'] == '1' ? "selected" : ''); ?>>Not Waived</option>
							<option value="3" <?= ($fields['asc_setupfeewaivedcode'] == '3' ? "selected" : ''); ?>>Waived</option>
							<option value="6" <?= ($fields['asc_setupfeewaivedcode'] == '6' ? "selected" : ''); ?>>TBD</option>
							<option value="2" <?= ($fields['asc_setupfeewaivedcode'] == '2' ? "selected" : ''); ?>>Waived Half</option>
							<option value="4" <?= ($fields['asc_setupfeewaivedcode'] == '4' ? "selected" : ''); ?>>Included</option>
							<option value="5" <?= ($fields['asc_setupfeewaivedcode'] == '5' ? "selected" : ''); ?>>N/A</option>
						</select>
					</td>
				</tr>
				<tr>
					<td style="width:20%;">Proposal Due Date:</td>
					<td style="width:30%;" class="info-td">
						<input type="hidden" name="ShowSalessprint" value="" />
						<input type="text" name="asc_proposalduedate" data-dojo-type="dijit.form.DateTextBox" value="<?= count($blocks) == 0 ? date("Y-m-d", strtotime("+1 Weekday")) : $fields["asc_proposalduedate"]; ?>">
					</td>
					<!------------ Enroll Support Azure US ID 21835 Task Id 23546 ------->
					<?php
						if(in_array("EnrollSupport",$additionalServices)){
					?>
						<td style="width:20%;">Number of Enrollment Support Days:</td>
						<td style="width:30%;" class="info-td">
							<input type="text" name="numdaysEnrollSupport" value="<?= count($blocks)>0 ? $fields["numdaysEnrollSupport"] : "1" ?>" onchange="ModelTabCalculation();" />
							<input type="hidden" name="EnrollSupportFee" value="<?= $enrollSupportFee ?>"/>
					</td>
					<?php } ?>
				</tr>
				<!---------- HTML Removed from Common Code ------------->
				<!----<td style="width:20%;">Est. Annual Investment Advisor Fee</td>
					<td style="width:30%;" class="info-td"><input type="text" name="EstAnnualInvestmentAdvisorFee" onblur="num_format(this);" value="<?= $fields['EstAnnualInvestmentAdvisorFee']; ?>" placeholder="$105.00"></td>
					<td style="width:20%;">Conversion/Setup Fee:</td>
					<td style="width:30%;" class="info-td">
						<input type="text" name="asc_conversionsetupfee" value="<?= html_entity_decode(htmlentities($fields["asc_conversionsetupfee"])); ?>">
					</td>
						<td style="width:20%;">Mesirow Rate</td>
					<td style="width:30%;" class="info-td"><input type="number" name="MesirowRate" value="<?= $fields['MesirowRate']; ?>" onKeyPress="handleNumKey(event)" placeholder="7"></td>
					<td style="width:20%;">Est. Additional Fees Due</td>
					<td style="width:30%;" class="info-td"><input type="text" onblur="num_format(this);" name="EstAdditionalFeesDue" value="<?= $fields['estAdditionalFeesDue']; ?>" placeholder="$75.00" onblur="num_format(this);" /></td>
					<td style="width:20%;">Advisor BPS</td>
					<td style="width:30%;" class="info-td"><input type="text" name="AdvisorBSP" value="<?= $fields['AdvisorBSP']; ?>" placeholder="Advisor BPS"></td>
					<td style="width:20%;">Investment Fiduciary Services Annual</td>
					<td style="width:30%;" class="info-td"><input type="text" name="InvestmentFiduciaryServicesAnnual" value="<?= $fields['InvestmentFiduciaryServicesAnnual']; ?>" placeholder="Investment Fiduciary Services Annual"></td>
					<td style="width:20%;">Est. Admin Fee Credit</td>
					<td style="width:30%;" class="info-td"><input type="text" name="EstAdminFeeCredit" onblur="num_format(this);" value="<?= $fields['EstAdminFeeCredit']; ?>" placeholder="$65.00"></td>
					<td style="width:20%;">Client City</td>
					<td style="width:30%;" class="info-td"><input type="text" name="ClientCity" value="<?= $fields['ClientCity']; ?>" placeholder="Client City"></td>
					<td style="width:20%;">Advisor BPS Tier 2</td>
						<td style="width:30%;" class="info-td"><input type="text" name="AdvisorBPSTier2" value="<?= $fields['AdvisorBPSTier2']; ?>" placeholder="Advisor BPS Tier 2"></td>
					<td style="width:20%;">Client State</td>
					<td style="width:30%;" class="info-td"><input type="text" name="asc_clientstate" value="<?= $fields['asc_clientstate']; ?>" /></td>---->
			</tbody>
		</table>
		<br />
		<br />
		<table cellpadding="0" cellspacing="0" class="table-lightblue-head">
			<thead>
				<tr>
					<th></th>
					<th class="info-td"><b>Flat Dollar</b></th>
					<th><b>Percentage</b></th>
				</tr>
			</thead>
			<tbody>
				<?php
				if(in_array("InvFiduciaryServices",$additionalServices)){
					$disableInvFidServ = true;
				?>
					<tr>
						<td>Investment Fiduciary Services:</td>
						<td class="info-td">
							<input type="text" name="InvFiduciaryServices" value="<?= $fields['InvFiduciaryServices'] ?>" onblur="ModelTabCalculation(); num_format(this);" <?php if(isset($fields['disableInvFidServInputName']) && $fields['disableInvFidServInputName'] == "InvFiduciaryServices") { ?> readonly="readonly" <?php } ?> <?php if($disableInvFidServ) ?> onchange="disableInvFiduciaryServices(this);" />
						</td>
						<td class="info-td">
							<input type="text" name="InvFiduciaryServicesPercentage" value="<?= $fields['InvFiduciaryServicesPercentage'] ?>"  onblur="ModelTabCalculation();" <?php if(isset($fields['disableInvFidServInputName']) && $fields['disableInvFidServInputName'] == "InvFiduciaryServicesPercentage") { ?> readonly="readonly" <?php } ?> <?php if($disableInvFidServ) ?> onchange="disableInvFiduciaryServices(this);" />
						</td>
					</tr>
					<input type="hidden" id="disableInvFidServInputName" name="disableInvFidServInputName" value="<?= $fields['disableInvFidServInputName'] ?>">
				<?php 
				}
				$disableFeeBasedInputEnabled = false;
				if (in_array($fields["PricingTools"], ['AFBFSStartUpAdvFee', 'AFBRKOStartUpAdvFee', 'MS338FSStartUp','MS338RKOStartUp','RJFBFSStartUp','RJFBRKOStartUp'])) {
					$disableFeeBasedInputEnabled = true;
				}else if(($fields["PartnerInvestLineup"]=="AscLPLCore" || $fields["PartnerInvestLineup"]=="AscLPLExpanded" || $fields["PartnerInvestLineup"]=="AscLPLPassive") && ($fields["InstallType"]=="860530002")){
					$disableFeeBasedInputEnabled = true;
				}
				//////////////// Goldman Startup check ///////////
				else if($productFamily == "Goldman" && $fields["InstallType"]=="860530002"){
					$disableFeeBasedInputEnabled = true;
				}
				if (in_array('AdvisorFeeFlatDollar', $fieldsShow)) {
				?>
					<tr>
						<td>Advisor Fee (flat dollar fee)</td>
						<td class="info-td">
							<input type="text" name="AdvF2" value="<?= htmlentities($fields['AdvF2']); ?>" <?php if ($disableFeeBasedInputEnabled) { ?> onchange="disableFeeBasedInput(this)" <?php } ?> onkeyup="ModelTabCalculation()" onblur="ModelTabCalculation(); num_format(this)" <?php if (in_array('AdvisorFeeFlatDollar', $disableFields) || (isset($fields['disableFeeBasedInputName']) && $fields['disableFeeBasedInputName'] == "AdvF2")) { ?>readonly="readonly" <?php } ?> />
						</td>
						<td class="info-td">
							<input type="text" name="AdvF" value="<?= htmlentities($fields['AdvF']); ?>" onkeyup="ModelTabCalculation()" onblur="ModelTabCalculation();" <?php if (in_array('AdvisorFeeFlatDollarPercentage', $disableFields)) { ?>readonly="readonly" <?php } ?>>
						</td>
					</tr>
					<input type="hidden" id="disableFeeBasedInputName" name="disableFeeBasedInputName" value="<?= $fields['disableFeeBasedInputName'] ?>">
				<?php }
				///////// User Story 35291: Ascensus | 3(16) Fiduciary Services /////////////
				if ($fields["FiduciaryServices316"]!="no316") {
					if($fields["FiduciaryServices316"]=="asc316"){
						$readonly316Fid	=	"readonly='readonly'";
					}
					else{
						$readonly316Fid	=	"";
					}
				?>
				<tr>
					<td>3(16) Administrative Fiduciary Services</td>
					<td class="info-td">
						<input type="text" name="FiduciaryServices316Value" <?= $readonly316Fid ?> value="<?= count($blocks)>0 ? ($fields["FiduciaryServices316"]!=$fields["SelectedOldFiduciaryServices"] ? ($fields["FiduciaryServices316"]=="asc316" ? "$".number_format($fiduciaryServiceFee,2) : "")  : $fields["FiduciaryServices316Value"] ) : ($fields["FiduciaryServices316"]=="asc316" ? "$".number_format($fiduciaryServiceFee,2) : "") ?>" onkeyup="ModelTabCalculation()" onblur="ModelTabCalculation(); num_format(this)" />
						<input type="hidden" name="SelectedOldFiduciaryServices" value="<?= $fields["SelectedOldFiduciaryServices"] ?>" />
					</td>
					<td class="info-td">
						<input type="text" name="FiduciaryServices316ValuePerc" readonly="readonly" value="<?= htmlentities($fields['FiduciaryServices316ValuePerc']); ?>" />
					</td>
				</tr>
			<?php
			}
			if (in_array('AdvisorFeeAsset', $fieldsShow)) { ?>
					<tr>
						<td>Advisor Fee (asset based fee)</td>
						<td class="info-td">
							<input type="text" name="AF2" value="<?= htmlentities($fields['AF2']) ?>" onkeyup="ModelTabCalculation()" onblur="ModelTabCalculation(); num_format(this)" <?php if (in_array('AdvisorFeeAsset', $disableFields)) { ?>readonly="readonly" <?php } ?>>
						</td>
						<td class="info-td">
							<input type="text" name="AF" value="<?= htmlentities($fields['AF']); ?>" <?php if ($disableFeeBasedInputEnabled) { ?> onchange="disableFeeBasedInput(this)" <?php } ?> onkeyup="ModelTabCalculation()" onblur="ModelTabCalculation()" <?php if (in_array('AdvisorFeeAssetPercentage', $disableFields) || (isset($fields['disableFeeBasedInputName']) && $fields['disableFeeBasedInputName'] == "AF")) { ?>readonly="readonly" <?php } ?>>
						</td>
					</tr>
				<?php } ?>
				 <?php if (in_array('TPAFees', $fieldsShow)) { ////////////// Azure ID 21835 Excel ClientPoint Additional Services Testing Log 3-Jan-2022 ////// ?>
					<!----- This is for Goldman User Story 21835 and 2484554 ----->
						<tr>
							<td>TPA Fees</td>
							<td class="info-td"><input type="text" name="TPAFees" onchange="disableTPAFee(this);" onblur="num_format(this);" value="<?= $fields['TPAFees']; ?>" <?php if (isset($fields["disableTPAFeeInputName"]) && $fields["disableTPAFeeInputName"]=="TPAFees") { ?> readonly="readonly" <?php } ?> /></td>
							<td class="info-td">
								<input type="text" name="TPAFeesPerc" onchange="disableTPAFee(this);" onblur="ModelTabCalculation();" value="<?= $fields['TPAFeesPerc']; ?>" <?php if (isset($fields["disableTPAFeeInputName"]) && $fields["disableTPAFeeInputName"]=="TPAFeesPerc") { ?> readonly="readonly" <?php } ?> />
								<input type="hidden" id="disableTPAFeeInputName" name="disableTPAFeeInputName" value="<?= $fields['disableTPAFeeInputName'] ?>">
							</td>
						</tr>
					<?php
					}
					?>

				<?php if (in_array('AncillaryFee', $fieldsShow)) { ?>
					<tr>
						<td>Ancillary Fee (flat dollar fee)</td>
						<td class="info-td">
							<input type="text" name="AncillaryFeeEntery2" value="<?= count($blocks) > 0 ? htmlentities($fields['AncillaryFeeEntery2']) : "$0.00" ?>" onkeyup="ModelTabCalculation()" onblur="ModelTabCalculation(); num_format(this)" <?php if (in_array('AncillaryFee', $disableFields)) { ?>readonly="readonly" <?php } ?>>
						</td>
						<td class="info-td">
							<input type="text" name="AncillaryFeeEntery" value="<?= count($blocks) > 0 ? htmlentities($fields['AncillaryFeeEntery']) : "0.00" ?>" onkeyup="ModelTabCalculation()" onblur="ModelTabCalculation();" <?php if (in_array('AncillaryFeePercentage', $disableFields)) { ?>readonly="readonly" <?php } ?>>
						</td>
					</tr>
				<?php } ?>

				<?php if (in_array('AdvisorFeeBSP', $fieldsShow)) { ?>
					<tr>
						<td>Advisor Fee BSP Tier 1</td>
						<td class="info-td">
							<input type="text" name="AdvisorBPSTier2" value="<?= $fields['AdvisorBPSTier2']; ?>" <?php if (in_array('AdvisorFeeBSP', $disableFields)) { ?>readonly="readonly" <?php } ?>>
						</td>
						<td class="info-td">
							<input type="text" name="AdvisorBPSTier2Pr" value="<?= $fields['AdvisorBPSTier2Pr']; ?>" <?php if (in_array('AdvisorFeeBSPPercentage', $disableFields)) { ?>readonly="readonly" <?php } ?>>
						</td>
					</tr>
				<?php } ?>

				<?php if (in_array('FASDiscretionaryAmount', $fieldsShow)) { ?>
					<tr>
						<td>FAS Discretionary Fee 1</td>
						<td class="info-td">
							<input type="text" name="FASDiscretionaryAmount" value="<?= htmlentities($fields['FASDiscretionaryAmount']); ?>" onchange="disableInputFieldsML(this);" onkeyup="disableInputFieldsML(this);" onblur="num_format(this);" <?php if (in_array('FASDiscretionaryAmount', $disableFields) || (isset($fields["FASDisableValue"]) && $fields['FASDisableValue'] == "FASDiscretionaryAmount")) { ?>readonly="readonly" tabindex="-1" <?php } ?> />
						</td>
						<td class="info-td">
							<input type="text" name="FASDiscretionaryPercentage" value="<?= htmlentities($fields['FASDiscretionaryPercentage']); ?>" onchange="disableInputFieldsML(this);" onkeyup="disableInputFieldsML(this);" onblur="converToPercentage(this);" <?php if (in_array('FASDiscretionaryPercentage', $disableFields) || (isset($fields["FASDisableValue"]) && $fields['FASDisableValue'] == "FASDiscretionaryPercentage")) { ?>readonly="readonly" tabindex="-1" <?php } ?>>
							<input type="hidden" name="FASDisableValue" value="<?= count($blocks)>0 ? $fields["FASDisableValue"] : "" ?>" />
						</td>
					</tr>
				<?php } ?>

				<?php if (in_array('FiduciaryAdvSF', $fieldsShow)) { ?>
					<tr>
						<td>Fiduciary Advisory Services Fee</td>
						<td class="info-td">
							<input type="text" name="FiduciaryAdvSF" value="<?= htmlentities($fields['FiduciaryAdvSF']); ?>" onchange="disableInputFieldsML(this);" onkeyup="disableInputFieldsML(this);" onblur="num_format(this);" <?php if (in_array('FiduciaryAdvSF', $disableFields) || (isset($fields["FiduciaryDisableValue"]) && $fields['FiduciaryDisableValue'] == "FiduciaryAdvSF")) { ?> readonly="readonly" tabindex="-1" tabindex="-1" <?php } ?> />
						</td>
						<td class="info-td">
							<input type="text" name="FiduciaryAdvSFPercentage" value="<?= htmlentities($fields['FiduciaryAdvSFPercentage']); ?>" onchange="disableInputFieldsML(this);" onkeyup="disableInputFieldsML(this);" onblur="converToPercentage(this);" <?php if (in_array('FiduciaryAdvSFPercentage', $disableFields) || (isset($fields["FiduciaryDisableValue"]) && $fields['FiduciaryDisableValue'] == "FiduciaryAdvSFPercentage")) { ?> readonly="readonly" tabindex="-1" <?php } ?>>
							<input type="hidden" name="FiduciaryDisableValue" value="<?= count($blocks)>0 ? $fields["FiduciaryDisableValue"] : "" ?>" />
						</td>
					</tr>
				<?php } ?>

				<?php if (in_array('EduPlanServiceAmount', $fieldsShow)) { ?>
					<tr>
						<td>Education and Plan Services 1</td>
						<td class="info-td">
							<input type="text" name="EduPlanServiceAmount" value="<?= htmlentities($fields['EduPlanServiceAmount']); ?>" onchange="disableInputFieldsML(this);" onkeyup="disableInputFieldsML(this);" onblur="disableInputFieldsML(this);num_format(this);" <?php if (in_array('EduPlanServiceAmount', $disableFields) || (isset($fields["EducationPlanDisableValue"]) && $fields['EducationPlanDisableValue'] == "EduPlanServiceAmount")) { ?> readonly="readonly" tabindex="-1" <?php } ?> />
						</td>
						<td class="info-td">
							<input type="text" name="EduPlanServicePercentage" value="<?= htmlentities($fields['EduPlanServicePercentage']); ?>" onchange="disableInputFieldsML(this);" onkeyup="disableInputFieldsML(this);" onblur="converToPercentage(this);disableInputFieldsML(this);" <?php if (in_array('EduPlanServicePercentage', $disableFields) || (isset($fields["EducationPlanDisableValue"]) && $fields['EducationPlanDisableValue'] == "EduPlanServicePercentage")) { ?> readonly="readonly" tabindex="-1" <?php } ?>>
							<input type="hidden" name="EducationPlanDisableValue" value="<?= count($blocks)>0 ? $fields["EducationPlanDisableValue"] : "" ?>" />
						</td>
					</tr>
				<?php } ?>
				<?php if (in_array('AdminFeeCreditAmount', $fieldsShow)) { ?>
					<tr>
						<td>Administrative Fee Credit Account (AFCA) - offset</td>
						<td class="info-td">
							<input type="text" name="AdminFeeCreditAmount" value="<?= htmlentities($fields['AdminFeeCreditAmount']); ?>" onkeyup="ModelTabCalculation()" onblur="ModelTabCalculation(); num_format(this)" <?php if (in_array('AdminFeeCreditAmount', $disableFields)) { ?>readonly="readonly" <?php } ?> />
						</td>
						<td class="info-td">
							<input type="text" name="AdminFeeCreditPercentange" value="<?= htmlentities($fields['AdminFeeCreditPercentange']); ?>" onkeyup="ModelTabCalculation()" onblur="converToPercentage(this);ModelTabCalculation();" <?php if (in_array('AdminFeeCreditPercentange', $disableFields)) { ?>readonly="readonly" <?php } ?>>
						</td>
					</tr>
				<?php } ?>

				<?php if (in_array('AnnualFeeDueAmount', $fieldsShow)) { ?>
					<tr>
						<td>Annual fees due or (excess dollars in AFCA)</td>
						<td class="info-td">
							<input type="text" name="AnnualFeeDueAmount" value="<?= htmlentities($fields['AnnualFeeDueAmount']); ?>" onkeyup="ModelTabCalculation()" onblur="ModelTabCalculation(); num_format(this)" <?php if (in_array('AnnualFeeDueAmount', $disableFields)) { ?>readonly="readonly" <?php } ?> />
						</td>
						<td class="info-td">
							<input type="text" name="AnnualFeeDuePercentage" value="<?= htmlentities($fields['AnnualFeeDuePercentage']); ?>" onkeyup="ModelTabCalculation()" onblur="converToPercentage(this);ModelTabCalculation();" <?php if (in_array('AnnualFeeDuePercentage', $disableFields)) { ?>readonly="readonly" <?php } ?>>
						</td>
					</tr>
				<?php } ?>

				<?php if (in_array('EstimateNetFundExpenseAmount', $fieldsShow)) { ?>
					<tr>
						<td>Estimated Net fund operating expense</td>
						<td class="info-td">
							<input type="text" name="EstimateNetFundExpenseAmount" value="<?= htmlentities($fields['EstimateNetFundExpenseAmount']); ?>" onkeyup="ModelTabCalculation()" onblur="ModelTabCalculation(); num_format(this)" <?php if (in_array('EstimateNetFundExpenseAmount', $disableFields)) { ?>readonly="readonly" <?php } ?> />
						</td>
						<td class="info-td">
							<input type="text" name="EstimateNetFundExpensePercentage" value="<?= htmlentities($fields['EstimateNetFundExpensePercentage']); ?>" onkeyup="ModelTabCalculation()" onblur="converToPercentage(this);ModelTabCalculation();" <?php if (in_array('EstimateNetFundExpensePercentage', $disableFields)) { ?>readonly="readonly" <?php } ?>>
						</td>
					</tr>
				<?php } ?>
				<tr>
					<td>RK Fees</td>
					<td class="info-td">
						<input type="text" name="RKFEE" value="<?= count($blocks) > 0 ? htmlentities($fields['RKFEE']) : "$0.00" ?>" readonly="readonly" />
					</td>
					<td></td>
				</tr>
				<?php
				if($productFamily=="Goldman"){
				?>
					<tr>
						<td>Rate for Over 1001+ Participants</td>
						<td class="info-td">
							<input type="text" placeholder="Enter the rate for the over 1000 participants" name="RateOneThousandPartic" value="<?= count($blocks) > 0 ? htmlentities($fields['RateOneThousandPartic']) : "" ?>"  onblur="updateTickerFD();num_format(this);" />
						</td>
						<td></td>
					</tr>
				<?php } ?>
				<tr>
					<td>Pro-rata Asset Spread Value:</td>
					<td class="info-td">
						<input type="text" name="PRAS" value="<?= htmlentities($fields['PRAS']) ?>" readonly="readonly" />
					</td>
					<td>
						<button class="form-button main_green" type="button" onclick="equalDistributeAssets();">Update Assets Per Fund</button>
					</td>
				</tr>
				<tr>
					<td>Fund Tickers</td>
					<td class="info-td" colspan="3">
						<p><textarea name="multiple_tickers" style="height:100px;"><?php echo $fields['multiple_tickers']; ?></textarea></p>
						<p>
							<button class="form-button main_green" type="button" onclick="Add_Multiple_Ticker_Tool();">Add Multiple Tickers</button>
							<button class="form-button main_green" type="button" onclick="$('textarea[name=multiple_tickers]').val('');">Reset</button>
						</p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
		<input type='hidden' name='asc_plantypecode' required='false' value="<?= $asc_plantypecode ?>" />
		<input type='hidden' name='asc_plantypecode' required='false' value="<?= $asc_plantypecode ?>" />
		<input type='hidden' name='asc_name' required='false' value="<?= $asc_name ?>" />
		<input type='hidden' name='asc_ParentOpportunity' required='false' value="<?= $asc_ParentOpportunity ?>" />
		<input type='hidden' name='asc_Productid' required='false' value="<?= $asc_Productid ?>" />
		<input type='hidden' name='asc_businessunitname' required='false' value="<?= $asc_businessunitname ?>" />
		<input type='hidden' name='asc_nextfollowupdate' required='false' value="<?= $asc_nextfollowupdate ?>" />
		<input type='hidden' name='service' value="<?= $fields['service'] ?>" />
		<input type='hidden' name='asc_ownerid' required='false' value="<?= $oppOwnerId ?>" />
		<input type='hidden' name='selAdditionalServices' value="<?= $selAdditionalServices ?>" />
		<!------------ Azure Id 10594 --------------------->
		<input type='hidden' name='asc_clientpointurl' required='false' value="<?= $asc_clientpointurl ?>" />
		<input type="hidden" name="asc_defaultfundlist" value="<?= count($blocks)>0 ? $fields["asc_defaultfundlist"] : "" ?>" />
		<!------------ Azure Id 10594 --------------------->
		<!--------------- Annual Notice Delivery User Story 30947 Task Id 31170 ---------->
		<input type="hidden" name="FTOpportunityIDCRM" value="<?= (!empty($fields["FTOpportunityID"]) && isset($fields["FTOpportunityID"])) ? $fields["FTOpportunityID"] : "" ?>" />
		<table id="FundEntry-tab-table" class="table-blue-head">
			<thead>
				<tr>
					<th style="width:5%">Action</th>
					<th style="width:10%;">Fund Ticker</th>
					<th style="width:10%;">Fund Name</th>
					<th style="width:10%;">Assets per fund</th>
					<th style="width:8%;" class="">Exp Ratio</th>
					<th style="width:5%;" class="hide">Fund Expense</th>
					<th style="width:8%;" class="">12b-1's</th>
					<th style="width:10%;" class="">Sub-TA</th>
					<th style="width:8%;" class="">Sub-TA PP Amt</th>
					<th style="width:5%;" class="hide">12b-1 ($)</th>
					<th style="width:5%;" class="hide">Sub TA ($)</th>
					<th style="width:5%;" class="hide">Exp ratio cost</th>
					<th style="width:8%;" class="">Minimums</th>
					<th style="width:15%;">Comments</th>
					<th style="width:6%;" class="hide">Special Criteria</th>
					<th style="width:4%;" class="hide">Per Pos Amount</th>
					<th style="width:5%;" class="hide">Default PP Expense</th>
					<th style="width:5%;" class="hide">25 bps</th>
					<th style="width:5%;" class="hide">50 bps</th>
					<th style="width:5%;" class="hide">75 bps</th>
					<th style="width:5%;" class="hide">100 bps</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if (count($blocks) > 0) {
					foreach ($blocks['fdEntry'] as $value) { ?>
						<tr class="templates">
							<td class="td-color">
								<div class="add-remove-sign">
									<div class="add-arrow" onclick="addNewRow(this)"><i class="material-icons">add_circle</i></div>
									<div class="remove-arrow" onclick="removeRow(this,'templates');updateTickerFD();"><i class="material-icons">remove_circle</i></div>
								</div>
							</td>
							<td>
								<input type="text" required name="ticker-#p#-fdEntry[]" value="<?= $value["ticker"] ?>" onchange="checkDuplicate(this);" />
								<input type="hidden" name="tickerHidden-#p#-fdEntry[]" value="<?= $value["tickerHidden"] ?>" />
							</td>
							<td><input type="text" required name="fund_name-#p#-fdEntry[]" value="<?= $value["fund_name"] ?>" onchange="displayTabCal();" /></td>
							<td><input type="text" name="assestPerFund-#p#-fdEntry[]" value="<?= $value["assestPerFund"] ?>" onchange="updateTickerFD();" onblur="updateTickerFD();num_format(this);" /></td>
							<td class=""><input type="text" name="net_ratio-#p#-fdEntry[]" value="<?= $value["net_ratio"] ?>" required onchange="updateCurrentTicker(this);" /></td>
							<td class="hide"><input type="text" name="fund_expense-#p#-fdEntry[]" value="<?= $value["fund_expense"] ?>" readonly="readonly" /></td>
							<td class=""><input type="text" name="12b_1C-#p#-fdEntry[]" value="<?= $value["12b_1C"] ?>" required onchange="updateCurrentTicker(this);" /></td>
							<td class=""><input type="text" name="subTARate-#p#-fdEntry[]" value="<?= $value["subTARate"] ?>" onchange="updateCurrentTicker(this);" required /></td>
							<td class="hide"><input type="text" name="12b1-#p#-fdEntry[]" value="<?= $value["12b1"] ?>" readonly="readonly" /></td>
							<td class="hide"><input type="text" name="subTARateDollar-#p#-fdEntry[]" value="<?= $value["subTARateDollar"] ?>" readonly="readonly" /></td>
							<td><input type="text" name="subTAPPAmt-#p#-fdEntry[]" value="<?= $value["subTAPPAmt"] ?>" onblur="num_format(this);" /></td>
							<td class="hide"><input type="text" name="expRationCost-#p#-fdEntry[]" value="<?= $value["expRationCost"] ?>" readonly="readonly" /></td>
							<td class=""><input type="text" name="Minimum-#p#-fdEntry[]" onchange="displayTabCal();" value="<?= $value["Minimum"] ?>" /></td>

							<td>
								<textarea name="Comments-#p#-fdEntry[]" onchange="updateChangeComments(this);"><?= $value["Comments"] ?></textarea>
							</td>
							<td class="hide"><input type="text" name="specialCreterial-#p#-fdEntry[]" value="<?= $value["specialCreterial"] ?>" readonly="readonly" /></td>
							<td class="hide"><input type="text" name="perPostAmount-#p#-fdEntry[]" value="<?= $value["perPostAmount"] ?>" readonly="readonly" /></td>
							<td class="hide"><input type="text" name="defaultPPExpense-#p#-fdEntry[]" value="<?= $value["defaultPPExpense"] ?>" readonly="readonly" /></td>
							<td class="hide"><input type="text" name="25bps-#p#-fdEntry[]" value="<?= $value["25bps"] ?>" /></td>
							<td class="hide"><input type="text" name="50bps-#p#-fdEntry[]" value="<?= $value["50bps"] ?>" /></td>
							<td class="hide"><input type="text" name="75bps-#p#-fdEntry[]" value="<?= $value["75bps"] ?>" /></td>
							<td class="hide"><input type="text" name="100bps-#p#-fdEntry[]" value="<?= $value["100bps"] ?>" /></td>
						</tr>
					<?php }
					echo "<script>formOnLoads();</script>";
				} else {
					foreach ($funds as $value) {
						// $value["12b1"]               =   getNumberValue($value["assestPerFund"]) * getNumberValue($value["12b_1C"]);
						// $value["subTARateDollar"]    =   getNumberValue($value["assestPerFund"]) * getNumberValue($value["subTARate"]);
						// $value["expRationCost"]      =   getNumberValue($value["assestPerFund"]) * getNumberValue($value["net_ratio"]);
						// $value["defaultPPExpense"]   =   (((getNumberValue($value["assestPerFund"]) / getNumberValue($value["GrandTotalAssets"])) * getNumberValue($value["GrandTotalAssets"])) / getNumberValue($fields["ParticipantBalance"]));
					?>
						<tr class="templates">
							<td class="td-color">
								<div class="add-remove-sign">
									<div class="add-arrow" onclick="addNewRow(this)"><i class="material-icons">add_circle</i></div>
									<div class="remove-arrow" onclick="removeRow(this,'templates');updateTickerFD();"><i class="material-icons">remove_circle</i></div>
								</div>
							</td>
							<td><input type="text" required name="ticker-#p#-fdEntry[]" value="<?= $value->asc_ticker; ?>" onchange="checkDuplicate(this);" />
								<input type="hidden" name="tickerHidden-#p#-fdEntry[]" value="<?= $value->asc_ticker; ?>" />
							</td>
							<td><input type="text" name="fund_name-#p#-fdEntry[]" value="<?= $value->asc_fund_legal_name; ?>" required /></td>

							<td class=""><input type="text" name="net_ratio-#p#-fdEntry[]" value="<?= $value->asc_net_operating_expense_pct; ?>" required /></td>
							<td class="hide"><input type="text" name="fund_expense-#p#-fdEntry[]" value="" readonly="readonly" /></td>
							<td class=""><input type="text" name="12b_1C-#p#-fdEntry[]" value="<?= $value->asc_actual_12b1; ?>" required /></td>
							<td class=""><input type="text" name="subTARate-#p#-fdEntry[]" value="<?= $value->asc_subtarate; ?>" required /></td>
							<td class="hide"><input type="text" name="12b1-#p#-fdEntry[]" value="" readonly="readonly" /></td>
							<td class="hide"><input type="text" name="subTARateDollar-#p#-fdEntry[]" onblur="num_format(this);" value="<?= "$" . number_format($value->asc_subtappamt, 2) ?>" readonly="readonly" /></td>
							<td><input type="text" name="subTAPPAmt-#p#-fdEntry[]" value="<?= "$" . number_format($value->asc_subtappamt, 2) ?>" onblur="num_format(this);" /></td>
							<td class="hide"><input type="text" name="expRationCost-#p#-fdEntry[]" value="" readonly="readonly" /></td>
							<td class=""><input type="text" name="Minimum-#p#-fdEntry[]" onchange="displayTabCal();" onblur="num_format(this);" value="<?= "$" . number_format($value->asc_fundminimuminvestment, 2) ?>" /></td>
							<td><input type="text" name="assestPerFund-#p#-fdEntry[]" value="" onchange="updateTickerFD();" onblur="updateTickerFD();num_format(this);" /></td>
							<td class="">
								<textarea name="Comments-#p#-fdEntry[]" onchange="updateChangeComments(this);"><?= $value->asc_comments; ?></textarea>
							</td>
							<td class="hide"><input type="text" name="specialCreterial-#p#-fdEntry[]" value="" readonly="readonly" /></td>
							<td class="hide"><input type="text" name="perPostAmount-#p#-fdEntry[]" value="" readonly="readonly" /></td>
							<td class="hide"><input type="text" name="defaultPPExpense-#p#-fdEntry[]" value="" readonly="readonly" /></td>
							<td class="hide"><input type="text" name="25bps-#p#-fdEntry[]" value="" /></td>
							<td class="hide"><input type="text" name="50bps-#p#-fdEntry[]" value="" /></td>
							<td class="hide"><input type="text" name="75bps-#p#-fdEntry[]" value="" /></td>
							<td class="hide"><input type="text" name="100bps-#p#-fdEntry[]" value="" /></td>
						</tr>
				<?php
					}
					echo "<script>formOnLoads();</script>";
				} ?>
			<tfoot>
				<td colspan="3"><strong>Grand Total Assets</strong></td>
				<td colspan="7"><input type="text" name="GrandTotalAssets" value="<?= count($blocks) == 0 ? "$0.00" : $fields["GrandTotalAssets"] ?>" readonly="readonly" /></td>
				<td class="hide"></td>
				<td class="hide"><input type="text" name="GrandTotalfund" value="<?= count($blocks) == 0 ? "$0.00" : $fields["GrandTotalFund"] ?>" readonly="readonly" /></td>
				<td class="hide"></td>
				<td class="hide"></td>
				<td class="hide"><input type="text" name="GrandTotalTwelBD" value="<?= count($blocks) == 0 ? "$0.00" : $fields["GrandTotalTwelBD"] ?>" readonly="readonly" /></td>
				<td class="hide"><input type="text" name="GrandTotalsubTARateDollar" value="<?= count($blocks) == 0 ? "$0.00" : $fields["GrandTotalsubTARateDollar"] ?>" readonly="readonly" /></td>
				<td class="hide"><input type="text" name="GrandTotalexpenseDollar" value="<?= count($blocks) == 0 ? "$0.00" : $fields["GrandTotalexpenseDollar"] ?>" readonly="readonly" /></td>
				<td class="hide"></td>
				<td class="hide"></td>
				<td class="hide"></td>
				<td class="hide"></td>
				<td class="hide"></td>
				<td class="hide"></td>
				<td class="hide"></td>
				<td class="hide"></td>
			</tfoot>
			</tbody>
		</table>
		<div class="fund_not_found_error" style="display:none;"></div>
	</div>
	<div class="overlay-loader" style="display:none;">
		<div class="loader"></div>
	</div>