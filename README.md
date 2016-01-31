# rootOOP

This project is designed to create the safest PHP script possible by moving all vulnerable functions to one file and calling them remotely. This automatically creates a MVC environment. Think of rootOOP as an automatic Model. A wiki will be coming soon.

**This project is still in development.**

## How it does/will work?

The objective of this project is to copy this folder onto a development area and configure the `config.php` file to quickly create a simple MySQL database for Users and other projects for custom development.

## How do I use it?

The best way to use this is currently by reading the script. It is now very small and can be easily understood if you have a grasp of OOP. (If you don't know what OOP means, wait for the wiki to try this.)

### Sample Codes for rootOOP:

    $MySQL->insert('table', array('name' => 'value', 'name2' => 'value2' ... ));
    $User->authenticate(array('username' => 'posted_user', 'password' => 'posted_password'));
  
rootOOP will automatically filter and validate posted (raw user) input so you don't have to spend your time worring about security and spend your time developing software. All security issues should be dealt with inside the `includes/class/` folder.

## Development of rootOOP:

This is being currently developed for system administration (creating clients, managing clients, sending invoices, managing income, etc...), but can be branched to do many other things. This is the reason why rootOOP is so raw. It is intended to be lightweight and efficient. If you'd like rootOOP to manage your site, this should be developed as a plugin.


## Help

I am a busy person who is in need of efficient code to manage my businesses. If you are out there and are willing to help me develop this code who can also envision this project (more details of the project's vision will be created in the wiki soon), feel free to contact me.
