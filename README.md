ContractLib
===========

**ContractLib** is a simple-to-use PHP component for easily enforcing programming contracts throughout your PHP components.  These programming contracts can go a long way to helping you, and the users of your components, develop more robust code.

ContractLib is loosely inspired by Microsoft Research's work on the
[Code Contracts Library for .NET](http://research.microsoft.com/en-us/projects/contracts/userdoc.pdf)

System-Wide Installation
------------------------

ContractLib should be installed using the [PEAR Installer](http://pear.php.net). This installer is the PHP community's de-facto standard for installing PHP components.

    sudo pear channel-discover pear.phix-project.org
    sudo pear install --alldeps phix/ContractLib

As A Dependency On Your Component
---------------------------------

If you are creating a component that relies on ContractLib, please make sure that you add LicenseLib to your component's package.xml file:

```xml
<dependencies>
  <required>
    <package>
      <name>ContractLib</name>
      <channel>pear.phix-project.org</channel>
      <min>1.0.0</min>
      <max>1.999.9999</max>
    </package>
  </required>
</dependencies>
```

Usage
-----

The best documentation for ContractLib are the unit tests, which are shipped in the package.  You will find them installed into your PEAR repository, which on Linux systems is normally /usr/share/php/test.

You can find them online on GitHub: http://github.com/stuartherbert/ContractLib/

Development Environment
-----------------------

If you want to patch or enhance this component, you will need to create a suitable development environment, by [installing phix](http://phix-project.org#install).

You can then clone the git repository:

    # ContractLib
    git clone git@github.com:stuartherbert/ContractLib.git

Then, install a local copy of this component's dependencies to complete the development environment:

    # build vendor/ folder
    phing build-vendor

To make life easier for you, common tasks (such as running unit tests, generating code review analytics, and creating the PEAR package) have been automated using [phing](http://phing.info).  You'll find the automated steps inside the build.xml file that ships with the component.

Run the command 'phing' in the component's top-level folder to see the full list of available automated tasks.

License
-------

See LICENSE.txt for full license details.
