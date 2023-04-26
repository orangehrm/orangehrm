<?php

namespace OrangeHRM\Core\config;

class DefaultConfig
{
    public const ALL_CONFIGS = [
        'timesheet_period_and_start_date' => '<TimesheetPeriod><PeriodType>Weekly</PeriodType><ClassName>WeeklyTimesheetPeriod</ClassName><StartDate>1</StartDate><Heading>Week</Heading></TimesheetPeriod>',
        'pim_show_deprecated_fields' => 0,
        'timesheet_time_format' => 1,
        'timesheet_period_set' => 'No',
        'admin.localization.default_language' => 'en_US',
        'admin.localization.use_browser_language' => 'No',
        'admin.localization.default_date_format' => 'Y-m-d',
        'authorize_user_role_manager_class' => 'BasicUserRoleManager',
        'include_supervisor_chain' => 'No',
        'leave.entitlement_consumption_algorithm' => 'FIFOEntitlementConsumptionStrategy',
        'leave.work_schedule_implementation' => 'BasicWorkSchedule',
        'themeName' => 'default',
        'leave.leavePeriodStatus' => '1',
        'leave.include_pending_leave_in_balance' => '1',
        'admin.default_workshift_start_time' => '09:00',
        'admin.default_workshift_end_time' => '17:00',
        'admin.product_type' => 'os',
        'domain.name' => 'localhost',
        'openId.provider.added' => 'on',
        'instance.version' => '5.3.0',
        'open_source_integrations' => '<xml><integrations></integrations></xml>',
        'authentication.status' => 'Enable',
        'authentication.enforce_password_strength' => 'on',
        'authentication.default_required_password_strength' => 'strong',
        'base_url' => 'https://marketplace.orangehrm.com',
        'email_config.sendmail_path' => '/usr/sbin/sendmail -bs',
        'help.url' => 'https://starterhelp.orangehrm.com',
        'help.processorClass' => 'ZendeskHelpProcessor',
        'dashboard.employees_on_leave_today.show_only_accessible' => '0'
    ];

    public static function getDefaultValue(string $key): ?string
    {
        return self::ALL_CONFIGS[$key] ?? null;
    }
}