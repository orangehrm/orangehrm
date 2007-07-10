%define name OrangeHRM
%define version 2.2
%define release 1
%define _prefix /var/www/html

Summary: Opensource HR management.
Name: %{name}
Version: %{version}
Release: %{release}
Source0:  http://downloads.sourceforge.net/orangehrm/%{name}-ver-%{version}.tar.gz
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
%setup -q -a 0 -n orangehrm2
%build

%install
[ "$RPM_BUILD_ROOT" != "/" ] && rm -rf $RPM_BUILD_ROOT

mkdir -p -m 755 $RPM_BUILD_ROOT/var/www/html/orangehrm2

cp -pr orangehrm2/* $RPM_BUILD_ROOT/var/www/html/orangehrm2/

%clean
[ "$RPM_BUILD_ROOT" != "/" ] && rm -rf $RPM_BUILD_ROOT

%files
%defattr(-,root,root)
%attr(-,apache,apache) %{_prefix}/orangehrm2/

%changelog
* Mon Jul 10 2007 S.H.Mohanjith <moha@mohanjith.net>
- OrangeHRM Appliance for Windows
- Bugs Fixed
+ 1750216   Clear not functional in Password Change form
+ 1748130   Edit doesnt work in Spanish
+ 1748309   SQL/PHP Code is written in Reports
+ 1748152   Can not access next page
+ 1748398   InnoDB Disabled in XAMPP by default
+ 1748851   Add Project and Customer screwed in IE
+ 1748398   InnoDB Disabled in XAMPP by default
