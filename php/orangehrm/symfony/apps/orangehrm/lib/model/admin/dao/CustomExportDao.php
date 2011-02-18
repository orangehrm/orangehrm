<?php
/**
 * Export DAO Class to save, retrieve, update Export model
 *
 * @author Sujith T
 */
class CustomExportDao extends BaseDao {

   /**
    * Get Custom Export List
    * @param String $orderField
    * @param String $orderBy
    * @return Collection
    * @throws DaoException
    */
   public function getCustomExportList($orderField = 'export_id', $orderBy = 'ASC') {
      try{

         $q = Doctrine_Query::create()
			    ->from('CustomExport ce')
			    ->orderBy($orderField . ' ' . $orderBy);

			$exportList = $q->execute();
			return  $exportList;

      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Saves Custom Export Model Object. saving/updating logic to be implemented later
    * @param CustomExport $customExport
    * @return boolean
    * @throws DaoException
    */
   public function saveCustomExport(CustomExport $customExport) {
      try {
         $customExport->save();
         return true;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Read Custom Export By the Id
    * @param int $id
    * @return CustomExport
    * @throws DaoException
    */
   public function readCustomExport($id) {
      try {
         $q = Doctrine_Query::create()
			    ->from('CustomExport')
			    ->where("export_id = ?", $id);

			$customExport = $q->fetchOne();
         return $customExport;

      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Search Custom Export
    * @param String $field
    * @param String $value
    * @return Collection
    * @throws DaoException
    */
   public function searchCustomExport($field, $value) {
      try {
         $q = Doctrine_Query::create()
             ->from('CustomExport')
             ->where($field . " = ?", $value);

         $exportList = $q->execute();
			return $exportList;

      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete Custom Export Model
    * @param int $id
    * @return boolean
    * @throws DaoException
    */
   public function deleteCustomExport($id) {
      try {
         $q = 	Doctrine_Query::create()
				->delete('CustomExport')
				->where('export_id = ?', $id);

         $numDeleted = $q->execute();
         if($numDeleted > 0) {
            return true;
         }
         return false;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

    /**
     * Get CSV Export Data
     *
     * @return DaoException
     */
    public function getCSVExportData() {

      $csv = array();

      try {
		$sql = "SELECT hs_hr_employee.emp_number, employee_id, emp_lastname, emp_firstname, emp_middle_name, emp_street1, emp_street2," .
		"city_code,provin_code,emp_zipcode,emp_gender,emp_birthday,emp_ssn_num,emp_status,joined_date, " .
		"tax_federal_status, tax_federal_exceptions, tax_state, tax_state_status, tax_state_exceptions, " .
		"tax_unemp_state,tax_work_state,custom1,custom2,custom3,custom4,custom5,custom6,custom7,custom8,custom9,custom10, " .
		" pay.payperiod_code,sal.ebsal_basic_salary,loc.loc_name,comp.title as workstation" .
		" FROM hs_hr_employee " .
		" LEFT JOIN hs_hr_emp_us_tax tax on (tax.emp_number = hs_hr_employee.emp_number) " .
		" LEFT JOIN hs_hr_emp_basicsalary sal on (hs_hr_employee.emp_number = sal.emp_number) " .
		" LEFT JOIN hs_hr_payperiod pay on (sal.payperiod_code = pay.payperiod_code) " .
		" LEFT JOIN hs_hr_compstructtree comp on (hs_hr_employee.work_station = comp.id) " .
		" LEFT JOIN hs_hr_location loc on (comp.loc_code = loc.loc_code) ";

		if (KeyHandler::keyExists()) {
			$key = KeyHandler::readKey();
			$sql = str_replace("emp_ssn_num", "IF(`emp_ssn_num` IS NOT NULL, AES_DECRYPT(emp_ssn_num, '$key'), '') AS `emp_ssn_num`", $sql);
			$sql = str_replace("sal.ebsal_basic_salary", "IF(`ebsal_basic_salary` IS NOT NULL, AES_DECRYPT(ebsal_basic_salary, '$key'), '') AS `ebsal_basic_salary`", $sql);
		}

          //
          // Direct query using PDO connection
          //
          $dbh = Doctrine_Manager::connection()->getDbh();
          $result = $dbh->query($sql);
          if ($result) {
              $csv = $result->fetchAll();
          }

          return ($csv);
          
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
    }
}
?>