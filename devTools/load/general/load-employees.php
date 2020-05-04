<?php
/*
 * Created on Feb 29, 2008
 * Author: Gayanath Jayarathne
 *
 * This script adds 110 employees, corresponding 110 ESS users and one Admin user
 * to OrangeHRM database. Each employee full name is unique.Once OrangeHRM is installed,
 * place this script in its root folder and call it accordingly in the web browser.
 * If data was added, you should see the message "Successfully Created!"
 *
 * Warning:
 * This would first remove all existing employees and users from the database.
 *
 * Admin username = admin		Admin password = admin
 *
 * For employee, "Kayla Abbey":
 *
 * ESS username = Kayla			ESS password = Kayla
 *
 * As above, for each employee, username and password would be his/her first name
 *
 */

// Database information
require_once "../../../lib/confs/Conf.php";

// Connecting to the database
$conf = new Conf();
$dbConnection = mysqli_connect($conf->dbhost, $conf->dbuser, $conf->dbpass, $conf->dbname, $conf->dbport);
if (!$dbConnection) { echo mysqli_error($dbConnection); die; }
$dbConnection->set_charset("utf8mb4");

//if (!mysqli_select_db($conf->dbname)) { echo mysqli_error(); exit(0); }

// Truncating tables
if (!mysqli_query($dbConnection,"DELETE from `hs_hr_employee`")) { echo mysqli_error($dbConnection); die; }
if (!mysqli_query($dbConnection,"DELETE from `ohrm_emp_termination`")) { echo mysqli_error($dbConnection); die; }
if (!mysqli_query($dbConnection,"DELETE from `ohrm_user`")) { echo mysqli_error($dbConnection); die; }

// Employee data
$employees[0][0] = "001"; $employees[0][1] = "Abbey"; $employees[0][2] = "Kayla";
$employees[1][0] = "002"; $employees[1][1] = "Abel"; $employees[1][2] = "Ashley";
$employees[2][0] = "003"; $employees[2][1] = "Abraham"; $employees[2][2] = "Tyler";
$employees[3][0] = "004"; $employees[3][1] = "Abrahams"; $employees[3][2] = "Landon";
$employees[4][0] = "005"; $employees[4][1] = "Abrahamson"; $employees[4][2] = "James";
$employees[5][0] = "006"; $employees[5][1] = "Abram"; $employees[5][2] = "Hailey";
$employees[6][0] = "007"; $employees[6][1] = "Abrams"; $employees[6][2] = "Jenna";
$employees[7][0] = "008"; $employees[7][1] = "Abramson"; $employees[7][2] = "Audrey";
$employees[8][0] = "009"; $employees[8][1] = "Achilles"; $employees[8][2] = "Hayden";
$employees[9][0] = "010"; $employees[9][1] = "Acker"; $employees[9][2] = "Lauren";
$employees[10][0] = "011"; $employees[10][1] = "Ackerman"; $employees[10][2] = "Grace";
$employees[11][0] = "012"; $employees[11][1] = "Adam"; $employees[11][2] = "Chase";
$employees[12][0] = "013"; $employees[12][1] = "Adams"; $employees[12][2] = "Brooklyn";
$employees[13][0] = "014"; $employees[13][1] = "Adamson"; $employees[13][2] = "Adrian";
$employees[14][0] = "015"; $employees[14][1] = "Adcock"; $employees[14][2] = "Alexa";
$employees[15][0] = "016"; $employees[15][1] = "Addison"; $employees[15][2] = "Anthony";
$employees[16][0] = "017"; $employees[16][1] = "Aiken"; $employees[16][2] = "William";
$employees[17][0] = "018"; $employees[17][1] = "Ainsworth"; $employees[17][2] = "Andrew";
$employees[18][0] = "019"; $employees[18][1] = "Aitken"; $employees[18][2] = "Benjamin";
$employees[19][0] = "020"; $employees[19][1] = "Akerman"; $employees[19][2] = "Amanda";
$employees[20][0] = "021"; $employees[20][1] = "Akers"; $employees[20][2] = "Lillian";
$employees[21][0] = "022"; $employees[21][1] = "Albert"; $employees[21][2] = "Jennifer";
$employees[22][0] = "023"; $employees[22][1] = "Alberts"; $employees[22][2] = "Carter";
$employees[23][0] = "024"; $employees[23][1] = "Albinson"; $employees[23][2] = "Kimberly";
$employees[24][0] = "025"; $employees[24][1] = "Alexander"; $employees[24][2] = "Amelia";
$employees[25][0] = "026"; $employees[25][1] = "Alfredson"; $employees[25][2] = "Mason";
$employees[26][0] = "027"; $employees[26][1] = "Alfson"; $employees[26][2] = "Taylor";
$employees[27][0] = "028"; $employees[27][1] = "Allard"; $employees[27][2] = "Brandon";
$employees[28][0] = "029"; $employees[28][1] = "Allen"; $employees[28][2] = "Lucas";
$employees[29][0] = "030"; $employees[29][1] = "Allsopp"; $employees[29][2] = "Dominic";
$employees[30][0] = "031"; $employees[30][1] = "Alvey"; $employees[30][2] = "Justin";
$employees[31][0] = "032"; $employees[31][1] = "Anderson"; $employees[31][2] = "Steven";
$employees[32][0] = "033"; $employees[32][1] = "Andrews"; $employees[32][2] = "Austin";
$employees[33][0] = "034"; $employees[33][1] = "Andrewson"; $employees[33][2] = "Marie";
$employees[34][0] = "035"; $employees[34][1] = "Anson"; $employees[34][2] = "Robert";
$employees[35][0] = "036"; $employees[35][1] = "Anthonyson"; $employees[35][2] = "Laura";
$employees[36][0] = "037"; $employees[36][1] = "Appleby"; $employees[36][2] = "Carson";
$employees[37][0] = "038"; $employees[37][1] = "Appleton"; $employees[37][2] = "Timothy";
$employees[38][0] = "039"; $employees[38][1] = "Archer"; $employees[38][2] = "Allison";
$employees[39][0] = "040"; $employees[39][1] = "Arkwright"; $employees[39][2] = "Isabelle";
$employees[40][0] = "041"; $employees[40][1] = "Armistead"; $employees[40][2] = "Katie";
$employees[41][0] = "042"; $employees[41][1] = "Arnold"; $employees[41][2] = "Andrea";
$employees[42][0] = "043"; $employees[42][1] = "Arrington"; $employees[42][2] = "Brooke";
$employees[43][0] = "044"; $employees[43][1] = "Arterberry"; $employees[43][2] = "Julian";
$employees[44][0] = "045"; $employees[44][1] = "Arterbury"; $employees[44][2] = "Christian";
$employees[45][0] = "046"; $employees[45][1] = "Arthur"; $employees[45][2] = "Patrick";
$employees[46][0] = "047"; $employees[46][1] = "Arthurson"; $employees[46][2] = "Sydney";
$employees[47][0] = "048"; $employees[47][1] = "Ash"; $employees[47][2] = "Chelsea";
$employees[48][0] = "049"; $employees[48][1] = "Ashley"; $employees[48][2] = "Victoria";
$employees[49][0] = "050"; $employees[49][1] = "Ashworth"; $employees[49][2] = "Charles";
$employees[50][0] = "051"; $employees[50][1] = "Atkins"; $employees[50][2] = "Jayden";
$employees[51][0] = "052"; $employees[51][1] = "Atkinson"; $employees[51][2] = "Caroline";
$employees[52][0] = "053"; $employees[52][1] = "Attaway"; $employees[52][2] = "Ashlyn";
$employees[53][0] = "054"; $employees[53][1] = "Atteberry"; $employees[53][2] = "Colin";
$employees[54][0] = "055"; $employees[54][1] = "Attwater"; $employees[54][2] = "Blake";
$employees[55][0] = "056"; $employees[55][1] = "Barker"; $employees[55][2] = "Haley";
$employees[56][0] = "057"; $employees[56][1] = "Barlow"; $employees[56][2] = "Katherine";
$employees[57][0] = "058"; $employees[57][1] = "Barton"; $employees[57][2] = "Charlotte";
$employees[58][0] = "059"; $employees[58][1] = "Bartram"; $employees[58][2] = "George";
$employees[59][0] = "060"; $employees[59][1] = "Bates"; $employees[59][2] = "Richard";
$employees[60][0] = "061"; $employees[60][1] = "Bateson"; $employees[60][2] = "Heather";
$employees[61][0] = "062"; $employees[61][1] = "Baxter"; $employees[61][2] = "Oliver";
$employees[62][0] = "063"; $employees[62][1] = "Beake"; $employees[62][2] = "Alexandra";
$employees[63][0] = "064"; $employees[63][1] = "Beasley"; $employees[63][2] = "Courtney";
$employees[64][0] = "065"; $employees[64][1] = "Beattie"; $employees[64][2] = "Ellie";
$employees[65][0] = "066"; $employees[65][1] = "Becket"; $employees[65][2] = "Skyler";
$employees[66][0] = "067"; $employees[66][1] = "Beckham"; $employees[66][2] = "Mariah";
$employees[67][0] = "068"; $employees[67][1] = "Benjaminson"; $employees[67][2] = "Scott";
$employees[68][0] = "069"; $employees[68][1] = "Bennett"; $employees[68][2] = "Isabel";
$employees[69][0] = "070"; $employees[69][1] = "Benson"; $employees[69][2] = "Christina";
$employees[70][0] = "071"; $employees[70][1] = "Bernard"; $employees[70][2] = "Jamie";
$employees[71][0] = "072"; $employees[71][1] = "Bishop"; $employees[71][2] = "Edward";
$employees[72][0] = "073"; $employees[72][1] = "Blackburn"; $employees[72][2] = "Stephen";
$employees[73][0] = "074"; $employees[73][1] = "Blackwood"; $employees[73][2] = "Karen";
$employees[74][0] = "075"; $employees[74][1] = "Blakeslee"; $employees[74][2] = "Parker";
$employees[75][0] = "076"; $employees[75][1] = "Bloodworth"; $employees[75][2] = "Jeremy";
$employees[76][0] = "077"; $employees[76][1] = "Boivin"; $employees[76][2] = "Peter";
$employees[77][0] = "078"; $employees[77][1] = "Bolton"; $employees[77][2] = "Lindsey";
$employees[78][0] = "079"; $employees[78][1] = "Booner"; $employees[78][2] = "Henry";
$employees[79][0] = "080"; $employees[79][1] = "Braddock"; $employees[79][2] = "Alice";
$employees[80][0] = "081"; $employees[80][1] = "Brooks"; $employees[80][2] = "Sandra";
$employees[81][0] = "082"; $employees[81][1] = "Burnham"; $employees[81][2] = "Travis";
$employees[82][0] = "083"; $employees[82][1] = "Cantrell"; $employees[82][2] = "Colton";
$employees[83][0] = "084"; $employees[83][1] = "Carpenter"; $employees[83][2] = "Brian";
$employees[84][0] = "085"; $employees[84][1] = "Chapman"; $employees[84][2] = "Daisy";
$employees[85][0] = "086"; $employees[85][1] = "Christopher"; $employees[85][2] = "Barbara";
$employees[86][0] = "087"; $employees[86][1] = "Clarke"; $employees[86][2] = "Jeffrey";
$employees[87][0] = "088"; $employees[87][1] = "Clawson"; $employees[87][2] = "Eliza";
$employees[88][0] = "089"; $employees[88][1] = "Clifford"; $employees[88][2] = "Helena";
$employees[89][0] = "090"; $employees[89][1] = "Clinton"; $employees[89][2] = "Linda";
$employees[90][0] = "091"; $employees[90][1] = "Cockburn"; $employees[90][2] = "Lilly";
$employees[91][0] = "092"; $employees[91][1] = "Collingwood"; $employees[91][2] = "Gregory";
$employees[92][0] = "093"; $employees[92][1] = "Cooper"; $employees[92][2] = "Harry";
$employees[93][0] = "094"; $employees[93][1] = "Dalton"; $employees[93][2] = "Nancy";
$employees[94][0] = "095"; $employees[94][1] = "Daniell"; $employees[94][2] = "Harrison";
$employees[95][0] = "096"; $employees[95][1] = "Danielson"; $employees[95][2] = "Monica";
$employees[96][0] = "097"; $employees[96][1] = "Darby"; $employees[96][2] = "Martin";
$employees[97][0] = "098"; $employees[97][1] = "Davidson"; $employees[97][2] = "Cindy";
$employees[98][0] = "099"; $employees[98][1] = "Dexter"; $employees[98][2] = "Violet";
$employees[99][0] = "100"; $employees[99][1] = "Dickens"; $employees[99][2] = "Vivian";
$employees[100][0] = "101"; $employees[100][1] = "Downer"; $employees[100][2] = "Dennis";
$employees[101][0] = "102"; $employees[101][1] = "Earlson"; $employees[101][2] = "Maggie";
$employees[102][0] = "103"; $employees[102][1] = "Edwards"; $employees[102][2] = "Justine";
$employees[103][0] = "104"; $employees[103][1] = "Emerson"; $employees[103][2] = "Ronald";
$employees[104][0] = "105"; $employees[104][1] = "Ericson"; $employees[104][2] = "Lawrence";
$employees[105][0] = "106"; $employees[105][1] = "Fleming"; $employees[105][2] = "Elvis";
$employees[106][0] = "107"; $employees[106][1] = "Forester"; $employees[106][2] = "Bruce";
$employees[107][0] = "108"; $employees[107][1] = "Franklin"; $employees[107][2] = "Melinda";
$employees[108][0] = "109"; $employees[108][1] = "Gibson"; $employees[108][2] = "Phillip";
$employees[109][0] = "110"; $employees[109][1] = "Gilbert"; $employees[109][2] = "Janet";

// User data
$users[0][0] = "USR002"; $users[0][1] = "Kayla"; $users[0][2] = md5("Kayla");
$users[1][0] = "USR003"; $users[1][1] = "Ashley"; $users[1][2] = md5("Ashley");
$users[2][0] = "USR004"; $users[2][1] = "Tyler"; $users[2][2] = md5("Tyler");
$users[3][0] = "USR005"; $users[3][1] = "Landon"; $users[3][2] = md5("Landon");
$users[4][0] = "USR006"; $users[4][1] = "James"; $users[4][2] = md5("James");
$users[5][0] = "USR007"; $users[5][1] = "Hailey"; $users[5][2] = md5("Hailey");
$users[6][0] = "USR008"; $users[6][1] = "Jenna"; $users[6][2] = md5("Jenna");
$users[7][0] = "USR009"; $users[7][1] = "Audrey"; $users[7][2] = md5("Audrey");
$users[8][0] = "USR010"; $users[8][1] = "Hayden"; $users[8][2] = md5("Hayden");
$users[9][0] = "USR011"; $users[9][1] = "Lauren"; $users[9][2] = md5("Lauren");
$users[10][0] = "USR012"; $users[10][1] = "Grace"; $users[10][2] = md5("Grace");
$users[11][0] = "USR013"; $users[11][1] = "Chase"; $users[11][2] = md5("Chase");
$users[12][0] = "USR014"; $users[12][1] = "Brooklyn"; $users[12][2] = md5("Brooklyn");
$users[13][0] = "USR015"; $users[13][1] = "Adrian"; $users[13][2] = md5("Adrian");
$users[14][0] = "USR016"; $users[14][1] = "Alexa"; $users[14][2] = md5("Alexa");
$users[15][0] = "USR017"; $users[15][1] = "Anthony"; $users[15][2] = md5("Anthony");
$users[16][0] = "USR018"; $users[16][1] = "William"; $users[16][2] = md5("William");
$users[17][0] = "USR019"; $users[17][1] = "Andrew"; $users[17][2] = md5("Andrew");
$users[18][0] = "USR020"; $users[18][1] = "Benjamin"; $users[18][2] = md5("Benjamin");
$users[19][0] = "USR021"; $users[19][1] = "Amanda"; $users[19][2] = md5("Amanda");
$users[20][0] = "USR022"; $users[20][1] = "Lillian"; $users[20][2] = md5("Lillian");
$users[21][0] = "USR023"; $users[21][1] = "Jennifer"; $users[21][2] = md5("Jennifer");
$users[22][0] = "USR024"; $users[22][1] = "Carter"; $users[22][2] = md5("Carter");
$users[23][0] = "USR025"; $users[23][1] = "Kimberly"; $users[23][2] = md5("Kimberly");
$users[24][0] = "USR026"; $users[24][1] = "Amelia"; $users[24][2] = md5("Amelia");
$users[25][0] = "USR027"; $users[25][1] = "Mason"; $users[25][2] = md5("Mason");
$users[26][0] = "USR028"; $users[26][1] = "Taylor"; $users[26][2] = md5("Taylor");
$users[27][0] = "USR029"; $users[27][1] = "Brandon"; $users[27][2] = md5("Brandon");
$users[28][0] = "USR030"; $users[28][1] = "Lucas"; $users[28][2] = md5("Lucas");
$users[29][0] = "USR031"; $users[29][1] = "Dominic"; $users[29][2] = md5("Dominic");
$users[30][0] = "USR032"; $users[30][1] = "Justin"; $users[30][2] = md5("Justin");
$users[31][0] = "USR033"; $users[31][1] = "Steven"; $users[31][2] = md5("Steven");
$users[32][0] = "USR034"; $users[32][1] = "Austin"; $users[32][2] = md5("Austin");
$users[33][0] = "USR035"; $users[33][1] = "Marie"; $users[33][2] = md5("Marie");
$users[34][0] = "USR036"; $users[34][1] = "Robert"; $users[34][2] = md5("Robert");
$users[35][0] = "USR037"; $users[35][1] = "Laura"; $users[35][2] = md5("Laura");
$users[36][0] = "USR038"; $users[36][1] = "Carson"; $users[36][2] = md5("Carson");
$users[37][0] = "USR039"; $users[37][1] = "Timothy"; $users[37][2] = md5("Timothy");
$users[38][0] = "USR040"; $users[38][1] = "Allison"; $users[38][2] = md5("Allison");
$users[39][0] = "USR041"; $users[39][1] = "Isabelle"; $users[39][2] = md5("Isabelle");
$users[40][0] = "USR042"; $users[40][1] = "Katie"; $users[40][2] = md5("Katie");
$users[41][0] = "USR043"; $users[41][1] = "Andrea"; $users[41][2] = md5("Andrea");
$users[42][0] = "USR044"; $users[42][1] = "Brooke"; $users[42][2] = md5("Brooke");
$users[43][0] = "USR045"; $users[43][1] = "Julian"; $users[43][2] = md5("Julian");
$users[44][0] = "USR046"; $users[44][1] = "Christian"; $users[44][2] = md5("Christian");
$users[45][0] = "USR047"; $users[45][1] = "Patrick"; $users[45][2] = md5("Patrick");
$users[46][0] = "USR048"; $users[46][1] = "Sydney"; $users[46][2] = md5("Sydney");
$users[47][0] = "USR049"; $users[47][1] = "Chelsea"; $users[47][2] = md5("Chelsea");
$users[48][0] = "USR050"; $users[48][1] = "Victoria"; $users[48][2] = md5("Victoria");
$users[49][0] = "USR051"; $users[49][1] = "Charles"; $users[49][2] = md5("Charles");
$users[50][0] = "USR052"; $users[50][1] = "Jayden"; $users[50][2] = md5("Jayden");
$users[51][0] = "USR053"; $users[51][1] = "Caroline"; $users[51][2] = md5("Caroline");
$users[52][0] = "USR054"; $users[52][1] = "Ashlyn"; $users[52][2] = md5("Ashlyn");
$users[53][0] = "USR055"; $users[53][1] = "Colin"; $users[53][2] = md5("Colin");
$users[54][0] = "USR056"; $users[54][1] = "Blake"; $users[54][2] = md5("Blake");
$users[55][0] = "USR057"; $users[55][1] = "Haley"; $users[55][2] = md5("Haley");
$users[56][0] = "USR058"; $users[56][1] = "Katherine"; $users[56][2] = md5("Katherine");
$users[57][0] = "USR059"; $users[57][1] = "Charlotte"; $users[57][2] = md5("Charlotte");
$users[58][0] = "USR060"; $users[58][1] = "George"; $users[58][2] = md5("George");
$users[59][0] = "USR061"; $users[59][1] = "Richard"; $users[59][2] = md5("Richard");
$users[60][0] = "USR062"; $users[60][1] = "Heather"; $users[60][2] = md5("Heather");
$users[61][0] = "USR063"; $users[61][1] = "Oliver"; $users[61][2] = md5("Oliver");
$users[62][0] = "USR064"; $users[62][1] = "Alexandra"; $users[62][2] = md5("Alexandra");
$users[63][0] = "USR065"; $users[63][1] = "Courtney"; $users[63][2] = md5("Courtney");
$users[64][0] = "USR066"; $users[64][1] = "Ellie"; $users[64][2] = md5("Ellie");
$users[65][0] = "USR067"; $users[65][1] = "Skyler"; $users[65][2] = md5("Skyler");
$users[66][0] = "USR068"; $users[66][1] = "Mariah"; $users[66][2] = md5("Mariah");
$users[67][0] = "USR069"; $users[67][1] = "Scott"; $users[67][2] = md5("Scott");
$users[68][0] = "USR070"; $users[68][1] = "Isabel"; $users[68][2] = md5("Isabel");
$users[69][0] = "USR071"; $users[69][1] = "Christina"; $users[69][2] = md5("Christina");
$users[70][0] = "USR072"; $users[70][1] = "Jamie"; $users[70][2] = md5("Jamie");
$users[71][0] = "USR073"; $users[71][1] = "Edward"; $users[71][2] = md5("Edward");
$users[72][0] = "USR074"; $users[72][1] = "Stephen"; $users[72][2] = md5("Stephen");
$users[73][0] = "USR075"; $users[73][1] = "Karen"; $users[73][2] = md5("Karen");
$users[74][0] = "USR076"; $users[74][1] = "Parker"; $users[74][2] = md5("Parker");
$users[75][0] = "USR077"; $users[75][1] = "Jeremy"; $users[75][2] = md5("Jeremy");
$users[76][0] = "USR078"; $users[76][1] = "Peter"; $users[76][2] = md5("Peter");
$users[77][0] = "USR079"; $users[77][1] = "Lindsey"; $users[77][2] = md5("Lindsey");
$users[78][0] = "USR080"; $users[78][1] = "Henry"; $users[78][2] = md5("Henry");
$users[79][0] = "USR081"; $users[79][1] = "Alice"; $users[79][2] = md5("Alice");
$users[80][0] = "USR082"; $users[80][1] = "Sandra"; $users[80][2] = md5("Sandra");
$users[81][0] = "USR083"; $users[81][1] = "Travis"; $users[81][2] = md5("Travis");
$users[82][0] = "USR084"; $users[82][1] = "Colton"; $users[82][2] = md5("Colton");
$users[83][0] = "USR085"; $users[83][1] = "Brian"; $users[83][2] = md5("Brian");
$users[84][0] = "USR086"; $users[84][1] = "Daisy"; $users[84][2] = md5("Daisy");
$users[85][0] = "USR087"; $users[85][1] = "Barbara"; $users[85][2] = md5("Barbara");
$users[86][0] = "USR088"; $users[86][1] = "Jeffrey"; $users[86][2] = md5("Jeffrey");
$users[87][0] = "USR089"; $users[87][1] = "Eliza"; $users[87][2] = md5("Eliza");
$users[88][0] = "USR090"; $users[88][1] = "Helena"; $users[88][2] = md5("Helena");
$users[89][0] = "USR091"; $users[89][1] = "Linda"; $users[89][2] = md5("Linda");
$users[90][0] = "USR092"; $users[90][1] = "Lilly"; $users[90][2] = md5("Lilly");
$users[91][0] = "USR093"; $users[91][1] = "Gregory"; $users[91][2] = md5("Gregory");
$users[92][0] = "USR094"; $users[92][1] = "Harry"; $users[92][2] = md5("Harry");
$users[93][0] = "USR095"; $users[93][1] = "Nancy"; $users[93][2] = md5("Nancy");
$users[94][0] = "USR096"; $users[94][1] = "Harrison"; $users[94][2] = md5("Harrison");
$users[95][0] = "USR097"; $users[95][1] = "Monica"; $users[95][2] = md5("Monica");
$users[96][0] = "USR098"; $users[96][1] = "Martin"; $users[96][2] = md5("Martin");
$users[97][0] = "USR099"; $users[97][1] = "Cindy"; $users[97][2] = md5("Cindy");
$users[98][0] = "USR100"; $users[98][1] = "Violet"; $users[98][2] = md5("Violet");
$users[99][0] = "USR101"; $users[99][1] = "Vivian"; $users[99][2] = md5("Vivian");
$users[100][0] = "USR102"; $users[100][1] = "Dennis"; $users[100][2] = md5("Dennis");
$users[101][0] = "USR103"; $users[101][1] = "Maggie"; $users[101][2] = md5("Maggie");
$users[102][0] = "USR104"; $users[102][1] = "Justine"; $users[102][2] = md5("Justine");
$users[103][0] = "USR105"; $users[103][1] = "Ronald"; $users[103][2] = md5("Ronald");
$users[104][0] = "USR106"; $users[104][1] = "Lawrence"; $users[104][2] = md5("Lawrence");
$users[105][0] = "USR107"; $users[105][1] = "Elvis"; $users[105][2] = md5("Elvis");
$users[106][0] = "USR108"; $users[106][1] = "Bruce"; $users[106][2] = md5("Bruce");
$users[107][0] = "USR109"; $users[107][1] = "Melinda"; $users[107][2] = md5("Melinda");
$users[108][0] = "USR110"; $users[108][1] = "Phillip"; $users[108][2] = md5("Phillip");
$users[109][0] = "USR111"; $users[109][1] = "Janet"; $users[109][2] = md5("Janet");

// Entering data into `hs_hr_employee` and `hs_hr_users`
//
// NOTE: Don't leave spaces after the heredoc identifiers (EMPSQLSTR and USERSQLSTR)
//

$count = count($employees);

for ($i=0; $i<$count; $i++) {
   $empNum = $i + 1;
   $empSql = <<< EMPSQLSTR
INSERT INTO hs_hr_employee SET
  emp_number = {$empNum},
  employee_id = '{$employees[$i][0]}',
  emp_lastname = '{$employees[$i][1]}',
  emp_firstname = '{$employees[$i][2]}',
  emp_middle_name = '',
  emp_nick_name = '',
  emp_smoker = 0,
  ethnic_race_code = NULL,
  emp_birthday = NULL,
  nation_code = NULL,
  emp_gender = NULL,
  emp_marital_status = NULL,
  emp_ssn_num = '',
  emp_sin_num = '',
  emp_other_id = '',
  emp_dri_lice_num = '',
  emp_dri_lice_exp_date = NULL,
  emp_military_service = '',
  emp_status = NULL,
  job_title_code = NULL,
  eeo_cat_code = NULL,
  work_station = NULL,
  emp_street1 = '',
  emp_street2 = '',
  city_code = '',
  coun_code = '',
  provin_code = '',
  emp_zipcode = NULL,
  emp_hm_telephone = NULL,
  emp_mobile = NULL,
  emp_work_telephone = NULL,
  emp_work_email = NULL,
  sal_grd_code = NULL,
  joined_date = NULL,
  emp_oth_email = NULL
EMPSQLSTR;

    if (!mysqli_query($dbConnection,$empSql)) { echo mysqli_error(); die; }

    if ($i == 0) {
        // Default admin
        $q = "INSERT INTO `ohrm_user` ( `emp_number`, `user_name`, `user_password`,`user_role_id`) VALUES ('1', 'admin', '21232f297a57a5a743894a0e4a801fc3', 1)";
        if (!mysqli_query($dbConnection, $q)) {
            echo mysqli_error($dbConnection);
            die;
        }
    }

    $userSql = <<< USERSQLSTR
INSERT INTO ohrm_user SET
  user_role_id = 2,
  emp_number = {$empNum},
  user_name = '{$users[$i][1]}',
  user_password = '{$users[$i][2]}'
USERSQLSTR;

    if (!mysqli_query($dbConnection,$userSql)) { echo mysqli_error($dbConnection); die; }
}
// Sets Last ID at `hs_hr_unique_id`
if (!mysqli_query($dbConnection,"UPDATE `hs_hr_unique_id` SET `last_id` = '".count($employees)."' WHERE `field_name` = 'emp_number' AND `table_name` = 'hs_hr_employee'")) { echo mysqli_error($dbConnection); exit(0); }

//End
echo "<h2>Successfully Created " . count($employees) . " employees and their user accounts!</h2>";

?>
<?php if (!mysqli_error($dbConnection)): ?>
<pre>
 * Admin username = admin		Admin password = admin
 *
 * For employee, "Kayla Abbey":
 *
 * ESS username = Kayla			ESS password = Kayla
 *
 * As above, for each employee, username and password would be his/her first name
</pre>
<?php endif; ?>
