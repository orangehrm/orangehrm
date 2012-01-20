%define name orangehrm
%define version 2.2.2
%define release 1
%define _prefix /var/www/html

Summary: Opensource HR management.
Name: %{name}
Version: %{version}
Release: %{release}
Source0:  http://downloads.sourceforge.net/orangehrm/%{name}-%{version}.tar.gz
Vendor: OrangeHRM Inc.
URL: http://orangehrm.com
License: GPL
Group: Enterprise
Prefix: %{_prefix}
Requires: httpd >= 1.3, mysql >= 5.0.12, php >= 5.1.2, php-mysql >= 5.1.2, php-common >= 5.1.2
Provides: orangehrm
BuildArch: noarch
BuildRoot: %{_topdir}/tmp/%{name}-%{version}-%{release}-buildroot

%description
OrangeHRM is emerging in line with the new generation of HR Information Systems (HRIS) and will assist you in managing your company's most important asset - human resource.

%prep
%setup -q -a 0 -n %{name}-%{version}
%build

%install
[ "$RPM_BUILD_ROOT" != "/" ] && rm -rf $RPM_BUILD_ROOT

mkdir -p -m 755 $RPM_BUILD_ROOT/var/www/html/%{name}-%{version}

cp -pr %{name}-%{version}/* $RPM_BUILD_ROOT/var/www/html/%{name}-%{version}/

%clean
[ "$RPM_BUILD_ROOT" != "/" ] && rm -rf $RPM_BUILD_ROOT

%files
%defattr(-,root,root)
%attr(-,apache,apache) %{_prefix}/%{name}-%{version}/

%changelog

* Mon Aug 31 2007 S.H.Mohanjith <moha@mohanjith.net>
- OrangeHRM Appliance for Linux 2.2.0.3
- BugId     Name
+ 1758646   Define Report - Assign User Group - No option to save
+ 1747768   ESS user Attachments pane should not view
+ 1720647   No validation for SMTP port no
+ 1739556   Admin-> Pay Grade : step increase can exceed Maximum salary
+ 1756630   When customer deleted, related timesheets not shown
+ 1756632   When customer deleted, related projects are not deleted
+ 1739533   Russian language pack     88912
+ 1759410   No of days of leave in subject is incorrect
+ 1755951   Unable to delete/edit Leave
+ 1756001   Upgrade issue from 2.2-rc2 to 2.2 (Upload limit)
+ 1758672   Reports :Text overlapped in qualification
+ 1741094   There is an unnecessary line in ESS Employee Box
+ 1756638   Timesheets allow entering times outside of timesheet period
+ 1619852   Duplicate values for Languages, Races, Skills etc.
+ 1761859   Leave Summery List Deleted Leave Types
+ 1756437   Not Display Properly(Leave Summary/Quota)
+ 1723581   [Usability] [CRB] Contact info should be at the top
+ 1764110   All text should be in resource files
+ 1779797   emppop.php
+ 1753736   The default leave view for supervisor
+ 1779300   No Paging in User Management module
+ 1766285   Membership name with quote not shown in PIM
+ 1783741   Ess User : Should not view Attachment Browse

* Mon Aug 27 2007 S.H.Mohanjith <moha@mohanjith.net>
- OrangeHRM Appliance for Linux 2.2.0.2
- Bugs Fixed
+ 1764128   Upgrading from 2.1 to 2.2 will not show the time module
+ 1765590   Upgrading to 2.2 from 2.0 or 2.1 hides leave module
+ 1765840   Upgrade from 2.0 to latest (2.2) fails due to leave changes
+ 1764192   Upgrade guide instructions may overwrite old version
+ 1766085   Adding image when creating employee in PIM - fails
+ 1766090   Adding first user after install fails
+ 1766276	Newly added supervisor not seen
+ 1769115   Leave Module not listed after upgrading to 2.2.0.2
+ 1755261   The images for tabs don't work in IE
+ 1769824   Report-to - do not assign with non English language packs

* Mon Jul 10 2007 S.H.Mohanjith <moha@mohanjith.net>
- OrangeHRM Appliance for Linux 2.2.1
- Bugs Fixed
+ 1750216   Clear not functional in Password Change form
+ 1748130   Edit doesnt work in Spanish
+ 1748309   SQL/PHP Code is written in Reports
+ 1748152   Can not access next page
+ 1748398   InnoDB Disabled in XAMPP by default
+ 1748851   Add Project and Customer screwed in IE
+ 1748398   InnoDB Disabled in XAMPP by default
