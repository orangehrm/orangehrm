<?php

namespace OrangeHRM\Installer\Migration\V4_3_3;

use Doctrine\DBAL\Types\Types;

class Migration extends \OrangeHRM\Installer\Util\V1\AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $this->getSchemaHelper()->changeColumn('hs_hr_employee', 'emp_number', [
            'Type'=>Types::INTEGER,'Length'=> 7, 'Notnull' => true,'Autoincrement'=>true
        ]);
        $this->getSchemaHelper()->changeColumn('ohrm_timesheet', 'timesheet_id', [
            'Type'=>Types::BIGINT,'Length'=> 20, 'Notnull' => true,'Autoincrement'=>true
        ]);
        $this->getSchemaHelper()->changeColumn('ohrm_timesheet_item', 'timesheet_item_id', [
            'Type'=>Types::BIGINT,'Length'=> 20, 'Notnull' => true,'Autoincrement'=>true
        ]);
        $this->getSchemaHelper()->changeColumn('ohrm_timesheet_action_log', 'timesheet_action_log_id', [
            'Type'=>Types::BIGINT,'Length'=> 20, 'Notnull' => true,'Autoincrement'=>true
        ]);
        $this->getSchemaHelper()->changeColumn('ohrm_attendance_record', 'id', [
            'Type'=>Types::BIGINT,'Length'=> 20, 'Notnull' => true,'Autoincrement'=>true
        ]);
        $this->getSchemaHelper()->changeColumn('ohrm_job_candidate', 'id', [
            'Type'=>Types::INTEGER,'Length'=> 13, 'Notnull' => true,'Autoincrement'=>true
        ]);
        $this->getSchemaHelper()->changeColumn('ohrm_job_candidate_vacancy', 'id', [
            'Type'=>Types::INTEGER,'Length'=> 13, 'Notnull' => true,'Autoincrement'=>true
        ]);
        $this->getSchemaHelper()->changeColumn('ohrm_job_vacancy', 'id', [
            'Type'=>Types::INTEGER,'Length'=> 13, 'Notnull' => true,'Autoincrement'=>true
        ]);
    }
}
