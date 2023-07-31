<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: support@codono.com
// +----------------------------------------------------------------------

/**
 * ThinkPHP defaultofdebuggingMode profile
 */
defined('THINK_PATH') or exit();
// debuggingmodethe followingdefaultSet up allowableApplication configuration directory下Againdefinition debug.php cover
return array(
    'LOG_RECORD' => false,  // Logging
    'LOG_EXCEPTION_RECORD' => false,    // whetherrecordingabnormalinformationJournal
    'LOG_LEVEL' => 'EMERG,ALERT,CRIT,ERR,WARN,NOTIC,INFO,DEBUG,SQL',  // allowrecordingofLog Level
    'DB_FIELDS_CACHE' => false, // Field cache information
    'DB_DEBUG' => false, // Opendebuggingmode recordingSQLJournal
    'TMPL_CACHE_ON' => true,        // whetherOpentemplateCompileCache,SetfalseThen every timeAgainCompile
    'TMPL_STRIP_SPACE' => false,       // whetherRemovalTemplate filesinsidehtmlSpaces and line breaks
    'SHOW_ERROR_MSG' => false,    // displayError Messages
    'URL_CASE_INSENSITIVE' => false,  // URLCase sensitive
    'SHOW_PAGE_TRACE' => false,
);