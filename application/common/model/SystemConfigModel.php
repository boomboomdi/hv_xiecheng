<?php
/**
 * Created by PhpStorm.
 * User: wmbl1228
 * Date: 2018/12/26
 * Time: 22:59
 */

namespace app\common\model;

use think\Model;
use think\Db;

class SystemConfigModel extends Model
{

    /**
     * 订单冻结期
     * @return int
     */
    public static function getOrderLockTime()
    {
        try {
            $where[] = ["configName", "=", "orderLockTime"];
            $where[] = ["status", "=", 1];
            $config = Db::table('bsa_system_config')
                ->where($where)
                ->find();
            if (isset($config['configContent']) && !empty($config['configContent'])) {
                return (int)$config['configContent'];
            }
            return 600;
        } catch (\Exception $exception) {
            return 600;
        } catch (\Error $error) {
            return 600;
        }
    }

    /**
     * 订单冻结期
     * @return int
     */
    public static function getOrderHxLockTime()
    {
        try {
            $where[] = ["configName", "=", "orderHxLockTime"];
            $where[] = ["status", "=", 1];
            $config = Db::table('bsa_system_config')
                ->where($where)
                ->find();
            if (isset($config['configContent']) && !empty($config['configContent'])) {
                return (int)$config['configContent'];
            }
            return 600;
        } catch (\Exception $exception) {
            return 600;
        } catch (\Error $error) {
            return 600;
        }
    }

    /**
     * 获取订单自动查询时间(第三次-第二次间隔)
     * @return int
     */
    public static function getAutoCheckOrderTime()
    {
        try {
            $where[] = ["configName", "=", "autoCheckOrderTime"];
            $where[] = ["status", "=", 1];
            $config = Db::table('bsa_system_config')
                ->where($where)
                ->find();
            if (isset($config['configContent']) && !empty($config['configContent'])) {
                return (int)$config['configContent'];
            }
            return 300;
        } catch (\Exception $exception) {
            return 300;
        } catch (\Error $error) {
            return 300;
        }
    }

    /**
     * 获取订单自动查询时间(第三次-第二次间隔)
     * @return int
     */
    public static function getOrderShowTime()
    {
        try {
            $where[] = ["configName", "=", "orderShowTime"];
            $where[] = ["status", "=", 1];
            $config = Db::table('bsa_system_config')
                ->where($where)
                ->find();
            if (isset($config['configContent']) && !empty($config['configContent'])) {
                return (int)$config['configContent'];
            }
            return 300;
        } catch (\Exception $exception) {
            return 300;
        } catch (\Error $error) {
            return 300;
        }
    }

    /**
     * 上传是否查单
     * @return bool
     */
    public static function getCheckHXOrderAmount()
    {
        try {
            $where[] = ["configName", "=", "checkHXOrderAmount"];
            $where[] = ["status", "=", 1];
            $config = Db::table('bsa_system_config')
                ->where($where)
                ->find();
            if (isset($config['configContent']) && !empty($config['configContent']) && $config['configContent'] == 'true') {
                return true;
            }
            return false;
        } catch (\Exception $exception) {
            return false;
        } catch (\Error $error) {
            return false;
        }
    }
}
