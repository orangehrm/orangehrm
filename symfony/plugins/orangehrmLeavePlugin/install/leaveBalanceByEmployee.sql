DELETE FROM ohrm_advanced_report where id = 6;
INSERT INTO ohrm_advanced_report (id, name, definition) VALUES
(1, 'Leave Entitlements and Usage Report', '
<report>
    <settings>
        <csv>
            <include_group_header>1</include_group_header>
            <include_header>1</include_header>
        </csv>
    </settings>
<filter_fields>
	<input_field type="text" name="empNumber" label="Employee Number"></input_field>
	<input_field type="text" name="fromDate" label="From"></input_field>
        <input_field type="text" name="toDate" label="To"></input_field>
        <input_field type="text" name="asOfDate" label="AsOf"></input_field>
</filter_fields> 

<sub_report type="sql" name="mainTable">       
    <query>FROM ohrm_leave_type WHERE deleted = 0 ORDER BY ohrm_leave_type.id</query>
    <id_field>leaveTypeId</id_field>
    <display_groups>
        <display_group name="leavetype" type="one" display="true">
            <group_header></group_header>
            <fields>
                <field display="false">
                    <field_name>ohrm_leave_type.id</field_name>
                    <field_alias>leaveTypeId</field_alias>
                    <display_name>Leave Type ID</display_name>
                    <width>1</width>	
                </field>   
                <field display="false">
                    <field_name>ohrm_leave_type.exclude_in_reports_if_no_entitlement</field_name>
                    <field_alias>exclude_if_no_entitlement</field_alias>
                    <display_name>Exclude</display_name>
                    <width>1</width>	
                </field>  
                <field display="true">
                    <field_name>ohrm_leave_type.name</field_name>
                    <field_alias>leaveType</field_alias>
                    <display_name>Leave Type</display_name>
                    <width>160</width>	
                </field>s                                                                                                     
            </fields>
        </display_group>
    </display_groups> 
</sub_report>

<sub_report type="sql" name="entitlementsTotal">
                    <query>

FROM (
SELECT ohrm_leave_entitlement.id as id, 
       ohrm_leave_entitlement.leave_type_id as leave_type_id,
       ohrm_leave_entitlement.no_of_days as no_of_days,
       sum(IF(ohrm_leave.status = 2, ohrm_leave_leave_entitlement.length_days, 0)) AS scheduled,
       sum(IF(ohrm_leave.status = 3, ohrm_leave_leave_entitlement.length_days, 0)) AS taken
       
FROM ohrm_leave_entitlement LEFT JOIN ohrm_leave_leave_entitlement ON
    ohrm_leave_entitlement.id = ohrm_leave_leave_entitlement.entitlement_id
    LEFT JOIN ohrm_leave ON ohrm_leave.id = ohrm_leave_leave_entitlement.leave_id AND 
    ( $X{&gt;,ohrm_leave.date,toDate} OR $X{&lt;,ohrm_leave.date,fromDate} )

WHERE ohrm_leave_entitlement.deleted=0 AND $X{=,ohrm_leave_entitlement.emp_number,empNumber} AND 
    (
      ( $X{&lt;=,ohrm_leave_entitlement.from_date,fromDate} AND $X{&gt;=,ohrm_leave_entitlement.to_date,fromDate} ) OR
      ( $X{&lt;=,ohrm_leave_entitlement.from_date,toDate} AND $X{&gt;=,ohrm_leave_entitlement.to_date,toDate} ) OR 
      ( $X{&gt;=,ohrm_leave_entitlement.from_date,fromDate} AND $X{&lt;=,ohrm_leave_entitlement.to_date,toDate} ) 
    )
    
GROUP BY ohrm_leave_entitlement.id
) AS A

GROUP BY A.leave_type_id
ORDER BY A.leave_type_id

</query>
    <id_field>leaveTypeId</id_field>
    <display_groups>
            <display_group name="g2" type="one" display="true">
                <group_header></group_header>
                <fields>
                    <field display="false">
                        <field_name>A.leave_type_id</field_name>
                        <field_alias>leaveTypeId</field_alias>
                        <display_name>Leave Type ID</display_name>
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
FROM ohrm_leave_type LEFT JOIN 
ohrm_leave ON ohrm_leave_type.id = ohrm_leave.leave_type_id AND
$X{=,ohrm_leave.emp_number,empNumber} AND
ohrm_leave.status = 2 AND
$X{&gt;=,ohrm_leave.date,fromDate} AND $X{&lt;=,ohrm_leave.date,toDate}
WHERE
ohrm_leave_type.deleted = 0

GROUP BY ohrm_leave_type.id
ORDER BY ohrm_leave_type.id
</query>
    <id_field>leaveTypeId</id_field>
    <display_groups>
            <display_group name="g5" type="one" display="true">
                <group_header></group_header>
                <fields>
                    <field display="false">
                        <field_name>ohrm_leave_type.id</field_name>
                        <field_alias>leaveTypeId</field_alias>
                        <display_name>Leave Type ID</display_name>
                        <width>1</width>
                    </field>                                
                    <field display="true">
                        <field_name>sum(length_days)</field_name>
                        <field_alias>scheduled</field_alias>
                        <display_name>Leave Scheduled</display_name>
                        <width>120</width>
                        <link>leave/viewLeaveList?empNumber=$P{empNumber}&amp;fromDate=$P{fromDate}&amp;toDate=$P{toDate}&amp;leaveTypeId=$P{leaveType}&amp;status=2</link>
                    </field>                                
                </fields>
            </display_group>
    </display_groups>
    </sub_report>

<sub_report type="sql" name="takenQuery">
<query>
FROM ohrm_leave WHERE $X{=,emp_number,empNumber} AND
status = 3 AND
$X{&gt;=,ohrm_leave.date,fromDate} AND $X{&lt;=,ohrm_leave.date,toDate}
GROUP BY leave_type_id
ORDER BY ohrm_leave.leave_type_id
</query>
    <id_field>leaveTypeId</id_field>
    <display_groups>
            <display_group name="g4" type="one" display="true">
                <group_header></group_header>
                <fields>
                    <field display="false">
                        <field_name>ohrm_leave.leave_type_id</field_name>
                        <field_alias>leaveTypeId</field_alias>
                        <display_name>Leave Type ID</display_name>
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
    <query>FROM ohrm_leave_type WHERE deleted = 0 ORDER BY ohrm_leave_type.id</query>
    <id_field>leaveTypeId</id_field>
    <display_groups>
        <display_group name="unused" type="one" display="true">
            <group_header></group_header>
            <fields>
                <field display="false">
                    <field_name>ohrm_leave_type.id</field_name>
                    <field_alias>leaveTypeId</field_alias>
                    <display_name>Leave Type ID</display_name>
                    <width>1</width>	
                </field>   
                <field display="true">
                    <field_name>ohrm_leave_type.name</field_name>
                    <field_alias>unused</field_alias>
                    <display_name>Unused Leave Entitlements</display_name>
                    <width>160</width>	
                </field>                                                                                                     
            </fields>
        </display_group>
    </display_groups> 
</sub_report>


    <join>             
        <join_by sub_report="mainTable" id="leaveTypeId"></join_by>              
        <join_by sub_report="entitlementsTotal" id="leaveTypeId"></join_by> 
        <join_by sub_report="scheduledQuery" id="leaveTypeId"></join_by>  
        <join_by sub_report="takenQuery" id="leaveTypeId"></join_by>  
        <join_by sub_report="unused" id="leaveTypeId"></join_by>  

    </join>
    <page_limit>100</page_limit>        
</report>'); 