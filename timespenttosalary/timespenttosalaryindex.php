<?php
/* Copyright (C) 2005		Rodolphe Quiedeville	<rodolphe@quiedeville.org>
 * Copyright (C) 2006-2020	Laurent Destailleur		<eldy@users.sourceforge.net>
 * Copyright (C) 2010-2012	Regis Houssin			<regis.houssin@inodbox.com>
 * Copyright (C) 2011		Juanjo Menent			<jmenent@2byte.es>
 * Copyright (C) 2018		Ferran Marcet			<fmarcet@2byte.es>
 * Copyright (C) 2018       Frédéric France         <frederic.france@netlogic.fr>
 * Copyright (C) 2019       Christophe Battarel		<christophe@altairis.fr>
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
 *	\file		htdocs/projet/tasks/time.php
 *	\ingroup	project
 *	\brief		Page to add new time spent on a task
 */

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/task.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';

// Load translation files required by the page
$langsLoad=array('projects', 'bills', 'orders');
if (!empty($conf->eventorganization->enabled)) {
	$langsLoad[]='eventorganization';
}

$langs->loadLangs($langsLoad);

$action		= GETPOST('action', 'alpha');
$massaction = GETPOST('massaction', 'alpha'); // The bulk action (combo box choice into lists)
$confirm	= GETPOST('confirm', 'alpha');
$cancel		= GETPOST('cancel', 'alpha');
$toselect = GETPOST('toselect', 'array'); // Array of ids of elements selected into a list
$contextpage = GETPOST('contextpage', 'aZ') ?GETPOST('contextpage', 'aZ') : 'timespentlist'; // To manage different context of search
$backtopage = GETPOST('backtopage', 'alpha'); // Go back to a dedicated page
$optioncss	= GETPOST('optioncss', 'alpha');

$id			= GETPOST('id', 'int');
$projectid	= GETPOST('projectid', 'int');
$ref		= GETPOST('ref', 'alpha');
$withproject = GETPOST('withproject', 'int');
$project_ref = GETPOST('project_ref', 'alpha');
$tab        = GETPOST('tab', 'aZ09');

$search_day = GETPOST('search_day', 'int');
$search_month = GETPOST('search_month', 'int');
$search_year = GETPOST('search_year', 'int');
$search_datehour = '';
$search_datewithhour = '';
$search_note = GETPOST('search_note', 'alpha');
$search_duration = GETPOST('search_duration', 'int');
$search_value = GETPOST('search_value', 'int');
$search_task_ref = GETPOST('search_task_ref', 'alpha');
$search_task_label = GETPOST('search_task_label', 'alpha');
$search_user = GETPOST('search_user', 'int');
$search_valuebilled = GETPOST('search_valuebilled', 'int');

// Security check
$socid = 0;
//if ($user->socid > 0) $socid = $user->socid;	  // For external user, no check is done on company because readability is managed by public status of project and assignement.
if (!$user->rights->projet->lire) {
	accessforbidden();
}

$limit = GETPOST('limit', 'int') ?GETPOST('limit', 'int') : $conf->liste_limit;
$sortfield = GETPOST("sortfield", 'alpha');
$sortorder = GETPOST("sortorder", 'alpha');
$page = GETPOSTISSET('pageplusone') ? (GETPOST('pageplusone') - 1) : GETPOST("page", 'int');
if (empty($page) || $page == -1) {
	$page = 0;
}		// If $page is not defined, or '' or -1
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (!$sortfield) {
	$sortfield = 'u.lastname';
}
if (!$sortorder) {
	$sortorder = 'DESC';
}

// Initialize technical object to manage hooks of page. Note that conf->hooks_modules contains array of hook context
//$object = new TaskTime($db);
$hookmanager->initHooks(array('projecttasktime', 'globalcard'));

$object = new Task($db);
$projectstatic = new Project($db);
$extrafields = new ExtraFields($db);
$extrafields->fetch_name_optionals_label($projectstatic->table_element);
$extrafields->fetch_name_optionals_label($object->table_element);


/*
 * Actions
 */

if (GETPOST('cancel', 'alpha')) {
	$action = '';
}
if (!GETPOST('confirmmassaction', 'alpha') && $massaction != 'presend' && $massaction != 'confirm_presend' && $massaction != 'confirm_generateinvoice') {
	$massaction = '';
}

$parameters = array('socid'=>$socid, 'projectid'=>$projectid);
$reshook = $hookmanager->executeHooks('doActions', $parameters, $object, $action); // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) {
	setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
}

include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';

// Purge search criteria
if (GETPOST('button_removefilter_x', 'alpha') || GETPOST('button_removefilter.x', 'alpha') || GETPOST('button_removefilter', 'alpha')) { // All tests are required to be compatible with all browsers
	$search_day = '';
	$search_month = '';
	$search_year = '';
	$search_date = '';
	$search_datehour = '';
	$search_datewithhour = '';
	$search_note = '';
	$search_duration = '';
	$search_value = '';
	$search_date_creation = '';
	$search_date_update = '';
	$search_task_ref = '';
	$search_task_label = '';
	$search_user = 0;
	$search_valuebilled = '';
	$toselect = '';
	$search_array_options = array();
	$action = '';
}

/*
if ($action == 'addtimespent' && $user->rights->projet->lire) {
	$error = 0;

	$timespent_durationhour = GETPOST('timespent_durationhour', 'int');
	$timespent_durationmin = GETPOST('timespent_durationmin', 'int');
	if (empty($timespent_durationhour) && empty($timespent_durationmin)) {
		setEventMessages($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv("Duration")), null, 'errors');
		$error++;
	}
	if (!GETPOST("userid", 'int')) {
		$langs->load("errors");
		setEventMessages($langs->trans('ErrorUserNotAssignedToTask'), null, 'errors');
		$error++;
	}

	if (!$error) {
		if ($id || $ref) {
			$object->fetch($id, $ref);
		} else {
			if (!GETPOST('taskid', 'int') || GETPOST('taskid', 'int') < 0) {
				setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Task")), null, 'errors');
				$action = 'createtime';
				$error++;
			} else {
				$object->fetch(GETPOST('taskid', 'int'));
			}
		}

		if (!$error) {
			$object->fetch_projet();

			if (empty($object->project->statut)) {
				setEventMessages($langs->trans("ProjectMustBeValidatedFirst"), null, 'errors');
				$action = 'createtime';
				$error++;
			} else {
				$object->timespent_note = GETPOST("timespent_note", 'alpha');
				if (GETPOST('progress', 'int') > 0) {
					$object->progress = GETPOST('progress', 'int'); // If progress is -1 (not defined), we do not change value
				}
				$object->timespent_duration = GETPOSTINT("timespent_durationhour") * 60 * 60; // We store duration in seconds
				$object->timespent_duration += (GETPOSTINT('timespent_durationmin') ? GETPOSTINT('timespent_durationmin') : 0) * 60; // We store duration in seconds
				if (GETPOST("timehour") != '' && GETPOST("timehour") >= 0) {	// If hour was entered
					$object->timespent_date = dol_mktime(GETPOST("timehour", 'int'), GETPOST("timemin", 'int'), 0, GETPOST("timemonth", 'int'), GETPOST("timeday", 'int'), GETPOST("timeyear", 'int'));
					$object->timespent_withhour = 1;
				} else {
					$object->timespent_date = dol_mktime(12, 0, 0, GETPOST("timemonth", 'int'), GETPOST("timeday", 'int'), GETPOST("timeyear", 'int'));
				}
				$object->timespent_fk_user = GETPOST("userid", 'int');
				$result = $object->addTimeSpent($user);
				if ($result >= 0) {
					setEventMessages($langs->trans("RecordSaved"), null, 'mesgs');
				} else {
					setEventMessages($langs->trans($object->error), null, 'errors');
					$error++;
				}
			}
		}
	} else {
		if (empty($id)) {
			$action = 'createtime';
		} else {
			$action = 'createtime';
		}
	}
}

if (($action == 'updateline' || $action == 'updatesplitline') && !$cancel && $user->rights->projet->lire) {
	$error = 0;

	if (!GETPOST("new_durationhour") && !GETPOST("new_durationmin")) {
		setEventMessages($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv("Duration")), null, 'errors');
		$error++;
	}

	if (!$error) {
		if (GETPOST('taskid', 'int') != $id) {		// GETPOST('taskid') is id of new task
			$id = GETPOST('taskid', 'int');

			$object->fetchTimeSpent(GETPOST('lineid', 'int'));
			// TODO Check that ($task_time->fk_user == $user->id || in_array($task_time->fk_user, $childids))
			$result = $object->delTimeSpent($user);

			$object->fetch($id, $ref);
			$object->timespent_note = GETPOST("timespent_note_line", 'alpha');
			$object->timespent_old_duration = GETPOST("old_duration");
			$object->timespent_duration = GETPOSTINT("new_durationhour") * 60 * 60; // We store duration in seconds
			$object->timespent_duration += (GETPOSTINT("new_durationmin") ? GETPOSTINT('new_durationmin') : 0) * 60; // We store duration in seconds
			if (GETPOST("timelinehour") != '' && GETPOST("timelinehour") >= 0) {	// If hour was entered
				$object->timespent_date = dol_mktime(GETPOST("timelinehour"), GETPOST("timelinemin"), 0, GETPOST("timelinemonth"), GETPOST("timelineday"), GETPOST("timelineyear"));
				$object->timespent_withhour = 1;
			} else {
				$object->timespent_date = dol_mktime(12, 0, 0, GETPOST("timelinemonth"), GETPOST("timelineday"), GETPOST("timelineyear"));
			}
			$object->timespent_fk_user = GETPOST("userid_line", 'int');
			$result = $object->addTimeSpent($user);
			if ($result >= 0) {
				setEventMessages($langs->trans("RecordSaved"), null, 'mesgs');
			} else {
				setEventMessages($langs->trans($object->error), null, 'errors');
				$error++;
			}
		} else {
			$object->fetch($id, $ref);
			// TODO Check that ($task_time->fk_user == $user->id || in_array($task_time->fk_user, $childids))

			$object->timespent_id = GETPOST("lineid", 'int');
			$object->timespent_note = GETPOST("timespent_note_line");
			$object->timespent_old_duration = GETPOST("old_duration");
			$object->timespent_duration = GETPOSTINT("new_durationhour") * 60 * 60; // We store duration in seconds
			$object->timespent_duration += (GETPOSTINT("new_durationmin") ? GETPOSTINT('new_durationmin') : 0) * 60; // We store duration in seconds
			if (GETPOST("timelinehour") != '' && GETPOST("timelinehour") >= 0) {	// If hour was entered
				$object->timespent_date = dol_mktime(GETPOST("timelinehour", 'int'), GETPOST("timelinemin", 'int'), 0, GETPOST("timelinemonth", 'int'), GETPOST("timelineday", 'int'), GETPOST("timelineyear", 'int'));
				$object->timespent_withhour = 1;
			} else {
				$object->timespent_date = dol_mktime(12, 0, 0, GETPOST("timelinemonth", 'int'), GETPOST("timelineday", 'int'), GETPOST("timelineyear", 'int'));
			}
			$object->timespent_fk_user = GETPOST("userid_line", 'int');

			$result = $object->updateTimeSpent($user);
			if ($result >= 0) {
				setEventMessages($langs->trans("RecordSaved"), null, 'mesgs');
			} else {
				setEventMessages($langs->trans($object->error), null, 'errors');
				$error++;
			}
		}
	} else {
		$action = '';
	}
}

if ($action == 'confirm_delete' && $confirm == "yes" && $user->rights->projet->lire) {
	$object->fetchTimeSpent(GETPOST('lineid', 'int'));
	// TODO Check that ($task_time->fk_user == $user->id || in_array($task_time->fk_user, $childids))
	$result = $object->delTimeSpent($user);

	if ($result < 0) {
		$langs->load("errors");
		setEventMessages($langs->trans($object->error), null, 'errors');
		$error++;
		$action = '';
	} else {
		setEventMessages($langs->trans("RecordDeleted"), null, 'mesgs');
	}
}*/

// Retrieve First Task ID of Project if withprojet is on to allow project prev next to work
if (!empty($project_ref) && !empty($withproject)) {
	if ($projectstatic->fetch(0, $project_ref) > 0) {
		$tasksarray = $object->getTasksArray(0, 0, $projectstatic->id, $socid, 0);
		if (count($tasksarray) > 0) {
			$id = $tasksarray[0]->id;
		} else {
			header("Location: ".DOL_URL_ROOT.'/projet/tasks.php?id='.$projectstatic->id.($withproject ? '&withproject=1' : '').(empty($mode) ? '' : '&mode='.$mode));
			exit;
		}
	}
}

// To show all time lines for project
$projectidforalltimes = 0;
if (GETPOST('projectid', 'int') > 0) {
	$projectidforalltimes = GETPOST('projectid', 'int');

	$result = $projectstatic->fetch($projectidforalltimes);
	if (!empty($projectstatic->socid)) {
		$projectstatic->fetch_thirdparty();
	}
	$res = $projectstatic->fetch_optionals();
} elseif (GETPOST('project_ref', 'alpha')) {
	$projectstatic->fetch(0, GETPOST('project_ref', 'alpha'));
	$projectidforalltimes = $projectstatic->id;
	$withproject = 1;
} elseif ($id > 0) {
	$object->fetch($id);
	$result = $projectstatic->fetch($object->fk_project);
}

/*if ($action == 'confirm_generateinvoice') {
	if (!empty($projectstatic->socid)) {
		$projectstatic->fetch_thirdparty();
	}

	if (!($projectstatic->thirdparty->id > 0)) {
		setEventMessages($langs->trans("ThirdPartyRequiredToGenerateInvoice"), null, 'errors');
	} else {
		include_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
		include_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
		include_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';

		$tmpinvoice = new Facture($db);
		$tmptimespent = new Task($db);
		$tmpproduct = new Product($db);
		$fuser = new User($db);

		$db->begin();
		$idprod = GETPOST('productid', 'int');
		$generateinvoicemode = GETPOST('generateinvoicemode', 'string');
		$invoiceToUse = GETPOST('invoiceid', 'int');

		$prodDurationHours = 1.0;
		if ($idprod > 0) {
			$tmpproduct->fetch($idprod);

			if (empty($tmpproduct->duration_value)) {
				$error++;
				$langs->load("errors");
				setEventMessages($langs->trans("ErrorDurationForServiceNotDefinedCantCalculateHourlyPrice"), null, 'errors');
			}

			if ($tmpproduct->duration_unit == 'i') {
				$prodDurationHours = 1. / 60;
			}
			if ($tmpproduct->duration_unit == 'h') {
				$prodDurationHours = 1.;
			}
			if ($tmpproduct->duration_unit == 'd') {
				$prodDurationHours = 24.;
			}
			if ($tmpproduct->duration_unit == 'w') {
				$prodDurationHours = 24. * 7;
			}
			if ($tmpproduct->duration_unit == 'm') {
				$prodDurationHours = 24. * 30;
			}
			if ($tmpproduct->duration_unit == 'y') {
				$prodDurationHours = 24. * 365;
			}
			$prodDurationHours *= $tmpproduct->duration_value;

			$dataforprice = $tmpproduct->getSellPrice($mysoc, $projectstatic->thirdparty, 0);

			$pu_ht = empty($dataforprice['pu_ht']) ? 0 : $dataforprice['pu_ht'];
			$txtva = $dataforprice['tva_tx'];
			$localtax1 = $dataforprice['localtax1'];
			$localtax2 = $dataforprice['localtax2'];
		} else {
			$prodDurationHours = 1;

			$pu_ht = 0;
			$txtva = get_default_tva($mysoc, $projectstatic->thirdparty);
			$localtax1 = get_default_localtax($mysoc, $projectstatic->thirdparty, 1);
			$localtax2 = get_default_localtax($mysoc, $projectstatic->thirdparty, 2);
		}

		$tmpinvoice->socid = $projectstatic->thirdparty->id;
		$tmpinvoice->date = dol_mktime(GETPOST('rehour', 'int'), GETPOST('remin', 'int'), GETPOST('resec', 'int'), GETPOST('remonth', 'int'), GETPOST('reday', 'int'), GETPOST('reyear', 'int'));
		$tmpinvoice->fk_project = $projectstatic->id;

		if ($invoiceToUse) {
			$tmpinvoice->fetch($invoiceToUse);
		} else {
			$result = $tmpinvoice->create($user);
			if ($result <= 0) {
				$error++;
				setEventMessages($tmpinvoice->error, $tmpinvoice->errors, 'errors');
			}
		}

		if (!$error) {
			if ($generateinvoicemode == 'onelineperuser') {
				$arrayoftasks = array();
				foreach ($toselect as $key => $value) {
					// Get userid, timepent
					$object->fetchTimeSpent($value);
					$arrayoftasks[$object->timespent_fk_user]['timespent'] += $object->timespent_duration;
					$arrayoftasks[$object->timespent_fk_user]['totalvaluetodivideby3600'] += ($object->timespent_duration * $object->timespent_thm);
				}

				foreach ($arrayoftasks as $userid => $value) {
					$fuser->fetch($userid);
					//$pu_ht = $value['timespent'] * $fuser->thm;
					$username = $fuser->getFullName($langs);

					// Define qty per hour
					$qtyhour = $value['timespent'] / 3600;
					$qtyhourtext = convertSecondToTime($value['timespent'], 'all', $conf->global->MAIN_DURATION_OF_WORKDAY);

					// If no unit price known
					if (empty($pu_ht)) {
						$pu_ht = price2num($value['totalvaluetodivideby3600'] / 3600, 'MU');
					}

					// Add lines
					$lineid = $tmpinvoice->addline($langs->trans("TimeSpentForInvoice", $username).' : '.$qtyhourtext, $pu_ht, round($qtyhour / $prodDurationHours, 2), $txtva, $localtax1, $localtax2, ($idprod > 0 ? $idprod : 0));

					// Update lineid into line of timespent
					$sql = 'UPDATE '.MAIN_DB_PREFIX.'projet_task_time SET invoice_line_id = '.((int) $lineid).', invoice_id = '.((int) $tmpinvoice->id);
					$sql .= ' WHERE rowid IN ('.$db->sanitize(join(',', $toselect)).') AND fk_user = '.((int) $userid);
					$result = $db->query($sql);
					if (!$result) {
						$error++;
						setEventMessages($db->lasterror(), null, 'errors');
						break;
					}
				}
			} elseif ($generateinvoicemode == 'onelineperperiod') {	// One line for each time spent line
				$arrayoftasks = array();
				foreach ($toselect as $key => $value) {
					// Get userid, timepent
					$object->fetchTimeSpent($value);
					// $object->id is the task id
					$ftask = new Task($db);
					$ftask->fetch($object->id);

					$fuser->fetch($object->timespent_fk_user);
					$username = $fuser->getFullName($langs);

					$arrayoftasks[$object->timespent_id]['timespent'] = $object->timespent_duration;
					$arrayoftasks[$object->timespent_id]['totalvaluetodivideby3600'] = $object->timespent_duration * $object->timespent_thm;
					$arrayoftasks[$object->timespent_id]['note'] = $ftask->ref.' - '.$ftask->label.' - '.$username.($object->timespent_note ? ' - '.$object->timespent_note : '');		// TODO Add user name in note
					$arrayoftasks[$object->timespent_id]['user'] = $object->timespent_fk_user;
				}

				foreach ($arrayoftasks as $timespent_id => $value) {
					$userid = $value['user'];
					//$pu_ht = $value['timespent'] * $fuser->thm;

					// Define qty per hour
					$qtyhour = $value['timespent'] / 3600;
					$qtyhourtext = convertSecondToTime($value['timespent'], 'all', $conf->global->MAIN_DURATION_OF_WORKDAY);

					// If no unit price known
					if (empty($pu_ht)) {
						$pu_ht = price2num($value['totalvaluetodivideby3600'] / 3600, 'MU');
					}

					// Add lines
					$lineid = $tmpinvoice->addline($value['note'], $pu_ht, round($qtyhour / $prodDurationHours, 2), $txtva, $localtax1, $localtax2, ($idprod > 0 ? $idprod : 0));
					//var_dump($lineid);exit;

					// Update lineid into line of timespent
					$sql = 'UPDATE '.MAIN_DB_PREFIX.'projet_task_time SET invoice_line_id = '.((int) $lineid).', invoice_id = '.((int) $tmpinvoice->id);
					$sql .= ' WHERE rowid IN ('.$db->sanitize(join(',', $toselect)).') AND fk_user = '.((int) $userid);
					$result = $db->query($sql);
					if (!$result) {
						$error++;
						setEventMessages($db->lasterror(), null, 'errors');
						break;
					}
				}
			} elseif ($generateinvoicemode == 'onelinepertask') {	// One line for each different task
				$arrayoftasks = array();
				foreach ($toselect as $key => $value) {
					// Get userid, timepent
					$object->fetchTimeSpent($value);		// Call method to get list of timespent for a timespent line id (We use the utiliy method found into Task object)
					// $object->id is now the task id
					$arrayoftasks[$object->id]['timespent'] += $object->timespent_duration;
					$arrayoftasks[$object->id]['totalvaluetodivideby3600'] += ($object->timespent_duration * $object->timespent_thm);
				}

				foreach ($arrayoftasks as $task_id => $value) {
					$ftask = new Task($db);
					$ftask->fetch($task_id);
					// Define qty per hour
					$qtyhour = $value['timespent'] / 3600;
					$qtyhourtext = convertSecondToTime($value['timespent'], 'all', $conf->global->MAIN_DURATION_OF_WORKDAY);

					if ($idprod > 0) {
						// If a product is defined, we msut use the $prodDurationHours and $pu_ht of product (already set previously).
						$pu_ht_for_task = $pu_ht;
						// If we want to reuse the value of timespent (so use same price than cost price)
						if (!empty($conf->global->PROJECT_TIME_SPENT_INTO_INVOICE_USE_VALUE)) {
							$pu_ht_for_task = price2num($value['totalvaluetodivideby3600'] / $value['timespent'], 'MU') * $prodDurationHours;
						}
						$pa_ht = price2num($value['totalvaluetodivideby3600'] / $value['timespent'], 'MU') * $prodDurationHours;
					} else {
						// If not product used, we use the hour unit for duration and unit price.
						$pu_ht_for_task = 0;
						// If we want to reuse the value of timespent (so use same price than cost price)
						if (!empty($conf->global->PROJECT_TIME_SPENT_INTO_INVOICE_USE_VALUE)) {
							$pu_ht_for_task = price2num($value['totalvaluetodivideby3600'] / $value['timespent'], 'MU');
						}
						$pa_ht = price2num($value['totalvaluetodivideby3600'] / $value['timespent'], 'MU');
					}

					// Add lines
					$date_start = '';
					$date_end = '';
					$lineName = $ftask->ref.' - '.$ftask->label;
					$lineid = $tmpinvoice->addline($lineName, $pu_ht_for_task, price2num($qtyhour / $prodDurationHours, 'MS'), $txtva, $localtax1, $localtax2, ($idprod > 0 ? $idprod : 0), 0, $date_start, $date_end, 0, 0, '', 'HT', 0, 1, -1, 0, '', 0, 0, null, $pa_ht);
					if ($lineid < 0) {
						$error++;
						setEventMessages($tmpinvoice->error, $tmpinvoice->errors, 'errors');
						break;
					}

					if (!$error) {
						// Update lineid into line of timespent
						$sql = 'UPDATE '.MAIN_DB_PREFIX.'projet_task_time SET invoice_line_id = '.((int) $lineid).', invoice_id = '.((int) $tmpinvoice->id);
						$sql .= ' WHERE rowid IN ('.$db->sanitize(join(',', $toselect)).')';
						$result = $db->query($sql);
						if (!$result) {
							$error++;
							setEventMessages($db->lasterror(), null, 'errors');
							break;
						}
					}
				}
			}
		}

		if (!$error) {
			$urltoinvoice = $tmpinvoice->getNomUrl(0);
			$mesg = $langs->trans("InvoiceGeneratedFromTimeSpent", '{s1}');
			$mesg = str_replace('{s1}', $urltoinvoice, $mesg);
			setEventMessages($mesg, null, 'mesgs');

			//var_dump($tmpinvoice);

			$db->commit();
		} else {
			$db->rollback();
		}
	}
}*/


/*
 * View
 */

$arrayofselected = is_array($toselect) ? $toselect : array();

llxHeader("", $langs->trans("Task"));

$form = new Form($db);
$formother = new FormOther($db);
$formproject = new FormProjets($db);
$userstatic = new User($db);

//if (($id > 0 || !empty($ref)) || $projectidforalltimes > 0) {
	/*
	 * Fiche projet en mode visu
	 */


	$userRead = $projectstatic->restrictedProjectArea($user, 'read');
	$linktocreatetime = '';


		// Initialize technical object to manage hooks. Note that conf->hooks_modules contains array
		$hookmanager->initHooks(array('tasktimelist'));

		// Definition of fields for list
		$arrayfields = array();
		$arrayfields['t.task_date'] = array('label'=>$langs->trans("Date"), 'checked'=>1);
		if ((empty($id) && empty($ref)) || !empty($projectidforalltimes)) {	// Not a dedicated task
			$arrayfields['t.task_ref'] = array('label'=>$langs->trans("RefTask"), 'checked'=>1);
			$arrayfields['t.task_label'] = array('label'=>$langs->trans("LabelTask"), 'checked'=>1);
		}
		$arrayfields['author'] = array('label'=>$langs->trans("By"), 'checked'=>1);
		$arrayfields['t.note'] = array('label'=>$langs->trans("Note"), 'checked'=>1);
		$arrayfields['t.task_duration'] = array('label'=>$langs->trans("Duration"), 'checked'=>1);
		$arrayfields['value'] = array('label'=>$langs->trans("Value"), 'checked'=>1, 'enabled'=>(empty($conf->salaries->enabled) ? 0 : 1));
		$arrayfields['valuebilled'] = array('label'=>$langs->trans("Billed"), 'checked'=>1, 'enabled'=>(((!empty($conf->global->PROJECT_HIDE_TASKS) || empty($conf->global->PROJECT_BILL_TIME_SPENT)) ? 0 : 1) && $projectstatic->usage_bill_time));
		// Extra fields
		include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_list_array_fields.tpl.php';

		$arrayfields = dol_sort_array($arrayfields, 'position');

		$param = '';
		if (!empty($contextpage) && $contextpage != $_SERVER["PHP_SELF"]) {
			$param .= '&contextpage='.urlencode($contextpage);
		}
		if ($limit > 0 && $limit != $conf->liste_limit) {
			$param .= '&limit='.urlencode($limit);
		}
		if ($search_month > 0) {
			$param .= '&search_month='.urlencode($search_month);
		}
		if ($search_year > 0) {
			$param .= '&search_year='.urlencode($search_year);
		}
		if ($search_user > 0) {
			$param .= '&search_user='.urlencode($search_user);
		}
		if ($search_task_ref != '') {
			$param .= '&search_task_ref='.urlencode($search_task_ref);
		}
		if ($search_task_label != '') {
			$param .= '&search_task_label='.urlencode($search_task_label);
		}
		if ($search_note != '') {
			$param .= '&search_note='.urlencode($search_note);
		}
		if ($search_duration != '') {
			$param .= '&amp;search_field2='.urlencode($search_duration);
		}
		if ($optioncss != '') {
			$param .= '&optioncss='.urlencode($optioncss);
		}
		/*
		 // Add $param from extra fields
		 include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_list_search_param.tpl.php';
		 */
		if ($id) {
			$param .= '&id='.urlencode($id);
		}
		if ($projectid) {
			$param .= '&projectid='.urlencode($projectid);
		}
		if ($withproject) {
			$param .= '&withproject='.urlencode($withproject);
		}

		print '<form id="frmspendsalary" method="POST" action="salary.php">';
		if ($optioncss != '') {
			print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
		}
		print '<input type="hidden" name="token" value="'.newToken().'">';
		print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
		print '<input type="hidden" name="action" value="create">';
		print '<input type="hidden" name="typec" value="spend">';
		print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
		print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';

		//print '<input type="hidden" name="id" value="'.$id.'">';
		//print '<input type="hidden" name="projectid" value="'.$projectidforalltimes.'">';
		print '<input type="hidden" name="withproject" value="'.$withproject.'">';
		print '<input type="hidden" name="tab" value="'.$tab.'">';


		/*
		 *	List of time spent
		 */
		$tasks = array();
		
		

		$sql = "SELECT t.rowid, t.fk_task, t.task_date, t.task_datehour, t.task_date_withhour, t.task_duration, t.fk_user, t.note, t.thm,";
		$sql .= " pt.ref, pt.label,";
		$sql .= " u.lastname, u.firstname, u.login, u.photo, u.statut as user_status,";
		$sql .= " il.fk_facture as invoice_id, inv.fk_statut";
		$sql .= " FROM ".MAIN_DB_PREFIX."projet_task_time as t";
		$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."facturedet as il ON il.rowid = t.invoice_line_id";
		$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."facture as inv ON inv.rowid = il.fk_facture,";
		$sql .= " ".MAIN_DB_PREFIX."projet_task as pt, ".MAIN_DB_PREFIX."user as u";
		$sql .= " WHERE t.fk_user = u.rowid AND t.fk_task = pt.rowid";

		$sql .= " AND t.rowid not in (select timespent_id from timespenttosalary_reg tr join ".MAIN_DB_PREFIX."salary sa on tr.salary_id = sa.rowid)";

		/*if (empty($projectidforalltimes)) {
			$sql .= " AND t.fk_task =".((int) $object->id);
		} else {
			$sql .= " AND pt.fk_projet IN (".$db->sanitize($projectidforalltimes).")";
		}*/
		if ($search_note) {
			$sql .= natural_search('t.note', $search_note);
		}
		if ($search_task_ref) {
			$sql .= natural_search('pt.ref', $search_task_ref);
		}
		if ($search_task_label) {
			$sql .= natural_search('pt.label', $search_task_label);
		}
		if ($search_user > 0) {
			$sql .= natural_search('t.fk_user', $search_user);
		}
		if ($search_valuebilled == '1') {
			$sql .= ' AND t.invoice_id > 0';
		}
		if ($search_valuebilled == '0') {
			$sql .= ' AND (t.invoice_id = 0 OR t.invoice_id IS NULL)';
		}
		$sql .= dolSqlDateFilter('t.task_datehour', $search_day, $search_month, $search_year);
		if($sortfield=="u.lastname")
		{
			$sortfield = " concat(u.lastname, '', u.firstname) ";
			$sql .= ' ORDER BY '. $sortfield .' '.$sortorder;
		}
		else
		
			$sql .= $db->order($sortfield, $sortorder);

		// Count total nb of records
		$nbtotalofrecords = '';
		if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST)) {
			$resql = $db->query($sql);
			$nbtotalofrecords = $db->num_rows($resql);
			if (($page * $limit) > $nbtotalofrecords) {	// if total of record found is smaller than page * limit, goto and load page 0
				$page = 0;
				$offset = 0;
			}
		}
		// if total of record found is smaller than limit, no need to do paging and to restart another select with limits set.
		if (is_numeric($nbtotalofrecords) && $limit > $nbtotalofrecords) {
			$num = $nbtotalofrecords;
		} else {
			$sql .= $db->plimit($limit + 1, $offset);

			$resql = $db->query($sql);
			if (!$resql) {
				dol_print_error($db);
				exit;
			}

			$num = $db->num_rows($resql);
		}
		
		
		$massactionbutton = '<a class="butAction" href="javascript:CreateSal()">'.$langs->trans('GenerateSalaries').'</a>';
		
		if ($num >= 0) {
			if (!empty($projectidforalltimes)) {
				print '<!-- List of time spent for project -->'."\n";

				$title = $langs->trans("ListTaskTimeUserProject");

				print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, $massactionbutton, $num, $nbtotalofrecords, 'clock', 0, $linktocreatetime, '', $limit, 0, 0, 1);
			} else {
				print '<!-- List of time spent for project -->'."\n";

				$title = $langs->trans("ListTaskTimeForTask");

				print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder,  $massactionbutton, $num, $nbtotalofrecords, 'clock', 0, $linktocreatetime, '', $limit, 0, 0, 1);
				
			}

			$i = 0;
			while ($i < $num) {
				$row = $db->fetch_object($resql);
				$tasks[$i] = $row;
				$i++;
			}
			$db->free($resql);
		} else {
			dol_print_error($db);
		}

		/*
		 * Form to add a new line of time spent
		 */
		/*if ($action == 'createtime' && $user->rights->projet->lire) {
			print '<!-- table to add time spent -->'."\n";
			if (!empty($id)) {
				print '<input type="hidden" name="taskid" value="'.$id.'">';
			}

			print '<div class="div-table-responsive-no-min">'; // You can use div-table-responsive-no-min if you dont need reserved height for your table
			print '<table class="noborder nohover centpercent">';

			print '<tr class="liste_titre">';
			print '<td>'.$langs->trans("Date").'</td>';
			if (empty($id)) {
				print '<td>'.$langs->trans("Task").'</td>';
			}
			print '<td>'.$langs->trans("By").'</td>';
			print '<td>'.$langs->trans("Note").'</td>';
			print '<td>'.$langs->trans("NewTimeSpent").'</td>';
			print '<td>'.$langs->trans("ProgressDeclared").'</td>';
			if (empty($conf->global->PROJECT_HIDE_TASKS) && !empty($conf->global->PROJECT_BILL_TIME_SPENT)) {
				print '<td></td>';
			}
			print '<td></td>';
			print "</tr>\n";

			print '<tr class="oddeven nohover">';

			// Date
			print '<td class="maxwidthonsmartphone">';
			$newdate = '';
			print $form->selectDate($newdate, 'time', ($conf->browser->layout == 'phone' ? 2 : 1), 1, 2, "timespent_date", 1, 0);
			print '</td>';

			// Task
			$nboftasks = 0;
			if (empty($id)) {
				print '<td class="maxwidthonsmartphone">';
				$nboftasks = $formproject->selectTasks(-1, GETPOST('taskid', 'int'), 'taskid', 0, 0, 1, 1, 0, 0, 'maxwidth300', $projectstatic->id, '');
				print '</td>';
			}

			// Contributor
			print '<td class="maxwidthonsmartphone nowraponall">';
			$contactsofproject = $projectstatic->getListContactId('internal');
			if (count($contactsofproject) > 0) {
				print img_object('', 'user', 'class="hideonsmartphone"');
				if (in_array($user->id, $contactsofproject)) {
					$userid = $user->id;
				} else {
					$userid = $contactsofproject[0];
				}

				if ($projectstatic->public) {
					$contactsofproject = array();
				}
				print $form->select_dolusers((GETPOST('userid', 'int') ? GETPOST('userid', 'int') : $userid), 'userid', 0, '', 0, '', $contactsofproject, 0, 0, 0, '', 0, $langs->trans("ResourceNotAssignedToProject"), 'maxwidth250');
			} else {
				if ($nboftasks) {
					print img_error($langs->trans('FirstAddRessourceToAllocateTime')).' '.$langs->trans('FirstAddRessourceToAllocateTime');
				}
			}
			print '</td>';

			// Note
			print '<td>';
			print '<textarea name="timespent_note" class="maxwidth100onsmartphone" rows="'.ROWS_2.'">'.($_POST['timespent_note'] ? $_POST['timespent_note'] : '').'</textarea>';
			print '</td>';

			// Duration - Time spent
			print '<td>';
			$durationtouse = ($_POST['timespent_duration'] ? $_POST['timespent_duration'] : '');
			if (GETPOSTISSET('timespent_durationhour') || GETPOSTISSET('timespent_durationmin')) {
				$durationtouse = (GETPOST('timespent_durationhour') * 3600 + GETPOST('timespent_durationmin') * 60);
			}
			print $form->select_duration('timespent_duration', $durationtouse, 0, 'text');
			print '</td>';

			// Progress declared
			print '<td class="nowrap">';
			print $formother->select_percent(GETPOST('progress') ?GETPOST('progress') : $object->progress, 'progress', 0, 5, 0, 100, 1);
			print '</td>';

			// Invoiced
			if (empty($conf->global->PROJECT_HIDE_TASKS) && !empty($conf->global->PROJECT_BILL_TIME_SPENT)) {
				print '<td>';
				print '</td>';
			}

			print '<td class="center">';
			print '<input type="submit" name="save" class="button buttongen marginleftonly margintoponlyshort marginbottomonlyshort" value="'.$langs->trans("Add").'">';
			print '<input type="submit" name="cancel" class="button buttongen marginleftonly margintoponlyshort marginbottomonlyshort button-cancel" value="'.$langs->trans("Cancel").'">';
			print '</td></tr>';

			print '</table>';
			print '</div>';

			print '<br>';
		}*/

		$moreforfilter = '';

		$parameters = array();
		$reshook = $hookmanager->executeHooks('printFieldPreListTitle', $parameters); // Note that $action and $object may have been modified by hook
		if (empty($reshook)) {
			$moreforfilter .= $hookmanager->resPrint;
		} else {
			$moreforfilter = $hookmanager->resPrint;
		}

		if (!empty($moreforfilter)) {
			print '<div class="liste_titre liste_titre_bydiv centpercent">';
			print $moreforfilter;
			print '</div>';
		}

		$varpage = empty($contextpage) ? $_SERVER["PHP_SELF"] : $contextpage;
		$selectedfields = $form->multiSelectArrayWithCheckbox('selectedfields', $arrayfields, $varpage); // This also change content of $arrayfields
		$selectedfields .= (is_array($arrayofmassactions) && count($arrayofmassactions) ? $form->showCheckAddButtons('checkforselect', 1) : '');
		$text = $langs->trans('ConfirmValidateOrder', 1223);
		
			if (1==1) {
				require_once DOL_DOCUMENT_ROOT.'/core/class/notify.class.php';
				$notify = new Notify($db);
				$text .= '<br>';
				$text .= $notify->confirmMessage('ORDER_VALIDATE', $object->id, $object);
				$formquestion = array(
				array()
			);
				$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('CONTINUE_CONVERT_SPEND_SALARY'), $text, 'confirm_validate', $formquestion, 0, 1, 520);
				
				 //Print form confirm
				$formconfirm = str_replace('autoOpen: true,', 'autoOpen: false,', $formconfirm);
			print $formconfirm;
			}
		print '<div class="div-table-responsive">';
		print '<table id="tosalary" class="tagtable nobottomiftotal liste'.($moreforfilter ? " listwithfilterbefore" : "").'">'."\n";

		// Fields title search
		print '<tr class="liste_titre_filter">';
		
		// Author
		if (!empty($arrayfields['author']['checked'])) {
			
			print '<td class="liste_titre">'.$form->select_dolusers(($search_user > 0 ? $search_user : -1), 'search_user', 1, null, 0, '', '', 0, 0, 0, '', 0, '', 'maxwidth250').'</td>';
		}
		
		// Date
		if (!empty($arrayfields['t.task_date']['checked'])) {
			print '<td class="liste_titre">';
			if (!empty($conf->global->MAIN_LIST_FILTER_ON_DAY)) {
				print '<input class="flat valignmiddle" type="text" size="1" maxlength="2" name="search_day" value="'.$search_day.'">';
			}
			print '<input class="flat valignmiddle" type="text" size="1" maxlength="2" name="search_month" value="'.$search_month.'">';
			$formother->select_year($search_year, 'search_year', 1, 20, 5);
			print '</td>';
		}
		if ((empty($id) && empty($ref)) || !empty($projectidforalltimes)) {	// Not a dedicated task
			if (!empty($arrayfields['t.task_ref']['checked'])) {
				print '<td class="liste_titre"><input type="text" class="flat maxwidth100" name="search_task_ref" value="'.dol_escape_htmltag($search_task_ref).'"></td>';
			}
			if (!empty($arrayfields['t.task_label']['checked'])) {
				print '<td class="liste_titre"><input type="text" class="flat maxwidth100" name="search_task_label" value="'.dol_escape_htmltag($search_task_label).'"></td>';
			}
		}
		// Note
		if (!empty($arrayfields['t.note']['checked'])) {
			print '<td class="liste_titre"><input type="text" class="flat maxwidth100" name="search_note" value="'.dol_escape_htmltag($search_note).'"></td>';
		}
		// Duration
		if (!empty($arrayfields['t.task_duration']['checked'])) {
			print '<td class="liste_titre right"></td>';
		}
		// Value in main currency
		if (!empty($arrayfields['value']['checked'])) {
			print '<td class="liste_titre"></td>';
		}
		// Value billed
		if (!empty($arrayfields['valuebilled']['checked'])) {
			print '<td class="liste_titre center">'.$form->selectyesno('search_valuebilled', $search_valuebilled, 1, false, 1).'</td>';
		}

		/*
		// Extra fields
		include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_list_search_input.tpl.php';
		*/
		// Fields from hook
		$parameters = array('arrayfields'=>$arrayfields);
		$reshook = $hookmanager->executeHooks('printFieldListOption', $parameters); // Note that $action and $object may have been modified by hook
		print $hookmanager->resPrint;
		// Action column
		print '<td class="liste_titre center">';
		$searchpicto = $form->showFilterButtons();
		print $searchpicto;
		print '</td>';
		print '</tr>'."\n";

		print '<tr class="liste_titre">';
		
		if (!empty($arrayfields['author']['checked'])) {
			$sortord = "DESC";
			if($sortorder=="desc")$sortord = "ASC";
			print_liste_field_titre($arrayfields['author']['label'], $_SERVER['PHP_SELF'], 'u.lastname', '', $param, 'id="Titlepor"', $sortfield, $sortord);
		}
		
		if (!empty($arrayfields['t.task_date']['checked'])) {
			print_liste_field_titre($arrayfields['t.task_date']['label'], $_SERVER['PHP_SELF'], 't.task_date,t.task_datehour,t.rowid', '', $param, '', $sortfield, $sortorder);
		}
		if ((empty($id) && empty($ref)) || !empty($projectidforalltimes)) {	// Not a dedicated task
			if (!empty($arrayfields['t.task_ref']['checked'])) {
				print_liste_field_titre($arrayfields['t.task_ref']['label'], $_SERVER['PHP_SELF'], 'pt.ref', '', $param, '', $sortfield, $sortorder);
			}
			if (!empty($arrayfields['t.task_label']['checked'])) {
				print_liste_field_titre($arrayfields['t.task_label']['label'], $_SERVER['PHP_SELF'], 'pt.label', '', $param, '', $sortfield, $sortorder);
			}
		}
		if (!empty($arrayfields['t.note']['checked'])) {
			print_liste_field_titre($arrayfields['t.note']['label'], $_SERVER['PHP_SELF'], 't.note', '', $param, '', $sortfield, $sortorder);
		}
		if (!empty($arrayfields['t.task_duration']['checked'])) {
			print_liste_field_titre($arrayfields['t.task_duration']['label'], $_SERVER['PHP_SELF'], 't.task_duration', '', $param, '', $sortfield, $sortorder, 'right ');
		}
		if (!empty($arrayfields['value']['checked'])) {
			print_liste_field_titre($arrayfields['value']['label'], $_SERVER['PHP_SELF'], '', '', $param, '', $sortfield, $sortorder, 'right ');
		}
		if (!empty($arrayfields['valuebilled']['checked'])) {
			print_liste_field_titre($arrayfields['valuebilled']['label'], $_SERVER['PHP_SELF'], 'il.total_ht', '', $param, '', $sortfield, $sortorder, 'center ', $langs->trans("SelectLinesOfTimeSpentToInvoice"));
		}
		/*
		// Extra fields
		include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_list_search_title.tpl.php';
		*/
		

		// Hook fields
		$parameters = array('arrayfields'=>$arrayfields, 'param'=>$param, 'sortfield'=>$sortfield, 'sortorder'=>$sortorder);
		$reshook = $hookmanager->executeHooks('printFieldListTitle', $parameters); // Note that $action and $object may have been modified by hook
		print $hookmanager->resPrint;
		print_liste_field_titre($selectedfields, $_SERVER["PHP_SELF"], "", '', '', 'width="80"', $sortfield, $sortorder, 'center maxwidthsearch ');
		print "</tr>\n";

		$tasktmp = new Task($db);
		$tmpinvoice = new Facture($db);

		$i = 0;

		$childids = $user->getAllChildIds();

		$total = 0;
		$totalvalue = 0;
		$totalarray = array();
		foreach ($tasks as $task_time) {
			if ($i >= $limit) {
				break;
			}

			$date1 = $db->jdate($task_time->task_date);
			$date2 = $db->jdate($task_time->task_datehour);

			print '<tr class="oddeven">';
			
						// By User
			if (!empty($arrayfields['author']['checked'])) {
				print '<td class="tdoverflowmax100">';
				if ($action == 'editline' && $_GET['lineid'] == $task_time->rowid) {
					if (empty($object->id)) {
						$object->fetch($id);
					}
					$contactsoftask = $object->getListContactId('internal');
					if (!in_array($task_time->fk_user, $contactsoftask)) {
						$contactsoftask[] = $task_time->fk_user;
					}
					if (count($contactsoftask) > 0) {
						print img_object('', 'user', 'class="hideonsmartphone"');
						print $form->select_dolusers($task_time->fk_user, 'userid_line', 0, '', 0, '', $contactsoftask, '0', 0, 0, '', 0, '', 'maxwidth200');
					} else {
						print img_error($langs->trans('FirstAddRessourceToAllocateTime')).$langs->trans('FirstAddRessourceToAllocateTime');
					}
				} else {
					$value = price2num($task_time->thm * $task_time->task_duration / 3600, 'MT', 1);
					print '<input id="spendu'.$i.'" name="spendu[]" type="checkbox" value="'. $task_time->rowid.'|'.$task_time->fk_user.'|'.$value.'"  /> ';
					print '<input id="spendnomu'.$i.'" type="hidden" value="'.$task_time->firstname.' '.$task_time->lastname.'"  /> ';
					
					$userstatic->id = $task_time->fk_user;
					$userstatic->lastname = $task_time->lastname;
					$userstatic->firstname = $task_time->firstname;
					$userstatic->photo = $task_time->photo;
					$userstatic->statut = $task_time->user_status;
					print $userstatic->getNomUrl(-1);
				}
				print '</td>';
				if (!$i) {
					$totalarray['nbfield']++;
				}
			}

			// Date
			if (!empty($arrayfields['t.task_date']['checked'])) {
				print '<td class="nowrap">';
				if ($action == 'editline' && $_GET['lineid'] == $task_time->rowid) {
					if (empty($task_time->task_date_withhour)) {
						print $form->selectDate(($date2 ? $date2 : $date1), 'timeline', 3, 3, 2, "timespent_date", 1, 0);
					} else {
						print $form->selectDate(($date2 ? $date2 : $date1), 'timeline', 1, 1, 2, "timespent_date", 1, 0);
					}
				} else {
					print dol_print_date(($date2 ? $date2 : $date1), ($task_time->task_date_withhour ? 'dayhour' : 'day'));
				}
				print '</td>';
				if (!$i) {
					$totalarray['nbfield']++;
				}
			}

			// Task ref
			if (!empty($arrayfields['t.task_ref']['checked'])) {
				if ((empty($id) && empty($ref)) || !empty($projectidforalltimes)) {   // Not a dedicated task
					print '<td class="nowrap">';
					if ($action == 'editline' && $_GET['lineid'] == $task_time->rowid) {
						$formproject->selectTasks(-1, GETPOST('taskid', 'int') ? GETPOST('taskid', 'int') : $task_time->fk_task, 'taskid', 0, 0, 1, 1, 0, 0, 'maxwidth300', $projectstatic->id, '');
					} else {
						$tasktmp->id = $task_time->fk_task;
						$tasktmp->ref = $task_time->ref;
						$tasktmp->label = $task_time->label;
						print $tasktmp->getNomUrl(1, 'withproject', 'time');
					}
					print '</td>';
					if (!$i) {
						$totalarray['nbfield']++;
					}
				}
			} else {
				print '<input type="hidden" name="taskid" value="'.$id.'">';
			}

			// Task label
			if (!empty($arrayfields['t.task_label']['checked'])) {
				if ((empty($id) && empty($ref)) || !empty($projectidforalltimes)) {	// Not a dedicated task
					print '<td class="nowrap tdoverflowmax300" title="'.dol_escape_htmltag($task_time->label).'">';
					print dol_escape_htmltag($task_time->label);
					print '</td>';
					if (!$i) {
						$totalarray['nbfield']++;
					}
				}
			}

			// Note
			if (!empty($arrayfields['t.note']['checked'])) {
				print '<td class="small">';
				if ($action == 'editline' && $_GET['lineid'] == $task_time->rowid) {
					print '<textarea name="timespent_note_line" width="95%" rows="'.ROWS_2.'">'.$task_time->note.'</textarea>';
				} else {
					print dol_nl2br($task_time->note);
				}
				print '</td>';
				if (!$i) {
					$totalarray['nbfield']++;
				}
			} elseif ($action == 'editline' && $_GET['lineid'] == $task_time->rowid) {
				print '<input type="hidden" name="timespent_note_line" value="'.$task_time->note.'">';
			}

			// Time spent
			if (!empty($arrayfields['t.task_duration']['checked'])) {
				print '<td class="right">';
				if ($action == 'editline' && $_GET['lineid'] == $task_time->rowid) {
					print '<input type="hidden" name="old_duration" value="'.$task_time->task_duration.'">';
					print $form->select_duration('new_duration', $task_time->task_duration, 0, 'text');
				} else {
					print convertSecondToTime($task_time->task_duration, 'allhourmin');
				}
				print '</td>';
				if (!$i) {
					$totalarray['nbfield']++;
				}
				if (!$i) {
					$totalarray['pos'][$totalarray['nbfield']] = 't.task_duration';
				}
				$totalarray['val']['t.task_duration'] += $task_time->task_duration;
				if (!$i) {
					$totalarray['totaldurationfield'] = $totalarray['nbfield'];
				}
				$totalarray['totalduration'] += $task_time->task_duration;
			}

			// Value spent
			if (!empty($arrayfields['value']['checked'])) {
				$langs->load("salaries");

				print '<td class="nowraponall right">';
				$value = price2num($task_time->thm * $task_time->task_duration / 3600, 'MT', 1);
				print '<span class="amount" title="'.$langs->trans("THM").': '.price($task_time->thm).'">';
				print price($value, 1, $langs, 1, -1, -1, $conf->currency);
				print '</span>';
				print '</td>';
				if (!$i) {
					$totalarray['nbfield']++;
				}
				if (!$i) {
					$totalarray['pos'][$totalarray['nbfield']] = 'value';
				}
				$totalarray['val']['value'] += $value;
				if (!$i) {
					$totalarray['totalvaluefield'] = $totalarray['nbfield'];
				}
				$totalarray['totalvalue'] += $value;
			}

			// Invoiced
			if (!empty($arrayfields['valuebilled']['checked'])) {
				print '<td class="center">'; // invoice_id and invoice_line_id
				if (empty($conf->global->PROJECT_HIDE_TASKS) && !empty($conf->global->PROJECT_BILL_TIME_SPENT)) {
					if ($projectstatic->usage_bill_time) {
						if ($task_time->invoice_id) {
							$result = $tmpinvoice->fetch($task_time->invoice_id);
							if ($result > 0) {
								print $tmpinvoice->getNomUrl(1);
							}
						} else {
							print $langs->trans("No");
						}
					} else {
						print '<span class="opacitymedium">'.$langs->trans("NA").'</span>';
					}
				}
				print '</td>';
				if (!$i) {
					$totalarray['nbfield']++;
				}
			}

			/*
			// Extra fields
			include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_list_print_fields.tpl.php';
			*/

			// Fields from hook
			$parameters = array('arrayfields'=>$arrayfields, 'obj'=>$task_time, 'i'=>$i, 'totalarray'=>&$totalarray);
			$reshook = $hookmanager->executeHooks('printFieldListValue', $parameters); // Note that $action and $object may have been modified by hook
			print $hookmanager->resPrint;

			// Action column
			print '<td  class="center nowraponall">';
			/*if (($action == 'editline' || $action == 'splitline') && $_GET['lineid'] == $task_time->rowid) {
				print '<input type="hidden" name="lineid" value="'.$_GET['lineid'].'">';
				print '<input type="submit" class="button buttongen margintoponlyshort marginbottomonlyshort button-save" name="save" value="'.$langs->trans("Save").'">';
				print '<br>';
				print '<input type="submit" class="button buttongen margintoponlyshort marginbottomonlyshort button-cancel" name="cancel" value="'.$langs->trans("Cancel").'">';
			} elseif ($user->rights->projet->lire || $user->rights->projet->all->creer) {	 // Read project and enter time consumed on assigned tasks
				if ($task_time->fk_user == $user->id || in_array($task_time->fk_user, $childids) || $user->rights->projet->all->creer) {
					if ($conf->MAIN_FEATURES_LEVEL >= 2) {
						print '&nbsp;';
						print '<a class="reposition" href="'.$_SERVER["PHP_SELF"].'?id='.$task_time->fk_task.'&amp;action=splitline&amp;lineid='.$task_time->rowid.$param.((empty($id) || $tab == 'timespent') ? '&tab=timespent' : '').'">';
						print img_split();
						print '</a>';
					}

					print '&nbsp;';
					print '<a class="reposition editfielda" href="'.$_SERVER["PHP_SELF"].'?id='.$task_time->fk_task.'&amp;action=editline&amp;lineid='.$task_time->rowid.$param.((empty($id) || $tab == 'timespent') ? '&tab=timespent' : '').'">';
					print img_edit();
					print '</a>';

					print '&nbsp;';
					print '<a class="reposition paddingleft" href="'.$_SERVER["PHP_SELF"].'?id='.$task_time->fk_task.'&amp;action=deleteline&amp;token='.newToken().'&amp;lineid='.$task_time->rowid.$param.((empty($id) || $tab == 'timespent') ? '&tab=timespent' : '').'">';
					print img_delete('default', 'class="pictodelete paddingleft"');
					print '</a>';

					if ($massactionbutton || $massaction) {	// If we are in select mode (massactionbutton defined) or if we have already selected and sent an action ($massaction) defined
						$selected = 0;
						if (in_array($task_time->rowid, $arrayofselected)) {
							$selected = 1;
						}
						print '&nbsp;';
						print '<input id="cb'.$task_time->rowid.'" class="flat checkforselect marginleftonly" type="checkbox" name="toselect[]" value="'.$task_time->rowid.'"'.($selected ? ' checked="checked"' : '').'>';
					}
				}
			}*/
			print '</td>';
			if (!$i) {
				$totalarray['nbfield']++;
			}

			print "</tr>\n";


			// Add line to split

			if ($action == 'splitline' && $_GET['lineid'] == $task_time->rowid) {
				print '<tr class="oddeven">';
				
								// User
				if (!empty($arrayfields['author']['checked'])) {
					print '<td>';
					if ($action == 'splitline' && $_GET['lineid'] == $task_time->rowid) {
						if (empty($object->id)) {
							$object->fetch($id);
						}
						$contactsoftask = $object->getListContactId('internal');
						if (!in_array($task_time->fk_user, $contactsoftask)) {
							$contactsoftask[] = $task_time->fk_user;
						}
						if (count($contactsoftask) > 0) {
							print img_object('', 'user', 'class="hideonsmartphone"');
							print $form->select_dolusers($task_time->fk_user, 'userid_line', 0, '', 0, '', $contactsoftask);
						} else {
							print img_error($langs->trans('FirstAddRessourceToAllocateTime')).$langs->trans('FirstAddRessourceToAllocateTime');
						}
					} else {
						$userstatic->id = $task_time->fk_user;
						$userstatic->lastname = $task_time->lastname;
						$userstatic->firstname = $task_time->firstname;
						$userstatic->photo = $task_time->photo;
						$userstatic->statut = $task_time->user_status;
						print $userstatic->getNomUrl(-1);
					}
					print '</td>';
				}

				// Date
				if (!empty($arrayfields['t.task_date']['checked'])) {
					print '<td class="nowrap">';
					if ($action == 'splitline' && $_GET['lineid'] == $task_time->rowid) {
						if (empty($task_time->task_date_withhour)) {
							print $form->selectDate(($date2 ? $date2 : $date1), 'timeline', 3, 3, 2, "timespent_date", 1, 0);
						} else {
							print $form->selectDate(($date2 ? $date2 : $date1), 'timeline', 1, 1, 2, "timespent_date", 1, 0);
						}
					} else {
						print dol_print_date(($date2 ? $date2 : $date1), ($task_time->task_date_withhour ? 'dayhour' : 'day'));
					}
					print '</td>';
				}

				// Task ref
				if (!empty($arrayfields['t.task_ref']['checked'])) {
					if ((empty($id) && empty($ref)) || !empty($projectidforalltimes)) {	// Not a dedicated task
						print '<td class="nowrap">';
						$tasktmp->id = $task_time->fk_task;
						$tasktmp->ref = $task_time->ref;
						$tasktmp->label = $task_time->label;
						print $tasktmp->getNomUrl(1, 'withproject', 'time');
						print '</td>';
					}
				}

				// Task label
				if (!empty($arrayfields['t.task_label']['checked'])) {
					if ((empty($id) && empty($ref)) || !empty($projectidforalltimes)) {	// Not a dedicated task
						print '<td class="tdoverflowmax300" title="'.dol_escape_htmltag($task_time->label).'">';
						print dol_escape_htmltag($task_time->label);
						print '</td>';
					}
				}


				// Note
				if (!empty($arrayfields['t.note']['checked'])) {
					print '<td class="tdoverflowmax300">';
					if ($action == 'splitline' && $_GET['lineid'] == $task_time->rowid) {
						print '<textarea name="timespent_note_line" width="95%" rows="'.ROWS_2.'">'.$task_time->note.'</textarea>';
					} else {
						print dol_nl2br($task_time->note);
					}
					print '</td>';
				} elseif ($action == 'splitline' && $_GET['lineid'] == $task_time->rowid) {
					print '<input type="hidden" name="timespent_note_line" value="'.$task_time->note.'">';
				}

				// Time spent
				if (!empty($arrayfields['t.task_duration']['checked'])) {
					print '<td class="right">';
					if ($action == 'splitline' && $_GET['lineid'] == $task_time->rowid) {
						print '<input type="hidden" name="old_duration" value="'.$task_time->task_duration.'">';
						print $form->select_duration('new_duration', $task_time->task_duration, 0, 'text');
					} else {
						print convertSecondToTime($task_time->task_duration, 'allhourmin');
					}
					print '</td>';
				}

				// Value spent
				if (!empty($arrayfields['value']['checked'])) {
					print '<td class="right">';
					$value = price2num($task_time->thm * $task_time->task_duration / 3600, 'MT', 1);
					print price($value, 1, $langs, 1, -1, -1, $conf->currency);
					print '</td>';
				}

				// Value billed
				if (!empty($arrayfields['valuebilled']['checked'])) {
					print '<td class="right">';
					$valuebilled = price2num($task_time->total_ht, '', 1);
					if (isset($task_time->total_ht)) {
						print price($valuebilled, 1, $langs, 1, -1, -1, $conf->currency);
					}
					print '</td>';
				}

				/*
				 // Extra fields
				 include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_list_print_fields.tpl.php';
				 */

				// Fields from hook
				$parameters = array('arrayfields'=>$arrayfields, 'obj'=>$task_time);
				$reshook = $hookmanager->executeHooks('printFieldListValue', $parameters); // Note that $action and $object may have been modified by hook
				print $hookmanager->resPrint;

				// Action column
				print '<td class="center nowraponall">';
				print '</td>';

				print "</tr>\n";


				// Line for second dispatching

				print '<tr class="oddeven">';
				
								// User
				if (!empty($arrayfields['author']['checked'])) {
					print '<td>';
					if ($action == 'splitline' && $_GET['lineid'] == $task_time->rowid) {
						if (empty($object->id)) {
							$object->fetch($id);
						}
						$contactsoftask = $object->getListContactId('internal');
						if (!in_array($task_time->fk_user, $contactsoftask)) {
							$contactsoftask[] = $task_time->fk_user;
						}
						if (count($contactsoftask) > 0) {
							print img_object('', 'user', 'class="hideonsmartphone"');
							print $form->select_dolusers($task_time->fk_user, 'userid_line_2', 0, '', 0, '', $contactsoftask);
						} else {
							print img_error($langs->trans('FirstAddRessourceToAllocateTime')).$langs->trans('FirstAddRessourceToAllocateTime');
						}
					} else {
						$userstatic->id = $task_time->fk_user;
						$userstatic->lastname = $task_time->lastname;
						$userstatic->firstname = $task_time->firstname;
						$userstatic->photo = $task_time->photo;
						$userstatic->statut = $task_time->user_status;
						print $userstatic->getNomUrl(-1);
					}
					print '</td>';
				}

				// Date
				if (!empty($arrayfields['t.task_date']['checked'])) {
					print '<td class="nowrap">';
					if ($action == 'splitline' && $_GET['lineid'] == $task_time->rowid) {
						if (empty($task_time->task_date_withhour)) {
							print $form->selectDate(($date2 ? $date2 : $date1), 'timeline_2', 3, 3, 2, "timespent_date", 1, 0);
						} else {
							print $form->selectDate(($date2 ? $date2 : $date1), 'timeline_2', 1, 1, 2, "timespent_date", 1, 0);
						}
					} else {
						print dol_print_date(($date2 ? $date2 : $date1), ($task_time->task_date_withhour ? 'dayhour' : 'day'));
					}
					print '</td>';
				}

				// Task ref
				if (!empty($arrayfields['t.task_ref']['checked'])) {
					if ((empty($id) && empty($ref)) || !empty($projectidforalltimes)) {	// Not a dedicated task
						print '<td class="nowrap">';
						$tasktmp->id = $task_time->fk_task;
						$tasktmp->ref = $task_time->ref;
						$tasktmp->label = $task_time->label;
						print $tasktmp->getNomUrl(1, 'withproject', 'time');
						print '</td>';
					}
				}

				// Task label
				if (!empty($arrayfields['t.task_label']['checked'])) {
					if ((empty($id) && empty($ref)) || !empty($projectidforalltimes)) {	// Not a dedicated task
						print '<td class="nowrap">';
						print $task_time->label;
						print '</td>';
					}
				}

				// Note
				if (!empty($arrayfields['t.note']['checked'])) {
					print '<td class="small tdoverflowmax300"">';
					if ($action == 'splitline' && $_GET['lineid'] == $task_time->rowid) {
						print '<textarea name="timespent_note_line_2" width="95%" rows="'.ROWS_2.'">'.$task_time->note.'</textarea>';
					} else {
						print dol_nl2br($task_time->note);
					}
					print '</td>';
				} elseif ($action == 'splitline' && $_GET['lineid'] == $task_time->rowid) {
					print '<input type="hidden" name="timespent_note_line_2" value="'.$task_time->note.'">';
				}

				// Time spent
				if (!empty($arrayfields['t.task_duration']['checked'])) {
					print '<td class="right">';
					if ($action == 'splitline' && $_GET['lineid'] == $task_time->rowid) {
						print '<input type="hidden" name="old_duration_2" value="0">';
						print $form->select_duration('new_duration_2', 0, 0, 'text');
					} else {
						print convertSecondToTime($task_time->task_duration, 'allhourmin');
					}
					print '</td>';
				}

				// Value spent
				if (!empty($arrayfields['value']['checked'])) {
					print '<td class="right">';
					$value = 0;
					print price($value, 1, $langs, 1, -1, -1, $conf->currency);
					print '</td>';
				}

				// Value billed
				if (!empty($arrayfields['valuebilled']['checked'])) {
					print '<td class="right">';
					$valuebilled = price2num($task_time->total_ht, '', 1);
					if (isset($task_time->total_ht)) {
						print price($valuebilled, 1, $langs, 1, -1, -1, $conf->currency);
					}
					print '</td>';
				}

				/*
				 // Extra fields
				 include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_list_print_fields.tpl.php';
				 */

				// Fields from hook
				$parameters = array('arrayfields'=>$arrayfields, 'obj'=>$task_time);
				$reshook = $hookmanager->executeHooks('printFieldListValue', $parameters); // Note that $action and $object may have been modified by hook
				print $hookmanager->resPrint;

				// Action column
				print '<td class="center nowraponall">';
				print '</td>';

				print "</tr>\n";
			}

			$i++;
		}

		// Show total line
		//include DOL_DOCUMENT_ROOT.'/core/tpl/list_print_total.tpl.php';
		if (isset($totalarray['totaldurationfield']) || isset($totalarray['totalvaluefield'])) {
			print '<tr class="liste_total">';
			$i = 0;
			while ($i < $totalarray['nbfield']) {
				$i++;
				if ($i == 1) {
					if ($num < $limit && empty($offset)) {
						print '<td class="left">'.$langs->trans("Total").'</td>';
					} else {
						print '<td class="left">'.$langs->trans("Totalforthispage").'</td>';
					}
				} elseif ($totalarray['totaldurationfield'] == $i) {
					print '<td class="right">'.convertSecondToTime($totalarray['totalduration'], 'allhourmin').'</td>';
				} elseif ($totalarray['totalvaluefield'] == $i) {
					print '<td class="right">'.price($totalarray['totalvalue']).'</td>';
					//} elseif ($totalarray['totalvaluebilledfield'] == $i) { print '<td class="center">'.price($totalarray['totalvaluebilled']).'</td>';
				} else {
					print '<td></td>';
				}
			}
			print '</tr>';
		}

		if (!count($tasks)) {
			$totalnboffields = 1;
			foreach ($arrayfields as $value) {
				if ($value['checked']) {
					$totalnboffields++;
				}
			}
			print '<tr class="oddeven"><td colspan="'.$totalnboffields.'">';
			print '<span class="opacitymedium">'.$langs->trans("None").'</span>';
			print '</td></tr>';
		}


		print "</table>";
		print '</div>';
		print "</form>";
	//}
//}

?>

<script>
	//function CreateSal(){$("#dialog-confirm").dialog("open");}

CreateSal = function()
{
	var emps = [[0,0,"Dummy"]];
	var cn = 0;
	var rows = (document.querySelectorAll("#tosalary tr")).length;
	for(var i=0; i<=rows; i++)
	{
		if($("#spendu"+i).length > 0)
		{
			//value="'. $task_time->rowid.'|'.$task_time->fk_user.'|'.$value.'"
			if($('#spendu'+i).is(':checked'))
			{
				var val_ = $('#spendu'+i).val();
				pos = -1;
				var ep = val_.split("|");
				for (var y = 0; y < emps.length; y++) {
					if(emps[y][0]==ep[1])
					{
						pos = y;
					}
				}
				if(pos!=-1)
				{
					emps[pos][1]=emps[pos][1]+Number(ep[2]);
				}
				else
				{
					emps.push([ep[1], Number(ep[2]), $('#spendnomu'+i).val()]);
				}
				cn++;
			}
				
		}
			
	}
	
	
	if(cn > 0)
	{
		//console.log(emps);
		$("#dialog-confirm").dialog("open");

		var table_ = '<div class="div-table-responsive">';
		table_ += '<table id="employesfin" class="tagtable nobottomiftotal liste">';
		table_ += '<tr class="liste_titre_filter">';
		
		table_ += '<td class="liste_titre"><?php echo $arrayfields['author']['label']?></td>';
		table_ += '<td class="liste_titre right"><?php echo $arrayfields['value']['label']?></td>';
		table_ += '</tr>';
		
		for(var emp_ of emps)
		{
			if(emp_[0]>0)
			{
				var vl_ = roundToTwo(emp_[1]);
				table_ += '<tr >';
				table_ += '<td class="small">'+emp_[2]+'</td>';
				table_ += '<td class="nowraponall right">'+vl_+'</td>';
				table_ += '</tr>';
			}
		}
		
		table_ += '</table>';
		
		$("#dialog-confirm").html(table_);

		//$("#dialog-confirm").html('Por favor seleccione almenos un albarán');
		//$("#dialog-confirm").dialog("open");
	}
	else
	{
		alert("<?php echo $langs->trans("Plis_Select_Spend")?>");
		//$("#dialog-confirm").html('Por favor seleccione almenos un albarán');
		//$("#dialog-confirm").dialog("open");
	}
	
	
}
$( document ).ready(function() {
	
	//var rows = (document.querySelectorAll(".ui-dialog-buttonset button")).length;
	setTimeout(function(){
		var btns = document.querySelectorAll(".ui-dialog-buttonset > button");
		for(var i=0; i<=btns.length; i++)
		{
			
			if(btns[i] && i == 0)
			{
				$(btns[i]).unbind( "click" );
				$(btns[i]).bind( "click", function() {
					$("#frmspendsalary").submit();
				});
			}
		}
	}, 2000);
	});

function roundToTwo(num) {
  var sal_ = +(Math.round(num + "e+2")  + "e-2");
  var retval = sal_;
  console.log( sal_);
  part_ = (sal_.toString()).split(".");
  if( typeof part_[1] === 'undefined')
  		retval = part_[0]+".00";
  else if(part_[1].length == 1)
  		retval = part_[0]+"."+part_[1]+"0";
  
  return retval;
}
</script>
<?php

// End of page
llxFooter();
$db->close();
