<?php
/* Copyright (C) 2021 SuperAdmin
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    timespenttosalary/class/actions_timespenttosalary.class.php
 * \ingroup timespenttosalary
 * \brief   Example hook overload.
 *
 * Put detailed description here.
 */

/**
 * Class ActionsTimeSpentToSalary
 */
class ActionsTimeSpentToSalary
{
	/**
	 * @var DoliDB Database handler.
	 */
	public $db;

	/**
	 * @var string Error code (or message)
	 */
	public $error = '';

	/**
	 * @var array Errors
	 */
	public $errors = array();


	/**
	 * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = array();

	/**
	 * @var string String displayed by executeHook() immediately after return
	 */
	public $resprints;


	/**
	 * Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}


	/**
	 * Execute action
	 *
	 * @param	array			$parameters		Array of parameters
	 * @param	CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param	string			$action      	'add', 'update', 'view'
	 * @return	int         					<0 if KO,
	 *                           				=0 if OK but we want to process standard actions too,
	 *                            				>0 if OK and we want to replace standard actions.
	 */
	public function getNomUrl($parameters, &$object, &$action)
	{
		global $db, $langs, $conf, $user;
		$this->resprints = '';
		return 0;
	}

	/**
	 * Overloading the doActions function : replacing the parent's function with the one below
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function doActions($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$error = 0; // Error counter

		/* print_r($parameters); print_r($object); echo "action: " . $action; */
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {	    // do something only for the context 'somecontext1' or 'somecontext2'
			// Do what you want here...
			// You can for example call global vars like $fieldstosearchall to overwrite them, or update database depending on $action and $_POST values.
		}

		if (!$error) {
			$this->results = array('myreturn' => 999);
			$this->resprints = 'A text to show';
			return 0; // or return 1 to replace standard code
		} else {
			$this->errors[] = 'Error message';
			return -1;
		}
	}


	/**
	 * Overloading the doMassActions function : replacing the parent's function with the one below
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function doMassActions($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$error = 0; // Error counter

		/* print_r($parameters); print_r($object); echo "action: " . $action; */
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {		// do something only for the context 'somecontext1' or 'somecontext2'
			foreach ($parameters['toselect'] as $objectid) {
				// Do action on each object id
			}
		}

		if (!$error) {
			$this->results = array('myreturn' => 999);
			$this->resprints = 'A text to show';
			return 0; // or return 1 to replace standard code
		} else {
			$this->errors[] = 'Error message';
			return -1;
		}
	}


	/**
	 * Overloading the addMoreMassActions function : replacing the parent's function with the one below
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function addMoreMassActions($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$error = 0; // Error counter
		$disabled = 1;

		/* print_r($parameters); print_r($object); echo "action: " . $action; */
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {		// do something only for the context 'somecontext1' or 'somecontext2'
			$this->resprints = '<option value="0"'.($disabled ? ' disabled="disabled"' : '').'>'.$langs->trans("TimeSpentToSalaryMassAction").'</option>';
		}

		if (!$error) {
			return 0; // or return 1 to replace standard code
		} else {
			$this->errors[] = 'Error message';
			return -1;
		}
	}



	/**
	 * Execute action
	 *
	 * @param	array	$parameters     Array of parameters
	 * @param   Object	$object		   	Object output on PDF
	 * @param   string	$action     	'add', 'update', 'view'
	 * @return  int 		        	<0 if KO,
	 *                          		=0 if OK but we want to process standard actions too,
	 *  	                            >0 if OK and we want to replace standard actions.
	 */
	public function beforePDFCreation($parameters, &$object, &$action)
	{
		global $conf, $user, $langs;
		global $hookmanager;

		$outputlangs = $langs;

		$ret = 0; $deltemp = array();
		dol_syslog(get_class($this).'::executeHooks action='.$action);

		/* print_r($parameters); print_r($object); echo "action: " . $action; */
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {		// do something only for the context 'somecontext1' or 'somecontext2'
		}

		return $ret;
	}

	/**
	 * Execute action
	 *
	 * @param	array	$parameters     Array of parameters
	 * @param   Object	$pdfhandler     PDF builder handler
	 * @param   string	$action         'add', 'update', 'view'
	 * @return  int 		            <0 if KO,
	 *                                  =0 if OK but we want to process standard actions too,
	 *                                  >0 if OK and we want to replace standard actions.
	 */
	public function afterPDFCreation($parameters, &$pdfhandler, &$action)
	{
		global $conf, $user, $langs;
		global $hookmanager;

		$outputlangs = $langs;

		$ret = 0; $deltemp = array();
		dol_syslog(get_class($this).'::executeHooks action='.$action);

		/* print_r($parameters); print_r($object); echo "action: " . $action; */
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {
			// do something only for the context 'somecontext1' or 'somecontext2'
		}

		return $ret;
	}



	/**
	 * Overloading the loadDataForCustomReports function : returns data to complete the customreport tool
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function loadDataForCustomReports($parameters, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$langs->load("timespenttosalary@timespenttosalary");

		$this->results = array();

		$head = array();
		$h = 0;

		if ($parameters['tabfamily'] == 'timespenttosalary') {
			$head[$h][0] = dol_buildpath('/module/index.php', 1);
			$head[$h][1] = $langs->trans("Home");
			$head[$h][2] = 'home';
			$h++;

			$this->results['title'] = $langs->trans("TimeSpentToSalary");
			$this->results['picto'] = 'timespenttosalary@timespenttosalary';
		}

		$head[$h][0] = 'customreports.php?objecttype='.$parameters['objecttype'].(empty($parameters['tabfamily']) ? '' : '&tabfamily='.$parameters['tabfamily']);
		$head[$h][1] = $langs->trans("CustomReports");
		$head[$h][2] = 'customreports';

		$this->results['head'] = $head;

		return 1;
	}



	/**
	 * Overloading the restrictedArea function : check permission on an object
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int 		      			  	<0 if KO,
	 *                          				=0 if OK but we want to process standard actions too,
	 *  	                            		>0 if OK and we want to replace standard actions.
	 */
	public function restrictedArea($parameters, &$action, $hookmanager)
	{
		global $user;

		if ($parameters['features'] == 'myobject') {
			if ($user->rights->timespenttosalary->myobject->read) {
				$this->results['result'] = 1;
				return 1;
			} else {
				$this->results['result'] = 0;
				return 1;
			}
		}

		return 0;
	}

	/**
	 * Execute action completeTabsHead
	 *
	 * @param   array           $parameters     Array of parameters
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         'add', 'update', 'view'
	 * @param   Hookmanager     $hookmanager    hookmanager
	 * @return  int                             <0 if KO,
	 *                                          =0 if OK but we want to process standard actions too,
	 *                                          >0 if OK and we want to replace standard actions.
	 */
	public function completeTabsHead(&$parameters, &$object, &$action, $hookmanager)
	{
		global $langs, $conf, $user;

		if (!isset($parameters['object']->element)) {
			return 0;
		}
		if ($parameters['mode'] == 'remove') {
			// utilisé si on veut faire disparaitre des onglets.
			return 0;
		} elseif ($parameters['mode'] == 'add') {
			$langs->load('timespenttosalary@timespenttosalary');
			// utilisé si on veut ajouter des onglets.
			$counter = count($parameters['head']);
			$element = $parameters['object']->element;
			$id = $parameters['object']->id;
			// verifier le type d'onglet comme member_stats où ça ne doit pas apparaitre
			// if (in_array($element, ['societe', 'member', 'contrat', 'fichinter', 'project', 'propal', 'commande', 'facture', 'order_supplier', 'invoice_supplier'])) {
			if (in_array($element, ['context1', 'context2'])) {
				$datacount = 0;

				$parameters['head'][$counter][0] = dol_buildpath('/timespenttosalary/timespenttosalary_tab.php', 1) . '?id=' . $id . '&amp;module='.$element;
				$parameters['head'][$counter][1] = $langs->trans('TimeSpentToSalaryTab');
				if ($datacount > 0) {
					$parameters['head'][$counter][1] .= '<span class="badge marginleftonlyshort">' . $datacount . '</span>';
				}
				$parameters['head'][$counter][2] = 'timespenttosalaryemails';
				$counter++;
			}
			if ($counter > 0 && (int) DOL_VERSION < 14) {
				$this->results = $parameters['head'];
				// return 1 to replace standard code
				return 1;
			} else {
				// en V14 et + $parameters['head'] est modifiable par référence
				return 0;
			}
		}
	}

	/* Add here any other hooked methods... */
	
	public function printFieldListOption($parameters)
	{
		$this->resprints = '<td id="rating_col"></td>';
		$script = '<script>
						
					$.moveColumn = function (table, from, to) {
						var rows = $("tr", table);
						var cols;
						rows.each(function() {
						        cols = $(this).children("th, td");
						        cols.eq(from).detach().insertBefore(cols.eq(to));
						    });
					}
					CheckedSAll = function()
					{
						var ck = $("#CheckAll").is(":checked");
						var rows = (document.querySelectorAll("#tosalary tr")).length;
						for(var i=0; i<=rows; i++)
						{
							if($("#spendu"+i).length > 0)
							{
								$("#spendu"+i).prop( "checked", ck);
							}
						}
					}
					$( document ).ready(function() {
						var from = ($("#rating_col").index()-1);
						
						//console.log("==>"+from);
						var tablesp = $("#rating_col").parents("table:first");
						$.moveColumn(tablesp, from, from-1);
						
						
						if($("#Titlepor").length>0)
						{
							$("#Titlepor").prepend("<input id=\"CheckAll\" onclick=\"CheckedSAll()\" type=\"checkbox\" value=\"\"  /> ");
						}
					});
					</script>
					'; 
		echo $script;
	}
	
	public function printFieldListTitle($parameters)
	{
		$this->resprints = '<th class="wrapcolumntitle right liste_titre">Rate</th>';

	}
	
	public function printFieldListValue($parameters)
	{
		global $task_time,$conf, $langs, $action; 
		$value = price2num($task_time->thm, 'MT', 1);
		
		//echo $lineid."==>".$action;
		if($action=="editline" && GETPOST('lineid', 'int') == $task_time->rowid)
			$this->resprints = '<td class="nowraponall right"><input class="flat maxwidth100" type="text" name="rate" value="'.price($value, 1, $langs, 1, -1, -1, '').'"></td>';
		else
			$this->resprints = '<td class="nowraponall right">'.price($value, 1, $langs, 1, -1, -1, $conf->currency).'</td>';
		
	}
	
	public function  printFieldPreListTitle($parameters)
	{
		global $action, $user, $result, $db;
//print_r($parameters);
		if (in_array($parameters['currentcontext'], array('tasktimelist')) && $action =="createtime") {

			$Rate = price2num(GETPOST('rate', 'alpha'));

			$rates = $this->get_users_rate($db);
			//echo "dd";
			$script = '
					<script>
						var irate = 0;
						$( document ).ready(function() {
							
					            var tablespend = $(".div-table-responsive-no-min > table");
						    var tabletrspend = $(".div-table-responsive-no-min > table tr");
						   
						    var td_ = document.createElement("td");
						    td_.innerHTML = "Rate";
							tabletrspend[0].append(td_);
							
							var td2_ = document.createElement("td");
							var inp = document.createElement("input");
						    inp.setAttribute("class", "flat maxwidth100");
						    inp.setAttribute("type","text");
						    inp.setAttribute("name", "rate");
						    inp.setAttribute("id", "rate_val");
						    inp.setAttribute("value", "'.$Rate.'");
						    td2_.append(inp);
							tabletrspend[1].append(td2_);
							
							var tabletrspendact = $(".div-table-responsive-no-min > table tr");
							var cols_  = Object.keys(tabletrspendact[0]).length;
							
							console.log(cols_);
							
							$.moveColumn(tablespend, -1, -2);
							var rates_user = '.json_encode($rates).';
							$("#userid").change(function() {
								if (typeof rates_user[$("#userid").val()] != "undefined")
									$("#rate_val").val(rates_user[$("#userid").val()]);
								else
									$("#rate_val").val(0);
						       
						    });
						    
						    if (typeof rates_user[$("#userid").val()] != "undefined" && (irate > 0 || "'.$Rate.'" =="")) 
						   		$("#rate_val").val(rates_user[$("#userid").val()]);
						   	else
								if((irate > 0 || "'.$Rate.'" ==""))
									$("#rate_val").val(0);
							
								
							irate++;
						});
					</script>
			';
			
			echo $script;
		}
		elseif (in_array($parameters['currentcontext'], array('tasktimelist')) && ($action =="addtimespent" || $action == 'updateline' || $action == 'updatesplitline') && $user->rights->projet->lire)
		{
			
			$objec = new Task($db);
			$sqj = "select rowid, thm, timespent_id from timespenttosalary_logs where status = 0";
			$resqj = $objec->db->query($sqj);
			if($resqj) {
				$num = $objec->db->num_rows($resqj);
	
				$i = 0;
				while ($i < $num)
				{
					$obj = $objec->db->fetch_object($resqj);
				
				
					// Update hourly rate of this time spent entry
					$sqm = "UPDATE ".MAIN_DB_PREFIX."projet_task_time ";
					$sqm .= " SET thm = '".$obj->thm."'"; // set average hour rate of set manually
					$sqm .= " WHERE rowid = ".$obj->timespent_id;
					
					
					dol_syslog(get_class($objec)."::addTimeSpent", LOG_DEBUG);
					if (!$objec->db->query($sqm)) {
						$objec->error = $objec->db->lasterror();
					}
					else
					{
						// Update hourly rate of this time spent entry
						$sqm2 = "UPDATE timespenttosalary_logs ";
						$sqm2 .= " SET status = 1"; // set status
						$sqm2 .= " WHERE rowid = ".$obj->rowid;
						$objec->db->query($sqm2);
					}
					$i++;
				}
				
				if($i>0)
				{
					echo '<script type="text/JavaScript"> $( document ).ready(function() {$(".button_search").click();}); </script>';
				}
				
				
			}
		}
		
		
	}
	
	public function get_users_rate($db)
	{
		$rates = array();		
		$objec = new Task($db);
		$sqj = "SELECT rowid, thm FROM ".MAIN_DB_PREFIX."user ";
		$resqj = $objec->db->query($sqj);
		if($resqj) {
			$num = $objec->db->num_rows($resqj);
			$i = 0;
			while ($i < $num)
			{
				$obj = $objec->db->fetch_object($resqj);
				$rates[$obj->rowid] = $obj->thm == "" ? "0" : $obj->thm;
				$i++;
			}
		}
		
		return $rates;
	}
	/*public function setContentSecurityPolicy()
	{
		//$parameters

		echo '<button type="button" id="sidebarrightbutton" class="btn">
               <div class="notif"><i class="fas fa-bell"></i><i class="fas fa-circle "></i></div>
            </button><!-- Sidebar right -->
      <nav id="sidebarright" class="sidenav">
         <div id="dismiss"><i class="fas fa-times"></i></div>

         <div class="wrapperww">
  <div class="boxww a">A</div>
  <div class="boxww b">B</div>
  <div class="boxww c">C</div>
  <div class="boxww d">D</div>
  <div class="boxww e">E</div>
  <div class="boxww f">F</div>
  <div class="boxww g">G</div>
  <div class="boxww h">H</div>
  <div class="boxww i">I</div>
</div>
      </nav>
      <!-- .Sidebar right-->';
	}*/
}
