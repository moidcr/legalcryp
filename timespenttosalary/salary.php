<?php
/* Copyright (C) 2011-2019  Alexandre Spangaro      <aspangaro@open-dsi.fr>
 * Copyright (C) 2014-2020  Laurent Destailleur     <eldy@users.sourceforge.net>
 * Copyright (C) 2015       Jean-François Ferry     <jfefe@aternatik.fr>
 * Copyright (C) 2015       Charlie BENKE           <charlie@patas-monkey.com>
 * Copyright (C) 2018-2021  Frédéric France         <frederic.france@netlogic.fr>
 * Copyright (C) 2021       Gauthier VERDOL         <gauthier.verdol@atm-consulting.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *  \file       htdocs/salaries/card.php
 *  \ingroup    salaries
 *  \brief      Page of salaries payments
 */
require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/salaries/class/salary.class.php';
require_once DOL_DOCUMENT_ROOT.'/salaries/class/paymentsalary.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/salaries.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT.'/accountancy/class/accountingjournal.class.php';
if (!empty($conf->projet->enabled)) {
	require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';
}

// Load translation files required by the page
$langs->loadLangs(array("compta", "banks", "bills", "users", "salaries", "hrm", "trips"));
if (!empty($conf->projet->enabled)) {
	$langs->load("projects");
}

$spendu = GETPOST('spendu', 'array');


$id = GETPOSTINT('id');
$ref = GETPOST('ref', 'alpha');
$action = GETPOST('action', 'aZ09');
$typec = GETPOST('typec', 'aZ09');
$cancel = GETPOST('cancel', 'aZ09');
$accountid = GETPOST('accountid', 'int') > 0 ? GETPOST('accountid', 'int') : 0;
$projectid = (GETPOST('projectid', 'int') ? GETPOST('projectid', 'int') : GETPOST('fk_project', 'int'));
$confirm = GETPOST('confirm');
if (GETPOSTISSET('auto_create_paiement') || $action === 'add') {
	$auto_create_paiement = GETPOST("auto_create_paiement", "int");
} else {
	$auto_create_paiement = empty($conf->global->CREATE_NEW_SALARY_WITHOUT_AUTO_PAYMENT);
}

$datep = dol_mktime(12, 0, 0, GETPOST("datepmonth", 'int'), GETPOST("datepday", 'int'), GETPOST("datepyear", 'int'));
$datev = dol_mktime(12, 0, 0, GETPOST("datevmonth", 'int'), GETPOST("datevday", 'int'), GETPOST("datevyear", 'int'));
$datesp = dol_mktime(12, 0, 0, GETPOST("datespmonth", 'int'), GETPOST("datespday", 'int'), GETPOST("datespyear", 'int'));
$dateep = dol_mktime(12, 0, 0, GETPOST("dateepmonth", 'int'), GETPOST("dateepday", 'int'), GETPOST("dateepyear", 'int'));
$label = GETPOST('label', 'alphanohtml');
$fk_user = GETPOSTINT('userid');

$object = new Salary($db);
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extrafields->fetch_name_optionals_label($object->table_element);

// Initialize technical object to manage hooks of page. Note that conf->hooks_modules contains array of hook context
$hookmanager->initHooks(array('salarycard', 'globalcard'));

$object = new Salary($db);
if ($id > 0 || !empty($ref)) {
	$object->fetch($id, $ref);
}

// Security check
$socid = GETPOSTINT('socid');
if ($user->socid) {
	$socid = $user->socid;
}
restrictedArea($user, 'salaries', $object->id, 'salary', '');


/**
 * Actions
 */

if ($cancel) {
	header("Location: list.php");
	exit;
}

// Link to a project
if ($action == 'classin' && $user->rights->banque->modifier) {
	$object->fetch($id);
	$object->setProject($projectid);
}

// set label
if ($action == 'setlabel' && $user->rights->salaries->write) {
	$object->fetch($id);
	$object->label = $label;
	$object->update($user);
}

// Classify paid
if ($action == 'confirm_paid' && $user->rights->salaries->write && $confirm == 'yes') {
	$object->fetch($id);
	$result = $object->set_paid($user);
}

if ($action == 'setfk_user' && $user->rights->salaries->write) {
	$result = $object->fetch($id);
	if ($result > 0) {
		$object->fk_user = $fk_user;
		$object->update($user);
	} else {
		dol_print_error($db);
		exit;
	}
}

if ($action == 'reopen' && $user->rights->salaries->write) {
	$result = $object->fetch($id);
	if ($object->paye) {
		$result = $object->set_unpaid($user);
		if ($result > 0) {
			header('Location: '.$_SERVER["PHP_SELF"].'?id='.$id);
			exit();
		} else {
			setEventMessages($object->error, $object->errors, 'errors');
		}
	}
}

// payment mode
if ($action == 'setmode' && $user->rights->salaries->write) {
	$object->fetch($id);
	$result = $object->setPaymentMethods(GETPOST('mode_reglement_id', 'int'));
	if ($result < 0)
		setEventMessages($object->error, $object->errors, 'errors');
}

// bank account
if ($action == 'setbankaccount' && $user->rights->salaries->write) {
	$object->fetch($id);
	$result = $object->setBankAccount(GETPOST('fk_account', 'int'));
	if ($result < 0) {
		setEventMessages($object->error, $object->errors, 'errors');
	}
}


function CreateSalarySpend($objecto, $uss, $spends)
{
	
	global $datev, $datep, $datesp, $dateep, $user, $projectid, $extrafields, $auto_create_paiement, $db;	
	
	$error = 0;
	
	// 0 = spend id | 1 = user id | 2 = amount spend
	$amount = 0;
	foreach($spends as $spend)
	{
		$amount += $spend[1];
	}
	
	if (empty($datev)) $datev = $datep;
	
	if($amount<=0)
		$error++;
	
	if(empty($error))
	{
		 $type_payment = GETPOST("paymenttype", 'alpha');
		//$amount = price2num(GETPOST("amount", 'alpha'), 'MT', 2);
	
		 $objecto->accountid = GETPOST("accountid", 'int') > 0 ? GETPOST("accountid", "int") : 0;
		 $objecto->fk_user = $uss;
		 $objecto->datev = $datev;
		 $objecto->datep = $datep;
		 $objecto->amount = $amount;
		 $objecto->label = GETPOST("label", 'alphanohtml');
		 $objecto->datesp = $datesp;
		 $objecto->dateep = $dateep;
		 $objecto->note = GETPOST("note", 'restricthtml');
		 $objecto->type_payment = ($type_payment > 0 ? $type_payment : 0);
		 $objecto->fk_user_author = $user->id;
		 $objecto->fk_project = $projectid;
	
		// Set user current salary as ref salary for the payment
		$fuser = new User($db);
		$fuser->fetch(GETPOST("fk_user", "int"));
		$objecto->salary = $fuser->salary;
	/*echo "<pre>";
	
			print_r($objecto);
			exit;*/
			$db->begin();
	
			$ret =  $objecto->create($user);
			if ($ret < 0) $error++;
			if (!empty($auto_create_paiement) && !$error) {
				// Create a line of payments
				$paiement = new PaymentSalary($db);
				$paiement->chid         =  $objecto->id;
				$paiement->datepaye     = $datep;
				$paiement->datev		= $datev;
				$paiement->amounts      = array( $objecto->id=>$amount); // Tableau de montant
				$paiement->paiementtype = $type_payment;
				$paiement->num_payment  = GETPOST("num_payment", 'alphanohtml');
				$paiement->note = GETPOST("note", 'none');
	
				if (!$error) {
					$paymentid = $paiement->create($user, (int) GETPOST('closepaidsalary'));
					if ($paymentid < 0) {
						$error++;
						setEventMessages($paiement->error, null, 'errors');
						$action = 'create';
					}
				}
	
				if (!$error) {
					$result = $paiement->addPaymentToBank($user, 'payment_salary', '(SalaryPayment)', GETPOST('accountid', 'int'), '', '');
					if (!($result > 0)) {
						$error++;
						setEventMessages($paiement->error, null, 'errors');
					}
				}
			}
		}

		if (empty($error)) {
			
			// 0 = spend id | 1 = user id | 2 = amount spend
			foreach($spends as $spend)
			{
			
				
				$sql_ = "INSERT INTO timespenttosalary_reg (";
				$sql_ .= "user_id";
				$sql_ .= ", timespent_id";
				$sql_ .= ", amount";
				$sql_ .= ", salary_id";
				$sql_ .= ") VALUES (";
				$sql_ .= "'".$uss."'";
				$sql_ .= ", '".$spend[0]."'";
				$sql_ .= ", '".$spend[1]."'";
				$sql_ .= ", '".$ret."'";
				$sql_ .= ")";
				//echo $sql_;
				//exit;
				$resql_ = $objecto->db->query($sql_);
			}
			
			$db->commit();
			return $ret;
				
		} else {
			$db->rollback();
			return -1;
		}
	
	
}

function CreateSalaryUser($objecto, $data)
{
	
	global $datev, $datep, $datesp, $dateep, $user, $projectid, $extrafields, $auto_create_paiement, $db;	
	
	$error = 0;
	
	// 0 = user id | 2 = amount salary

	$amount += $data[1];

	
	if (empty($datev)) $datev = $datep;
	
	if($amount<=0)
		$error++;
	
	if(empty($error))
	{
		 $type_payment = GETPOST("paymenttype", 'alpha');
		//$amount = price2num(GETPOST("amount", 'alpha'), 'MT', 2);
	
		 $objecto->accountid = GETPOST("accountid", 'int') > 0 ? GETPOST("accountid", "int") : 0;
		 $objecto->fk_user = $data[0];
		 $objecto->datev = $datev;
		 $objecto->datep = $datep;
		 $objecto->amount = $amount;
		 $objecto->label = GETPOST("label", 'alphanohtml');
		 $objecto->datesp = $datesp;
		 $objecto->dateep = $dateep;
		 $objecto->note = GETPOST("note", 'restricthtml');
		 $objecto->type_payment = ($type_payment > 0 ? $type_payment : 0);
		 $objecto->fk_user_author = $user->id;
		 $objecto->fk_project = $projectid;
	
		// Set user current salary as ref salary for the payment
		$fuser = new User($db);
		$fuser->fetch(GETPOST("fk_user", "int"));
		$objecto->salary = $fuser->salary;
	/*echo "<pre>";
	
			print_r($objecto);
			exit;*/
			$db->begin();
	
			$ret =  $objecto->create($user);
			if ($ret < 0) $error++;
			if (!empty($auto_create_paiement) && !$error) {
				// Create a line of payments
				$paiement = new PaymentSalary($db);
				$paiement->chid         =  $objecto->id;
				$paiement->datepaye     = $datep;
				$paiement->datev		= $datev;
				$paiement->amounts      = array( $objecto->id=>$amount); // Tableau de montant
				$paiement->paiementtype = $type_payment;
				$paiement->num_payment  = GETPOST("num_payment", 'alphanohtml');
				$paiement->note = GETPOST("note", 'none');
	
				if (!$error) {
					$paymentid = $paiement->create($user, (int) GETPOST('closepaidsalary'));
					if ($paymentid < 0) {
						$error++;
						setEventMessages($paiement->error, null, 'errors');
						$action = 'create';
					}
				}
	
				if (!$error) {
					$result = $paiement->addPaymentToBank($user, 'payment_salary', '(SalaryPayment)', GETPOST('accountid', 'int'), '', '');
					if (!($result > 0)) {
						$error++;
						setEventMessages($paiement->error, null, 'errors');
					}
				}
			}
		}

		if (empty($error)) {
			
			// 0 = spend id | 1 = user id | 2 = amount spend
			/*foreach($spends as $spend)
			{
			
				
				$sql_ = "INSERT INTO timespenttosalary_reg (";
				$sql_ .= "user_id";
				$sql_ .= ", timespent_id";
				$sql_ .= ", amount";
				$sql_ .= ", salary_id";
				$sql_ .= ") VALUES (";
				$sql_ .= "'".$uss."'";
				$sql_ .= ", '".$spend[0]."'";
				$sql_ .= ", '".$spend[1]."'";
				$sql_ .= ", '".$ret."'";
				$sql_ .= ")";
				//echo $sql_;
				//exit;
				$resql_ = $objecto->db->query($sql_);
			}*/
			
			$db->commit();
			return $ret;
				
		} else {
			$db->rollback();
			return -1;
		}
	
	
}

if ($action == 'add' && empty($cancel)) {
	
	$error = 0;
	
	$type_payment = GETPOST("paymenttype", 'alpha');
	// Fill array 'array_options' with data from add form
	$ret = $extrafields->setOptionalsFromPost(null,   $object);
	if ($ret < 0) {
		$error++;
	}

	if (!empty($auto_create_paiement) && empty($datep)) {
		setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("DATE_PAIEMENT")), null, 'errors');
		$error++;
	}
	if (empty($datesp) || empty($dateep)) {
		setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Date")), null, 'errors');
		$error++;
	}
	/*if (empty(  $object->fk_user) ||   $object->fk_user < 0) {
		setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Employee")), null, 'errors');
		$error++;
	}*/
	if (!empty($auto_create_paiement) && (empty($type_payment) || $type_payment < 0)) {
		setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("PaymentMode")), null, 'errors');
		$error++;
	}
	/*if (empty(  $object->amount)) {
		setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Amount")), null, 'errors');
		$error++;
	}*/
	if (!empty($conf->banque->enabled) && !empty($auto_create_paiement) && !  $object->accountid > 0) {
		setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("BankAccount")), null, 'errors');
		$error++;
	}

	if (!$error) {
		if($typec == "spend")
		{
			$spendss = array();
			foreach($spendu  as $spend_)
			{
				$spend = explode("|", $spend_);// 0 = spend id | 1 = user id | 2 = amount spend
				
				$spendss[$spend[1]][] = array($spend[0],$spend[2]);
			}
			
			foreach($spendss as $key => $spends)
			{
				$ret = CreateSalarySpend($object, $key, $spends);
			}
		}
		else
		{
			foreach($spendu  as $spend_)
			{
				$spend = explode("|", $spend_);// 0 = user id | 2 = amount salary

				$ret = CreateSalaryUser($object, $spend);
			}
		}
		
		header("Location: ../../salaries/list.php?leftmenu=tax_salary");
		exit;
	}
	else
	{
		$action = 'create';
	}
	

}



/*
 *	View
 */

$title = $langs->trans('Salary')." - ".$langs->trans('Card');
$help_url = "";
llxHeader("", $title, $help_url);

$form = new Form($db);
if (!empty($conf->projet->enabled)) $formproject = new FormProjets($db);

if ($id) {
	$object = new Salary($db);
	$result = $object->fetch($id);
	if ($result <= 0) {
		dol_print_error($db);
		exit;
	}
}

// Create
if ($action == 'create') {
	$year_current = strftime("%Y", dol_now());
	$pastmonth = strftime("%m", dol_now()) - 1;
	$pastmonthyear = $year_current;
	if ($pastmonth == 0) {
		$pastmonth = 12;
		$pastmonthyear--;
	}

	$datespmonth = GETPOST('datespmonth', 'int');
	$datespday = GETPOST('datespday', 'int');
	$datespyear = GETPOST('datespyear', 'int');
	$dateepmonth = GETPOST('dateepmonth', 'int');
	$dateepday = GETPOST('dateepday', 'int');
	$dateepyear = GETPOST('dateepyear', 'int');
	$datesp = dol_mktime(0, 0, 0, $datespmonth, $datespday, $datespyear);
	$dateep = dol_mktime(23, 59, 59, $dateepmonth, $dateepday, $dateepyear);

	if (empty($datesp) || empty($dateep)) { // We define date_start and date_end
		$datesp = dol_get_first_day($pastmonthyear, $pastmonth, false); $dateep = dol_get_last_day($pastmonthyear, $pastmonth, false);
	}

	print '<form name="salary" action="'.$_SERVER["PHP_SELF"].'" method="post">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="typec" value="'.$typec.'">';
	
	foreach($spendu as $spendu_)
	{
		print '<input name="spendu[]" type="hidden" value="'. $spendu_ .'"  /> ';
	}
	
	print load_fiche_titre($langs->trans("NewSalary"), '', 'salary');

	if (!empty($conf->use_javascript_ajax)) {
		print "\n".'<script type="text/javascript" language="javascript">';
		print /** @lang JavaScript */'
			$(document).ready(function () {
				let onAutoCreatePaiementChange = function () {
					if($("#auto_create_paiement").is(":checked")) {
						$("#label_fk_account").find("span").addClass("fieldrequired");
						$("#label_type_payment").find("span").addClass("fieldrequired");
						$(".hide_if_no_auto_create_payment").show();
					} else {
						$("#label_fk_account").find("span").removeClass("fieldrequired");
						$("#label_type_payment").find("span").removeClass("fieldrequired");
						$(".hide_if_no_auto_create_payment").hide();
					}
				};
				$("#radiopayment").click(function() {
					$("#label").val($(this).data("label"));
				});
				$("#radiorefund").click(function() {
					$("#label").val($(this).data("label"));
				});
				$("#auto_create_paiement").click(function () {
					onAutoCreatePaiementChange();
				});
				onAutoCreatePaiementChange();
			});
			';
		print '</script>'."\n";
	}

	print dol_get_fiche_head('', '');

	print '<table class="border centpercent">';

	// Employee
	/*print '<tr><td class="titlefieldcreate">';
	print $form->editfieldkey('Employee', 'fk_user', '', $object, 0, 'string', '', 1).'</td><td>';
	$noactive = 0; // We keep active and unactive users
	print img_picto('', 'user', 'class="paddingrighonly"').$form->select_dolusers(GETPOST('fk_user', 'int'), 'fk_user', 1, '', 0, '', '', 0, 0, 0, 'AND employee=1', 0, '', 'maxwidth300', $noactive);
	print '</td></tr>';*/

	// Label
	print '<tr><td>';
	print $form->editfieldkey('Label', 'label', '', $object, 0, 'string', '', 1).'</td><td>';
	print '<input name="label" id="label" class="minwidth300" value="'.(GETPOST("label") ?GETPOST("label") : $langs->trans("Salary")).'">';
	print '</td></tr>';

	// Date start period
	print '<tr><td>';
	print $form->editfieldkey('DateStartPeriod', 'datesp', '', $object, 0, 'string', '', 1).'</td><td>';
	print $form->selectDate($datesp, "datesp", '', '', '', 'add');
	print '</td></tr>';

	// Date end period
	print '<tr><td>';
	print $form->editfieldkey('DateEndPeriod', 'dateep', '', $object, 0, 'string', '', 1).'</td><td>';
	print $form->selectDate($dateep, "dateep", '', '', '', 'add');
	print '</td></tr>';

	// Amount
	/*print '<tr><td>';
	print $form->editfieldkey('Amount', 'amount', '', $object, 0, 'string', '', 1).'</td><td>';
	print '<input name="amount" id="amount" class="minwidth75 maxwidth100" value="'.GETPOST("amount").'">';
	print '</td></tr>';*/

	// Project
	if (!empty($conf->projet->enabled)) {
		$formproject = new FormProjets($db);

		print '<tr><td>'.$langs->trans("Project").'</td><td>';
		$formproject->select_projects(-1, $projectid, 'fk_project', 0, 0, 1, 1);
		print '</td></tr>';
	}

	// Comments
	print '<tr>';
	print '<td class="tdtop">'.$langs->trans("Comments").'</td>';
	print '<td class="tdtop"><textarea name="note" wrap="soft" cols="60" rows="'.ROWS_3.'">'.GETPOST('note', 'restricthtml').'</textarea></td>';
	print '</tr>';

	print '<tr><td colspan="2"><hr></td></tr>';

	// Auto create payment
	print '<tr><td>'.$langs->trans('AutomaticCreationPayment').'</td>';
	print '<td><input id="auto_create_paiement" name="auto_create_paiement" type="checkbox" ' . (empty($auto_create_paiement) ? '' : 'checked="checked"') . ' value="1"></td></tr>'."\n";	// Date payment

	// Bank
	if (!empty($conf->banque->enabled)) {
		print '<tr><td id="label_fk_account">';
		print $form->editfieldkey('BankAccount', 'selectaccountid', '', $object, 0, 'string', '', 1).'</td><td>';
		print img_picto('', 'bank_account', 'class="paddingrighonly"');
		$form->select_comptes($accountid, "accountid", 0, '', 1); // Affiche liste des comptes courant
		print '</td></tr>';
	}

	// Type payment
	print '<tr><td id="label_type_payment">';
	print $form->editfieldkey('PaymentMode', 'selectpaymenttype', '', $object, 0, 'string', '', 1).'</td><td>';
	$form->select_types_paiements(GETPOST("paymenttype", 'aZ09'), "paymenttype", '');
	print '</td></tr>';

	// Date payment
	print '<tr class="hide_if_no_auto_create_payment"><td>';
	print $form->editfieldkey('DatePayment', 'datep', '', $object, 0, 'string', '', 1).'</td><td>';
	print $form->selectDate((empty($datep) ? '' : $datep), "datep", 0, 0, 0, 'add', 1, 1);
	print '</td></tr>';

	// Date value for bank
	print '<tr class="hide_if_no_auto_create_payment"><td>';
	print $form->editfieldkey('DateValue', 'datev', '', $object, 0).'</td><td>';
	print $form->selectDate((empty($datev) ?-1 : $datev), "datev", '', '', '', 'add', 1, 1);
	print '</td></tr>';

	// Number
	if (!empty($conf->banque->enabled)) {
		// Number
		print '<tr class="hide_if_no_auto_create_payment"><td><label for="num_payment">'.$langs->trans('Numero');
		print ' <em>('.$langs->trans("ChequeOrTransferNumber").')</em>';
		print '</label></td>';
		print '<td><input name="num_payment" id="num_payment" type="text" value="'.GETPOST("num_payment").'"></td></tr>'."\n";
	}

	// Bouton Save payment
	/*
	print '<tr class="hide_if_no_auto_create_payment"><td>';
	print $langs->trans("ClosePaidSalaryAutomatically");
	print '</td><td><input type="checkbox" checked value="1" name="closepaidsalary"></td></tr>';
	*/

	// Other attributes
	$parameters = array();
	$reshook = $hookmanager->executeHooks('formObjectOptions', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
	print $hookmanager->resPrint;
	if (empty($reshook)) {
		print $object->showOptionals($extrafields, 'edit');
	}

	print '</table>';

	print dol_get_fiche_end();

	print '<div class="center">';

	print '<div class="hide_if_no_auto_create_payment paddingbottom">';
	print '<input type="checkbox" checked value="1" name="closepaidsalary">'.$langs->trans("ClosePaidSalaryAutomatically");
	print '<br>';
	print '</div>';

	print '<input type="submit" class="button button-save" name="save" value="'.$langs->trans("Save").'">';
	print '&nbsp;&nbsp; &nbsp;&nbsp;';
	print '<input type="submit" class="button" name="saveandnew" value="'.$langs->trans("SaveAndNew").'">';
	print '&nbsp;&nbsp; &nbsp;&nbsp;';
	print '<input type="submit" class="button button-cancel" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}


/* ************************************************************************** */
/*                                                                            */
/* View mode                                                                  */
/*                                                                            */
/* ************************************************************************** */


// End of page
llxFooter();
$db->close();
