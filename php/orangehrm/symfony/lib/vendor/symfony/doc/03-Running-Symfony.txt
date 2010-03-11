Chapter 3 - Running Symfony
===========================

As you've learned in previous chapters, the symfony framework is a set of files written in PHP. A symfony project uses these files, so installing symfony means getting these files and making them available for the project.

Symfony requires at least PHP 5.2. Make sure you have it installed by opening a command line and typing this command:

    > php -v

    PHP 5.2.5 (cli) (built: Nov 20 2007 16:55:40) 
    Copyright (c) 1997-2007 The PHP Group
    Zend Engine v2.2.0, Copyright (c) 1998-2007 Zend Technologies

If the version number is 5.2 or higher, then you're ready for the installation, as described in this chapter.

Installing the Sandbox
----------------------

If you just want to see what symfony is capable of, you'll probably go for the fast installation. In that case, you need the sandbox.

The sandbox is a simple archive of files. It contains an empty symfony project including all the required libraries (symfony, lime, Prototype with Scriptaculous, Doctrine and Propel with Phing), a default application, and basic configuration. It will work out of the box, without specific server configuration or any additional packages.

To install it, download the sandbox archive from [http://www.symfony-project.org/get/sf_sandbox_1_2.tgz](http://www.symfony-project.org/get/sf_sandbox_1_2.tgz). Unpack it under the root web directory configured for your server (usually `web/` or `www/`). For the purposes of uniformity, this chapter will assume you unpacked it to the directory `sf_sandbox/`.

>**CAUTION**
>Having all the files under the root web directory is fine for your own tests in a local host, but is a bad practice in a production server. It makes all the internals of your application visible to end users.

Test your installation by executing the symfony CLI. Go to the new `sf_sandbox/` directory and type the following:

    > php symfony -V

You should see the sandbox version number:

    symfony version 1.2.0 (/path/to/the/symfony/lib/dir/used/by/the/sandbox)

Now make sure that your web server can browse the sandbox by requesting this URL:

    http://localhost/sf_sandbox/web/frontend_dev.php/

You should see a congratulations page that looks like Figure 3-1, and it means that your installation is finished. If not, then an error message will guide you through the configuration changes needed. You can also refer to the "Troubleshooting" section later in this chapter.

Figure 3-1 - Sandbox congratulations page

![Sandbox congratulations page](/images/book/F0301.jpg "Sandbox congratulations page")

The sandbox is intended for you to practice with symfony on a local computer, not to develop complex applications that may end up on the Web. However, the version of symfony shipped with the sandbox is fully functional and equivalent to the one you can install via PEAR.

To uninstall a sandbox, just remove the `sf_sandbox/` directory from your `web/` folder.

Installing the symfony Libraries
--------------------------------

When developing an application, you will probably need to install symfony twice: once for your development environment and once for the host server (unless your host already has symfony installed). For each server, you will probably want to avoid duplication by keeping all the symfony files in a single place, whether you develop only one application or several applications.

Since the symfony framework evolves quickly, a new stable version could very well be released only a few days after your first installation. You need to think of the framework upgrade as a major concern, and that's another reason why you should share one instance of the symfony libraries across all your symfony projects.

When it comes to installing the libraries for a real application development, you have two alternatives:

  * The PEAR installation is recommended for most people. It can be easily shared and upgraded, and the installation process is straightforward.
  * The Subversion (SVN) installation is meant to be used only by advanced PHP developers, who want to take advantage of the latest patches, add features of their own, and/or contribute to the symfony project.

Symfony integrates a few other packages:

  * lime is a unit testing utility.
  * Propel is for ORM. It provides object persistence and query service.
  * Phing is a build system used by Propel to generate the model classes.

Lime is developed by the symfony team. Propel, and Phing come from another team and are released under the GNU Lesser Public General License (LGPL). All these packages are bundled with symfony.

>**TIP**
>The symfony framework is licensed under a MIT license. All the copyrights for the bundled third party libraries can be found in the `COPYRIGHT` file and the associated licenses are stored in the `licenses/` directory.

### Installing the symfony PEAR Package

The symfony PEAR package contains the symfony libraries and all its dependencies. It also contains a script that will extend your CLI to include the `symfony` command.

The first step to install it is to add the symfony channel to PEAR, by issuing this command:

    > pear channel-discover pear.symfony-project.com

To see the libraries available in this channel, type the following:

    > pear remote-list -c symfony

Now you are ready to install the latest stable version of symfony. Issue this command:

    > pear install symfony/symfony

    downloading symfony-1.2.0.tgz ...
    Starting to download symfony-1.2.0.tgz (1,283,270 bytes)
    .................................................................
    .................................................................
    .............done: 1,283,270 bytes
    install ok: channel://pear.symfony-project.com/symfony-1.2.0

That's it. The symfony files and CLI are installed. Check that the installation succeeded by calling the new `symfony` command line, asking for the version number:

    > symfony -V

    symfony version 1.2.0 (/path/to/the/pear/symfony/lib/dir)

The symfony libraries are now installed in directories as follows:

  * `$php_dir/symfony/` contains the main libraries.
  * `$data_dir/symfony/` contains the web assets used by symfony default modules.
  * `$doc_dir/symfony/` contains the documentation.
  * `$test_dir/symfony/` contains symfony core unit and functional tests.

The `_dir` variables are part of your PEAR configuration. To see their values, type the following:

    > pear config-show

### Checking Out symfony from the SVN Repository

For production servers, or when PEAR is not an option, you can download the 
latest version of the symfony libraries directly from the symfony Subversion
repository by requesting a checkout:

    > mkdir /path/to/symfony
    > cd /path/to/symfony
    > svn checkout http://svn.symfony-project.com/tags/RELEASE_1_2_0/ .

>**TIP**
>For the latest stable bug-fix release on the 1.2 branch (1.2.x) refer to 
>([http://www.symfony-project.org/installation/1_2](http://www.symfony-project.org/installation/1_2))

The `symfony` command, available only for PEAR installations, is a call to the `/path/to/symfony/data/bin/symfony` script. So the following would be the equivalent to the `symfony -V` command for an SVN installation:

    > php /path/to/symfony/data/bin/symfony -V

    symfony version 1.2.0 (/path/to/the/svn/symfony/lib/dir)

If you chose an SVN installation, you probably already have an existing symfony project. For this project to make use of the symfony files, you need to change the path defined in the project's `config/ProjectConfiguration.class.php` file, as follows:

    [php]
    <?php

    require_once '/path/to/symfony/lib/autoload/sfCoreAutoload.class.php';
    sfCoreAutoload::register();

    class ProjectConfiguration extends sfProjectConfiguration
    {
      // ...
    }

Chapter 19 proposes other ways to link a project with a symfony installation (including symbolic links and relative paths).

>**TIP**
>Alternatively, you can also download the PEAR package. Refer to  
>([http://www.symfony-project.org/installation/1_2](http://www.symfony-project.org/installation/1_2))
>for latest 1.2 release. You will have the same result as with a checkout.

Setting Up an Application
-------------------------

As you learned in Chapter 2, symfony gathers related applications in projects. All the applications of a project share the same databases. In order to set up an application, you must first set up a project.

### Creating the Project

Each symfony project follows a predefined directory structure. The symfony command line automates the creation of new projects by initiating the skeleton of the project, with the proper tree structure and access rights. So to create a project, simply create a new directory and ask symfony to make it a project.

For a PEAR installation, issue these commands:

    > mkdir ~/myproject
    > cd ~/myproject
    > symfony generate:project myproject

For an SVN installation, create a project with these commands:

    > mkdir ~/myproject
    > cd ~/myproject
    > php /path/to/symfony/data/bin/symfony generate:project myproject

Symfony will create a directory structure that looks like this:

    apps/
    cache/
    config/
    data/
    doc/
    lib/
    log/
    plugins/
    test/
    web/

>**TIP**
>The `generate:project` task adds a `symfony` script in the project root directory. This PHP script does the same as the `symfony` command installed by PEAR, so you can call `php symfony` instead of `symfony` if you don't have native command-line support (for SVN installations).

### Creating the Application

The project is not yet ready to be viewed, because it requires at least one application. To initialize it, use the `symfony generate:app` command and pass the name of the application as an argument:

    > php symfony generate:app frontend

This will create a `frontend/` directory in the `apps/` folder of the project root, with a default application configuration and a set of directories ready to host the file of your website:

    apps/
      frontend/
        config/
        i18n/
        lib/
        modules/
        templates/

Some PHP files corresponding to the front controllers of each default environment are also created in the project `web` directory:

    web/
      index.php
      frontend_dev.php

`index.php` is the production front controller of the new application. Because you created the first application of the project, symfony created a file called `index.php` instead of `frontend.php` (if you now add a new application called `backend`, the new production front controller will be named `backend.php`). To run your application in the development environment, call the front controller `frontend_dev.php`. Note that for security reasons the development controller is available only for localhost by default. You'll learn more about these environments in Chapter 5.

The `symfony` command must always be called from the project's root directory (`myproject/` in the preceding examples), because all the tasks performed by this command are project-specific.

Configuring the Web Server
--------------------------

The scripts of the `web/` directory are the entry points to the application. To be able to access them from the Internet, the web server must be configured. In your development server, as well as in a professional hosting solution, you probably have access to the Apache configuration and you can set up a virtual host. On a shared-host server, you probably have access only to an `.htaccess` file.

### Setting Up a Virtual Host

Listing 3-1 is an example of Apache configuration, where a new virtual host is added in the `httpd.conf` file.

Listing 3-1 - Sample Apache Configuration, in `apache/conf/httpd.conf`

    <VirtualHost *:80>
      ServerName myapp.example.com
      DocumentRoot "/home/steve/myproject/web"
      DirectoryIndex index.php
      Alias /sf /$sf_symfony_data_dir/web/sf
      <Directory "/$sf_symfony_data_dir/web/sf">
        AllowOverride All
        Allow from All
      </Directory>
      <Directory "/home/steve/myproject/web">
        AllowOverride All
        Allow from All
      </Directory>
    </VirtualHost>

In the configuration in Listing 3-1, the `$sf_symfony_data_dir` placeholder 
must be replaced by the actual path. For example, for a PEAR installation in
*nix, you should type something like this:

        Alias /sf /usr/local/lib/php/data/symfony/web/sf

>**NOTE**
>The alias to the `web/sf/` directory is not mandatory. It allows Apache to 
find images, style sheets, and JavaScript files for the web debug toolbar, 
the admin generator, the default symfony pages, and the Ajax support. 
An alternative to this alias would be to create a symbolic link (symlink) or 
copy the `/path/to/symfony/data/web/sf/` directory to `myproject/web/sf/`.

>**TIP**
>If you have installed symfony via PEAR and can't find the symfony shared data
>directory, look in the PEAR `data_dir` which is listed in the PEAR config:
>
>    pear config-show

Restart Apache, and that's it. Your newly created application can now be called
and viewed through a standard web browser at the following URL:

    http://localhost/frontend_dev.php/

You should see a congratulations page similar to the one shown earlier in Figure 3-1.

>**SIDEBAR**
>URL Rewriting
>
>Symfony uses URL rewriting to display "smart URLs"--meaningful locations that display well on search engines and hide all the technical data from the user. You will learn more about this feature, called routing, in Chapter 9.
>
>If your version of Apache is not compiled with the `mod_rewrite` module, check that you have the `mod_rewrite` Dynamic Shared Object (DSO) installed and the following lines in your `httpd.conf`:
>
>    AddModule mod_rewrite.c
>    LoadModule rewrite_module modules/mod_rewrite.so
>
>For Internet Information Services (IIS), you will need `isapi/rewrite` installed and running. Check the symfony online cookbook for a detailed IIS installation guide.

### Configuring a Shared-Host Server

Setting up an application in a shared host is a little trickier, since the host usually has a specific directory layout that you can't change.

>**CAUTION**
>Doing tests and development directly in a shared host is not a good practice. One reason is that it makes the application visible even if it is not finished, revealing its internals and opening large security breaches. Another reason is that the performance of shared hosts is often not sufficient to browse your application with the debug tools on efficiently. So you should not start your development with a shared-host installation, but rather build your application locally and deploy it to the shared host when it is finished. Chapter 16 will tell you more about deployment techniques and tools.

Let's imagine that your shared host requires that the web folder is named `www/` instead of `web/`, and that it doesn't give you access to the `httpd.conf` file, but only to an `.htaccess` file in the web folder.

In a symfony project, every path to a directory is configurable. Chapter 19 will tell you more about it, but in the meantime, you can still rename the `web` directory to `www` and have the application take it into account by changing the configuration, as shown in Listing 3-2. These lines are to be added to the end of the `config/ProjectConfiguration.class.php` file.

Listing 3-2 - Changing the Default Directory Structure Settings, in `config/ProjectConfiguration.class.php`

    [php]
    class ProjectConfiguration extends sfProjectConfiguration
    {
       public function setup()
       {
         $this->setWebDir($this->getRootDir().'/www');
       }
    }

The project web root contains an `.htaccess` file by default. It is shown in Listing 3-3. Modify it as necessary to match your shared host requirements.

Listing 3-3 - Default `.htaccess` Configuration, Now in `myproject/www/.htaccess`

    Options +FollowSymLinks +ExecCGI

    <IfModule mod_rewrite.c>
      RewriteEngine On

      # uncomment the following line, if you are having trouble
      # getting no_script_name to work
      #RewriteBase /

      # we skip all files with .something
      #RewriteCond %{REQUEST_URI} \..+$
      #RewriteCond %{REQUEST_URI} !\.html$
      #RewriteRule .* - [L]

      # we check if the .html version is here (caching)
      RewriteRule ^$ index.html [QSA]
      RewriteRule ^([^.]+)$ $1.html [QSA]
      RewriteCond %{REQUEST_FILENAME} !-f

      # no, so we redirect to our front web controller
      RewriteRule ^(.*)$ index.php [QSA,L]
    </IfModule>

You should now be ready to browse your application. Check the congratulation page by requesting this URL:

    http://www.example.com/frontend_dev.php/

>**SIDEBAR**
>Other Server Configurations
>
>Symfony is compatible with other server configurations. You can, for instance, access a symfony application using an alias instead of a virtual host. You can also run a symfony application with an IIS server. There are as many techniques as there are configurations, and it is not the purpose of this book to explain them all.
>
>To find directions for a specific server configuration, refer to the symfony wiki ([http://trac.symfony-project.org/](http://trac.symfony-project.org/)), which contains many step-by-step tutorials.

Troubleshooting
---------------

If you encounter problems during the installation, try to make the best out of the errors or exceptions thrown to the shell or to the browser. They are often self-explanatory and may even contain links to specific resources on the Web about your issue.

### Typical Problems

If you are still having problems getting symfony running, check the following:

  * Some PHP installations come with both a PHP 4 and a PHP 5 command. In that case, the command line is probably `php5` instead of `php`, so try calling `php5 symfony` instead of the `symfony` command. You may also need to add `SetEnv PHP_VER 5` to your `.htaccess` configuration, or rename the scripts of the `web/` directory from `.php` to `.php5`. The error thrown by a PHP 4 command line trying to access symfony looks like this:

        Parse error, unexpected ',', expecting '(' in .../symfony.php on line 19.

  * The memory limit, defined in the `php.ini`, must be set to `32M` at least. The usual symptom for this problem is an error message when installing symfony via PEAR or using the command line.

        Allowed memory size of 8388608 bytes exhausted

  * The `zend.ze1_compatibility_mode` setting must be set to `off` in your `php.ini`. If it is not, trying to browse to one of the web scripts will produce an "implicit cloning" error:

        Strict Standards: Implicit cloning object of class 'sfTimer'because of 'zend.ze1_compatibility_mode'

  * The `log/` and `cache/` directories of your project must be writable by the web server. Attempts to browse a symfony application without these directory permissions will result in an exception:

        sfCacheException [message] Unable to write cache file"/usr/myproject/cache/frontend/prod/config/config_config_handlers.yml.php"

  * The include path of your system must include the path to the `php` command, and the include path of your `php.ini` must contain a path to PEAR (if you use PEAR).

  * Sometimes, there is more than one `php.ini` on a server's file system (for instance, if you use the WAMP package). Call `phpinfo()` to know the exact location of the `php.ini` file used by your application.

>**NOTE**
>Although it is not mandatory, it is strongly advised, for performance reasons, to set the `magic_quotes_gpc` and `register_globals` settings to `off` in your `php.ini`.

### Symfony Resources

You can check if your problem has already happened to someone else and find solutions in various places:

  * The symfony installation forum ([http://www.symfony-project.org/forum/](http://www.symfony-project.org/forum/)) is full of installation questions about a given platform, environment, configuration, host, and so on.
  * The archives of the users mailing-list ([http://groups.google.fr/group/symfony-users](http://groups.google.fr/group/symfony-users)) are also searchable. You may find similar experiences to your own there.
  * The symfony wiki ([http://trac.symfony-project.org/#Installingsymfony](http://trac.symfony-project.org/#Installingsymfony)) contains step-by-step tutorials, contributed by symfony users, about installation.

If you don't find any answer, try posing your question to the symfony community. You can post your query in the forum, the mailing list, or even drop to the `#symfony` IRC channel to get feedback from the most active members of the community.

Source Versioning
-----------------

Once the setup of the application is done, starting a source versioning (or version control) process is recommended. Source versioning keeps track of all modifications in the code, gives access to previous releases, facilitates patching, and allows for efficient team work. Symfony natively supports CVS, although Subversion ([http://subversion.tigris.org/](http://subversion.tigris.org/)) is recommended. The following examples show the commands for Subversion, and assume that you already have a Subversion server installed and that you wish to create a new repository for your project. For Windows users, a recommended Subversion client is TortoiseSVN ([http://tortoisesvn.tigris.org/](http://tortoisesvn.tigris.org/)). For more information about source versioning and the commands used here, consult the Subversion documentation.

The following example assumes that `$SVNREP_DIR` is defined as an environment variable. If you don't have it defined, you will need to substitute the actual location of the repository in place of `$SVNREP_DIR`.

So let's create the new repository for the `myproject` project:

    > svnadmin create $SVNREP_DIR/myproject

Then the base structure (layout) of the repository is created with the `trunk`, `tags`, and `branches` directories with this pretty long command:

    > svn mkdir -m "layout creation" file:///$SVNREP_DIR/myproject/trunk file:///$SVNREP_DIR/myproject/tags file:///$SVNREP_DIR/myproject/branches

This will be your first revision. Now you need to import the files of the project except the cache and log temporary files:

    > cd ~/myproject
    > rm -rf cache/*
    > rm -rf log/*
    > svn import -m "initial import" . file:///$SVNREP_DIR/myproject/trunk

Check the committed files by typing the following:

    > svn ls file:///$SVNREP_DIR/myproject/trunk/

That seems good. Now the SVN repository has the reference version (and the history) of all your project files. This means that the files of the actual `~/myproject/` directory need to refer to the repository. To do that, first rename the `myproject/` directory--you will erase it soon if everything works well--and do a checkout of the repository in a new directory:

    > cd ~
    > mv myproject myproject.origin
    > svn co file:///$SVNREP_DIR/myproject/trunk myproject
    > ls myproject

That's it. Now you can work on the files located in ~`/myproject/` and commit your modifications to the repository. Don't forget to do some cleanup and erase the `myproject.origin/` directory, which is now useless.

There is one remaining thing to set up. If you commit your working directory to the repository, you may copy some unwanted files, like the ones located in the `cache` and `log` directories of your project. So you need to specify an ignore list to SVN for this project. You also need to set full access to the `cache/` and `log/` directories again:

    > cd ~/myproject
    > chmod 777 cache
    > chmod 777 log
    > svn propedit svn:ignore log
    > svn propedit svn:ignore cache

The default text editor configured for SVN should launch. If this doesn't happen, make Subversion use your preferred editor by typing this:

    > export SVN_EDITOR=<name of editor>
    > svn propedit svn:ignore log
    > svn propedit svn:ignore cache

Now simply add all files from the subdirectories of `myproject/` that SVN should ignore when committing:

    *

Save and quit. You're finished.

Summary
-------

To test and play with symfony on your local server, your best option for installation is definitely the sandbox, which contains a preconfigured symfony environment.

For a real development or in a production server, opt for the PEAR installation or the SVN checkout. This will install the symfony libraries, and you still need to initialize a project and an application. The last step of the application setup is the server configuration, which can be done in many ways. Symfony works perfectly fine with a virtual host, and it is the recommended solution.

If you have any problems during installation, you will find many tutorials and answers to frequently asked questions on the symfony website. If necessary, you can submit your problem to the symfony community, and you will get a quick and effective answer.

Once your project is initiated, it is a good habit to start a version-control process.

Now that you are ready to use symfony, it is time to see how to build a basic web application.
