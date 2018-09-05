<?php
/*****************************************************************************
*       PatientAllergyIterator.php
*
*       Author:  ClearHealth Inc. (www.clear-health.com)        2009
*       
*       ClearHealth(TM), HealthCloud(TM), WebVista(TM) and their 
*       respective logos, icons, and terms are registered trademarks 
*       of ClearHealth Inc.
*
*       Though this software is open source you MAY NOT use our 
*       trademarks, graphics, logos and icons without explicit permission. 
*       Derivitive works MUST NOT be primarily identified using our 
*       trademarks, though statements such as "Based on ClearHealth(TM) 
*       Technology" or "incoporating ClearHealth(TM) source code" 
*       are permissible.
*
*       This file is licensed under the GPL V3, you can find
*       a copy of that license by visiting:
*       http://www.fsf.org/licensing/licenses/gpl.html
*       
*****************************************************************************/


class PatientAllergyIterator extends WebVista_Model_ORMIterator implements Iterator {

	public function __construct($dbSelect = null,$autoLoad = true) {
		$this->_ormClass = 'PatientAllergy';
		// autoLoad gives an option to query the entire rows which takes time if more data in the table
		if ($autoLoad) {
			parent::__construct($this->_ormClass,$dbSelect);
		}
	}

	public function setFilters($filters) {
		$db = Zend_Registry::get('dbAdapter');
		$orm = new $this->_ormClass();

		$dbSelect = $db->select(null)
				->from($orm->_table);
		$fields = $orm->ORMFields();
		foreach ($filters as $field=>$value) {
			if (!in_array($field,$fields)) {
				continue;
			}
			switch ($field) {
				case 'dateRange':
					$dateRange = explode(';',$value);
					$start = isset($dateRange[0])?date('Y-m-d 00:00:00',strtotime($dateRange[0])):date('Y-m-d 00:00:00');
					$end = isset($dateRange[1])?date('Y-m-d 23:59:59',strtotime($dateRange[1])):date('Y-m-d 23:59:59',strtotime($start));
					$dbSelect->where("dateTimeCreated BETWEEN '{$start}' AND '{$end}'");
					break;
				default:
					$dbSelect->where($field.' = ?',$value);
			}
		}

		trigger_error($dbSelect->__toString(),E_USER_NOTICE);
		$this->_dbSelect = $dbSelect;
		$this->_dbStmt = $db->query($this->_dbSelect);
	}

}
