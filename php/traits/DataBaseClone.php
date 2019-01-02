<?php
/**
 * Created by PhpStorm.
 * User: demonius
 * Date: 30.12.18
 * Time: 9:41
 */

namespace Astkon\Traits;

use Astkon\GlobalConst;

trait DataBaseClone
{
    /**
     * https://dev.mysql.com/doc/refman/5.6/en/mysqldump-copying-database.html
     * @param string $fromDb
     * @param string $toDb
     */
    public static function CloneDataBase(string $fromDb, string $toDb) {
        $dumpFileName = getcwd() . DIRECTORY_SEPARATOR . GlobalConst::TmpDirName . DIRECTORY_SEPARATOR . $fromDb .'_dump.sql';
        var_dump($dumpFileName);
        shell_exec('mysqldump ' . $fromDb . ' > ' . $dumpFileName);
        shell_exec('mysqladmin drop ' . $toDb);
        die();
//        shell_exec('mysqladmin create ' . $toDb);
//        shell_exec('mysql ' . $toDb . ' < ' . $dumpFileName);
//        shell_exec('rm ' . $dumpFileName);

    }
}