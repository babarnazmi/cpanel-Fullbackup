<?php
// Must include cPanel API
include "xmlapi.php";
 
//CONFIG SECTION
/*******************************************************/
// Credentials for cPanel account
$source_server_ip = "your_domain_or_IP"; // Server IP or domain name eg: 212.122.3.77 or cpanel.domain.tld
$cpanel_account = "userid"; // cPanel username
$cpanel_password = "password"; // cPanel password
// Credentials for FTP remote site
$ftphost = "ip_or_hostname_of_ftp"; // FTP host IP or domain name
$ftpacct = "userid"; // FTP account
$ftppass = "password"; // FTP password
$logs_dir = "/"; //FTP Remote Folder
$email_notify = 'your_email@domain.com'; // Email address for backup notification
$backupexpireindays=21; //3 weeks expire time in days, 21 days = 7*24*60
//END OF CONFIG SECTION
/*******************************************************/
//Do not edit below this line

$backupexpireindays=($backupexpireindays*24)*3600; //convert it to seconds, 24 hours * 60 minutes * 60 seconds

$xmlapi = new xmlapi($source_server_ip);
$xmlapi->password_auth($cpanel_account,$cpanel_password);
$xmlapi->set_port('2083');
 
// Delete any other backup with filetime greater than expire time, before create new backup
$conn_id = ftp_connect($ftphost);
$login_result = ftp_login($conn_id, $ftpacct, $ftppass);

ftp_chdir($conn_id, $logs_dir);
$files = ftp_nlist($conn_id, ".");
foreach ($files as $filename) {
        $fileCreationTime = ftp_mdtm($conn_id, $filename);
        //$date = date("F j, Y, g:i a", ftp_mdtm($conn_id, $filename));
        //print "<br>Timestamp of '$filename': $date";
        $fileAge=time();
        $fileAge=$fileAge-$fileCreationTime;
        if ($fileAge > $backupexpireindays) { // Is the file older than the given time span?
               //echo "<br>The file $filename is older than Expire time :$expiretime ...Deleting\n";
               ftp_delete($conn_id, $filename);
               //echo "<br>Deleted<br><br>";
               }
}

ftp_close($conn_id);
 
$api_args = array(
                           'passiveftp',
                           $ftphost,
                           $ftpacct,
                           $ftppass,
                           $email_notify,
                            21,
                            '/'
                         );
 
$xmlapi->set_output('json');
print $xmlapi->api1_query($cpanel_account,'Fileman','fullbackup',$api_args);
 
?>
