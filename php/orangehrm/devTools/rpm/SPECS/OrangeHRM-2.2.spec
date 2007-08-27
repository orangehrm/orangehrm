%define name orangehrm
%define version 2.2.0.2
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
* Mon Aug 27 2007 S.H.Mohanjith <moha@mohanjith.net>
- OrangeHRM Appliance for Linux
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
- OrangeHRM Appliance for Linux
- Bugs Fixed
+ 1750216   Clear not functional in Password Change form
+ 1748130   Edit doesnt work in Spanish
+ 1748309   SQL/PHP Code is written in Reports
+ 1748152   Can not access next page
+ 1748398   InnoDB Disabled in XAMPP by default
+ 1748851   Add Project and Customer screwed in IE
+ 1748398   InnoDB Disabled in XAMPP by default
