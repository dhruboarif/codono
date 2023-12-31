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
namespace Behavior;
/**
 * System behavior extension：runtimeinformationdisplay
 */
class ShowRuntimeBehavior
{

    // Behavior extensionofExecution entrymust berun
    public function run(&$content)
    {
        if (C('SHOW_RUN_TIME')) {
            if (false !== strpos($content, '{__NORUNTIME__}')) {
                $content = str_replace('{__NORUNTIME__}', '', $content);
            } else {
                $runtime = $this->showTime();
                if (strpos($content, '{__RUNTIME__}'))
                    $content = str_replace('{__RUNTIME__}', $runtime, $content);
                else
                    $content .= $runtime;
            }
        } else {
            $content = str_replace(array('{__NORUNTIME__}', '{__RUNTIME__}'), '', $content);
        }
    }

    /**
     * displayruntime,dataStorehouseoperating,Cachefrequency,RAMuseinformation
     * @access private
     * @return string
     */
    private function showTime()
    {
        // displayruntime
        G('beginTime', $GLOBALS['_beginTime']);
        G('viewEndTime');
        $showTime = 'Process: ' . G('beginTime', 'viewEndTime') . 's ';
        if (C('SHOW_ADV_TIME')) {
            // displaydetailedruntime
            $showTime .= '( Load:' . G('beginTime', 'loadTime') . 's Init:' . G('loadTime', 'initTime') . 's Exec:' . G('initTime', 'viewStartTime') . 's Template:' . G('viewStartTime', 'viewEndTime') . 's )';
        }
        if (C('SHOW_DB_TIMES')) {
            // displaydatabaseoperatingfrequency
            $showTime .= ' | DB :' . N('db_query') . ' queries ' . N('db_write') . ' writes ';
        }
        if (C('SHOW_CACHE_TIMES')) {
            // displayCacheRead and write times
            $showTime .= ' | Cache :' . N('cache_read') . ' gets ' . N('cache_write') . ' writes ';
        }
        if (MEMORY_LIMIT_ON && C('SHOW_USE_MEM')) {
            // displayMemory overhead
            $showTime .= ' | UseMem:' . number_format((memory_get_usage() - $GLOBALS['_startUseMems']) / 1024) . ' kb';
        }
        if (C('SHOW_LOAD_FILE')) {
            $showTime .= ' | LoadFile:' . count(get_included_files());
        }
        if (C('SHOW_FUN_TIMES')) {
            $fun = get_defined_functions();
            $showTime .= ' | CallFun:' . count($fun['user']) . ',' . count($fun['internal']);
        }
        return $showTime;
    }
}