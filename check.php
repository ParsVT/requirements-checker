<?php
/******************************************
 * Program: ParsVT CRM Requirements Checker
 * Developer: Hamid Rabiei, Mohammad Hadadpour
 * Release: 1402-03-05
 * Update: 1402-09-29
 ******************************************/
ini_set("display_errors", "Off");
error_reporting(0);
session_start();
if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "reset") {
unset($_SESSION["hostname"]);
unset($_SESSION["database"]);
unset($_SESSION["username"]);
unset($_SESSION["password"]);
$_SESSION["logout"] = true;
header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
exit();
} elseif (!$_SESSION["logout"]) {
if (file_exists("config.inc.php")) {
require_once "config.inc.php";
if (isset($dbconfig) && !empty($dbconfig)) {
$_SESSION["hostname"] = $dbconfig["db_server"];
$_SESSION["database"] = $dbconfig["db_name"];
$_SESSION["username"] = $dbconfig["db_username"];
$_SESSION["password"] = $dbconfig["db_password"];
}
}
}
class ParsVT_Check_Requirements{public static $disaplayCapacity=true;public static $latest_ioncube="13.0.2";private static $tmpPath;public static $yes='<svg viewBox="0 0 448 512" class="check"><use xlink:href="#icon-check"></use></svg>';public static $no='<svg viewBox="0 0 384 512" class="xmark"><use xlink:href="#icon-xmark"></use></svg>';private static $levelNames=[E_ERROR=>"E_ERROR",E_WARNING=>"E_WARNING",E_PARSE=>"E_PARSE",E_NOTICE=>"E_NOTICE",E_STRICT=>"E_STRICT",E_CORE_ERROR=>"E_CORE_ERROR",E_CORE_WARNING=>"E_CORE_WARNING",E_COMPILE_ERROR=>"E_COMPILE_ERROR",E_COMPILE_WARNING=>"E_COMPILE_WARNING",E_USER_ERROR=>"E_USER_ERROR",E_USER_WARNING=>"E_USER_WARNING",E_USER_NOTICE=>"E_USER_NOTICE",E_RECOVERABLE_ERROR=>"E_RECOVERABLE_ERROR",E_DEPRECATED=>"E_DEPRECATED",E_USER_DEPRECATED=>"E_USER_DEPRECATED",];public static $library=["IMAP"=>["type"=>"f","name"=>"imap_open","mandatory"=>true,],"Zlib"=>["type"=>"f","name"=>"gzinflate","mandatory"=>true,],"OpenSSL"=>["type"=>"e","name"=>"openssl","mandatory"=>true,],"cURL"=>["type"=>"e","name"=>"curl","mandatory"=>true,],"GD"=>["type"=>"e","name"=>"gd","mandatory"=>true,],"XML"=>["type"=>"e","name"=>"xml","mandatory"=>true,],"JSON"=>["type"=>"e","name"=>"json","mandatory"=>true,],"Session"=>["type"=>"e","name"=>"session","mandatory"=>true,],"DOM"=>["type"=>"e","name"=>"dom","mandatory"=>true,],"Zip"=>["type"=>"e","name"=>"zip","mandatory"=>true,],"Multibyte"=>["type"=>"e","name"=>"mbstring","mandatory"=>true,],"SOAP"=>["type"=>"e","name"=>"soap","mandatory"=>true,],"Fileinfo"=>["type"=>"e","name"=>"fileinfo","mandatory"=>true,],"iconv"=>["type"=>"e","name"=>"iconv","mandatory"=>true,],"Exif"=>["type"=>"f","name"=>"exif_read_data","mandatory"=>false,],"LDAP"=>["type"=>"f","name"=>"ldap_connect","mandatory"=>false,],"Sockets"=>["type"=>"f","name"=>"fsockopen","mandatory"=>false,],];private static function getStabilitIniConf(){$time_limit=600;if(file_exists("config.inc.php")){require_once "config.inc.php";if(isset($site_URL)){$time_limit=60;}}$directiveValues=["PHP"=>["recommended"=>"5.4.x, 5.5.x, 5.6.x, 7.0.x, 7.1.x, 7.2.x, 7.3.x, 7.4.x","help"=>"LBL_PHP_HELP_TEXT","fn"=>"validatePhp","max"=>"7.2",],"ionCube"=>["recommended"=>"13.x.x","help"=>"LBL_IONCUBE_HELP_TEXT","fn"=>"validateIonCube",],"Installed version"=>["recommended"=>false,"help"=>"LBL_INSTALLED_VERSION_HELP_TEXT","fn"=>"validateIonCubeInstalledVersion",],"error_reporting"=>["recommended"=>"E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT","help"=>"LBL_ERROR_REPORTING_HELP_TEXT","fn"=>"validateErrorReporting",],"output_buffering"=>["recommended"=>"On","help"=>"LBL_OUTPUT_BUFFERING_HELP_TEXT","fn"=>"validateOnOffInt",],"max_execution_time"=>["recommended"=>$time_limit,"help"=>"LBL_MAX_EXECUTION_TIME_HELP_TEXT","fn"=>"validateGreater",],"max_input_time"=>["recommended"=>$time_limit,"help"=>"LBL_MAX_INPUT_TIME_HELP_TEXT","fn"=>"validateGreater",],"default_socket_timeout"=>["recommended"=>$time_limit,"help"=>"LBL_DEFAULT_SOCKET_TIMEOUT_HELP_TEXT","fn"=>"validateGreater",],"memory_limit"=>["recommended"=>"512 MB","help"=>"LBL_MEMORY_LIMIT_HELP_TEXT","fn"=>"validateGreaterMb",],"log_errors"=>["recommended"=>"Off","help"=>"LBL_LOG_ERRORS_HELP_TEXT","fn"=>"validateOnOff",],"file_uploads"=>["recommended"=>"On","help"=>"LBL_FILE_UPLOADS_HELP_TEXT","fn"=>"validateOnOff",],"short_open_tag"=>["recommended"=>"On","help"=>"LBL_SHORT_OPEN_TAG_HELP_TEXT","fn"=>"validateOnOff",],"post_max_size"=>["recommended"=>"128 MB","help"=>"LBL_POST_MAX_SIZE_HELP_TEXT","fn"=>"validateGreaterMb",],"upload_max_filesize"=>["recommended"=>"128 MB","help"=>"LBL_UPLOAD_MAX_FILESIZE_HELP_TEXT","fn"=>"validateGreaterMb",],"max_input_vars"=>["recommended"=>"10000","help"=>"LBL_MAX_INPUT_VARS_HELP_TEXT","fn"=>"validateGreater",],"zlib.output_compression"=>["recommended"=>"Off","help"=>"LBL_ZLIB_OUTPUT_COMPRESSION_HELP_TEXT","fn"=>"validateOnOff",],"session.auto_start"=>["recommended"=>"Off","help"=>"LBL_SESSION_AUTO_START_HELP_TEXT","fn"=>"validateOnOff",],"session.gc_maxlifetime"=>["recommended"=>"21600","help"=>"LBL_SESSION_GC_MAXLIFETIME_HELP_TEXT","fn"=>"validateGreater",],"session.gc_divisor"=>["recommended"=>"1000","help"=>"LBL_SESSION_GC_DIVISOR_HELP_TEXT","fn"=>"validateGreater",],"session.gc_probability"=>["recommended"=>"1","help"=>"LBL_SESSION_GC_PROBABILITY_HELP_TEXT","fn"=>"validateEqual",],"mbstring.func_overload"=>["recommended"=>"Off","help"=>"LBL_MBSTRING_FUNC_OVERLOAD_HELP_TEXT","fn"=>"validateOnOff",],"date.timezone"=>["recommended"=>"Asia/Tehran","fn"=>"validateTimezone",],"allow_url_fopen"=>["recommended"=>"On","help"=>"LBL_ALLOW_URL_FOPEN_HELP_TEXT","fn"=>"validateOnOff",],"disable_functions"=>["recommended"=>"","help"=>"LBL_DISABLE_FUNCTION_HELP_TEXT","fn"=>"validateDisableFunctions",],];if(extension_loaded("suhosin")){$directiveValues["suhosin.session.encrypt"]=["recommended"=>"Off","fn"=>"validateOnOff",];$directiveValues["suhosin.request.max_vars"]=["recommended"=>"5000","fn"=>"validateGreater",];$directiveValues["suhosin.post.max_vars"]=["recommended"=>"5000","fn"=>"validateGreater",];$directiveValues["suhosin.post.max_value_length"]=["recommended"=>"1500000","fn"=>"validateGreater",];}return $directiveValues;}public static function getLibrary(){foreach(static::$library as $k=>$v){if($v["type"]=="f"){$status=function_exists($v["name"]);}elseif($v["type"]=="e"){$status=extension_loaded($v["name"]);}static::$library[$k]["status"]=$status?"Yes":"No";}return static::$library;}public static function getStabilityConf($onlyError=false){$ini=static::getPhpIniConf();$conf=static::getStabilitIniConf();$cliConf=false;foreach($conf as $key=>&$value){if($cliConf){$value["cli"]=$value["current"]=$cliConf[$key];if(isset($value["fn"])){$value=call_user_func_array([__CLASS__,$value["fn"]],[$value,true]);$value["cli"]=$value["current"];}}$value["current"]=$ini[$key];if(isset($value["fn"])){$value=call_user_func_array([__CLASS__,$value["fn"]],[$value,false]);unset($value["fn"]);}}if($onlyError){foreach($conf as $key=>$value){if(empty($value["incorrect"])){unset($conf[$key]);}}}return $conf;}public static function getSecurityConf($onlyError=false){$directiveValues=["display_errors"=>["recommended"=>"On","help"=>"LBL_DISPLAY_ERRORS_HELP_TEXT","current"=>static::getFlag(ini_get("display_errors")),"status"=>(ini_get("display_errors")!=1||stripos(ini_get("display_errors"),"Off")!==false),],"HTTPS"=>["recommended"=>"On","help"=>"LBL_HTTPS_HELP_TEXT",],"session.use_strict_mode"=>["recommended"=>"On","help"=>"LBL_SESSION_USE_STRICT_MODE_HELP_TEXT","current"=>static::getFlag(ini_get("session.use_strict_mode")),"status"=>(ini_get("session.use_strict_mode")!=1||stripos(ini_get("session.use_strict_mode"),"Off")!==false),],"session.use_trans_sid"=>["recommended"=>"Off","help"=>"LBL_SESSION_USE_TRANS_SID_HELP_TEXT","current"=>static::getFlag(ini_get("session.use_trans_sid")),"status"=>(ini_get("session.use_trans_sid")==1||stripos(ini_get("session.use_trans_sid"),"On")!==false),],"session.cookie_httponly"=>["recommended"=>"On","help"=>"LBL_SESSION_COOKIE_HTTPONLY_HELP_TEXT","current"=>static::getFlag(ini_get("session.cookie_httponly")),"status"=>(ini_get("session.cookie_httponly")!=1||stripos(ini_get("session.cookie_httponly"),"Off")!==false),],"session.use_only_cookies"=>["recommended"=>"On","help"=>"LBL_SESSION_USE_ONLY_COOKIES_HELP_TEXT","current"=>static::getFlag(ini_get("session.use_only_cookies")),"status"=>(ini_get("session.use_only_cookies")!=1||stripos(ini_get("session.use_only_cookies"),"Off")!==false),],"expose_php"=>["recommended"=>"Off","help"=>"LBL_EXPOSE_PHP_HELP_TEXT","current"=>static::getFlag(ini_get("expose_php")),"status"=>(ini_get("expose_php")==1||stripos(ini_get("expose_php"),"On")!==false),],"Header: X-Frame-Options"=>["recommended"=>"SAMEORIGIN","help"=>"LBL_HEADER_X_FRAME_OPTIONS_HELP_TEXT","current"=>"?",],"Header: X-XSS-Protection"=>["recommended"=>"1; mode=block","help"=>"LBL_HEADER_X_XSS_PROTECTION_HELP_TEXT","current"=>"?",],"Header: X-Content-Type-Options"=>["recommended"=>"nosniff","help"=>"LBL_HEADER_X_CONTENT_TYPE_OPTIONS_HELP_TEXT","current"=>"?",],"Header: X-Robots-Tag"=>["recommended"=>"none","help"=>"LBL_HEADER_X_ROBOTS_TAG_HELP_TEXT","current"=>"?",],"Header: X-Permitted-Cross-Domain-Policies"=>["recommended"=>"none","help"=>"LBL_HEADER_X_PERMITTED_CROSS_DOMAIN_POLICIES_HELP_TEXT","current"=>"?",],"Header: X-Powered-By"=>["recommended"=>"","help"=>"LBL_HEADER_X_POWERED_BY_HELP_TEXT","current"=>"?",],"Header: Server"=>["recommended"=>"","help"=>"LBL_HEADER_SERVER_HELP_TEXT","current"=>"?",],"Header: Expect-CT"=>["recommended"=>"enforce; max-age=3600","help"=>"LBL_HEADER_EXPECT_CT_HELP_TEXT","current"=>"?",],"Header: Referrer-Policy"=>["recommended"=>"same-origin","help"=>"LBL_HEADER_REFERRER_POLICY_HELP_TEXT","current"=>"?",],"Header: Strict-Transport-Security"=>["recommended"=>"max-age=31536000; includeSubDomains; preload","help"=>"LBL_HEADER_STRICT_TRANSPORT_SECURITY_HELP_TEXT","current"=>"?",],];if(isset($_SERVER["HTTPS"])&&$_SERVER["HTTPS"]==="on"){$directiveValues["HTTPS"]["status"]=false;$directiveValues["HTTPS"]["current"]=static::getFlag(true);$directiveValues["session.cookie_secure"]=["recommended"=>"On"];if(ini_get("session.cookie_secure")!="1"&&stripos(ini_get("session.cookie_secure"),"On")!==false){$directiveValues["session.cookie_secure"]["status"]=true;$directiveValues["session.cookie_secure"]["current"]=static::getFlag(false);}else{$directiveValues["session.cookie_secure"]["current"]=static::getFlag(true);}}else{$directiveValues["HTTPS"]["status"]=true;$directiveValues["HTTPS"]["current"]=static::getFlag(false);if(ini_get("session.cookie_secure")=="1"||stripos(ini_get("session.cookie_secure"),"On")===false){$directiveValues["session.cookie_secure"]["current"]=static::getFlag(true);$directiveValues["session.cookie_secure"]["recommended"]=static::getFlag(false);$directiveValues["session.cookie_secure"]["status"]=true;}}stream_context_set_default(["ssl"=>["verify_peer"=>false,"verify_peer_name"=>false,]]);$prev=stream_context_get_options(stream_context_get_default());stream_context_set_default(["http"=>["timeout"=>3]]);$requestUrl=(isset($_SERVER["HTTPS"])&&$_SERVER["HTTPS"]==="on"?"https":"http")."://".$_SERVER["HTTP_HOST"].$_SERVER["SCRIPT_NAME"];$rqheaders=get_headers($requestUrl,1);if($rqheaders){$headers=array_change_key_case($rqheaders,CASE_UPPER);if(stripos($headers[0],"200")===false){$headers=[];}}stream_context_set_default($prev);if($headers){$directiveValues["Header: X-Frame-Options"]["status"]=strtolower($headers["X-FRAME-OPTIONS"])!=="sameorigin";$directiveValues["Header: X-Frame-Options"]["current"]=$headers["X-FRAME-OPTIONS"];$directiveValues["Header: X-XSS-Protection"]["status"]=strtolower($headers["X-XSS-PROTECTION"])!=="1; mode=block";$directiveValues["Header: X-XSS-Protection"]["current"]=$headers["X-XSS-PROTECTION"];$directiveValues["Header: X-Content-Type-Options"]["status"]=strtolower($headers["X-CONTENT-TYPE-OPTIONS"])!=="nosniff";$directiveValues["Header: X-Content-Type-Options"]["current"]=$headers["X-CONTENT-TYPE-OPTIONS"];$directiveValues["Header: X-Powered-By"]["status"]=!empty($headers["X-POWERED-BY"]);$directiveValues["Header: X-Powered-By"]["current"]=$headers["X-POWERED-BY"];$directiveValues["Header: X-Robots-Tag"]["status"]=strtolower($headers["X-ROBOTS-TAG"])!=="none";$directiveValues["Header: X-Robots-Tag"]["current"]=$headers["X-ROBOTS-TAG"];$directiveValues["Header: X-Permitted-Cross-Domain-Policies"]["status"]=strtolower($headers["X-PERMITTED-CROSS-DOMAIN-POLICIES"])!=="none";$directiveValues["Header: X-Permitted-Cross-Domain-Policies"]["current"]=$headers["X-PERMITTED-CROSS-DOMAIN-POLICIES"];$directiveValues["Header: X-Powered-By"]["status"]=!empty($headers["X-POWERED-BY"]);$directiveValues["Header: X-Powered-By"]["current"]=$headers["X-POWERED-BY"];$directiveValues["Header: Server"]["status"]=!empty($headers["SERVER"]);$directiveValues["Header: Server"]["current"]=$headers["SERVER"];$directiveValues["Header: Referrer-Policy"]["status"]=strtolower($headers["REFERRER-POLICY"])!=="no-referrer";$directiveValues["Header: Referrer-Policy"]["current"]=$headers["REFERRER-POLICY"];$directiveValues["Header: Expect-CT"]["status"]=strtolower($headers["EXPECT-CT"])!=="enforce; max-age=3600";$directiveValues["Header: Expect-CT"]["current"]=$headers["EXPECT-CT"];$directiveValues["Header: Strict-Transport-Security"]["status"]=strtolower($headers["STRICT-TRANSPORT-SECURITY"])!=="max-age=31536000; includesubdomains; preload";$directiveValues["Header: Strict-Transport-Security"]["current"]=$headers["STRICT-TRANSPORT-SECURITY"];}if(!isset($headers["X-ROBOTS-TAG"])){unset($directiveValues["Header: X-Robots-Tag"]);}if(!isset($headers["X-POWERED-BY"])){unset($directiveValues["Header: X-Powered-By"]);}if(!isset($headers["X-CONTENT-TYPE-OPTIONS"])){unset($directiveValues["Header: X-Content-Type-Options"]);}if(!isset($headers["X-XSS-PROTECTION"])){unset($directiveValues["Header: X-XSS-Protection"]);}if(!isset($headers["X-FRAME-OPTIONS"])){unset($directiveValues["Header: X-Frame-Options"]);}if(!isset($headers["X-PERMITTED-CROSS-DOMAIN-POLICIES"])){unset($directiveValues["Header: X-Permitted-Cross-Domain-Policies"]);}if(!isset($headers["X-POWERED-BY"])){unset($directiveValues["Header: X-Powered-By"]);}if(!isset($headers["SERVER"])){unset($directiveValues["Header: Server"]);}if(!isset($headers["REFERRER-POLICY"])){unset($directiveValues["Header: Referrer-Policy"]);}if(!isset($headers["EXPECT-CT"])){unset($directiveValues["Header: Expect-CT"]);}if(!isset($headers["STRICT-TRANSPORT-SECURITY"])){unset($directiveValues["Header: Strict-Transport-Security"]);}if($onlyError){foreach($directiveValues as $key=>$value){if(empty($value["status"])){unset($directiveValues[$key]);}}}return $directiveValues;}public static function GetDBStatus($con){$sql='SELECT default_collation_name, default_character_set_name FROM information_schema.SCHEMATA WHERE schema_name = "'.$_SESSION["database"].'"';$result=mysqli_query($con,$sql);if(mysqli_num_rows($result)>0){while($row=mysqli_fetch_array($result,MYSQLI_ASSOC)){$default_collation_name=$row["default_collation_name"];$default_character_set_name=$row["default_character_set_name"];}}else{return[true,"Unknown"];}if(in_array($default_character_set_name,array('utf8','utf8mb3','utf8mb4'))&&in_array($default_collation_name,array('utf8_general_ci','utf8_unicode_ci','utf8_persian_ci','utf8mb3_general_ci','utf8mb3_unicode_ci','utf8mb3_persian_ci','utf8mb4_general_ci','utf8mb4_unicode_ci','utf8mb4_persian_ci'))){return[false,$default_collation_name];}elseif($default_character_set_name=="utf8mb4"){return[false,$default_collation_name];}else{return[true,$default_collation_name];}}public static function getDbConf($con,$onlyError=false){$dbstatus=self::GetDBStatus($con);$directiveValues=["Database engine"=>["recommended"=>false,"current"=>"mysql","help"=>"LBL_DB_DRIVER_HELP_TEXT",],"Engine version"=>["recommended"=>false,"current"=>$con->server_info,],"Client version"=>["recommended"=>false,"current"=>$con->client_info,],"Connection status"=>["recommended"=>false,"current"=>$con->host_info,],"Server information"=>["recommended"=>false,"current"=>$con->stat,],"Database collation"=>["recommended"=>"utf8mb4_general_ci","current"=>$dbstatus[1],"status"=>$dbstatus[0],],];if(empty($con->stat)){unset($directiveValues["Server information"]);}if(!in_array($con->dataProvider,explode(",",$directiveValues["LBL_DB_DRIVER"]["recommended"]))){$directiveValues["wait_timeout"]["status"]=true;}$directiveValues=array_merge($directiveValues,["innodb_lock_wait_timeout"=>["recommended"=>"600","help"=>"LBL_INNODB_LOCK_WAIT_TIMEOUT_HELP_TEXT",],"wait_timeout"=>["recommended"=>"600","help"=>"LBL_WAIT_TIMEOUT_HELP_TEXT",],"interactive_timeout"=>["recommended"=>"600","help"=>"LBL_INTERACTIVE_TIMEOUT_HELP_TEXT",],"sql_mode"=>["recommended"=>"","help"=>"LBL_SQL_MODE_HELP_TEXT",],"log_bin_trust_function_creators"=>["recommended"=>"On","help"=>"LBL_LOG_BIN_TRUST_FUNCTION_CREATORS_HELP_TEXT",],"max_allowed_packet"=>["recommended"=>"16 MB","help"=>"LBL_MAX_ALLOWED_PACKET_HELP_TEXT",],"log_error"=>["recommended"=>false,],"max_connections"=>["recommended"=>false,],"thread_cache_size"=>["recommended"=>false,],"key_buffer_size"=>["recommended"=>false,],"query_cache_size"=>["recommended"=>false,],"tmp_table_size"=>["recommended"=>false,],"max_heap_table_size"=>["recommended"=>false,],"innodb_file_per_table"=>["recommended"=>"On","help"=>"LBL_INNODB_FILE_PER_TABLE_HELP_TEXT",],"innodb_stats_on_metadata"=>["recommended"=>"Off","help"=>"LBL_INNODB_STATS_ON_METADATA_HELP_TEXT",],"innodb_buffer_pool_instances"=>["recommended"=>false,],"innodb_buffer_pool_size"=>["recommended"=>false,],"innodb_log_file_size"=>["recommended"=>false,],"innodb_io_capacity_max"=>["recommended"=>false,],]);$sql="SHOW VARIABLES";$result=mysqli_query($con,$sql);if(mysqli_num_rows($result)>0){while($row=mysqli_fetch_array($result,MYSQLI_ASSOC)){$conf[$row["Variable_name"]]=$row["Value"];}}$directiveValues["max_allowed_packet"]["current"]=self::showBytes($conf["max_allowed_packet"]);$directiveValues["key_buffer_size"]["current"]=self::showBytes($conf["key_buffer_size"]);$directiveValues["query_cache_size"]["current"]=self::showBytes($conf["query_cache_size"]);$directiveValues["tmp_table_size"]["current"]=self::showBytes($conf["tmp_table_size"]);$directiveValues["max_heap_table_size"]["current"]=self::showBytes($conf["max_heap_table_size"]);$directiveValues["innodb_buffer_pool_size"]["current"]=self::showBytes($conf["innodb_buffer_pool_size"]);$directiveValues["innodb_log_file_size"]["current"]=self::showBytes($conf["innodb_log_file_size"]);$directiveValues["innodb_lock_wait_timeout"]["current"]=$conf["innodb_lock_wait_timeout"];$directiveValues["wait_timeout"]["current"]=$conf["wait_timeout"];$directiveValues["interactive_timeout"]["current"]=$conf["interactive_timeout"];$directiveValues["sql_mode"]["current"]=$conf["sql_mode"];$query="select DISTINCT * from information_schema . user_privileges where  IS_GRANTABLE = 'YES' and (PRIVILEGE_TYPE like 'SUPER' or PRIVILEGE_TYPE like 'CREATE ROUTINE' or PRIVILEGE_TYPE like 'TRIGGER') and GRANTEE like '%".$_SESSION["username"]."%'";$result2=mysqli_query($con,$query);if(mysqli_num_rows($result2)>0){$log_bin_trust_function_creators_current="On";$log_bin_trust_function_creators_status=false;}else{$log_bin_trust_function_creators_current="Off";$log_bin_trust_function_creators_status=true;}if(isset($conf["log_bin_trust_function_creators"])){$directiveValues["log_bin_trust_function_creators"]["current"]=ucfirst(strtolower($conf["log_bin_trust_function_creators"]));$status=ucfirst(strtolower($conf["log_bin_trust_function_creators"]))==="On"?false:true;$directiveValues["log_bin_trust_function_creators"]["status"]=$status===false?false:$log_bin_trust_function_creators_status;}else{$directiveValues["log_bin_trust_function_creators"]["current"]=$log_bin_trust_function_creators_current;$directiveValues["log_bin_trust_function_creators"]["status"]=$log_bin_trust_function_creators_status;}$directiveValues["log_error"]["current"]=$conf["log_error"];$directiveValues["max_connections"]["current"]=$conf["max_connections"];$directiveValues["thread_cache_size"]["current"]=$conf["thread_cache_size"];$directiveValues["innodb_buffer_pool_instances"]["current"]=$conf["innodb_buffer_pool_instances"];$directiveValues["innodb_io_capacity_max"]["current"]=$conf["innodb_io_capacity_max"];$directiveValues["innodb_file_per_table"]["current"]=$conf["innodb_file_per_table"];$directiveValues["innodb_stats_on_metadata"]["current"]=$conf["innodb_stats_on_metadata"];if(isset($conf["tx_isolation"])){$directiveValues["tx_isolation"]=["current"=>$conf["tx_isolation"],"recommended"=>false];}if(isset($conf["transaction_isolation"])){$directiveValues["transaction_isolation"]=["current"=>$conf["transaction_isolation"],"recommended"=>false];}if($conf["max_allowed_packet"]<16777216){$directiveValues["max_allowed_packet"]["status"]=true;}if($conf["innodb_lock_wait_timeout"]<600){$directiveValues["innodb_lock_wait_timeout"]["status"]=true;}if($conf["wait_timeout"]<600){$directiveValues["wait_timeout"]["status"]=true;}if($conf["interactive_timeout"]<600){$directiveValues["interactive_timeout"]["status"]=true;}if(!empty($conf["sql_mode"])&&(strpos($conf["sql_mode"],"STRICT_TRANS_TABLE")!==false||strpos($conf["sql_mode"],"ONLY_FULL_GROUP_BY")!==false)){$directiveValues["sql_mode"]["status"]=true;}if($onlyError){foreach($directiveValues as $key=>$value){if(empty($value["status"])){unset($directiveValues[$key]);}}}return $directiveValues;}public static function getSystemInfo(){$root_directory=getcwd();$ini=static::getPhpIniConf();$dir=str_replace("\\","/",$root_directory);$params=["Local directory"=>$root_directory];if(self::$disaplayCapacity){$params["Capacity"]="Total".": ".(!function_exists("disk_total_space")||!function_exists("disk_free_space")||self::exec_disabled("disk_total_space")||self::exec_disabled("disk_free_space")?"Unknown":self::showBytes(disk_total_space($dir))).", "."Used".": ".(!function_exists("disk_total_space")||!function_exists("disk_free_space")||self::exec_disabled("disk_total_space")||self::exec_disabled("disk_free_space")?"Unknown":self::showBytes(disk_total_space($dir)-disk_free_space($dir))).", "."Free".": ".(!function_exists("disk_free_space")||self::exec_disabled("disk_free_space")?"Unknown":self::showBytes(disk_free_space($dir)));}$params["Operating system"]=function_exists("php_uname")?php_uname():"Unknown";if(isset($ini["SAPI"])){$params["Server API"]=$ini["SAPI"];}if(isset($ini["LOG_FILE"])){$params["Logs"]=$ini["LOG_FILE"];}if(isset($ini["INI_FILE"])){$params["PHP configuration"]=$ini["INI_FILE"];}return $params;}public static function getPhpIniConf(){$iniAll=@ini_get_all();$values=[];foreach(static::getStabilitIniConf()as $key=>$value){if(isset($iniAll[$key])){$values[$key]=$iniAll[$key]["local_value"];}}$values["PHP"]=PHP_VERSION;$values["SAPI"]=PHP_SAPI;$values["INI_FILE"]=!function_exists("php_ini_loaded_file")||self::exec_disabled("php_ini_loaded_file")?"Unable to show for security reasons":@php_ini_loaded_file();$values["INI_FILES"]=!function_exists("php_ini_scanned_files")||self::exec_disabled("php_ini_scanned_files")?"Unable to show for security reasons":@php_ini_scanned_files();$values["LOG_FILE"]=$iniAll["error_log"]["local_value"];return $values;}public static function validateGreater($row,$isCli){if((int) $row["current"]>0&&(int) $row["current"]<$row["recommended"]){$row["incorrect"]=true;}return $row;}public static function validateGreaterMb($row,$isCli){if(self::parseBytes($row["current"])<self::parseBytes($row["recommended"])){$row["incorrect"]=true;}$row["current"]=self::showBytes($row["current"]);$row["recommended"]=self::showBytes(self::parseBytes($row["recommended"]));return $row;}public static function validateOnOff($row,$isCli){static $map=["on"=>true,"true"=>true,"off"=>false,"false"=>false];$current=isset($map[strtolower($row["current"])])?$map[strtolower($row["current"])]:(bool) $row["current"];if($current!==($row["recommended"]==="On")){$row["incorrect"]=true;}if(is_bool($current)){$row["current"]=$current?"On":"Off";}else{$row["current"]=static::getFlag($row["current"]);}return $row;}public static function validateOnOffInt($row,$isCli){if(!$isCli&&strtolower($row["current"])!=="on"){$row["incorrect"]=true;}return $row;}public static function validateEqual($row,$isCli){if((int) $row["current"]!==(int) $row["recommended"]){$row["incorrect"]=true;}return $row;}public static function validatePhp($row,$isCli){try{$newest=static::getNewestPhpVersion($row["max"]);}catch(Exception $exc){$newest=false;}if($newest){$row["recommended"]=$newest;}if(version_compare($row["current"],str_replace("x",0,$row["recommended"]),"<")){$row["incorrect"]=true;}return $row;}public static function GetIonCubeLoaderVersion(){if(function_exists("ioncube_loader_iversion")){$version=ioncube_loader_iversion();$version=sprintf("%d.%d.%d",$version/10000,($version/100)%100,$version%100);return $version;}return "Not found!";}public static function validateIonCubeInstalledVersion($row,$isCli){if(version_compare(PHP_VERSION,"5.6.0")>=0&&version_compare(PHP_VERSION,"8.0.0")<0){$iconcube_version="5.6";}elseif(version_compare(PHP_VERSION,"7.4.0")>=0){$iconcube_version="7.4";}elseif(version_compare(PHP_VERSION,"5.6.0")<0){$iconcube_version="5.4";}$row["current"]=$iconcube_version;return $row;}public static function validateIonCube($row,$isCli){$version=self::GetIonCubeLoaderVersion();$row["current"]=$version;$row["recommended"]=self::$latest_ioncube;if(version_compare($version,"12.0.0")<=0){$row["incorrect"]=true;}return $row;}public static function validateTimezone($row,$isCli){if(ini_get("date.timezone")){$default_timezone=ini_get("date.timezone");}else{$default_timezone="Asia/Tehran";}$row["current"]=$default_timezone;try{new DateTimeZone($row["current"]);}catch(Exception $e){$row["current"]="Invalid TimeZone ".$row["current"];$row["incorrect"]=true;}return $row;}public static function error2string($value){$levels=[];if(($value&E_ALL)==E_ALL){$levels[]="E_ALL";$value&=~E_ALL;}foreach(static::$levelNames as $level=>$name){if(($value&$level)==$level){$levels[]=$name;}}return $levels;}public static function validateErrorReporting($row,$isCli){$errorReporting=stripos(error_reporting(),"_")===false?self::error2string(error_reporting()):error_reporting();if(in_array("E_NOTICE",$errorReporting)||in_array("E_ALL",$errorReporting)){$row["incorrect"]=true;}$row["current"]=implode(" | ",$errorReporting);return $row;}public static function validateDisableFunctions($row,$isCli){$functions=["exec","shell_exec","system","ini_set","passthru","popen","curl_exec","readfile","eval","ftp_connect","php_uname",];$disabled=explode(",",@ini_get("disable_functions"));$row["current"]=implode(" , ",$disabled);foreach($disabled as $item){if(in_array($item,$functions)){$row["incorrect"]=true;}}return $row;}private static function getNewestPhpVersion($version){return false;if(!class_exists("Requests")){return false;}$resonse=Requests::get("http://php.net/releases/index.php?json&max=10&version=".$version[0]);$data=array_keys((array) \App\Json::decode($resonse->body));natsort($data);foreach(array_reverse($data)as $ver){if(strpos($ver,$version)===0){return $ver;}}return false;}private static function getFlag($val){if($val=="On"||$val==1||stripos($val,"On")!==false){return "On";}return "Off";}public static function getRemoteIP($onlyIP=false){$address=$_SERVER["REMOTE_ADDR"];if(!empty($_SERVER["HTTP_X_REAL_IP"])){$remote_ip[]="X-Real-IP: ".$_SERVER["HTTP_X_REAL_IP"];}if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){$remote_ip[]="X-Forwarded-For: ".$_SERVER["HTTP_X_FORWARDED_FOR"];}if(!empty($remote_ip)&&$onlyIP==false){$address.="(".implode(",",$remote_ip).")";}return $address;}public static function parseBytes($str){if(is_numeric($str)){return floatval($str);}if(preg_match("/([0-9\.]+)\s*([a-z]*)/i",$str,$regs)){$bytes=floatval($regs[1]);switch(strtolower($regs[2])){case "g":case "gb":$bytes*=1073741824;break;case "m":case "mb":$bytes*=1048576;break;case "k":case "kb":$bytes*=1024;break;}}return floatval($bytes);}public static function showBytes($bytes,&$unit=null){$bytes=self::parseBytes($bytes);if($bytes>=1073741824){$unit="GB";$gb=$bytes/1073741824;$str=sprintf($gb>=10?"%d ":"%.1f ",$gb).$unit;}elseif($bytes>=1048576){$unit="MB";$mb=$bytes/1048576;$str=sprintf($mb>=10?"%d ":"%.1f ",$mb).$unit;}elseif($bytes>=1024){$unit="KB";$str=sprintf("%d ",round($bytes/1024)).$unit;}else{$unit="B";$str=sprintf("%d ",$bytes).$unit;}return $str;}public static function getMaxUploadSize(){$maxFileSize=self::parseBytes(ini_get("upload_max_filesize"));$maxPostSize=self::parseBytes(ini_get("post_max_size"));if($maxPostSize&&$maxPostSize<$maxFileSize){$maxFileSize=$maxPostSize;}return $maxFileSize;}public static function exec_disabled($value="exec"){$disabled=explode(",",ini_get("disable_functions"));return in_array($value,$disabled);}}
$Libraries = ParsVT_Check_Requirements::getLibrary();
$STABILITYCONF = ParsVT_Check_Requirements::getStabilityConf(false);
$SYSINFO = ParsVT_Check_Requirements::getSystemInfo();
$Security = ParsVT_Check_Requirements::getSecurityConf();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Check system requirements</title>
<style>
@import "https://fonts.googleapis.com/css?family=Montserrat:400,700|Raleway:300,400";*{margin:0;padding:0}body{background-color:#efefef;color:#333;font-family:"Raleway";height:100%;width:100%;min-height:100%;min-width:100%;padding-bottom:80px}body h1{text-align:center;color:#428bff;font-weight:300;padding:40px 0 20px 0;margin:0}.tabs{left:50%;transform:translateX(-50%);position:relative;background-color:#fff;padding:50px;width:75%;box-shadow:0 14px 28px rgba(0,0,0,.25),0 10px 10px rgba(0,0,0,.22);border-radius:5px;min-height:240px;min-width:240px}@media (max-width:400px){.tabs{width:70%}}.tabs input[name="tab-control"]{display:none}.tabs .content section h2,.tabs ul li label{font-family:"Montserrat";font-weight:700;font-size:18px;color:#428bff}.tabs ul{list-style-type:none;padding-left:0;display:flex;flex-direction:row;margin-bottom:10px;justify-content:space-between;align-items:flex-end;flex-wrap:wrap}.tabs ul li{box-sizing:border-box;flex:1;width:25%;padding:0 10px;text-align:center}.tabs ul li label{transition:all 0.3s ease-in-out;color:#929daf;padding:5px auto;overflow:hidden;text-overflow:ellipsis;display:block;cursor:pointer;transition:all 0.2s ease-in-out;white-space:nowrap;-webkit-touch-callout:none}.tabs ul li label br{display:none}.tabs ul li label svg{fill:#929daf;height:1.2em;vertical-align:bottom;margin-right:.2em;transition:all 0.2s ease-in-out}.tabs ul li label:hover,.tabs ul li label:focus,.tabs ul li label:active{outline:0;color:#bec5cf}.tabs ul li label:hover svg,.tabs ul li label:focus svg,.tabs ul li label:active svg{fill:#bec5cf}.tabs .slider{position:relative;width:25%;transition:all 0.33s cubic-bezier(.38,.8,.32,1.07)}.tabs .slider .indicator{position:relative;width:50px;max-width:100%;margin:0 auto;height:4px;background:#428bff;border-radius:1px}.tabs .content{margin-top:30px}.tabs .content section{display:none;animation-name:content;animation-direction:normal;animation-duration:0.3s;animation-timing-function:ease-in-out;animation-iteration-count:1;line-height:1.4}.tabs .content section h2{color:#428bff;display:none;width:100%}.tabs .content section h2::after{content:"";position:relative;display:block;width:30px;height:3px;background:#428bff;margin-top:5px;left:1px}.tabs .content section h3{color:#428bff;font-family:"Montserrat";font-size:16px;padding-bottom:20px;width:100%}.tabs input[name="tab-control"]:nth-of-type(1):checked~ul>li:nth-child(1)>label{cursor:default;color:#428bff}.tabs input[name="tab-control"]:nth-of-type(1):checked~ul>li:nth-child(1)>label svg{fill:#428bff}@media (max-width:600px){.tabs input[name="tab-control"]:nth-of-type(1):checked~ul>li:nth-child(1)>label{background:rgba(0,0,0,.08)}}.tabs input[name="tab-control"]:nth-of-type(1):checked~.slider{transform:translateX(-10%)}.tabs input[name="tab-control"]:nth-of-type(1):checked~.content>section:nth-child(1){display:block}.tabs input[name="tab-control"]:nth-of-type(2):checked~ul>li:nth-child(2)>label{cursor:default;color:#428bff}.tabs input[name="tab-control"]:nth-of-type(2):checked~ul>li:nth-child(2)>label svg{fill:#428bff}@media (max-width:600px){.tabs input[name="tab-control"]:nth-of-type(2):checked~ul>li:nth-child(2)>label{background:rgba(0,0,0,.08)}}.tabs input[name="tab-control"]:nth-of-type(2):checked~.slider{transform:translateX(70%)}.tabs input[name="tab-control"]:nth-of-type(2):checked~.content>section:nth-child(2){display:block}.tabs input[name="tab-control"]:nth-of-type(3):checked~ul>li:nth-child(3)>label{cursor:default;color:#428bff}.tabs input[name="tab-control"]:nth-of-type(3):checked~ul>li:nth-child(3)>label svg{fill:#428bff}@media (max-width:600px){.tabs input[name="tab-control"]:nth-of-type(3):checked~ul>li:nth-child(3)>label{background:rgba(0,0,0,.08)}}.tabs input[name="tab-control"]:nth-of-type(3):checked~.slider{transform:translateX(150%)}.tabs input[name="tab-control"]:nth-of-type(3):checked~.content>section:nth-child(3){display:block}.tabs input[name="tab-control"]:nth-of-type(4):checked~ul>li:nth-child(4)>label{cursor:default;color:#428bff}.tabs input[name="tab-control"]:nth-of-type(4):checked~ul>li:nth-child(4)>label svg{fill:#428bff}@media (max-width:600px){.tabs input[name="tab-control"]:nth-of-type(4):checked~ul>li:nth-child(4)>label{background:rgba(0,0,0,.08)}}.tabs input[name="tab-control"]:nth-of-type(4):checked~.slider{transform:translateX(230%)}.tabs input[name="tab-control"]:nth-of-type(4):checked~.content>section:nth-child(4){display:block}.tabs input[name="tab-control"]:nth-of-type(5):checked~ul>li:nth-child(5)>label{cursor:default;color:#428bff}.tabs input[name="tab-control"]:nth-of-type(5):checked~ul>li:nth-child(5)>label svg{fill:#428bff}@media (max-width:600px){.tabs input[name="tab-control"]:nth-of-type(5):checked~ul>li:nth-child(5)>label{background:rgba(0,0,0,.08)}}.tabs input[name="tab-control"]:nth-of-type(5):checked~.slider{transform:translateX(310%)}.tabs input[name="tab-control"]:nth-of-type(5):checked~.content>section:nth-child(5){display:block}@keyframes content{from{opacity:0;transform:translateY(5%)}to{opacity:1;transform:translateY(0%)}}@media (max-width:1000px){.tabs ul li label{white-space:initial}.tabs ul li label br{display:initial}.tabs ul li label svg{height:1.5em;margin-right:0}}@media (max-width:600px){.tabs ul li label{padding:5px;border-radius:5px}.tabs ul li label span{display:none}.tabs .slider{display:none}.tabs .content{margin-top:20px}.tabs .content section h2{display:block;padding-bottom:20px}}.align{display:grid;place-items:center;padding:10px}.grid{inline-size:90%;margin-inline:auto;max-inline-size:20rem}.icons{display:none}.icon-login{block-size:1em;display:inline-block;fill:#eee;inline-size:1em;vertical-align:middle}input{background-image:none;border:0;color:inherit;font:inherit;margin:0;outline:0;padding:0;transition:background-color 0.3s}input[type="submit"]{cursor:pointer}.form{display:grid;gap:.875rem}.form input[type="password"],.form input[type="text"],.form input[type="submit"]{inline-size:100%}.form-field{display:flex}.form-input{flex:1}.login{color:#141414}.login label,.login input[type="text"],.login input[type="password"],.login input[type="submit"]{border-radius:.25rem;padding:.8rem}.login label{background-color:#428bff;border-bottom-right-radius:0;border-top-right-radius:0;padding-inline:1.25rem}.login input[type="password"],.login input[type="text"]{background-color:#c9deff;border-bottom-left-radius:0;border-top-left-radius:0}.login input[type="password"]:focus,.login input[type="password"]:hover,.login input[type="text"]:focus,.login input[type="text"]:hover{background-color:#b3d0ff}.login input[type="submit"]{background-color:#428bff;color:#eee;font-weight:700;text-transform:uppercase}.login input[type="submit"]:focus,.login input[type="submit"]:hover{background-color:#267aff}table{width:100%;border-collapse:collapse;overflow:hidden;border-radius:5px;table-layout:fixed;word-wrap:break-word}th,td{padding:15px;background-color:rgba(201,222,255,.7);color:#141414}th{text-align:left}td{font-family:monospace;font-size:14px}td svg{width:18px;vertical-align:top}thead th{background-color:#428bff;color:#eee}tbody tr{transition:background-color 0.2s}tbody tr:hover{background-color:#b3d0ff}@media (max-width:570px){table{width:125%!important;vertical-align:bottom}thead th{vertical-align:top}}.check{fill:green}.xmark{fill:red}a{text-decoration:none}.sticky_sidebar a{padding:0 10px;color:#428bff}.sticky_sidebar a svg{width:22px;fill:#eee}ul.sticky_sidebar{position:fixed;top:45%;z-index:9999;background:#428bff;-webkit-border-top-right-radius:10px;-webkit-border-bottom-right-radius:10px;-moz-border-radius-topright:10px;-moz-border-radius-bottomright:10px;border-top-right-radius:10px;border-bottom-right-radius:10px}ul.sticky_sidebar li{padding:10px 5px}tr.blink_me.danger{background-color:red}tr.blink_me.warning{background-color:#fd0}tr.blink_me.danger:hover{background-color:#b00000}tr.blink_me.warning:hover{background-color:#b09800}.app-footer{font-family:monospace;font-size:11px;position:fixed;bottom:0;width:100%}.app-footer p{width:100%;text-align:center;background:#fbfbfb;margin-bottom:0;padding:4px 0;border-top:1px solid #ddd}
</style>
<script> 
document.addEventListener('DOMContentLoaded', function(event) {
var nVer = navigator.appVersion;
var nAgt = navigator.userAgent;
var browserName = navigator.appName;
var fullVersion = '' + parseFloat(navigator.appVersion);
var majorVersion = parseInt(navigator.appVersion, 10);
var nameOffset, verOffset, ix;
if ((verOffset = nAgt.indexOf('OPR')) != -1) {
browserName = 'Opera';
fullVersion = nAgt.substring(verOffset + 4);
if ((verOffset = nAgt.indexOf('Version')) != -1)
fullVersion = nAgt.substring(verOffset + 8);
}
else if ((verOffset = nAgt.indexOf('Edg')) != -1) {
browserName = 'Microsoft Edge';
fullVersion = nAgt.substring(verOffset + 4);
}
else if ((verOffset = nAgt.indexOf('MSIE')) != -1) {
browserName = 'Microsoft Internet Explorer';
fullVersion = nAgt.substring(verOffset + 5);
}
else if ((verOffset = nAgt.indexOf('Chrome')) != -1) {
browserName = 'Chrome';
fullVersion = nAgt.substring(verOffset + 7);
}
else if ((verOffset = nAgt.indexOf('Safari')) != -1) {
browserName = 'Safari';
fullVersion = nAgt.substring(verOffset + 7);
if ((verOffset = nAgt.indexOf('Version')) != -1)
fullVersion = nAgt.substring(verOffset + 8);
}
else if ((verOffset = nAgt.indexOf('Firefox')) != -1) {
browserName = 'Firefox';
fullVersion = nAgt.substring(verOffset + 8);
}
else if ((nameOffset = nAgt.lastIndexOf(' ') + 1) <
(verOffset = nAgt.lastIndexOf('/'))) {
browserName = nAgt.substring(nameOffset, verOffset);
fullVersion = nAgt.substring(verOffset + 1);
if (browserName.toLowerCase() == browserName.toUpperCase()) {
browserName = navigator.appName;
}
}
if ((ix = fullVersion.indexOf(';')) != -1)
fullVersion = fullVersion.substring(0, ix);
if ((ix = fullVersion.indexOf(' ')) != -1)
fullVersion = fullVersion.substring(0, ix);
majorVersion = parseInt('' + fullVersion, 10);
if (isNaN(majorVersion)) {
fullVersion = '' + parseFloat(navigator.appVersion);
majorVersion = parseInt(navigator.appVersion, 10);
}
var test_canvas = document.createElement('canvas') //Try and create sample canvas element
var HMTL5 = (test_canvas.getContext) ? true : false
var OSName = 'Unknown';
if (window.navigator.userAgent.indexOf('Windows NT 10.0') != -1) OSName = 'Windows 10';
if (window.navigator.userAgent.indexOf('Windows NT 6.3') != -1) OSName = 'Windows 8.1';
if (window.navigator.userAgent.indexOf('Windows NT 6.2') != -1) OSName = 'Windows 8';
if (window.navigator.userAgent.indexOf('Windows NT 6.1') != -1) OSName = 'Windows 7';
if (window.navigator.userAgent.indexOf('Windows NT 6.0') != -1) OSName = 'Windows Vista';
if (window.navigator.userAgent.indexOf('Windows NT 5.1') != -1) OSName = 'Windows XP';
if (window.navigator.userAgent.indexOf('Windows NT 5.0') != -1) OSName = 'Windows 2000';
if (window.navigator.userAgent.indexOf('Mac') != -1) OSName = 'Mac/iOS';
if (window.navigator.userAgent.indexOf('X11') != -1) OSName = 'UNIX';
if (window.navigator.userAgent.indexOf('Linux') != -1) OSName = 'Linux';
var yes = '<?php echo ParsVT_Check_Requirements::$yes; ?>';
var no = '<?php echo ParsVT_Check_Requirements::$no; ?>';
document.getElementById('screenheight1').innerHTML = screen.height;
document.getElementById('screenheight2').innerHTML = (screen.height < 768 ? no : yes);
document.getElementById('screenwidth1').innerHTML = screen.width;
document.getElementById('screenwidth2').innerHTML = (screen.width < 1025 ? no : yes);
document.getElementById('userAgent1').innerHTML = navigator.userAgent;
document.getElementById('userAgent2').innerHTML = yes;
document.getElementById('cookieEnabled1').innerHTML = navigator.cookieEnabled;
document.getElementById('cookieEnabled2').innerHTML = (navigator.cookieEnabled ? yes : no);
document.getElementById('browserName1').innerHTML = browserName + ' v' + fullVersion;
document.getElementById('browserName2').innerHTML = yes;
document.getElementById('HTML1').innerHTML = HMTL5;
document.getElementById('HTML2').innerHTML = (HMTL5 ? yes : no);
document.getElementById('OS1').innerHTML = OSName;
document.getElementById('OS2').innerHTML = yes;
});
</script>
</head>
<body>
<h1>Checking Pre-Requisites Before Installation ParsVT CRM</h1>
<div class="tabs">
<input type="radio" id="tab1" name="tab-control" checked>
<input type="radio" id="tab2" name="tab-control">
<input type="radio" id="tab3" name="tab-control">
<input type="radio" id="tab4" name="tab-control">
<input type="radio" id="tab5" name="tab-control">
<ul>
<li title="Database Information">
<label for="tab1" role="button">
<svg viewBox="0 0 448 512">
<use xlink:href="#icon-database"></use>
</svg>
<br><span>Database Info</span>
</label>
</li>
<li title="PHP Extensions">
<label for="tab2" role="button">
<svg viewBox="0 0 640 512">
<use xlink:href="#icon-php"></use>
</svg>
<br><span>PHP Extensions</span>
</label>
</li>
<li title="PHP Configuration">
<label for="tab3" role="button">
<svg viewBox="0 0 640 512">
<use xlink:href="#icon-config"></use>
</svg>
<br><span>PHP Config</span>
</label>
</li>
<li title="Security Configuration">
<label for="tab4" role="button">
<svg viewBox="0 0 512 512">
<use xlink:href="#icon-security"></use>
</svg>
<br><span>Security Config</span>
</label>
</li>
<li title="Other Information">
<label for="tab5" role="button">
<svg viewBox="0 0 512 512">
<use xlink:href="#icon-server"></use>
</svg>
<br><span>Other Info</span>
</label>
</li>
</ul>
<div class="slider">
<div class="indicator"></div>
</div>
<div class="content">
<section>
<?php
$connection = false;
$error = "";
if (isset($_SESSION["hostname"], $_SESSION["database"], $_SESSION["username"], $_SESSION["password"]) || (isset( $_REQUEST["hostname"], $_REQUEST["database"], $_REQUEST["username"], $_REQUEST["password"]) && !empty($_REQUEST["hostname"]) && !empty($_REQUEST["database"]) && !empty($_REQUEST["username"]))) {
if (isset($_REQUEST["hostname"], $_REQUEST["database"], $_REQUEST["username"], $_REQUEST["password"])) {
$_SESSION["hostname"] = $_REQUEST["hostname"];
$_SESSION["database"] = $_REQUEST["database"];
$_SESSION["username"] = $_REQUEST["username"];
$_SESSION["password"] = $_REQUEST["password"];
}
$con = mysqli_connect($_SESSION["hostname"], $_SESSION["username"], $_SESSION["password"], $_SESSION["database"]);
if (mysqli_connect_errno()) {
$connection = false;
$error = "<h3 style='color:red;'>Failed to connect to MySQL: " . mysqli_connect_error() . "</h3><br>";
unset($_SESSION["hostname"]);
unset($_SESSION["database"]);
unset($_SESSION["username"]);
unset($_SESSION["password"]);
} else {
$connection = true;
$DbConf = ParsVT_Check_Requirements::getDbConf($con);
echo "<div>";
echo "<div class='align'>";
echo "<h2>Database Information</h2><h3>In database settings there are some recommended settings as well as some settings that we strongly recommend not to use.<br>So, first at all, we DO NOT recommend to have for SQL Mode set STRICT_TRANS_TABLE. We recommend STRICT_TRANS_TABLE turned off.</h3><br>";
echo "<table><thead><tr><th>Parameter</th><th>Current value</th><th>Recommended</th><th>Status</th></tr></thead><tbody>";
foreach ($DbConf as $key => $item) {
echo "<tr " . ($item["status"] ? 'class="blink_me danger" ' . (isset($item["help"]) && $item["status"] ? 'data-toggle="tooltip" title="' . $item["help"] . '" title="' . $item["help"] . '"' : "") : "") . "><td>" . $key . "</td>";
if ($item["recommended"] === false) {
echo '<td colspan="3">' . $item["current"] . "</td>";
} else {
echo "<td>" . $item["current"] . "</td>";
echo "<td>" . $item["recommended"] . "</td>";
echo "<td>" . ($item["status"] ? ParsVT_Check_Requirements::$no : ParsVT_Check_Requirements::$yes) . "</td>";
}
echo "</tr>";
}
echo "<div>";
echo "<div>";
echo "<div>";
echo "</tbody></table>";
echo "<div>";
echo "</div>";
}
}
if (!$connection) {
?>
<div id="loginForm">
<div class="align">
<h2>Database Information</h2>
<h3>To obtain more information on MySQL requirements, please enter MySQL database connection information.</h3>
<br><?php echo $error; ?>
<div class="grid">
<form method="POST" class="form login">
<div class="form-field">
<label for="login-host">
<svg viewBox="0 0 512 512" class="icon-login">
<use xlink:href="#icon-server"></use>
</svg>
</label>
<input autocomplete="username" id="login-host" type="text" name="hostname" class="form-input" placeholder="Host name" value="<?php echo $_REQUEST["hostname"]; ?>" required>
</div>
<div class="form-field">
<label for="login-database">
<svg viewBox="0 0 448 512" class="icon-login">
<use xlink:href="#icon-database"></use>
</svg>
</label>
<input autocomplete="localhost" id="login-database" type="text" name="database" class="form-input" placeholder="Database name" value="<?php echo $_REQUEST["database"]; ?>" required>
</div>
<div class="form-field">
<label for="login-username">
<svg viewBox="0 0 448 512" class="icon-login">
<use xlink:href="#icon-user"></use>
</svg>
</label>
<input autocomplete="username" id="login-username" type="text" name="username" class="form-input" placeholder="Username"  value="<?php echo $_REQUEST["username"]; ?>" required>
</div>
<div class="form-field">
<label for="login-password">
<svg viewBox="0 0 448 512" class="icon-login">
<use xlink:href="#icon-lock"></use>
</svg>
</label>
<input id="login-password" type="password" name="password" class="form-input" placeholder="Password"  value="<?php echo $_REQUEST["hostname"]; ?>">
</div>
<div class="form-field"><input type="submit" value="Check Requirements"></div>
</form>
</div>
</div>
</div>
<?php } ?>
</section>
<section>
<div class="align">
<h2>PHP Extensions</h2>
<h3>Following extensions should be enabled for your PHP setup.</h3>
<table>
<thead>
<tr>
<th>Library</th>
<th>Mandatory</th>
<th>Installed</th>
</tr>
</thead>
<tbody>
<?php
foreach ($Libraries as $key => $item) {
$help = $signature = "";
if ($item["status"] == "No" && $item["mandatory"]) {
$signature = ' class="blink_me danger" ';
} elseif ($item["status"] == "No") {
$signature = ' class="blink_me warning" ';
}
if (isset($item["help"]) && $item["status"]) {
$help = 'data-toggle="tooltip" title="' . $item["help"] . '" title="' . $item["help"] . '"';
}
$icon = strtolower($item["status"]) == "yes" ? ParsVT_Check_Requirements::$yes : ParsVT_Check_Requirements::$no;
echo "<tr  " . $signature . " " . $help . " ><td>" . $key . "</td><td>" . ($item["mandatory"] ? "Mandatory" : "Optional") . "</td><td>" . $icon . "</td></tr>";
}
?>
</tbody>
</table>
</div>
</section>
<section>
<div class="align">
<h2>PHP Configuration</h2>
<h3>It is recommended to have php.ini values set as below. In case php.ini requirements are not met, installation process can be still proceeded. This can be adjusted later.</h3>
<table>
<thead>
<tr>
<th>Parameter</th>
<th>Current value</th>
<th>Recommended</th>
<th>Status</th>
</tr>
</thead>
<tbody>
<?php
foreach ($STABILITYCONF as $key => $item) {
echo "<tr " . ($item["incorrect"] ? 'class="blink_me danger" ' . (isset($item["help"]) && $item["incorrect"] ? 'data-toggle="tooltip" title="' . $item["help"] . '" title="' . $item["help"] . '"' : "") : "") . '><td>' . $key . "</td>";
if ($item["recommended"] === false) {
echo ' <td colspan="2">' . $item["current"] . "</td>";
} else {
echo "<td>" . $item["current"] . "</td><td>" . $item["recommended"] . "</td>";
}
echo "<td>" . ($item["incorrect"] ? ParsVT_Check_Requirements::$no : ParsVT_Check_Requirements::$yes) . "</td>";
echo "</tr>";
}
?>
</tbody>
</table>
</div>
</section>
<section>
<div class="align">
<h2>Security Configuration</h2>
<h3>Check server security.</h3>
<table>
<thead>
<tr>
<th>Parameter</th>
<th>Current value</th>
<th>Recommended</th>
<th>Status</th>
</tr>
</thead>
<tbody><?php foreach ($Security as $key => $item) {echo '<tr ' . ($item['status'] ? 'class="blink_me danger" ' . ((isset($item['help']) && $item['status']) ? 'data-toggle="tooltip" title="' . $item['help'] . '" title="' . $item['help'] . '"' : '') : '') . '>';echo '<td>' . $key . '</td><td>' . $item['current'] . '</td><td>' . $item['recommended'] . '</td><td>' . ($item['status'] ? ParsVT_Check_Requirements::$no : ParsVT_Check_Requirements::$yes) . '</td></tr>';}?></tbody>
</table>
</div>
</section>
<section>
<div class="align">
<h2>Other Information</h2>
<h3>Server Information</h3>
<table>
<thead>
<tr>
<th>Parameter</th>
<th colspan="2">Value</th>
</tr>
</thead>
<tbody><?php foreach($SYSINFO as $key=>$item){echo "<tr><td>".$key.'</label></td><td colspan="2">'.$item."</td></tr>";} ?></tbody>
</table>
</div>
<div class='align'>
<h3>Client Information</h3>
<table>
<thead>
<tr>
<th>Parameter</th>
<th colspan="2">Value</th>
<th>Status</th>
</tr>
</thead>
<tbody>
<tr>
<td>Browser</td>
<td colspan='2' id="browserName1"></td>
<td id="browserName2"></td>
</tr>
<tr>
<td>User-Agent</td>
<td colspan='2' id="userAgent1"></td>
<td id='userAgent2'></td>
</tr>
<tr>
<td>Operating system</td>
<td colspan='2' id="OS1"></td>
<td id='OS2'></td>
</tr>
<tr>
<td>Cookie enabled</td>
<td colspan='2' id="cookieEnabled1"></td>
<td id='cookieEnabled2'></td>
</tr>
<tr>
<td>HTML5 enabled</td>
<td colspan='2' id="HTML1"></td>
<td id='HTML2'></td>
</tr>
<tr>
<td>Screen width</td>
<td colspan='2' id="screenwidth1"></td>
<td id='screenwidth2'></td>
</tr>
<tr>
<td>Screen height</td>
<td colspan='2' id="screenheight1"></td>
<td id='screenheight2'></td>
</tr>
</tbody>
</table>
</div>
</section>
</div>
</div>
<footer class='app-footer'>
<p><span>Version</span> - 1.0.2 &nbsp;|&nbsp; <span>Patch</span> - 14020929 &nbsp;|&nbsp; <span>© 2024 <a href="https://parsvt.com" target="_blank">ParsVT Group</a>.</span></p>
</footer>
<ul class="sticky_sidebar">
<li>
<a href="#" onClick="window.location.reload();">
<svg viewBox="0 0 512 512">
<use xlink:href="#icon-refresh"></use>
</svg>
</a>
</li>
<?php if ($connection) { ?>
<li>
<a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?action=reset">
<svg viewBox="0 0 512 512">
<use xlink:href="#icon-logout"></use>
</svg>
</a>
</li>
<?php } ?>
</ul>
<svg xmlns="http://www.w3.org/2000/svg" class="icons">
<symbol id="icon-user">
<path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z" />
</symbol>
<symbol id="icon-lock">
<path d="M144 144v48H304V144c0-44.2-35.8-80-80-80s-80 35.8-80 80zM80 192V144C80 64.5 144.5 0 224 0s144 64.5 144 144v48h16c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V256c0-35.3 28.7-64 64-64H80z" />
</symbol>
<symbol id="icon-database">
<path d="M448 80v48c0 44.2-100.3 80-224 80S0 172.2 0 128V80C0 35.8 100.3 0 224 0S448 35.8 448 80zM393.2 214.7c20.8-7.4 39.9-16.9 54.8-28.6V288c0 44.2-100.3 80-224 80S0 332.2 0 288V186.1c14.9 11.8 34 21.2 54.8 28.6C99.7 230.7 159.5 240 224 240s124.3-9.3 169.2-25.3zM0 346.1c14.9 11.8 34 21.2 54.8 28.6C99.7 390.7 159.5 400 224 400s124.3-9.3 169.2-25.3c20.8-7.4 39.9-16.9 54.8-28.6V432c0 44.2-100.3 80-224 80S0 476.2 0 432V346.1z" />
</symbol>
<symbol id="icon-php">
<path d="M320 104.5c171.4 0 303.2 72.2 303.2 151.5S491.3 407.5 320 407.5c-171.4 0-303.2-72.2-303.2-151.5S148.7 104.5 320 104.5m0-16.8C143.3 87.7 0 163 0 256s143.3 168.3 320 168.3S640 349 640 256 496.7 87.7 320 87.7zM218.2 242.5c-7.9 40.5-35.8 36.3-70.1 36.3l13.7-70.6c38 0 63.8-4.1 56.4 34.3zM97.4 350.3h36.7l8.7-44.8c41.1 0 66.6 3 90.2-19.1 26.1-24 32.9-66.7 14.3-88.1-9.7-11.2-25.3-16.7-46.5-16.7h-70.7L97.4 350.3zm185.7-213.6h36.5l-8.7 44.8c31.5 0 60.7-2.3 74.8 10.7 14.8 13.6 7.7 31-8.3 113.1h-37c15.4-79.4 18.3-86 12.7-92-5.4-5.8-17.7-4.6-47.4-4.6l-18.8 96.6h-36.5l32.7-168.6zM505 242.5c-8 41.1-36.7 36.3-70.1 36.3l13.7-70.6c38.2 0 63.8-4.1 56.4 34.3zM384.2 350.3H421l8.7-44.8c43.2 0 67.1 2.5 90.2-19.1 26.1-24 32.9-66.7 14.3-88.1-9.7-11.2-25.3-16.7-46.5-16.7H417l-32.8 168.7z" />
</symbol>
<symbol id="icon-server">
<path d="M64 32C28.7 32 0 60.7 0 96v64c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V96c0-35.3-28.7-64-64-64H64zm280 72a24 24 0 1 1 0 48 24 24 0 1 1 0-48zm48 24a24 24 0 1 1 48 0 24 24 0 1 1 -48 0zM64 288c-35.3 0-64 28.7-64 64v64c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V352c0-35.3-28.7-64-64-64H64zm280 72a24 24 0 1 1 0 48 24 24 0 1 1 0-48zm56 24a24 24 0 1 1 48 0 24 24 0 1 1 -48 0z" />
</symbol>
<symbol id="icon-check">
<path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z" />
</symbol>
<symbol id="icon-xmark">
<path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z" />
</symbol>
<symbol id="icon-config">
<path d="M308.5 135.3c7.1-6.3 9.9-16.2 6.2-25c-2.3-5.3-4.8-10.5-7.6-15.5L304 89.4c-3-5-6.3-9.9-9.8-14.6c-5.7-7.6-15.7-10.1-24.7-7.1l-28.2 9.3c-10.7-8.8-23-16-36.2-20.9L199 27.1c-1.9-9.3-9.1-16.7-18.5-17.8C173.9 8.4 167.2 8 160.4 8h-.7c-6.8 0-13.5 .4-20.1 1.2c-9.4 1.1-16.6 8.6-18.5 17.8L115 56.1c-13.3 5-25.5 12.1-36.2 20.9L50.5 67.8c-9-3-19-.5-24.7 7.1c-3.5 4.7-6.8 9.6-9.9 14.6l-3 5.3c-2.8 5-5.3 10.2-7.6 15.6c-3.7 8.7-.9 18.6 6.2 25l22.2 19.8C32.6 161.9 32 168.9 32 176s.6 14.1 1.7 20.9L11.5 216.7c-7.1 6.3-9.9 16.2-6.2 25c2.3 5.3 4.8 10.5 7.6 15.6l3 5.2c3 5.1 6.3 9.9 9.9 14.6c5.7 7.6 15.7 10.1 24.7 7.1l28.2-9.3c10.7 8.8 23 16 36.2 20.9l6.1 29.1c1.9 9.3 9.1 16.7 18.5 17.8c6.7 .8 13.5 1.2 20.4 1.2s13.7-.4 20.4-1.2c9.4-1.1 16.6-8.6 18.5-17.8l6.1-29.1c13.3-5 25.5-12.1 36.2-20.9l28.2 9.3c9 3 19 .5 24.7-7.1c3.5-4.7 6.8-9.5 9.8-14.6l3.1-5.4c2.8-5 5.3-10.2 7.6-15.5c3.7-8.7 .9-18.6-6.2-25l-22.2-19.8c1.1-6.8 1.7-13.8 1.7-20.9s-.6-14.1-1.7-20.9l22.2-19.8zM112 176a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zM504.7 500.5c6.3 7.1 16.2 9.9 25 6.2c5.3-2.3 10.5-4.8 15.5-7.6l5.4-3.1c5-3 9.9-6.3 14.6-9.8c7.6-5.7 10.1-15.7 7.1-24.7l-9.3-28.2c8.8-10.7 16-23 20.9-36.2l29.1-6.1c9.3-1.9 16.7-9.1 17.8-18.5c.8-6.7 1.2-13.5 1.2-20.4s-.4-13.7-1.2-20.4c-1.1-9.4-8.6-16.6-17.8-18.5L583.9 307c-5-13.3-12.1-25.5-20.9-36.2l9.3-28.2c3-9 .5-19-7.1-24.7c-4.7-3.5-9.6-6.8-14.6-9.9l-5.3-3c-5-2.8-10.2-5.3-15.6-7.6c-8.7-3.7-18.6-.9-25 6.2l-19.8 22.2c-6.8-1.1-13.8-1.7-20.9-1.7s-14.1 .6-20.9 1.7l-19.8-22.2c-6.3-7.1-16.2-9.9-25-6.2c-5.3 2.3-10.5 4.8-15.6 7.6l-5.2 3c-5.1 3-9.9 6.3-14.6 9.9c-7.6 5.7-10.1 15.7-7.1 24.7l9.3 28.2c-8.8 10.7-16 23-20.9 36.2L315.1 313c-9.3 1.9-16.7 9.1-17.8 18.5c-.8 6.7-1.2 13.5-1.2 20.4s.4 13.7 1.2 20.4c1.1 9.4 8.6 16.6 17.8 18.5l29.1 6.1c5 13.3 12.1 25.5 20.9 36.2l-9.3 28.2c-3 9-.5 19 7.1 24.7c4.7 3.5 9.5 6.8 14.6 9.8l5.4 3.1c5 2.8 10.2 5.3 15.5 7.6c8.7 3.7 18.6 .9 25-6.2l19.8-22.2c6.8 1.1 13.8 1.7 20.9 1.7s14.1-.6 20.9-1.7l19.8 22.2zM464 304a48 48 0 1 1 0 96 48 48 0 1 1 0-96z"/>
</symbol>
<symbol id="icon-security">
<path d="M256 0c4.6 0 9.2 1 13.4 2.9L457.7 82.8c22 9.3 38.4 31 38.3 57.2c-.5 99.2-41.3 280.7-213.6 363.2c-16.7 8-36.1 8-52.8 0C57.3 420.7 16.5 239.2 16 140c-.1-26.2 16.3-47.9 38.3-57.2L242.7 2.9C246.8 1 251.4 0 256 0zm0 66.8V444.8C394 378 431.1 230.1 432 141.4L256 66.8l0 0z"/>
</symbol>
<symbol id="icon-refresh">
<path d='M105.1 202.6c7.7-21.8 20.2-42.3 37.8-59.8c62.5-62.5 163.8-62.5 226.3 0L386.3 160H336c-17.7 0-32 14.3-32 32s14.3 32 32 32H463.5c0 0 0 0 0 0h.4c17.7 0 32-14.3 32-32V64c0-17.7-14.3-32-32-32s-32 14.3-32 32v51.2L414.4 97.6c-87.5-87.5-229.3-87.5-316.8 0C73.2 122 55.6 150.7 44.8 181.4c-5.9 16.7 2.9 34.9 19.5 40.8s34.9-2.9 40.8-19.5zM39 289.3c-5 1.5-9.8 4.2-13.7 8.2c-4 4-6.7 8.8-8.1 14c-.3 1.2-.6 2.5-.8 3.8c-.3 1.7-.4 3.4-.4 5.1V448c0 17.7 14.3 32 32 32s32-14.3 32-32V396.9l17.6 17.5 0 0c87.5 87.4 229.3 87.4 316.7 0c24.4-24.4 42.1-53.1 52.9-83.7c5.9-16.7-2.9-34.9-19.5-40.8s-34.9 2.9-40.8 19.5c-7.7 21.8-20.2 42.3-37.8 59.8c-62.5 62.5-163.8 62.5-226.3 0l-.1-.1L125.6 352H176c17.7 0 32-14.3 32-32s-14.3-32-32-32H48.4c-1.6 0-3.2 .1-4.8 .3s-3.1 .5-4.6 1z'/>
</symbol>
<symbol id="icon-logout">
<path d='M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z'/>
</symbol>
</svg>
</body>
</html>
