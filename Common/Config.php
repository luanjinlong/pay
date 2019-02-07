<?php

namespace Common;

class Config implements \ArrayAccess
{
    /**
     * 配置文件名
     * @var string
     */
    protected $path_name;

    /**
     * 配置缓存变量
     * @var array
     */
    protected $configs = array();

    public function __construct($path_name)
    {
        // 如果传入的文件名在数组中不存在，则加载此配置文件，并存入缓存变量
        if (!in_array($path_name, $this->configs)) {
            $this->path_name = $path_name;
            $file_path = BASEDIR . '/Config/' . $this->path_name . '.php';
            if (!file_exists($file_path)) {
                throw new \Exception('配置文件' . $file_path . '不存在');
            }
            $this->configs[$this->path_name] = require $file_path;
        }
    }

    /**
     * 获取配置文件的数据
     * @param bool $file_name
     * @return array
     */
    public function getConfigs($file_name = false)
    {
        if (isset($file_name)) {
            if (!isset($this->configs[$file_name])) {
                return [];
            }
            return $this->configs[$file_name];
        }
        return $this->configs;
    }

    /**
     * 获取数组的 $key
     * @param mixed $key
     * @return mixed
     * @throws \Exception
     */
    public function offsetGet($key)
    {
        if (isset($this->configs[$this->path_name][$key])) {
            return $this->configs[$this->path_name][$key];
        }
        throw new \Exception($this->path_name . '中' . $key . '的配置不存在');
    }

    /**
     * @param mixed $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return isset($this->configs[$this->path_name][$key]);
    }

    /**
     * @param mixed $key
     * @param mixed $value
     * @throws \Exception
     */
    public function offsetSet($key, $value)
    {
        throw new \Exception('cant not write config file');
    }

    /**
     * 删除数组的 $key
     * @param mixed $key
     */
    public function offsetUnset($key)
    {
        unset($this->configs[$this->path_name][$key]);
    }

}