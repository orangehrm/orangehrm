DELETE FROM ohrm_advanced_report where id = 5;
INSERT INTO ohrm_advanced_report (id, name, definition) VALUES
(5, 'Leave Entitlements and Usage Report', '
<report>
    <settings>
        <csv>
            <include_group_header>1</include_group_header>
            <include_header>1</include_header>
        </csv>
    </settings>
<filter_fields>
	<input_field type="text" name="leaveType" label="Leave Type"></input_field>
	<input_field type="text" name="fromDate" label="From"></input_field>
        <input_field type="text" name="toDate" label="To"></input_field>
        <input_field type="text" name="asOfDate" label="AsOf"></input_field>
</filter_fields> 

<sub_report type="sql" name="mainTable">       
    <query>FROM hs_hr_employee ORDER BY hs_hr_employee.emp_number</query>
    <id_field>empNumber</id_field>
    <display_groups>
        <display_group name="personalDetails" type="one" display="true">
            <group_header></group_header>
            <fields>
                <field display="false">
                    <field_name>hs_hr_employee.emp_number</field_name>
                    <field_alias>empNumber</field_alias>
                    <display_name>Employee Number</display_name>
                    <width>1</width>	
                </field>                
                <field display="true">
                    <field_name>CONCAT(hs_hr_employee.emp_firstname, \' \', hs_hr_employee.emp_lastname)</field_name>
                    <field_alias>employeeName</field_alias>
                    <display_name>Employee</display_name>
                    <width>150</width>
                </field>                                                                                               
            </fields>
        </display_group>
    </display_groups> 
</sub_report>

<sub_report type="sql" name="entitlementsTotal">
                    <query>

FROM (
SELECT ohrm_leave_entitlement.id as id, 
       ohrm_leave_entitlement.emp_number as emp_number,
       ohrm_leave_entitlement.no_of_days as no_of_days,
       sum(IF(ohrm_leave.status = 2, ohrm_leave_leave_entitlement.length_days, 0)) AS scheduled,
       sum(IF(ohrm_leave.status = 3, ohrm_leave_leave_entitlement.length_days, 0)) AS taken
       
FROM ohrm_leave_entitlement LEFT JOIN ohrm_leave_leave_entitlement ON
    ohrm_leave_entitlement.id = ohrm_leave_leave_entitlement.entitlement_id
    LEFT JOIN ohrm_leave ON ohrm_leave.id = ohrm_leave_leave_entitlement.leave_id AND 
    ( $X{&gt;,ohrm_leave.date,toDate} OR $X{&lt;,ohrm_leave.date,fromDate} )

WHERE ohrm_leave_entitlement.deleted=0 AND $X{=,ohrm_leave_entitlement.leave_type_id,leaveType} AND 
    (
      ( $X{&lt;=,ohrm_leave_entitlement.from_date,fromDate} AND $X{&gt;=,ohrm_leave_entitlement.to_date,fromDate} ) OR
      ( $X{&lt;=,ohrm_leave_entitlement.from_date,toDate} AND $X{&gt;=,ohrm_leave_entitlement.to_date,toDate} ) OR 
      ( $X{&gt;=,ohrm_leave_entitlement.from_date,fromDate} AND $X{&lt;=,ohrm_leave_entitlement.to_date,toDate} ) 
    )
    
GROUP BY ohrm_leave_entitlement.id
) AS A

GROUP BY A.emp_number
ORDER BY A.emp_number

</query>
    <id_field>empNumber</id_field>
    <display_groups>
            <display_group name="g2" type="one" display="true">
                <group_header></group_header>
                <fields>
                    <field display="false">
                        <field_name>A.emp_number</field_name>
                        <field_alias>empNumber</field_alias>
                        <display_name>Emp Number</display_name>
                        <width>1</width>
                    </field>                                
                    <field display="true">
                        <field_name>sum(A.no_of_days) - sum(A.scheduled) - sum(A.taken)</field_name>
                        <field_alias>entitlement_total</field_alias>
                        <display_name>Leave Entitlements</display_name>
                        <width>120</width>
                    </field>                                
                </fields>
            </display_group>
    </display_groups>
</sub_report>

<sub_report type="sql" name="scheduledQuery">
<query>
FROM ohrm_leave WHERE $X{=,ohrm_leave.leave_type_id,leaveType} AND
status = 2 AND
$X{&gt;=,ohrm_leave.date,fromDate} AND $X{&lt;=,ohrm_leave.date,toDate}
GROUP BY emp_number
ORDER BY ohrm_leave.emp_number
</query>
    <id_field>empNumber</id_field>
    <display_groups>
            <display_group name="g5" type="one" display="true">
                <group_header></group_header>
                <fields>
                    <field display="false">
                        <field_name>ohrm_leave.emp_number</field_name>
                        <field_alias>empNumber</field_alias>
                        <display_name>Emp Number</display_name>
                        <width>1</width>
                    </field>                                
                    <field display="true">
                        <field_name>sum(length_days)</field_name>
                        <field_alias>scheduled</field_alias>
                        <display_name>Leave Scheduled</display_name>
                        <width>121</width>
                        <link>leave/viewLeaveList?empNumber=$P{empNumber}&amp;fromDate=$P{fromDate}&amp;toDate=$P{toDate}&amp;leaveTypeId=$P{leaveType}&amp;status=2</link>
                    </field>                                
                </fields>
            </display_group>
    </display_groups>
</sub_report>

<sub_report type="sql" name="takenQuery">
<query>
FROM ohrm_leave WHERE $X{=,ohrm_leave.leave_type_id,leaveType} AND
status = 3 AND
$X{&gt;=,ohrm_leave.date,fromDate} AND $X{&lt;=,ohrm_leave.date,toDate}
GROUP BY emp_number
ORDER BY ohrm_leave.emp_number
</query>
    <id_field>empNumber</id_field>
    <display_groups>
            <display_group name="g4" type="one" display="true">
                <group_header></group_header>
                <fields>
                    <field display="false">
                        <field_name>ohrm_leave.emp_number</field_name>
                        <field_alias>empNumber</field_alias>
                        <display_name>Emp Number</display_name>
                        <width>1</width>
                    </field>                                
                    <field display="true">
                        <field_name>sum(length_days)</field_name>
                        <field_alias>taken</field_alias>
                        <display_name>Leave Taken</display_name>
                        <width>120</width>
                        <link>leave/viewLeaveList?empNumber=$P{empNumber}&amp;fromDate=$P{fromDate}&amp;toDate=$P{toDate}&amp;leaveTypeId=$P{leaveType}&amp;status=3</link>
                    </field>                                
                </fields>
            </display_group>
    </display_groups>
</sub_report>
<sub_report type="sql" name="unused">       
    <query>FROM hs_hr_employee ORDER BY hs_hr_employee.emp_number</query>
    <id_field>empNumber</id_field>
    <display_groups>
        <display_group name="unused" type="one" display="true">
            <group_header></group_header>
            <fields>    
                <field display="false">
                    <field_name>hs_hr_employee.emp_number</field_name>
                    <field_alias>empNumber</field_alias>
                    <display_name>Employee Number</display_name>
                    <width>1</width>	
                </field>                
                <field display="true">
                    <field_name>hs_hr_employee.emp_firstname</field_name>
                    <field_alias>unused</field_alias>
                    <display_name>Unused Leave Entitlements</display_name>
                    <width>150</width>
                </field> 
                                                                                               
            </fields>
        </display_group>
    </display_groups> 
</sub_report>
    <join>             
        <join_by sub_report="mainTable" id="empNumber"></join_by>            
        <join_by sub_report="entitlementsTotal" id="empNumber"></join_by> 
        <join_by sub_report="scheduledQuery" id="empNumber"></join_by>
        <join_by sub_report="takenQuery" id="empNumber"></join_by> 
        <join_by sub_report="unused" id="empNumber"></join_by>  
    </join>
    <page_limit>20</page_limit>       
</report>'); 