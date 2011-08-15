1.  check out "http://orangehrm.com/repos/qa/test-automation/framework" into symfony/test/functional folder
    after the checkout the folder structure should be symfony/test/functional/framework/*
2.  check out selenium-server-standalone-*.jar from http://orangehrm.com/repos/qa/test-automation/tools to somewhere in the system.
3.  cd to the directory where the above file is located and run the selenium-server-standalone with the below command
    java -jar selenium-server-standalone-*.jar
4.  Make sure symfony/config/databases.yml is pointing only to the production db.
    you can just keep the section 'all:' and delete the section 'test:'. (around 9 lines)
5.  cd to symfony and run the tests via 'phpunit test/RecruitmentModuleAllFunctionalTests.php'