<?php
/**
 * 封装Yii 2.0.10常用操作变量，简化操作
 * Created by lobtao.
 * Date: 2016/10/21
 * UpDate: 2016/12/23
 */
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

/**
 * 当前APP对象
 * @return \yii\console\Application|\yii\web\Application
 */
function APP() {
    return \Yii::$app;
}

/**
 * 通过sql创建一个Command
 * @param string|null $sql
 * @return \yii\db\Command
 */
function M($sql = null) {
    return \Yii::$app->db->createCommand($sql);
}

/**
 * 通过表名创建一个Query
 * @param null $tableName
 * @return $this|\yii\db\Query
 */
function Q($tableName = null) {
    $query = new \yii\db\Query();
    if ($tableName) {
        return $query->from($tableName);
    } else {
        return $query;
    }
}

/**
 * 当前数据库连接
 * @return \yii\db\Connection
 */
function DB() {
    return \Yii::$app->db;
}

/**
 * 生成URL地址
 * @param        $route
 * @param array $params
 * @param string $ampersand
 * @return string
 */
function U($route, $params = array(), $ampersand = '&') {
    return \Yii::$app->urlManager->createUrl($route, $params, $ampersand);
}

/**
 * 当前网址根路径
 * @param bool|true $absolute
 * @return string
 */
function ROOT($absolute = true) {
    //return \Yii::$app->urlManager->getBaseUrl($absolute);
    return \Yii::getAlias('@web');
}

/**
 * 获取当前的URL参数
 * 仅可使用Yii 1.xx
 * @param      $name
 * @param null $defaultValue
 * @return mixed
 */
function P($name, $defaultValue = null) {
    if (\Yii::$app->request->isGet)
        return \Yii::$app->request->get($name, $defaultValue);
    if (\Yii::$app->request->isPost)
        return \Yii::$app->request->post($name, $defaultValue);
}

/**
 * 文件快速读写
 * @param        $name
 * @param string $value
 * @return array|bool|int|mixed|string
 */
function F($name, $value = '') {
    static $_cache = array();
    $basePath = \Yii::$app->basePath . DS . 'runtime' . DS . 'data';

    $filename = $basePath . DS . $name . '.php';
    if ('' !== $value) {
        if (is_null($value)) {
            // 删除缓存
            if (file_exists($filename))
                return false !== strpos($name, '*') ? array_map("unlink", glob($filename)) : unlink($filename);
            else
                return true;
        } else {
            // 缓存数据
            $dir = dirname($filename);
            // 目录不存在则创建
            if (!is_dir($dir))
                mkdir($dir, 0755, true);
            $_cache[$name] = $value;
            return file_put_contents($filename, "<?php\treturn " . var_export($value, true) . ";?>");
        }
    }
    if (isset($_cache[$name]))
        return $_cache[$name];
    // 获取缓存数据
    if (is_file($filename)) {
        $value = include $filename;
        $_cache[$name] = $value;
    } else {
        $value = false;
    }
    return $value;
}

/**
 * 根据模型校验输入值
 * @param \yii\base\Model $model
 * @param array $params
 * @param bool|true $showException
 * @return string
 * @throws Exception
 */
function V($model, $scenario, $params, $showException = true) {
    //校验输入值
    $model->setAttributes($params,false);
    $model->setScenario($scenario);
    $msg = '';
    if (!$model->validate()) {
        $errors = $model->getErrors();

        foreach ($errors as $key => $value) {
            $msg = $value[0];
            break;
        }
        if ($showException) throw new \Exception($msg);
    }
    return $msg;
}

/**
 * 打印输出变量
 * @param $target
 */
function dump($target) {
    \yii\helpers\VarDumper::dump($target, 10, true);
    echo '<br/>';
}

/*
function mk_dir($dir, $mode = 0777) {
    if (is_dir($dir) || @mkdir($dir, $mode)) return true;
    if (!mk_dir(dirname($dir), $mode)) return false;
    return @mkdir($dir, $mode);
}
*/
