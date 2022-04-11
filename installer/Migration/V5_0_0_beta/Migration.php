<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\Installer\Migration\V5_0_0_beta;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Types\Types;
use OrangeHRM\Installer\Util\V1\AbstractMigration;

class Migration extends AbstractMigration
{
    private array $reportDisplayGroups = [];
    private array $emails = [];

    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $this->getSchemaHelper()->renameColumn('hs_hr_config', '`key`', 'name');
        $this->getSchemaHelper()->addColumn('ohrm_menu_item', '`additional_params`', Types::TEXT, [
            'Notnull' => false,
            'Default' => null,
            'Comment' => '(DC2Type:json)',
        ]);
        $this->updateHomePage('Admin', 'pim/viewPimModule');
        $this->updateHomePage('ESS', 'pim/viewPimModule');

        $this->updateMenuItemIconNames('admin', 'Admin');
        $this->updateMenuItemIconNames('pim', 'PIM');
        $this->updateMenuItemIconNames('time', 'Time');
        $this->updateMenuItemIconNames('leave', 'Leave');
        $this->updateMenuItemIconNames('recruitment', 'Recruitment');
        $this->updateMenuItemIconNames('myinfo', 'My Info');
        $this->updateMenuItemIconNames('performance', 'Performance');
        $this->updateMenuItemIconNames('dashboard', 'Dashboard');
        $this->updateMenuItemIconNames('directory', 'Directory');
        $this->updateMenuItemIconNames('maintenance', 'Maintenance');
        $this->updateMenuItemIconNames('buzz', 'Buzz');

        $this->getSchemaHelper()->createTable('ohrm_api_permission')
            ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
            ->addColumn('module_id', Types::INTEGER, ['Notnull' => false])
            ->addColumn('data_group_id', Types::INTEGER, ['Notnull' => false])
            ->addColumn('api_name', Types::STRING, ['Length' => 255])
            ->addUniqueIndex(['api_name'], 'api_name')
            ->setPrimaryKey(['id'])
            ->addForeignKeyConstraint('ohrm_module', ['module_id'], ['id'], [], 'fk_ohrm_module_module_id')
            ->addForeignKeyConstraint(
                'ohrm_data_group',
                ['data_group_id'],
                ['id'],
                [],
                'fk_ohrm_data_group_data_group_id'
            )
            ->create();

        $this->getDataGroupHelper()->insertApiPermissions(__DIR__ . '/permission/api.yaml');
        $this->getDataGroupHelper()->insertDataGroupPermissions(__DIR__ . '/permission/data_group.yaml');
        $this->getDataGroupHelper()->insertScreenPermissions(__DIR__ . '/permission/screen.yaml');

        $this->updateScreenModuleId('pim', 'viewDefinedPredefinedReports', 'PIM Reports List');
        $this->updateScreenModuleId('pim', 'definePredefinedReport', 'Define PIM reports');
        $this->updateScreenModuleId('pim', 'displayPredefinedReport', 'Display PIM reports');
        $this->updateScreenModuleId('pim', 'pimCsvImport', 'Data Import');

        $this->createQueryBuilder()
            ->update('ohrm_menu_item', 'menuItem')
            ->set('menuItem.screen_id', ':screenId')
            ->setParameter(
                'screenId',
                $this->getDataGroupHelper()->getScreenIdByModuleAndUrl(
                    $this->getDataGroupHelper()->getModuleIdByName('performance'),
                    'viewPerformanceModule'
                )
            )
            ->andWhere('menuItem.menu_title = :menuTitle')
            ->setParameter('menuTitle', 'Performance')
            ->executeQuery();

        $this->getSchemaHelper()->dropColumn('ohrm_leave_request_comment', 'created_by_name');
        $this->getSchemaHelper()->dropColumn('ohrm_leave_comment', 'created_by_name');
        $this->getSchemaHelper()->dropColumn('ohrm_leave_entitlement', 'created_by_name');

        $this->getSchemaHelper()->createTable('ohrm_mail_queue', 'utf8mb4')
            ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
            ->addColumn('to_list', Types::TEXT, ['Comment' => '(DC2Type:array)'])
            ->addColumn('cc_list', Types::TEXT, ['Notnull' => false, 'Default' => null, 'Comment' => '(DC2Type:array)'])
            ->addColumn(
                'bcc_list',
                Types::TEXT,
                ['Notnull' => false, 'Default' => null, 'Comment' => '(DC2Type:array)']
            )
            ->addColumn('subject', Types::STRING, ['Length' => 1000, 'Notnull' => false, 'Default' => null])
            ->addColumn('body', Types::TEXT, ['CustomSchemaOptions' => ['collation' => 'utf8mb4_unicode_ci']])
            ->addColumn('created_at', Types::DATETIME_MUTABLE)
            ->addColumn('sent_at', Types::DATETIME_MUTABLE, ['Notnull' => false, 'Default' => null])
            ->addColumn('status', Types::STRING, ['Length' => 12, 'Notnull' => false, 'Default' => null])
            ->addColumn('content_type', Types::STRING, ['Length' => 20, 'Notnull' => false, 'Default' => null])
            ->setPrimaryKey(['id'])
            ->create();

        $this->getSchemaHelper()->addColumn(
            'ohrm_display_field',
            'class_name',
            Types::STRING,
            ['Length' => 255, 'Notnull' => false, 'Default' => null]
        );
        $this->getSchemaHelper()->addColumn(
            'ohrm_filter_field',
            'class_name',
            Types::STRING,
            ['Length' => 255, 'Notnull' => false, 'Default' => null]
        );

        $this->updateReportDisplayFieldByGroup(
            'Personal',
            'OrangeHRM\\Core\\Report\\DisplayField\\GenericBasicDisplayField'
        );
        $this->updateReportDisplayFieldByGroup(
            'Contact Details',
            'OrangeHRM\\Core\\Report\\DisplayField\\GenericBasicDisplayField'
        );
        $this->updateReportDisplayFieldByGroup(
            'Emergency Contacts',
            'OrangeHRM\\Core\\Report\\DisplayField\\EmergencyContact\\EmergencyContact'
        );
        $this->updateReportDisplayFieldByGroup(
            'Dependents',
            'OrangeHRM\\Core\\Report\\DisplayField\\Dependent\\Dependent'
        );
        $this->updateReportDisplayFieldByGroup(
            'Immigration',
            'OrangeHRM\\Core\\Report\\DisplayField\\Immigration\\Immigration'
        );
        $this->updateReportDisplayFieldByGroup(
            'Job',
            'OrangeHRM\\Core\\Report\\DisplayField\\GenericBasicDisplayField'
        );
        $this->updateReportDisplayFieldByGroup('Salary', 'OrangeHRM\\Core\\Report\\DisplayField\\Salary\\Salary');
        $this->updateReportDisplayFieldByGroup(
            'Subordinates',
            'OrangeHRM\\Core\\Report\\DisplayField\\Subordinate\\Subordinate'
        );
        $this->updateReportDisplayFieldByGroup(
            'Supervisors',
            'OrangeHRM\\Core\\Report\\DisplayField\\Supervisor\\Supervisor'
        );
        $this->updateReportDisplayFieldByGroup(
            'Work Experience',
            'OrangeHRM\\Core\\Report\\DisplayField\\WorkExperience\\WorkExperience'
        );
        $this->updateReportDisplayFieldByGroup(
            'Education',
            'OrangeHRM\\Core\\Report\\DisplayField\\Education\\Education'
        );
        $this->updateReportDisplayFieldByGroup('Skills', 'OrangeHRM\\Core\\Report\\DisplayField\\Skill\\Skill');
        $this->updateReportDisplayFieldByGroup(
            'Languages',
            'OrangeHRM\\Core\\Report\\DisplayField\\Language\\Language'
        );
        $this->updateReportDisplayFieldByGroup('License', 'OrangeHRM\\Core\\Report\\DisplayField\\License\\License');
        $this->updateReportDisplayFieldByGroup(
            'Memberships',
            'OrangeHRM\\Core\\Report\\DisplayField\\Membership\\Membership'
        );
        $this->updateReportDisplayFieldByGroup(
            'Custom Fields',
            'OrangeHRM\\Core\\Report\\DisplayField\\GenericBasicDisplayField'
        );

        $this->createQueryBuilder()
            ->update('ohrm_display_field', 'reportDisplayField')
            ->set('reportDisplayField.is_value_list', ':isValueList')
            ->setParameter('isValueList', false, ParameterType::BOOLEAN)
            ->andWhere('reportDisplayField.display_field_group_id = :groupId')
            ->setParameter('groupId', $this->getReportDisplayGroupId('Job'))
            ->executeQuery();
        $this->updateReportDisplayFieldByFieldAlias(
            'empBirthday',
            'OrangeHRM\\Core\\Report\\DisplayField\\GenericDateDisplayField'
        );
        $this->updateReportDisplayFieldByFieldAlias(
            'licenseExpiryDate',
            'OrangeHRM\\Core\\Report\\DisplayField\\GenericDateDisplayField'
        );
        $this->updateReportDisplayFieldByFieldAlias(
            'empGender',
            'OrangeHRM\\Core\\Report\\DisplayField\\Personal\\EmployeeGender'
        );
        $this->updateReportDisplayFieldByFieldAlias(
            'address',
            'OrangeHRM\\Core\\Report\\DisplayField\\ContactDetail\\EmployeeAddress'
        );
        $this->updateReportDisplayFieldByFieldAlias(
            'empContStartDate',
            'OrangeHRM\\Core\\Report\\DisplayField\\GenericDateDisplayField'
        );
        $this->updateReportDisplayFieldByFieldAlias(
            'empContEndDate',
            'OrangeHRM\\Core\\Report\\DisplayField\\GenericDateDisplayField'
        );
        $this->updateReportDisplayFieldByFieldAlias(
            'empJoinedDate',
            'OrangeHRM\\Core\\Report\\DisplayField\\GenericDateDisplayField'
        );
        $this->updateReportDisplayFieldByFieldAlias(
            'terminationDate',
            'OrangeHRM\\Core\\Report\\DisplayField\\GenericDateDisplayField'
        );

        $this->renameReportDisplayFieldAlias('nationality', 'employeeNationality');
        $this->renameReportDisplayFieldAlias('getNote', 'terminationNote');
        $this->renameReportDisplayFieldAlias('terminationReason', 'empTerminationReason');
        $this->renameReportDisplayFieldAlias('name', 'membershipName');

        $this->updateReportFilterFieldByFieldName(
            'employee_name',
            'OrangeHRM\\Core\\Report\\FilterField\\EmployeeNumber'
        );
        $this->updateReportFilterFieldByFieldName('pay_grade', 'OrangeHRM\\Core\\Report\\FilterField\\PayGrade');
        $this->updateReportFilterFieldByFieldName(
            'education',
            'OrangeHRM\\Core\\Report\\FilterField\\EmployeeEducation'
        );
        $this->updateReportFilterFieldByFieldName(
            'employment_status',
            'OrangeHRM\\Core\\Report\\FilterField\\EmploymentStatus'
        );
        $this->updateReportFilterFieldByFieldName(
            'service_period',
            'OrangeHRM\\Core\\Report\\FilterField\\ServicePeriod'
        );
        $this->updateReportFilterFieldByFieldName('joined_date', 'OrangeHRM\\Core\\Report\\FilterField\\JoinedDate');
        $this->updateReportFilterFieldByFieldName('job_title', 'OrangeHRM\\Core\\Report\\FilterField\\JobTitle');
        $this->updateReportFilterFieldByFieldName('language', 'OrangeHRM\\Core\\Report\\FilterField\\EmployeeLanguage');
        $this->updateReportFilterFieldByFieldName('skill', 'OrangeHRM\\Core\\Report\\FilterField\\EmployeeSkill');
        $this->updateReportFilterFieldByFieldName('age_group', 'OrangeHRM\\Core\\Report\\FilterField\\AgeGroup');
        $this->updateReportFilterFieldByFieldName('sub_unit', 'OrangeHRM\\Core\\Report\\FilterField\\Subunit');
        $this->updateReportFilterFieldByFieldName('gender', 'OrangeHRM\\Core\\Report\\FilterField\\EmployeeGender');
        $this->updateReportFilterFieldByFieldName('location', 'OrangeHRM\\Core\\Report\\FilterField\\Location');
        $this->updateReportFilterFieldByFieldName('include', 'OrangeHRM\\Core\\Report\\FilterField\\IncludeEmployee');

        $this->renameSelectedReportFilterFieldWhereCondition('=', 'eq');
        $this->renameSelectedReportFilterFieldWhereCondition('<>', 'neq');
        $this->renameSelectedReportFilterFieldWhereCondition('<', 'lt');
        $this->renameSelectedReportFilterFieldWhereCondition('>', 'gt');
        $this->renameSelectedReportFilterFieldWhereCondition('BETWEEN', 'between');
        $this->renameSelectedReportFilterFieldWhereCondition('IN', 'in');
        $this->renameSelectedReportFilterFieldWhereCondition('IS NULL', 'isNull');
        $this->renameSelectedReportFilterFieldWhereCondition('IS NOT NULL', 'isNotNull');

        $this->updateEmailProcessorClassByEmailName(
            'leave.apply',
            'OrangeHRM\\Leave\\Mail\\Processor\\LeaveAllocateEmailProcessor'
        );
        $this->updateEmailProcessorClassByEmailName(
            'leave.assign',
            'OrangeHRM\\Leave\\Mail\\Processor\\LeaveAllocateEmailProcessor'
        );
        $this->updateEmailProcessorClassByEmailName(
            'leave.approve',
            'OrangeHRM\\Leave\\Mail\\Processor\\LeaveStatusChangeEmailProcessor'
        );
        $this->updateEmailProcessorClassByEmailName(
            'leave.cancel',
            'OrangeHRM\\Leave\\Mail\\Processor\\LeaveStatusChangeEmailProcessor'
        );
        $this->updateEmailProcessorClassByEmailName(
            'leave.reject',
            'OrangeHRM\\Leave\\Mail\\Processor\\LeaveStatusChangeEmailProcessor'
        );

        $this->updateEmailTemplateSubjectAndBodyByEmailNameAndRecipientRole(
            'leave.apply',
            'supervisor',
            '/orangehrmLeavePlugin/Mail/templates/en_US/apply/leaveApplicationSubject.txt.twig',
            '/orangehrmLeavePlugin/Mail/templates/en_US/apply/leaveApplicationBody.html.twig'
        );
        $this->updateEmailTemplateSubjectAndBodyByEmailNameAndRecipientRole(
            'leave.apply',
            'subscriber',
            '/orangehrmLeavePlugin/Mail/templates/en_US/apply/leaveApplicationSubject.txt.twig',
            '/orangehrmLeavePlugin/Mail/templates/en_US/apply/leaveApplicationSubscriberBody.html.twig'
        );
        $this->updateEmailTemplateSubjectAndBodyByEmailNameAndRecipientRole(
            'leave.assign',
            'ess',
            '/orangehrmLeavePlugin/Mail/templates/en_US/assign/leaveAssignmentSubject.txt.twig',
            '/orangehrmLeavePlugin/Mail/templates/en_US/assign/leaveAssignmentBody.html.twig'
        );
        $this->updateEmailTemplateSubjectAndBodyByEmailNameAndRecipientRole(
            'leave.assign',
            'supervisor',
            '/orangehrmLeavePlugin/Mail/templates/en_US/assign/leaveAssignmentSubjectForSupervisors.txt.twig',
            '/orangehrmLeavePlugin/Mail/templates/en_US/assign/leaveAssignmentBodyForSupervisors.html.twig'
        );
        $this->updateEmailTemplateSubjectAndBodyByEmailNameAndRecipientRole(
            'leave.assign',
            'subscriber',
            '/orangehrmLeavePlugin/Mail/templates/en_US/assign/leaveAssignmentSubscriberSubject.txt.twig',
            '/orangehrmLeavePlugin/Mail/templates/en_US/assign/leaveAssignmentSubscriberBody.html.twig'
        );
        $this->updateEmailTemplateSubjectAndBodyByEmailNameAndRecipientRole(
            'leave.approve',
            'ess',
            '/orangehrmLeavePlugin/Mail/templates/en_US/approve/leaveApprovalSubject.txt.twig',
            '/orangehrmLeavePlugin/Mail/templates/en_US/approve/leaveApprovalBody.html.twig'
        );
        $this->updateEmailTemplateSubjectAndBodyByEmailNameAndRecipientRole(
            'leave.approve',
            'subscriber',
            '/orangehrmLeavePlugin/Mail/templates/en_US/approve/leaveApprovalSubscriberSubject.txt.twig',
            '/orangehrmLeavePlugin/Mail/templates/en_US/approve/leaveApprovalSubscriberBody.html.twig'
        );
        $this->updateEmailTemplateSubjectAndBodyByEmailNameAndRecipientRole(
            'leave.reject',
            'ess',
            '/orangehrmLeavePlugin/Mail/templates/en_US/reject/leaveRejectionSubject.txt.twig',
            '/orangehrmLeavePlugin/Mail/templates/en_US/reject/leaveRejectionBody.html.twig'
        );
        $this->updateEmailTemplateSubjectAndBodyByEmailNameAndRecipientRole(
            'leave.reject',
            'subscriber',
            '/orangehrmLeavePlugin/Mail/templates/en_US/reject/leaveRejectionSubscriberSubject.txt.twig',
            '/orangehrmLeavePlugin/Mail/templates/en_US/reject/leaveRejectionSubscriberBody.html.twig'
        );
        $this->updateEmailTemplateSubjectAndBodyByEmailNameAndRecipientRole(
            'leave.cancel',
            'supervisor',
            '/orangehrmLeavePlugin/Mail/templates/en_US/cancel/leaveEmployeeCancellationSubject.txt.twig',
            '/orangehrmLeavePlugin/Mail/templates/en_US/cancel/leaveEmployeeCancellationBody.html.twig'
        );
        $this->updateEmailTemplateSubjectAndBodyByEmailNameAndRecipientRole(
            'leave.cancel',
            'ess',
            '/orangehrmLeavePlugin/Mail/templates/en_US/cancel/leaveCancellationSubject.txt.twig',
            '/orangehrmLeavePlugin/Mail/templates/en_US/cancel/leaveCancellationBody.html.twig'
        );
        $this->createQueryBuilder()
            ->update('ohrm_email_template', 'emailTemplate')
            ->set('emailTemplate.subject', ':subject')
            ->setParameter(
                'subject',
                '/orangehrmLeavePlugin/Mail/templates/en_US/cancel/leaveEmployeeCancellationSubscriberSubject.txt.twig'
            )
            ->set('emailTemplate.body', ':body')
            ->setParameter(
                'body',
                '/orangehrmLeavePlugin/Mail/templates/en_US/cancel/leaveEmployeeCancellationSubscriberBody.html.twig'
            )
            ->andWhere('emailTemplate.email_id = :emailId')
            ->setParameter('emailId', $this->getEmailIdByName('leave.cancel'))
            ->andWhere('emailTemplate.recipient_role = :recipientRole')
            ->setParameter('recipientRole', 'subscriber')
            ->andWhere('emailTemplate.performer_role = :performerRole')
            ->setParameter('performerRole', 'ess')
            ->executeQuery();
        $qb = $this->createQueryBuilder()
            ->update('ohrm_email_template', 'emailTemplate')
            ->set('emailTemplate.subject', ':subject')
            ->setParameter(
                'subject',
                '/orangehrmLeavePlugin/Mail/templates/en_US/cancel/leaveCancellationSubscriberSubject.txt.twig'
            )
            ->set('emailTemplate.body', ':body')
            ->setParameter(
                'body',
                '/orangehrmLeavePlugin/Mail/templates/en_US/cancel/leaveCancellationSubscriberBody.html.twig'
            )
            ->andWhere('emailTemplate.email_id = :emailId')
            ->setParameter('emailId', $this->getEmailIdByName('leave.cancel'))
            ->andWhere('emailTemplate.recipient_role = :recipientRole')
            ->setParameter('recipientRole', 'subscriber');
        $qb->andWhere($qb->expr()->isNull('emailTemplate.performer_role'))
            ->executeQuery();
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '5.0-beta';
    }

    /**
     * @param string $userRole
     * @param string $url
     */
    private function updateHomePage(string $userRole, string $url): void
    {
        $this->createQueryBuilder()
            ->update('ohrm_home_page', 'homePage')
            ->set('homePage.action', ':url')
            ->setParameter('url', $url)
            ->andWhere('homePage.user_role_id = :userRoleId')
            ->setParameter('userRoleId', $this->getDataGroupHelper()->getUserRoleIdByName($userRole))
            ->executeQuery();
    }

    /**
     * @param string $name
     * @return int
     */
    private function getEmailIdByName(string $name): int
    {
        if (!isset($this->emails[$name])) {
            $qb = $this->getConnection()->createQueryBuilder()
                ->select('email.id')
                ->from('ohrm_email', 'email')
                ->where('email.name = :name')
                ->setParameter('name', $name)
                ->setMaxResults(1);
            $this->emails[$name] = $qb->fetchOne();
        }
        return $this->emails[$name];
    }

    /**
     * @param string $emailName
     * @param string $className
     */
    private function updateEmailProcessorClassByEmailName(string $emailName, string $className): void
    {
        $this->createQueryBuilder()
            ->update('ohrm_email_processor', 'emailProcessor')
            ->set('emailProcessor.class_name', ':className')
            ->setParameter('className', $className)
            ->andWhere('emailProcessor.email_id = :emailId')
            ->setParameter('emailId', $this->getEmailIdByName($emailName))
            ->executeQuery();
    }

    /**
     * @param string $emailName
     * @param string $recipientRole
     * @param string $subject
     * @param string $body
     */
    private function updateEmailTemplateSubjectAndBodyByEmailNameAndRecipientRole(
        string $emailName,
        string $recipientRole,
        string $subject,
        string $body
    ): void {
        $this->createQueryBuilder()
            ->update('ohrm_email_template', 'emailTemplate')
            ->set('emailTemplate.subject', ':subject')
            ->setParameter('subject', $subject)
            ->set('emailTemplate.body', ':body')
            ->setParameter('body', $body)
            ->andWhere('emailTemplate.email_id = :emailId')
            ->setParameter('emailId', $this->getEmailIdByName($emailName))
            ->andWhere('emailTemplate.recipient_role = :recipientRole')
            ->setParameter('recipientRole', $recipientRole)
            ->executeQuery();
    }

    /**
     * @param string $groupName
     * @return int
     */
    private function getReportDisplayGroupId(string $groupName): int
    {
        if (!isset($this->reportDisplayGroups[$groupName])) {
            $qb = $this->getConnection()->createQueryBuilder()
                ->select('reportDisplayGroup.id')
                ->from('ohrm_display_field_group', 'reportDisplayGroup')
                ->where('reportDisplayGroup.name = :groupName')
                ->setParameter('groupName', $groupName)
                ->setMaxResults(1);
            $this->reportDisplayGroups[$groupName] = $qb->fetchOne();
        }
        return $this->reportDisplayGroups[$groupName];
    }

    /**
     * @param string $groupName
     * @param string $className
     */
    private function updateReportDisplayFieldByGroup(string $groupName, string $className): void
    {
        $this->createQueryBuilder()
            ->update('ohrm_display_field', 'reportDisplayField')
            ->set('reportDisplayField.class_name', ':className')
            ->setParameter('className', $className)
            ->andWhere('reportDisplayField.display_field_group_id = :groupId')
            ->setParameter('groupId', $this->getReportDisplayGroupId($groupName))
            ->executeQuery();
    }

    /**
     * @param string $name
     * @param string $className
     */
    private function updateReportFilterFieldByFieldName(string $name, string $className): void
    {
        $this->createQueryBuilder()
            ->update('ohrm_filter_field', 'reportFilterField')
            ->set('reportFilterField.class_name', ':className')
            ->setParameter('className', $className)
            ->andWhere('reportFilterField.name = :name')
            ->setParameter('name', $name)
            ->executeQuery();
    }

    /**
     * @param string $oldAlias
     * @param string $newAlias
     */
    private function renameReportDisplayFieldAlias(string $oldAlias, string $newAlias): void
    {
        $this->createQueryBuilder()
            ->update('ohrm_display_field', 'reportDisplayField')
            ->set('reportDisplayField.field_alias', ':newAlias')
            ->setParameter('newAlias', $newAlias)
            ->andWhere('reportDisplayField.field_alias = :oldAlias')
            ->setParameter('oldAlias', $oldAlias)
            ->executeQuery();
    }

    /**
     * @param string $oldValue
     * @param string $newValue
     */
    private function renameSelectedReportFilterFieldWhereCondition(string $oldValue, string $newValue): void
    {
        $this->createQueryBuilder()
            ->update('ohrm_selected_filter_field', 'selectedReportFilterField')
            ->set('selectedReportFilterField.where_condition', ':newAlias')
            ->setParameter('newAlias', $newValue)
            ->andWhere('selectedReportFilterField.where_condition = :oldAlias')
            ->setParameter('oldAlias', $oldValue)
            ->executeQuery();
    }

    /**
     * @param string $fieldAlias
     * @param string $className
     */
    private function updateReportDisplayFieldByFieldAlias(string $fieldAlias, string $className): void
    {
        $this->createQueryBuilder()
            ->update('ohrm_display_field', 'reportDisplayField')
            ->set('reportDisplayField.class_name', ':className')
            ->setParameter('className', $className)
            ->andWhere('reportDisplayField.field_alias = :fieldAlias')
            ->setParameter('fieldAlias', $fieldAlias)
            ->executeQuery();
    }

    /**
     * @param string $module
     * @param string $url
     * @param string $screenName
     */
    private function updateScreenModuleId(string $module, string $url, string $screenName): void
    {
        $this->createQueryBuilder()
            ->update('ohrm_screen', 'screen')
            ->set('screen.module_id', ':moduleId')
            ->setParameter(
                'moduleId',
                $this->getDataGroupHelper()->getModuleIdByName($module)
            )
            ->andWhere('screen.action_url = :url')
            ->setParameter('url', $url)
            ->andWhere('screen.name = :screenName')
            ->setParameter('screenName', $screenName)
            ->executeQuery();
    }

    private function updateMenuItemIconNames(string $iconName, string $menuTitle): void
    {
        $this->createQueryBuilder()
            ->update('ohrm_menu_item', 'menuItem')
            ->set('menuItem.additional_params', ':additionalParams')
            ->setParameter('additionalParams', '{"icon":"' . $iconName . '"}')
            ->andWhere('menuItem.menu_title = :menuTitle')
            ->setParameter('menuTitle', $menuTitle)
            ->executeQuery();
    }
}
