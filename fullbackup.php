<!--?php
// Must include cPanel API
include "xmlapi.php";
 
// Credentials for cPanel account
$source_server_ip = "your_domain_or_IP"; // Server IP or domain name eg: 212.122.3.77 or cpanel.domain.tld
$cpanel_account = "userid"; // cPanel username
$cpanel_password = "password"; // cPanel password
 
// Credentials for FTP remote site
$ftphost = "ip_or_hostname_of_ftp"; // FTP host IP or domain name
$ftpacct = "userid"; // FTP account
$ftppass = "password"; // FTP password
$email_notify = 'your_email@domain.com'; // Email address for backup notification
 
$xmlapi = new xmlapi($source_server_ip);
$xmlapi--->password_auth($cpanel_account,$cpanel_password);
$xmlapi->set_port('2083');
 
// Delete any other backup before create new backup
$conn_id = ftp_connect($ftphost);
$login_result = ftp_login($conn_id, $ftpacct, $ftppass);
$logs_dir = "/";
 
ftp_chdir($conn_id, $logs_dir);
$files = ftp_nlist($conn_id, ".");
foreach ($files as $file){
    ftp_delete($conn_id, $file);
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
