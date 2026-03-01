 ANGEPASSTER PLAN: E-Mail & Username auf mindestens 120 Zeichen (KEINE Reduzierungen)
---
🎯 NEUE STRATEGIE
Geänderte Regel:
- Felder unter 120 Zeichen → auf 120 erhöhen
- Felder über 120 Zeichen → NICHT ändern (bleiben bei aktueller Länge)
Betroffene Felder mit Sonderbehandlung:
| Feld | Aktuell | Neue Regel | Begründung |
|------|---------|------------|------------|
| EmailSubscriber.email | 255 | Bleibt 255 ✅ | Keine Reduzierung |
| EmailConfiguration.sent_as | 250 | Bleibt 250 ✅ | Keine Reduzierung |
| EmailConfiguration.testEmailAddress | 250 | Bleibt 250 ✅ | Keine Reduzierung |
| LoginLog.user_name | 255 | Nicht ändern ✅ | Separates Feld für Logs |
---
📦 TEIL 1: E-MAIL-FELDER (AUF MINDESTENS 120)
1.1 BACKEND: Entity-Definitionen (5 Änderungen statt 6)
A. Employee Entity ✅
Datei: src/plugins/orangehrmPimPlugin/entity/Employee.php
| Zeile | Feld | Alt | Neu |
|-------|------|-----|-----|
| 283 | emp_work_email | length=50 | length=120 |
| 306 | emp_oth_email | length=50 | length=120 |
B. Candidate Entity ✅
Datei: src/plugins/orangehrmRecruitmentPlugin/entity/Candidate.php
| Zeile | Feld | Alt | Neu |
|-------|------|-----|-----|
| 71 | email | length=100 | length=120 |
C. Organization Entity ✅
Datei: src/plugins/orangehrmAdminPlugin/entity/Organization.php
| Zeile | Feld | Alt | Neu |
|-------|------|-----|-----|
| 79 | email | length=30 | length=120 |
D. EmailSubscriber Entity ❌ KEINE ÄNDERUNG
Datei: src/plugins/orangehrmAdminPlugin/entity/EmailSubscriber.php
| Zeile | Feld | Aktuell | Aktion |
|-------|------|---------|--------|
| 57 | email | length=255 | Bleibt 255 (keine Reduzierung) |
E. ResetPasswordRequest Entity ✅
Datei: src/plugins/orangehrmAuthenticationPlugin/entity/ResetPasswordRequest.php
| Zeile | Feld | Alt | Neu |
|-------|------|-----|-----|
| 43 | reset_email | length=60 | length=120 |
F. EmailConfiguration Entity ❌ KEINE ÄNDERUNG
Datei: src/plugins/orangehrmAdminPlugin/entity/EmailConfiguration.php
| Zeile | Feld | Aktuell | Aktion |
|-------|------|---------|--------|
| 53 | sentAs (email) | length=250 | Bleibt 250 (keine Reduzierung) |
---
1.2 BACKEND: Service-Konstanten (1 Änderung)
EmployeeService ✅
Datei: src/plugins/orangehrmPimPlugin/Service/EmployeeService.php
| Zeile | Konstante | Alt | Neu |
|-------|-----------|-----|-----|
| 54 | WORK_EMAIL_MAX_LENGTH | = 50 | = 120 |
---
1.3 BACKEND: API-Validierungen (8 Änderungen statt 10)
A. EmployeeContactDetailsAPI ✅
Datei: src/plugins/orangehrmPimPlugin/Api/EmployeeContactDetailsAPI.php
| Zeile | Konstante | Alt | Neu |
|-------|-----------|-----|-----|
| 66 | PARAM_RULE_OTHER_EMAIL_MAX_LENGTH | = 50 | = 120 |
B. ValidationEmployeeEmailAPI ✅
Datei: src/plugins/orangehrmPimPlugin/Api/ValidationEmployeeEmailAPI.php
| Zeile | Konstante | Alt | Neu |
|-------|-----------|-----|-----|
| 43 | PARAM_RULE_WORK_EMAIL_MAX_LENGTH | = 50 | = 120 |
C. ValidationEmployeeOtherEmailAPI ✅
Datei: src/plugins/orangehrmPimPlugin/Api/ValidationEmployeeOtherEmailAPI.php
| Zeile | Konstante | Alt | Neu |
|-------|-----------|-----|-----|
| 43 | PARAM_RULE_OTHER_EMAIL_MAX_LENGTH | = 50 | = 120 |
D. OrganizationAPI ✅
Datei: src/plugins/orangehrmAdminPlugin/Api/OrganizationAPI.php
| Zeile | Konstante | Alt | Neu |
|-------|-----------|-----|-----|
| 58 | PARAM_RULE_EMAIL_MAX_LENGTH | = 30 | = 120 |
E. EmailSubscriberAPI ❌ KEINE ÄNDERUNG
Datei: src/plugins/orangehrmAdminPlugin/Api/EmailSubscriberAPI.php
| Zeile | Konstante | Aktuell | Aktion |
|-------|-----------|---------|--------|
| 51 | PARAM_RULE_STRING_MAX_LENGTH | = 100 | Auf 120 erhöhen ✅ |
Hinweis: API validiert 100, DB erlaubt 255 → API auf 120 erhöhen (noch unter DB-Limit)
F. EmailConfigurationAPI ❌ TEILWEISE ÄNDERUNG
Datei: src/plugins/orangehrmAdminPlugin/Api/EmailConfigurationAPI.php
| Zeile | Konstante | Aktuell | Aktion |
|-------|-----------|---------|--------|
| 53 | PARAM_RULE_SENT_AS_MAX_LENGTH | = 100 | Auf 120 erhöhen ✅ (DB: 250) |
| 60 | PARAM_RULE_TEST_EMAIL_ADDRESS_MAX_LENGTH | = 250 | Bleibt 250 ✅ (keine Reduzierung) |
G. CandidateAPI ❌ NEU: Validierung HINZUFÜGEN
Datei: src/plugins/orangehrmRecruitmentPlugin/Api/CandidateAPI.php
Nach Zeile 75 hinzufügen:
public const PARAM_RULE_EMAIL_MAX_LENGTH = 120;
Zeile 662-665 ändern von:
new ParamRule(
    self::PARAMETER_EMAIL,
    new Rule(Rules::EMAIL)
),
Zu:
new ParamRule(
    self::PARAMETER_EMAIL,
    new Rule(Rules::EMAIL),
    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_EMAIL_MAX_LENGTH])
),
H. ApplicantController ❌ NEU: Validierung HINZUFÜGEN
Datei: src/plugins/orangehrmRecruitmentPlugin/Controller/PublicController/ApplicantController.php
Nach den bestehenden Konstanten hinzufügen:
public const PARAM_RULE_EMAIL_MAX_LENGTH = 120;
Zeile 262-265 ändern von:
new ParamRule(
    self::PARAMETER_EMAIL,
    new Rule(Rules::EMAIL)
),
Zu:
new ParamRule(
    self::PARAMETER_EMAIL,
    new Rule(Rules::EMAIL),
    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_EMAIL_MAX_LENGTH])
),
I. AdminUserAPI (Installer) ❌ NEU: Validierung HINZUFÜGEN
Datei: installer/Controller/Installer/Api/AdminUserAPI.php
Nach Zeile 39 hinzufügen:
// Validate email length (max 120 characters)
if (strlen($email) > 120) {
    throw new InvalidArgumentException('Email must not exceed 120 characters');
}
// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    throw new InvalidArgumentException('Invalid email format');
}
---
1.4 FRONTEND: Vue-Komponenten (7 Änderungen statt 9)
A. EmployeeContactDetails ✅
Datei: src/client/src/orangehrmPimPlugin/pages/employee/EmployeeContactDetails.vue
| Zeile | Feld | Alt | Neu |
|-------|------|-----|-----|
| 210 | workEmail | shouldNotExceedCharLength(50) | shouldNotExceedCharLength(120) |
| 218 | otherEmail | shouldNotExceedCharLength(50) | shouldNotExceedCharLength(120) |
B. ViewOrganizationGeneralInformation ✅
Datei: src/client/src/orangehrmAdminPlugin/pages/organizationGeneralInformation/ViewOrganizationGeneralInformation.vue
| Zeile | Feld | Alt | Neu |
|-------|------|-----|-----|
| 253 | email | shouldNotExceedCharLength(30) | shouldNotExceedCharLength(120) |
C. SaveCandidate ✅
Datei: src/client/src/orangehrmRecruitmentPlugin/pages/SaveCandidate.vue
| Zeile | Feld | Alt | Neu |
|-------|------|-----|-----|
| 231 | email | shouldNotExceedCharLength(50) | shouldNotExceedCharLength(120) |
D. ApplyJobVacancy ✅
Datei: src/client/src/orangehrmRecruitmentPlugin/pages/ApplyJobVacancy.vue
| Zeile | Feld | Alt | Neu |
|-------|------|-----|-----|
| 302 | email | shouldNotExceedCharLength(50) | shouldNotExceedCharLength(120) |
E. CandidateProfile ✅
Datei: src/client/src/orangehrmRecruitmentPlugin/components/CandidateProfile.vue
| Zeile | Feld | Alt | Neu |
|-------|------|-----|-----|
| 280 | email | shouldNotExceedCharLength(50) | shouldNotExceedCharLength(120) |
F. SaveSubscriber ✅
Datei: src/client/src/orangehrmAdminPlugin/pages/emailSubscription/SaveSubscriber.vue
| Zeile | Feld | Alt | Neu |
|-------|------|-----|-----|
| 116 | email | shouldNotExceedCharLength(100) | shouldNotExceedCharLength(120) |
G. EditSubscriber ✅
Datei: src/client/src/orangehrmAdminPlugin/pages/emailSubscription/EditSubscriber.vue
| Zeile | Feld | Alt | Neu |
|-------|------|-----|-----|
| 117 | email | shouldNotExceedCharLength(100) | shouldNotExceedCharLength(120) |
H. ViewEmailConfiguration ✅ TEILWEISE ÄNDERUNG
Datei: src/client/src/orangehrmAdminPlugin/pages/emailConfiguration/ViewEmailConfiguration.vue
| Zeile | Feld | Alt | Neu | Hinweis |
|-------|------|-----|-----|---------|
| 273 | sentAs | shouldNotExceedCharLength(100) | shouldNotExceedCharLength(120) | E-Mail-Adresse (DB: 250) |
| 282 | testEmailAddress | shouldNotExceedCharLength(250) | Bleibt 250 | ✅ Keine Reduzierung |
I. AdminUserCreationScreen (Installer) ✅
Datei: installer/client/src/pages/AdminUserCreationScreen.vue
| Zeile | Feld | Alt | Neu |
|-------|------|-----|-----|
| 173 | email | shouldNotExceedCharLength(50) | shouldNotExceedCharLength(120) |
---
📦 TEIL 2: USERNAME-FELDER AUF 120 ZEICHEN
2.1 BACKEND: Entity-Definition (1 Änderung)
User Entity ✅
Datei: src/plugins/orangehrmCorePlugin/entity/User.php
| Zeile | Feld | Alt | Neu |
|-------|------|-----|-----|
| 50 | user_name | length=40 | length=120 |
Hinweis: LoginLog.user_name (255 Zeichen) bleibt unverändert - separates Feld
---
2.2 BACKEND: Service-Konstante (1 Änderung)
UserService ✅
Datei: src/plugins/orangehrmAdminPlugin/Service/UserService.php
| Zeile | Konstante | Alt | Neu |
|-------|-----------|-----|-----|
| 37 | USERNAME_MAX_LENGTH | = 40 | = 120 |
---
2.3 BACKEND: API-Validierungen (2 Änderungen)
A. UserAPI ✅
Datei: src/plugins/orangehrmAdminPlugin/Api/UserAPI.php
OpenAPI-Dokumentation aktualisieren:
- Zeile 240: 'maxLength' => 40 → 'maxLength' => 120
- Zeile 374: 'maxLength' => 40 → 'maxLength' => 120
B. ValidationUserNameAPI ✅
Datei: src/plugins/orangehrmAdminPlugin/Api/ValidationUserNameAPI.php
| Zeile | Konstante | Alt | Neu |
|-------|-----------|-----|-----|
| 43 | PARAM_RULE_USER_NAME_MAX_LENGTH | = 40 | = 120 |
---
2.4 FRONTEND: Vue-Komponenten (4 Änderungen)
A. SaveSystemUser ✅
Datei: src/client/src/orangehrmAdminPlugin/pages/systemUser/SaveSystemUser.vue
| Zeile | Feld | Alt | Neu |
|-------|------|-----|-----|
| 148 | username | shouldNotExceedCharLength(40) | shouldNotExceedCharLength(120) |
B. EditSystemUser ✅
Datei: src/client/src/orangehrmAdminPlugin/pages/systemUser/EditSystemUser.vue
| Zeile | Feld | Alt | Neu |
|-------|------|-----|-----|
| 166 | username | shouldNotExceedCharLength(40) | shouldNotExceedCharLength(120) |
C. SaveEmployee ✅
Datei: src/client/src/orangehrmPimPlugin/pages/employee/SaveEmployee.vue
| Zeile | Feld | Alt | Neu |
|-------|------|-----|-----|
| 239 | username | shouldNotExceedCharLength(40) | shouldNotExceedCharLength(120) |
D. AdminUserCreationScreen (Installer) ✅
Datei: installer/client/src/pages/AdminUserCreationScreen.vue
| Zeile | Feld | Alt | Neu |
|-------|------|-----|-----|
| 177 | username | shouldNotExceedCharLength(40) | shouldNotExceedCharLength(120) |
---
📦 TEIL 3: DATENBANK-MIGRATION (ANGEPASST)
3.1 Neue Migration: V5_9_0
Datei: installer/Migration/V5_9_0/Migration.php
<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */
namespace OrangeHRM\Installer\Migration\V5_9_0;
use OrangeHRM\Installer\Util\V1\AbstractMigration;
class Migration extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        // Increase email field lengths to 120 characters (where currently less than 120)
        
        // 1. Employee work email (50 -> 120)
        $this->getConnection()->executeStatement(
            'ALTER TABLE hs_hr_employee 
             MODIFY COLUMN emp_work_email VARCHAR(120) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci'
        );
        
        // 2. Employee other email (50 -> 120)
        $this->getConnection()->executeStatement(
            'ALTER TABLE hs_hr_employee 
             MODIFY COLUMN emp_oth_email VARCHAR(120) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci'
        );
        
        // 3. Candidate email (100 -> 120)
        $this->getConnection()->executeStatement(
            'ALTER TABLE ohrm_candidate 
             MODIFY COLUMN email VARCHAR(120) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL'
        );
        
        // 4. Organization email (30 -> 120)
        $this->getConnection()->executeStatement(
            'ALTER TABLE ohrm_organization_gen_info 
             MODIFY COLUMN email VARCHAR(120) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci'
        );
        
        // 5. Reset password email (60 -> 120)
        $this->getConnection()->executeStatement(
            'ALTER TABLE ohrm_reset_password_request 
             MODIFY COLUMN reset_email VARCHAR(120) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL'
        );
        
        // Note: ohrm_email_subscriber.email stays at 255 (no change)
        // Note: ohrm_email_configuration.sent_as stays at 250 (no change)
        
        // Increase username field length to 120 characters
        
        // 6. User username (40 -> 120)
        $this->getConnection()->executeStatement(
            'ALTER TABLE ohrm_user 
             MODIFY COLUMN user_name VARCHAR(120) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci'
        );
    }
    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '5.9.0';
    }
}
---
📊 AKTUALISIERTE ÜBERSICHT
Zusammenfassung der Änderungen:
| Feld | Datenbank | Backend API | Frontend | Aktion |
|------|-----------|-------------|----------|--------|
| Employee Work Email | 50→120 | 50→120 | 50→120 | ✅ Erhöhen |
| Employee Other Email | 50→120 | 50→120 | 50→120 | ✅ Erhöhen |
| Candidate Email | 100→120 | NEU: 120 | 50→120 | ✅ Erhöhen |
| Organization Email | 30→120 | 30→120 | 30→120 | ✅ Erhöhen |
| Email Subscriber | 255 bleibt | 100→120 | 100→120 | ✅ API/Frontend erhöhen |
| Email Config Sent As | 250 bleibt | 100→120 | 100→120 | ✅ API/Frontend erhöhen |
| Email Config Test | 250 bleibt | 250 bleibt | 250 bleibt | ❌ Keine Änderung |
| Reset Password Email | 60→120 | NEU: 120 | NEU: 120 | ✅ Erhöhen, Validierung hinzufügen |
| Installer Admin Email | - | NEU: 120 | 50→120 | ✅ Erhöhen, Validierung hinzufügen |
| User Username | 40→120 | 40→120 | 40→120 | ✅ Erhöhen |
---
📝 DATEILISTE (29 Dateien)
Backend PHP (17 Dateien):
Entities (5 statt 7):
1. src/plugins/orangehrmPimPlugin/entity/Employee.php ✅
2. src/plugins/orangehrmRecruitmentPlugin/entity/Candidate.php ✅
3. src/plugins/orangehrmAdminPlugin/entity/Organization.php ✅
4. src/plugins/orangehrmAdminPlugin/entity/EmailSubscriber.php ❌ KEINE ÄNDERUNG
5. src/plugins/orangehrmAuthenticationPlugin/entity/ResetPasswordRequest.php ✅
6. src/plugins/orangehrmAdminPlugin/entity/EmailConfiguration.php ❌ KEINE ÄNDERUNG
7. src/plugins/orangehrmCorePlugin/entity/User.php ✅
Services (2):
8. src/plugins/orangehrmPimPlugin/Service/EmployeeService.php ✅
9. src/plugins/orangehrmAdminPlugin/Service/UserService.php ✅
APIs (7):
10. src/plugins/orangehrmPimPlugin/Api/EmployeeContactDetailsAPI.php ✅
11. src/plugins/orangehrmPimPlugin/Api/ValidationEmployeeEmailAPI.php ✅
12. src/plugins/orangehrmPimPlugin/Api/ValidationEmployeeOtherEmailAPI.php ✅
13. src/plugins/orangehrmAdminPlugin/Api/OrganizationAPI.php ✅
14. src/plugins/orangehrmAdminPlugin/Api/EmailSubscriberAPI.php ✅ (nur API/Frontend)
15. src/plugins/orangehrmAdminPlugin/Api/EmailConfigurationAPI.php ✅ (nur sentAs)
16. src/plugins/orangehrmAdminPlugin/Api/UserAPI.php ✅
17. src/plugins/orangehrmAdminPlugin/Api/ValidationUserNameAPI.php ✅
NEU: Validierungen hinzufügen (3):
18. src/plugins/orangehrmRecruitmentPlugin/Api/CandidateAPI.php ✅
19. src/plugins/orangehrmRecruitmentPlugin/Controller/PublicController/ApplicantController.php ✅
20. installer/Controller/Installer/Api/AdminUserAPI.php ✅
Frontend Vue (13 Dateien):
E-Mail-Felder (9):
1. src/client/src/orangehrmPimPlugin/pages/employee/EmployeeContactDetails.vue ✅
2. src/client/src/orangehrmAdminPlugin/pages/organizationGeneralInformation/ViewOrganizationGeneralInformation.vue ✅
3. src/client/src/orangehrmRecruitmentPlugin/pages/SaveCandidate.vue ✅
4. src/client/src/orangehrmRecruitmentPlugin/pages/ApplyJobVacancy.vue ✅
5. src/client/src/orangehrmRecruitmentPlugin/components/CandidateProfile.vue ✅
6. src/client/src/orangehrmAdminPlugin/pages/emailSubscription/SaveSubscriber.vue ✅
7. src/client/src/orangehrmAdminPlugin/pages/emailSubscription/EditSubscriber.vue ✅
8. src/client/src/orangehrmAdminPlugin/pages/emailConfiguration/ViewEmailConfiguration.vue ✅ (nur sentAs)
9. installer/client/src/pages/AdminUserCreationScreen.vue ✅ (E-Mail)
Username-Felder (4):
10. src/client/src/orangehrmAdminPlugin/pages/systemUser/SaveSystemUser.vue ✅
11. src/client/src/orangehrmAdminPlugin/pages/systemUser/EditSystemUser.vue ✅
12. src/client/src/orangehrmPimPlugin/pages/employee/SaveEmployee.vue ✅
13. installer/client/src/pages/AdminUserCreationScreen.vue ✅ (Username)
Migration (1 Datei):
1. installer/Migration/V5_9_0/Migration.php ✅ (NEU)
---
✅ VORTEILE DES ANGEPASSTEN PLANS
✅ Keine Datenprüfung erforderlich:
- Keine Felder werden reduziert
- Alle bestehenden Daten bleiben gültig
- Kein Risiko von Datenverlust oder -kürzung
✅ Konsistente Validierung:
- API-Validierungen werden an DB-Limits angepasst
- Frontend-Validierungen werden einheitlich auf mindestens 120 gesetzt
- Felder mit größeren DB-Limits bleiben flexibel
✅ Rückwärtskompatibilität:
- Alle bestehenden E-Mail-Adressen bleiben gültig
- Keine Breaking Changes
- Sicheres Update ohne Datenprüfung
---
📌 FINALE ZUSAMMENFASSUNG
Änderungen:
- 29 Dateien (statt 31)
- 5 Entity-Änderungen (statt 7)
- 6 ALTER TABLE Statements in Migration (statt 7)
- KEINE Feldreduzierungen ✅
Migration SQL-Statements:
1. hs_hr_employee.emp_work_email: 50 → 120
2. hs_hr_employee.emp_oth_email: 50 → 120
3. ohrm_candidate.email: 100 → 120
4. ohrm_organization_gen_info.email: 30 → 120
5. ohrm_reset_password_request.reset_email: 60 → 120
6. ohrm_user.user_name: 40 → 120
Unveränderte Felder:
- ✅ ohrm_email_subscriber.email: Bleibt 255
- ✅ ohrm_email_configuration.sent_as: Bleibt 250
- ✅ ohrm_email_configuration (testEmailAddress in API): Bleibt 250
# Todos
[✓] 
Passe Plan an: Keine Feldlängen-Reduzierungen
---
✅ Plan aktualisiert! 
Hauptänderungen:
- ❌ EmailSubscriber.email bleibt bei 255 Zeichen (keine Reduzierung)
- ❌ EmailConfiguration.sent_as bleibt bei 250 Zeichen (keine Reduzierung)  
- ❌ EmailConfiguration testEmailAddress bleibt bei 250 Zeichen (keine Reduzierung)
- ✅ Alle anderen Felder werden auf mindestens 120 erhöht
Resultat:
- 29 Dateien zu ändern (statt 31)
- 6 Migrations-Statements (statt 7)
- KEINE Datenprüfung nötig vor Migration
- 100% rückwärtskompatibel
Ist dieser angepasste Plan so in Ordnung?
