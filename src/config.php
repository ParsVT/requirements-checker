<?php

class ParsVT_Check_Requirements
{
    public static $disaplayCapacity = true;
    public static $latest_ioncube = "12.0.5";
    private static $tmpPath;
    public static $yes = '<svg viewBox="0 0 448 512" class="check"><use xlink:href="#icon-check"></use></svg>';
    public static $no = '<svg viewBox="0 0 384 512" class="xmark"><use xlink:href="#icon-xmark"></use></svg>';
	
    /*
    Errors level.
    @var string[]
    */
	
    private static $levelNames = [
        E_ERROR => "E_ERROR",
        E_WARNING => "E_WARNING",
        E_PARSE => "E_PARSE",
        E_NOTICE => "E_NOTICE",
        E_STRICT => "E_STRICT",
        E_CORE_ERROR => "E_CORE_ERROR",
        E_CORE_WARNING => "E_CORE_WARNING",
        E_COMPILE_ERROR => "E_COMPILE_ERROR",
        E_COMPILE_WARNING => "E_COMPILE_WARNING",
        E_USER_ERROR => "E_USER_ERROR",
        E_USER_WARNING => "E_USER_WARNING",
        E_USER_NOTICE => "E_USER_NOTICE",
        E_RECOVERABLE_ERROR => "E_RECOVERABLE_ERROR",
        E_DEPRECATED => "E_DEPRECATED",
        E_USER_DEPRECATED => "E_USER_DEPRECATED",
        //E_ALL => "E_ALL",
    ];
	
    /*
    List of libraries.
    @var array
    */
	
    public static $library = [
        "IMAP" => [
            "type" => "f",
            "name" => "imap_open",
            "mandatory" => true,
        ],
        "Zlib" => [
            "type" => "f",
            "name" => "gzinflate",
            "mandatory" => true,
        ],
        "OpenSSL" => [
            "type" => "e",
            "name" => "openssl",
            "mandatory" => true,
        ],
        "cURL" => [
            "type" => "e",
            "name" => "curl",
            "mandatory" => true,
        ],
        "GD" => [
            "type" => "e",
            "name" => "gd",
            "mandatory" => true,
        ],
        "XML" => [
            "type" => "e",
            "name" => "xml",
            "mandatory" => true,
        ],
        "JSON" => [
            "type" => "e",
            "name" => "json",
            "mandatory" => true,
        ],
        "Session" => [
            "type" => "e",
            "name" => "session",
            "mandatory" => true,
        ],
        "DOM" => [
            "type" => "e",
            "name" => "dom",
            "mandatory" => true,
        ],
        "Zip" => [
            "type" => "e",
            "name" => "zip",
            "mandatory" => true,
        ],
        "Multibyte" => [
            "type" => "e",
            "name" => "mbstring",
            "mandatory" => true,
        ],
        "SOAP" => [
            "type" => "e",
            "name" => "soap",
            "mandatory" => true,
        ],
        "Fileinfo" => [
            "type" => "e",
            "name" => "fileinfo",
            "mandatory" => true,
        ],
        "iconv" => [
            "type" => "e",
            "name" => "iconv",
            "mandatory" => true,
        ],
        "Exif" => [
            "type" => "f",
            "name" => "exif_read_data",
            "mandatory" => false,
        ],
        "LDAP" => [
            "type" => "f",
            "name" => "ldap_connect",
            "mandatory" => false,
        ],
        "Sockets" => [
            "type" => "f",
            "name" => "fsockopen",
            "mandatory" => false,
        ],
    ];
	
    private static function getStabilitIniConf()
    {
        $time_limit = 600;
        if (file_exists("config.inc.php")) {
            require_once "config.inc.php";
            if (isset($site_URL)) {
                $time_limit = 60;
            }
        }
        $directiveValues = [
            "PHP" => [
                "recommended" => "5.4.x, 5.5.x, 5.6.x, 7.0.x, 7.1.x, 7.2.x, 7.3.x, 7.4.x",
                "help" => "LBL_PHP_HELP_TEXT",
                "fn" => "validatePhp",
                "max" => "7.2",
            ],
            "ionCube" => [
                "recommended" => "12.x",
                "help" => "LBL_IONCUBE_HELP_TEXT",
                "fn" => "validateIonCube",
            ],
            "Installed version" => [
                "recommended" => false,
                "help" => "LBL_INSTALLED_VERSION_HELP_TEXT",
                "fn" => "validateIonCubeInstalledVersion",
            ],
            "error_reporting" => [
                "recommended" => "E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT",
                "help" => "LBL_ERROR_REPORTING_HELP_TEXT",
                "fn" => "validateErrorReporting",
            ],
            "output_buffering" => [
                "recommended" => "On",
                "help" => "LBL_OUTPUT_BUFFERING_HELP_TEXT",
                "fn" => "validateOnOffInt",
            ],
            "max_execution_time" => [
                "recommended" => $time_limit,
                "help" => "LBL_MAX_EXECUTION_TIME_HELP_TEXT",
                "fn" => "validateGreater",
            ],
            "max_input_time" => [
                "recommended" => $time_limit,
                "help" => "LBL_MAX_INPUT_TIME_HELP_TEXT",
                "fn" => "validateGreater",
            ],
            "default_socket_timeout" => [
                "recommended" => $time_limit,
                "help" => "LBL_DEFAULT_SOCKET_TIMEOUT_HELP_TEXT",
                "fn" => "validateGreater",
            ],
            "memory_limit" => [
                "recommended" => "512 MB",
                "help" => "LBL_MEMORY_LIMIT_HELP_TEXT",
                "fn" => "validateGreaterMb",
            ],
            "log_errors" => [
                "recommended" => "Off",
                "help" => "LBL_LOG_ERRORS_HELP_TEXT",
                "fn" => "validateOnOff",
            ],
            "file_uploads" => [
                "recommended" => "On",
                "help" => "LBL_FILE_UPLOADS_HELP_TEXT",
                "fn" => "validateOnOff",
            ],
            "short_open_tag" => [
                "recommended" => "On",
                "help" => "LBL_SHORT_OPEN_TAG_HELP_TEXT",
                "fn" => "validateOnOff",
            ],
            "post_max_size" => [
                "recommended" => "50 MB",
                "help" => "LBL_POST_MAX_SIZE_HELP_TEXT",
                "fn" => "validateGreaterMb",
            ],
            "upload_max_filesize" => [
                "recommended" => "100 MB",
                "help" => "LBL_UPLOAD_MAX_FILESIZE_HELP_TEXT",
                "fn" => "validateGreaterMb",
            ],
            "max_input_vars" => [
                "recommended" => "10000",
                "help" => "LBL_MAX_INPUT_VARS_HELP_TEXT",
                "fn" => "validateGreater",
            ],
            "zlib.output_compression" => [
                "recommended" => "Off",
                "help" => "LBL_ZLIB_OUTPUT_COMPRESSION_HELP_TEXT",
                "fn" => "validateOnOff",
            ],
            "session.auto_start" => [
                "recommended" => "Off",
                "help" => "LBL_SESSION_AUTO_START_HELP_TEXT",
                "fn" => "validateOnOff",
            ],
            "session.gc_maxlifetime" => [
                "recommended" => "21600",
                "help" => "LBL_SESSION_GC_MAXLIFETIME_HELP_TEXT",
                "fn" => "validateGreater",
            ],
            "session.gc_divisor" => [
                "recommended" => "500",
                "help" => "LBL_SESSION_GC_DIVISOR_HELP_TEXT",
                "fn" => "validateGreater",
            ],
            "session.gc_probability" => [
                "recommended" => "1",
                "help" => "LBL_SESSION_GC_PROBABILITY_HELP_TEXT",
                "fn" => "validateEqual",
            ],
            "mbstring.func_overload" => [
                "recommended" => "Off",
                "help" => "LBL_MBSTRING_FUNC_OVERLOAD_HELP_TEXT",
                "fn" => "validateOnOff",
            ], //Roundcube
            "date.timezone" => [
                "recommended" => "Asia/Tehran",
                "fn" => "validateTimezone",
            ], //Roundcube
            "allow_url_fopen" => [
                "recommended" => "On",
                "help" => "LBL_ALLOW_URL_FOPEN_HELP_TEXT",
                "fn" => "validateOnOff",
            ], //Roundcube
            "disable_functions" => [
                "recommended" => "",
                "help" => "LBL_DISABLE_FUNCTION_HELP_TEXT",
                "fn" => "validateDisableFunctions",
            ], //Roundcube
        ];
        if (extension_loaded("suhosin")) {
            $directiveValues["suhosin.session.encrypt"] = [
                "recommended" => "Off",
                "fn" => "validateOnOff",
            ]; //Roundcube
            $directiveValues["suhosin.request.max_vars"] = [
                "recommended" => "5000",
                "fn" => "validateGreater",
            ];
            $directiveValues["suhosin.post.max_vars"] = [
                "recommended" => "5000",
                "fn" => "validateGreater",
            ];
            $directiveValues["suhosin.post.max_value_length"] = [
                "recommended" => "1500000",
                "fn" => "validateGreater",
            ];
        }
        return $directiveValues;
    }
	
    public static function getLibrary()
    {
        foreach (static::$library as $k => $v) {
            if ($v["type"] == "f") {
                $status = function_exists($v["name"]);
            } elseif ($v["type"] == "e") {
                $status = extension_loaded($v["name"]);
            }
            static::$library[$k]["status"] = $status ? "Yes" : "No";
        }
        return static::$library;
    }
	
    /*
    @param $onlyError
    @return array
    */
	
    public static function getStabilityConf($onlyError = false)
    {
        $ini = static::getPhpIniConf();
        $conf = static::getStabilitIniConf();
        $cliConf = false;
        foreach ($conf as $key => &$value) {
            if ($cliConf) {
                $value["cli"] = $value["current"] = $cliConf[$key];
                if (isset($value["fn"])) {
                    $value = call_user_func_array([__CLASS__, $value["fn"]], [$value, true]);
                    $value["cli"] = $value["current"];
                }
            }
            $value["current"] = $ini[$key];
            if (isset($value["fn"])) {
                $value = call_user_func_array([__CLASS__, $value["fn"]], [$value, false]);
                unset($value["fn"]);
            }
        }
        if ($onlyError) {
            foreach ($conf as $key => $value) {
                if (empty($value["incorrect"])) {
                    unset($conf[$key]);
                }
            }
        }
        return $conf;
    }
	
    /*
    @param $onlyError
    @return array
    */
	
    public static function getSecurityConf($onlyError = false)
    {
        $directiveValues = [
            "display_errors" => [
                "recommended" => "On",
                "help" => "LBL_DISPLAY_ERRORS_HELP_TEXT",
                "current" => static::getFlag(ini_get("display_errors")),
                "status" => ini_get("display_errors") == 1 || stripos(ini_get("display_errors"), "On") === false,
            ],
            "HTTPS" => [
                "recommended" => "On",
                "help" => "LBL_HTTPS_HELP_TEXT",
            ],
            "session.use_strict_mode" => [
                "recommended" => "On",
                "help" => "LBL_SESSION_USE_STRICT_MODE_HELP_TEXT",
                "current" => static::getFlag(ini_get("session.use_strict_mode")),
                "status" => ini_get("session.use_strict_mode") != 1 && stripos(ini_get("session.use_strict_mode"), "Off") !== false,
            ],
            "session.use_trans_sid" => [
                "recommended" => "Off",
                "help" => "LBL_SESSION_USE_TRANS_SID_HELP_TEXT",
                "current" => static::getFlag(ini_get("session.use_trans_sid")),
                "status" => ini_get("session.use_trans_sid") == 1 || stripos(ini_get("session.use_trans_sid"), "On") !== false,
            ],
            "session.cookie_httponly" => [
                "recommended" => "On",
                "help" => "LBL_SESSION_COOKIE_HTTPONLY_HELP_TEXT",
                "current" => static::getFlag(ini_get("session.cookie_httponly")),
                "status" => ini_get("session.cookie_httponly") != 1 && stripos(ini_get("session.cookie_httponly"), "Off") !== false,
            ],
            "session.use_only_cookies" => [
                "recommended" => "On",
                "help" => "LBL_SESSION_USE_ONLY_COOKIES_HELP_TEXT",
                "current" => static::getFlag(ini_get("session.use_only_cookies")),
                "status" => ini_get("session.use_only_cookies") != 1 && stripos(ini_get("session.use_only_cookies"), "Off") !== false,
            ],
            "expose_php" => [
                "recommended" => "Off",
                "help" => "LBL_EXPOSE_PHP_HELP_TEXT",
                "current" => static::getFlag(ini_get("expose_php")),
                "status" => ini_get("expose_php") == 1 || stripos(ini_get("expose_php"), "On") !== false,
            ],
            "Header: X-Frame-Options" => [
                "recommended" => "SAMEORIGIN",
                "help" => "LBL_HEADER_X_FRAME_OPTIONS_HELP_TEXT",
                "current" => "?",
            ],
            "Header: X-XSS-Protection" => [
                "recommended" => "1; mode=block",
                "help" => "LBL_HEADER_X_XSS_PROTECTION_HELP_TEXT",
                "current" => "?",
            ],
            "Header: X-Content-Type-Options" => [
                "recommended" => "nosniff",
                "help" => "LBL_HEADER_X_CONTENT_TYPE_OPTIONS_HELP_TEXT",
                "current" => "?",
            ],
            "Header: X-Robots-Tag" => [
                "recommended" => "none",
                "help" => "LBL_HEADER_X_ROBOTS_TAG_HELP_TEXT",
                "current" => "?",
            ],
            "Header: X-Permitted-Cross-Domain-Policies" => [
                "recommended" => "none",
                "help" => "LBL_HEADER_X_PERMITTED_CROSS_DOMAIN_POLICIES_HELP_TEXT",
                "current" => "?",
            ],
            "Header: X-Powered-By" => [
                "recommended" => "",
                "help" => "LBL_HEADER_X_POWERED_BY_HELP_TEXT",
                "current" => "?",
            ],
            "Header: Server" => [
                "recommended" => "",
                "help" => "LBL_HEADER_SERVER_HELP_TEXT",
                "current" => "?",
            ],
            "Header: Expect-CT" => [
                "recommended" => "enforce; max-age=3600",
                "help" => "LBL_HEADER_EXPECT_CT_HELP_TEXT",
                "current" => "?",
            ],
            "Header: Referrer-Policy" => [
                "recommended" => "same-origin",
                "help" => "LBL_HEADER_REFERRER_POLICY_HELP_TEXT",
                "current" => "?",
            ],
            "Header: Strict-Transport-Security" => [
                "recommended" => "max-age=31536000; includeSubDomains; preload",
                "help" => "LBL_HEADER_STRICT_TRANSPORT_SECURITY_HELP_TEXT",
                "current" => "?",
            ],
        ];
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on") {
            $directiveValues["HTTPS"]["status"] = false;
            $directiveValues["HTTPS"]["current"] = static::getFlag(true);
            $directiveValues["session.cookie_secure"] = ["recommended" => "On"];
            if (ini_get("session.cookie_secure") != "1" && stripos(ini_get("session.cookie_secure"), "On") !== false) {
                $directiveValues["session.cookie_secure"]["status"] = true;
                $directiveValues["session.cookie_secure"]["current"] = static::getFlag(false);
            } else {
                $directiveValues["session.cookie_secure"]["current"] = static::getFlag(true);
            }
        } else {
            $directiveValues["HTTPS"]["status"] = true;
            $directiveValues["HTTPS"]["current"] = static::getFlag(false);
            if (ini_get("session.cookie_secure") == "1" || stripos(ini_get("session.cookie_secure"), "On") === false) {
                $directiveValues["session.cookie_secure"]["current"] = static::getFlag(true);
                $directiveValues["session.cookie_secure"]["recommended"] = static::getFlag(false);
                $directiveValues["session.cookie_secure"]["status"] = true;
            }
        }
        stream_context_set_default(["ssl" => ["verify_peer" => false, "verify_peer_name" => false,]]);
        $prev = stream_context_get_options(stream_context_get_default());
        //Set a small timeout
        stream_context_set_default(["http" => ["timeout" => 3]]); //Seconds
        $requestUrl = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on" ? "https" : "http") . "://" . $_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"];
        $rqheaders = get_headers($requestUrl, 1);
        if ($rqheaders) {
            $headers = array_change_key_case($rqheaders, CASE_UPPER);
            if (stripos($headers[0], "200") === false) {
                $headers = [];
            }
        }
        //Restore previous default context
        stream_context_set_default($prev);
        if ($headers) {
            $directiveValues["Header: X-Frame-Options"]["status"] = strtolower($headers["X-FRAME-OPTIONS"]) !== "sameorigin";
            $directiveValues["Header: X-Frame-Options"]["current"] = $headers["X-FRAME-OPTIONS"];
            $directiveValues["Header: X-XSS-Protection"]["status"] = strtolower($headers["X-XSS-PROTECTION"]) !== "1; mode=block";
            $directiveValues["Header: X-XSS-Protection"]["current"] = $headers["X-XSS-PROTECTION"];
            $directiveValues["Header: X-Content-Type-Options"]["status"] = strtolower($headers["X-CONTENT-TYPE-OPTIONS"]) !== "nosniff";
            $directiveValues["Header: X-Content-Type-Options"]["current"] = $headers["X-CONTENT-TYPE-OPTIONS"];
            $directiveValues["Header: X-Powered-By"]["status"] = !empty($headers["X-POWERED-BY"]);
            $directiveValues["Header: X-Powered-By"]["current"] = $headers["X-POWERED-BY"];
            $directiveValues["Header: X-Robots-Tag"]["status"] = strtolower($headers["X-ROBOTS-TAG"]) !== "none";
            $directiveValues["Header: X-Robots-Tag"]["current"] = $headers["X-ROBOTS-TAG"];
            $directiveValues["Header: X-Permitted-Cross-Domain-Policies"]["status"] = strtolower($headers["X-PERMITTED-CROSS-DOMAIN-POLICIES"]) !== "none";
            $directiveValues["Header: X-Permitted-Cross-Domain-Policies"]["current"] = $headers["X-PERMITTED-CROSS-DOMAIN-POLICIES"];
            $directiveValues["Header: X-Powered-By"]["status"] = !empty($headers["X-POWERED-BY"]);
            $directiveValues["Header: X-Powered-By"]["current"] = $headers["X-POWERED-BY"];
            $directiveValues["Header: Server"]["status"] = !empty($headers["SERVER"]);
            $directiveValues["Header: Server"]["current"] = $headers["SERVER"];
            $directiveValues["Header: Referrer-Policy"]["status"] = strtolower($headers["REFERRER-POLICY"]) !== "no-referrer";
            $directiveValues["Header: Referrer-Policy"]["current"] = $headers["REFERRER-POLICY"];
            $directiveValues["Header: Expect-CT"]["status"] = strtolower($headers["EXPECT-CT"]) !== "enforce; max-age=3600";
            $directiveValues["Header: Expect-CT"]["current"] = $headers["EXPECT-CT"];
            $directiveValues["Header: Strict-Transport-Security"]["status"] = strtolower($headers["STRICT-TRANSPORT-SECURITY"]) !== "max-age=31536000; includesubdomains; preload";
            $directiveValues["Header: Strict-Transport-Security"]["current"] = $headers["STRICT-TRANSPORT-SECURITY"];
        }
        if (!isset($headers["X-ROBOTS-TAG"])) {
            unset($directiveValues["Header: X-Robots-Tag"]);
        }
        if (!isset($headers["X-POWERED-BY"])) {
            unset($directiveValues["Header: X-Powered-By"]);
        }
        if (!isset($headers["X-CONTENT-TYPE-OPTIONS"])) {
            unset($directiveValues["Header: X-Content-Type-Options"]);
        }
        if (!isset($headers["X-XSS-PROTECTION"])) {
            unset($directiveValues["Header: X-XSS-Protection"]);
        }
        if (!isset($headers["X-FRAME-OPTIONS"])) {
            unset($directiveValues["Header: X-Frame-Options"]);
        }
        if (!isset($headers["X-PERMITTED-CROSS-DOMAIN-POLICIES"])) {
            unset($directiveValues["Header: X-Permitted-Cross-Domain-Policies"]);
        }
        if (!isset($headers["X-POWERED-BY"])) {
            unset($directiveValues["Header: X-Powered-By"]);
        }
        if (!isset($headers["SERVER"])) {
            unset($directiveValues["Header: Server"]);
        }
        if (!isset($headers["REFERRER-POLICY"])) {
            unset($directiveValues["Header: Referrer-Policy"]);
        }
        if (!isset($headers["EXPECT-CT"])) {
            unset($directiveValues["Header: Expect-CT"]);
        }
        if (!isset($headers["STRICT-TRANSPORT-SECURITY"])) {
            unset($directiveValues["Header: Strict-Transport-Security"]);
        }
        if ($onlyError) {
            foreach ($directiveValues as $key => $value) {
                if (empty($value["status"])) {
                    unset($directiveValues[$key]);
                }
            }
        }
        return $directiveValues;
    }
	
    public static function GetDBStatus($con)
    {
        $sql = 'SELECT default_collation_name, default_character_set_name FROM information_schema.SCHEMATA WHERE schema_name = "' . $_SESSION["database"] . '"';
        $result = mysqli_query($con, $sql);
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                $default_collation_name = $row["default_collation_name"];
                $default_character_set_name = $row["default_character_set_name"];
            }
        } else {
            return [true, "Unknown"];
        }
        if ($default_character_set_name == "utf8" && ($default_collation_name == "utf8_persian_ci" || $default_collation_name == "utf8_general_ci" || $default_collation_name == "utf8_unicode_ci")) {
            return [false, $default_collation_name];
        } elseif ($default_character_set_name == "utf8") {
            return [false, $default_collation_name];
        } else {
            return [true, $default_collation_name];
        }
    }
	
    /*
    @param type $onlyError
    @return bool
    */
	
    public static function getDbConf($con, $onlyError = false)
    {
        $dbstatus = self::GetDBStatus($con);
        $directiveValues = [
            "Database engine" => [
                "recommended" => false,
                "current" => "mysql",
                "help" => "LBL_DB_DRIVER_HELP_TEXT",
            ],
            "Engine version" => [
                "recommended" => false,
                "current" => $con->server_info,
            ],
            "Client version" => [
                "recommended" => false,
                "current" => $con->client_info,
            ],
            "Connection status" => [
                "recommended" => false,
                "current" => $con->host_info,
            ],
            "Server information" => [
                "recommended" => false,
                "current" => $con->stat,
            ],
            "Database collation" => [
                "recommended" => "utf8_general_ci",
                "current" => $dbstatus[1],
                "status" => $dbstatus[0],
            ],
        ];
        if (empty($con->stat)) {
            unset($directiveValues["Server information"]);
        }
        if (!in_array($con->dataProvider, explode(",", $directiveValues["LBL_DB_DRIVER"]["recommended"]))) {
            $directiveValues["wait_timeout"]["status"] = true;
        }
        $directiveValues = array_merge($directiveValues, [
            "innodb_lock_wait_timeout" => [
                "recommended" => "600",
                "help" => "LBL_INNODB_LOCK_WAIT_TIMEOUT_HELP_TEXT",
            ],
            "wait_timeout" => [
                "recommended" => "600",
                "help" => "LBL_WAIT_TIMEOUT_HELP_TEXT",
            ],
            "interactive_timeout" => [
                "recommended" => "600",
                "help" => "LBL_INTERACTIVE_TIMEOUT_HELP_TEXT",
            ],
            "sql_mode" => [
                "recommended" => "",
                "help" => "LBL_SQL_MODE_HELP_TEXT",
            ],
            "log_bin_trust_function_creators" => [
                "recommended" => "On",
                "help" => "LBL_LOG_BIN_TRUST_FUNCTION_CREATORS_HELP_TEXT",
            ],
            "max_allowed_packet" => [
                "recommended" => "10 MB",
                "help" => "LBL_MAX_ALLOWED_PACKET_HELP_TEXT",
            ],
            "log_error" => [
				"recommended" => false,
			],
            "max_connections" => [
				"recommended" => false,
			],
            "thread_cache_size" => [
				"recommended" => false,
			],
            "key_buffer_size" => [
				"recommended" => false,
			],
            "query_cache_size" => [
				"recommended" => false,
			],
            "tmp_table_size" => [
				"recommended" => false,
			],
            "max_heap_table_size" => [
				"recommended" => false,
			],
            "innodb_file_per_table" => [
                "recommended" => "On",
                "help" => "LBL_INNODB_FILE_PER_TABLE_HELP_TEXT",
            ],
            "innodb_stats_on_metadata" => [
                "recommended" => "Off",
                "help" => "LBL_INNODB_STATS_ON_METADATA_HELP_TEXT",
            ],
            "innodb_buffer_pool_instances" => [
				"recommended" => false,
			],
            "innodb_buffer_pool_size" => [
				"recommended" => false,
			],
            "innodb_log_file_size" => [
				"recommended" => false,
			],
            "innodb_io_capacity_max" => [
				"recommended" => false,
			],
        ]);
        $sql = "SHOW VARIABLES";
        $result = mysqli_query($con, $sql);
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                $conf[$row["Variable_name"]] = $row["Value"];
            }
        }
        $directiveValues["max_allowed_packet"]["current"] = self::showBytes($conf["max_allowed_packet"]);
        $directiveValues["key_buffer_size"]["current"] = self::showBytes($conf["key_buffer_size"]);
        $directiveValues["query_cache_size"]["current"] = self::showBytes($conf["query_cache_size"]);
        $directiveValues["tmp_table_size"]["current"] = self::showBytes($conf["tmp_table_size"]);
        $directiveValues["max_heap_table_size"]["current"] = self::showBytes($conf["max_heap_table_size"]);
        $directiveValues["innodb_buffer_pool_size"]["current"] = self::showBytes($conf["innodb_buffer_pool_size"]);
        $directiveValues["innodb_log_file_size"]["current"] = self::showBytes($conf["innodb_log_file_size"]);
        $directiveValues["innodb_lock_wait_timeout"]["current"] = $conf["innodb_lock_wait_timeout"];
        $directiveValues["wait_timeout"]["current"] = $conf["wait_timeout"];
        $directiveValues["interactive_timeout"]["current"] = $conf["interactive_timeout"];
        $directiveValues["sql_mode"]["current"] = $conf["sql_mode"];
        $query = "select DISTINCT * from information_schema . user_privileges where  IS_GRANTABLE = 'YES' and (PRIVILEGE_TYPE like 'SUPER' or PRIVILEGE_TYPE like 'CREATE ROUTINE' or PRIVILEGE_TYPE like 'TRIGGER') and GRANTEE like '%" . $_SESSION["username"] . "%'";
        $result2 = mysqli_query($con, $query);
        if (mysqli_num_rows($result2) > 0) {
            $log_bin_trust_function_creators_current = "On";
            $log_bin_trust_function_creators_status = false;
        } else {
            $log_bin_trust_function_creators_current = "Off";
            $log_bin_trust_function_creators_status = true;
        }
        if (isset($conf["log_bin_trust_function_creators"])) {
            $directiveValues["log_bin_trust_function_creators"]["current"] = ucfirst(strtolower($conf["log_bin_trust_function_creators"]));
            $status = ucfirst(strtolower($conf["log_bin_trust_function_creators"])) === "On" ? false : true;
            $directiveValues["log_bin_trust_function_creators"]["status"] = $status === false ? false : $log_bin_trust_function_creators_status;
        } else {
            $directiveValues["log_bin_trust_function_creators"]["current"] = $log_bin_trust_function_creators_current;
            $directiveValues["log_bin_trust_function_creators"]["status"] = $log_bin_trust_function_creators_status;
        }
        $directiveValues["log_error"]["current"] = $conf["log_error"];
        $directiveValues["max_connections"]["current"] = $conf["max_connections"];
        $directiveValues["thread_cache_size"]["current"] = $conf["thread_cache_size"];
        $directiveValues["innodb_buffer_pool_instances"]["current"] = $conf["innodb_buffer_pool_instances"];
        $directiveValues["innodb_io_capacity_max"]["current"] = $conf["innodb_io_capacity_max"];
        $directiveValues["innodb_file_per_table"]["current"] = $conf["innodb_file_per_table"];
        $directiveValues["innodb_stats_on_metadata"]["current"] = $conf["innodb_stats_on_metadata"];
        if (isset($conf["tx_isolation"])) {
            $directiveValues["tx_isolation"] = ["current" => $conf["tx_isolation"], "recommended" => false];
        }
        if (isset($conf["transaction_isolation"])) {
            $directiveValues["transaction_isolation"] = ["current" => $conf["transaction_isolation"], "recommended" => false];
        }
        if ($conf["max_allowed_packet"] < 16777216) {
            $directiveValues["max_allowed_packet"]["status"] = true;
        }
        if ($conf["innodb_lock_wait_timeout"] < 600) {
            $directiveValues["innodb_lock_wait_timeout"]["status"] = true;
        }
        if ($conf["wait_timeout"] < 600) {
            $directiveValues["wait_timeout"]["status"] = true;
        }
        if ($conf["interactive_timeout"] < 600) {
            $directiveValues["interactive_timeout"]["status"] = true;
        }
        if (!empty($conf["sql_mode"]) && (strpos($conf["sql_mode"], "STRICT_TRANS_TABLE") !== false || strpos($conf["sql_mode"], "ONLY_FULL_GROUP_BY") !== false)) {
            $directiveValues["sql_mode"]["status"] = true;
        }
        if ($onlyError) {
            foreach ($directiveValues as $key => $value) {
                if (empty($value["status"])) {
                    unset($directiveValues[$key]);
                }
            }
        }
        return $directiveValues;
    }
	
    /*
    Get system details.
    @return array
    */
	
    public static function getSystemInfo()
    {
        $root_directory = getcwd();
        $ini = static::getPhpIniConf();
        $dir = str_replace("\\", "/", $root_directory);
        $params = ["Local directory" => $root_directory];
        if (self::$disaplayCapacity) {
            $params["Capacity"] = "Total" . ": " . (!function_exists("disk_total_space") || !function_exists("disk_free_space") || self::exec_disabled("disk_total_space") || self::exec_disabled("disk_free_space") ? "Unknown" : self::showBytes(disk_total_space($dir))) . ", " . "Used" . ": " . (!function_exists("disk_total_space") || !function_exists("disk_free_space") || self::exec_disabled("disk_total_space") || self::exec_disabled("disk_free_space") ? "Unknown" : self::showBytes(disk_total_space($dir) - disk_free_space($dir))) . ", " . "Free" . ": " . (!function_exists("disk_free_space") || self::exec_disabled("disk_free_space") ? "Unknown" : self::showBytes(disk_free_space($dir)));
        }
        $params["Operating system"] = function_exists("php_uname") ? php_uname() : "Unknown";
        if (isset($ini["SAPI"])) {
            $params["Server API"] = $ini["SAPI"];
        }
        if (isset($ini["LOG_FILE"])) {
            $params["Logs"] = $ini["LOG_FILE"];
        }
        if (isset($ini["INI_FILE"])) {
            $params["PHP configuration"] = $ini["INI_FILE"];
        }
        return $params;
    }
	
    /*
    Get php.ini configuration.
    @return array
    */
	
    public static function getPhpIniConf()
    {
        $iniAll = @ini_get_all();
        $values = [];
        foreach (static::getStabilitIniConf() as $key => $value) {
            if (isset($iniAll[$key])) {
                $values[$key] = $iniAll[$key]["local_value"];
            }
        }
        $values["PHP"] = PHP_VERSION;
        $values["SAPI"] = PHP_SAPI;
        $values["INI_FILE"] = !function_exists("php_ini_loaded_file") || self::exec_disabled("php_ini_loaded_file") ? "Unable to show for security reasons" : @php_ini_loaded_file();
        $values["INI_FILES"] = !function_exists("php_ini_scanned_files") || self::exec_disabled("php_ini_scanned_files") ? "Unable to show for security reasons" : @php_ini_scanned_files();
        $values["LOG_FILE"] = $iniAll["error_log"]["local_value"];
        return $values;
    }
	
    /*
    Validate number greater than recommended.
    @param mixed $row
    @return mixed
    */
	
    public static function validateGreater($row, $isCli)
    {
        if ((int) $row["current"] > 0 && (int) $row["current"] < $row["recommended"]) {
            $row["incorrect"] = true;
        }
        return $row;
    }
	
    /*
    Validate number in bytes greater than recommended.
    @param mixed $row
    @return mixed
    */
	
    public static function validateGreaterMb($row, $isCli)
    {
        if (self::parseBytes($row["current"]) < self::parseBytes($row["recommended"])) {
            $row["incorrect"] = true;
        }
        $row["current"] = self::showBytes($row["current"]);
        $row["recommended"] = self::showBytes(self::parseBytes($row["recommended"]));
        return $row;
    }
	
    /*
    Validate on and off values.
    @param mixed $row
    @return mixed
    */
	
    public static function validateOnOff($row, $isCli)
    {
        static $map = ["on" => true, "true" => true, "off" => false, "false" => false];
        $current = isset($map[strtolower($row["current"])]) ? $map[strtolower($row["current"])] : (bool) $row["current"];
        if ($current !== ($row["recommended"] === "On")) {
            $row["incorrect"] = true;
        }
        if (is_bool($current)) {
            $row["current"] = $current ? "On" : "Off";
        } else {
            $row["current"] = static::getFlag($row["current"]);
        }
        return $row;
    }
	
    /*
    Validate on, off and int values.
    @param mixed $row
    @return mixed
    */
	
    public static function validateOnOffInt($row, $isCli)
    {
        if (!$isCli && strtolower($row["current"]) !== "on") {
            $row["incorrect"] = true;
        }
        return $row;
    }
	
    /*
    Validate equal value "recommended == current".
    @param mixed $row
    @return mixed
    */
	
    public static function validateEqual($row, $isCli)
    {
        if ((int) $row["current"] !== (int) $row["recommended"]) {
            $row["incorrect"] = true;
        }
        return $row;
    }
	
    /*
    Validate php version.
    @param mixed $row
    @return mixed
    */
	
    public static function validatePhp($row, $isCli)
    {
        try {
            //sdie(v);
            $newest = static::getNewestPhpVersion($row["max"]);
        } catch (Exception $exc) {
            $newest = false;
        }
        if ($newest) {
            $row["recommended"] = $newest;
        }
        if (version_compare($row["current"], str_replace("x", 0, $row["recommended"]), "<")) {
            $row["incorrect"] = true;
        }
        return $row;
    }
	
    public static function GetIonCubeLoaderVersion()
    {
        if (function_exists("ioncube_loader_iversion")) {
            $version = ioncube_loader_iversion();
            $version = sprintf("%d.%d.%d", $version / 10000, ($version / 100) % 100, $version % 100);
            return $version;
        }
        return "Not found!";
    }
	
    public static function validateIonCubeInstalledVersion($row, $isCli)
    {
        if (version_compare(PHP_VERSION, "5.6.0") >= 0 && version_compare(PHP_VERSION, "8.0.0") < 0) {
            $iconcube_version = "5.6";
        } elseif (version_compare(PHP_VERSION, "7.4.0") >= 0) {
            $iconcube_version = "7.4";
        } elseif (version_compare(PHP_VERSION, "5.6.0") < 0) {
            $iconcube_version = "5.4";
        }
        $row["current"] = $iconcube_version;
        return $row;
    }
	
    public static function validateIonCube($row, $isCli)
    {
        $version = self::GetIonCubeLoaderVersion();
        $row["current"] = $version;
        $row["recommended"] = self::$latest_ioncube;
        if (version_compare($version, "12.0.0") <= 0) {
            $row["incorrect"] = true;
        }
        return $row;
    }
	
    /*
    Validate date timezone.
    @param mixed $row
    @return mixed
    */
	
    public static function validateTimezone($row, $isCli)
    {
        if (ini_get("date.timezone")) {
            $default_timezone = ini_get("date.timezone");
        } else {
            $default_timezone = "Asia/Tehran";
        }
        $row["current"] = $default_timezone;
        try {
            new DateTimeZone($row["current"]);
        } catch (Exception $e) {
            $row["current"] = "Invalid TimeZone " . $row["current"];
            $row["incorrect"] = true;
        }
        return $row;
    }
	
    /*
    Convert error number to string.
    @param int $value
    @return string[]
    */
	
    public static function error2string($value)
    {
        $levels = [];
        if (($value & E_ALL) == E_ALL) {
            $levels[] = "E_ALL";
            $value &= ~E_ALL;
        }
        foreach (static::$levelNames as $level => $name) {
            if (($value & $level) == $level) {
                $levels[] = $name;
            }
        }
        return $levels;
    }
	
    /*
    Validate error reporting.
    @param mixed $row
    @return mixed
    */
	
    public static function validateErrorReporting($row, $isCli)
    {
        $errorReporting = stripos(error_reporting(), "_") === false ? self::error2string(error_reporting()) : error_reporting();
        if (in_array("E_NOTICE", $errorReporting) || in_array("E_ALL", $errorReporting)) {
            $row["incorrect"] = true;
        }
        $row["current"] = implode(" | ", $errorReporting);
        return $row;
    }
	
    /*
    Validate error reporting.
    @param mixed $row
    @return mixed
    */
	
    public static function validateDisableFunctions($row, $isCli)
    {
        $functions = [
            "exec",
            "shell_exec",
            "system",
            "ini_set",
            "passthru",
            "popen",
            "curl_exec",
            "readfile",
            "eval",
            "ftp_connect",
            "php_uname",
        ];
        $disabled = explode(",", @ini_get("disable_functions"));
        $row["current"] = implode(" , ", $disabled);
        foreach ($disabled as $item) {
            if (in_array($item, $functions)) {
                $row["incorrect"] = true;
            }
        }
        return $row;
    }
	
    /*
    Get actual version of PHP.
    @param string $version eg. "5.6", "7.1"
    @return string eg. 7.1.12
    */
	
    private static function getNewestPhpVersion($version)
    {
        return false;
        if (!class_exists("Requests")) {
            return false;
        }
        $resonse = Requests::get("http://php.net/releases/index.php?json&max=10&version=" . $version[0]);
        $data = array_keys((array) \App\Json::decode($resonse->body));
        natsort($data);
        foreach (array_reverse($data) as $ver) {
            if (strpos($ver, $version) === 0) {
                return $ver;
            }
        }
        return false;
    }
	
    /*
    Get ini flag.
    @param mixed $val
    @return string
    */
	
    private static function getFlag($val)
    {
        if ($val == "On" || $val == 1 || stripos($val, "On") !== false) {
            return "On";
        }
        return "Off";
    }
	
    //By Hamid
    public static function getRemoteIP($onlyIP = false)
    {
        $address = $_SERVER["REMOTE_ADDR"];
        //Append the NGINX X-Real-IP header, if set
        if (!empty($_SERVER["HTTP_X_REAL_IP"])) {
            $remote_ip[] = "X-Real-IP: " . $_SERVER["HTTP_X_REAL_IP"];
        }
        //Append the X-Forwarded-For header, if set
        if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $remote_ip[] = "X-Forwarded-For: " . $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        if (!empty($remote_ip) && $onlyIP == false) {
            $address .= "(" . implode(",", $remote_ip) . ")";
        }
        return $address;
    }
	
    public static function parseBytes($str)
    {
        if (is_numeric($str)) {
            return floatval($str);
        }
        if (preg_match("/([0-9\.]+)\s*([a-z]*)/i", $str, $regs)) {
            $bytes = floatval($regs[1]);
            switch (strtolower($regs[2])) {
                case "g":
                case "gb":
                    $bytes *= 1073741824;
                    break;
                case "m":
                case "mb":
                    $bytes *= 1048576;
                    break;
                case "k":
                case "kb":
                    $bytes *= 1024;
                    break;
            }
        }
        return floatval($bytes);
    }
	
    public static function showBytes($bytes, &$unit = null)
    {
        $bytes = self::parseBytes($bytes);
        if ($bytes >= 1073741824) {
            $unit = "GB";
            $gb = $bytes / 1073741824;
            $str = sprintf($gb >= 10 ? "%d " : "%.1f ", $gb) . $unit;
        } elseif ($bytes >= 1048576) {
            $unit = "MB";
            $mb = $bytes / 1048576;
            $str = sprintf($mb >= 10 ? "%d " : "%.1f ", $mb) . $unit;
        } elseif ($bytes >= 1024) {
            $unit = "KB";
            $str = sprintf("%d ", round($bytes / 1024)) . $unit;
        } else {
            $unit = "B";
            $str = sprintf("%d ", $bytes) . $unit;
        }
        return $str;
    }
	
    public static function getMaxUploadSize()
    {
        //Find max filesize value
        $maxFileSize = self::parseBytes(ini_get("upload_max_filesize"));
        $maxPostSize = self::parseBytes(ini_get("post_max_size"));
        if ($maxPostSize && $maxPostSize < $maxFileSize) {
            $maxFileSize = $maxPostSize;
        }
        return $maxFileSize;
    }
	
    public static function exec_disabled($value = "exec")
    {
        $disabled = explode(",", ini_get("disable_functions"));
        return in_array($value, $disabled);
    }
}

?>