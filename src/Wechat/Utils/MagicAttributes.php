<?php
/**
 * MagicAttributes.php
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace Overtrue\Wechat\Utils;

use InvalidArgumentException;

//这个工具的作用就是把所有的属性放在一个数组里，而且还管理还很方便
//跟分散的没什么区别

/**
 * 用于操作通用数组式属性的工具类
 */
abstract class MagicAttributes {

    /**
     * 允许设置的属性名称
     *
     * @var array
     */
    protected $attributes = array();

    /**
     * 方法名转换缓存
     *
     * @var array
     */
    protected static $snakeCache = array();

    /**
     * 设置属性
     *
     * @param string $attribute
     * @param string $value
     */
    public function setAttribute($attribute, $value) {
        return $this->with($attribute, $value);
    }

    /**
     * 设置属性
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return MagicAttributes
     */
    public function with($attribute, $value) {
        $attribute = $this->snake($attribute);

        //感觉这个validate也很重要！自己写的properties是允许的属性，attributes是用户添加的属性，
        //通过这个validate来验证，用户添加的属性是否在attributes里面！
        //卧槽，怎么这么绕弯子呀！这么做的好处是什么呢？
        if (!$this->validate($attribute, $value)) {
            throw new InvalidArgumentException("错误的属性值'{$attribute}'");
        }

        $this->attributes[$attribute] = $value;

        //太尼玛屌！这个就是我把那些属性串起来的关键！
        //我用一个make('text')，只是new 了一个Text对象，然后后面的->text()其实是通过了__call然后执行with，然后的是当前对象
        //所以说，所有我自己写的函数比如说text的返回值都是消息对象本身
        return $this;
    }

    /**
     * 生成数组
     *
     * @return array
     */
    public function toArray() {
        return $this->attributes;
    }

    /**
     * 验证
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    protected function validate($attribute, $value) {
        return true;
    }

    /**
     * 调用不存在的方法
     *
     * @param string $method
     * @param array  $args
     *
     * @return MagicAttributes
     */
    public function __call($method, $args) {
        if (stripos($method, 'with') === 0) {
            $method = substr($method, 4);
        }
        //这个call才是关键呀！我用的那些make()->text()都是通过这个调用的呀！
        //我真的是佩服死了…他们是怎么想到的呀…卧槽！
        //make('text')->text()这样后，就把text这个函数变成了attributes里面的属性啊！而且值也设好了！
        return $this->with($method, array_shift($args));
    }

    //soga 我一直搞不懂的直接读array里面的元素的解释就在这里啊
    //这个魔术读取方法，如果类中没有这个属性的话，就从attributes这个数组里面返回值
    //这样的做法是什么呢？所有的属性传递都打包到这样一个数组里面了吗？
    /**
     * 魔术读取
     *
     * @param string $property
     */
    public function __get($property) {
        return !isset($this->attributes[$property]) ? null : $this->attributes[$property];
    }

    //注意这里的get和set方法，后面的子类很多都是用了这两个方法，搞得我一直看不懂
    /**
     * 魔术写入
     *
     * @param string $property
     * @param mixed  $value
     */
    public function __set($property, $value) {
        return $this->with($property, $value);
    }

    /**
     * 转换为下划线模式字符串
     *
     * @param string $value
     * @param string $delimiter
     *
     * @return string
     */
    protected function snake($value, $delimiter = '_') {
        $key = $value . $delimiter;

        /**
         * 如果key已经缓存，则直接读取缓存，否则更新缓存
         * 将传入的属性值转换为下划线模式的字符串，转换后存入缓存中
         */
        if (isset(static::$snakeCache[$key])) {
            return static::$snakeCache[$key];
        }
        //如果字符串中含有大写字符的话就替换为连字符模式
        if (!ctype_lower($value)) {
            //如果在字符串中有大写字符，就在大写字母前加上下划线，然后全部转为小写
            //差不多懂了，还有两个东西不懂，?=是什么意思？ $1为什么在文档里没有说明？
            //懂了，这个叫零宽断言，还是有点复杂的…?=
            //零宽度正预测先行断言，它断言自身(这个自身也就是指匹配的东西，也就是正则表达式的内容，也就是这个正则表达式捕获的东西)出现的位置的后面               能匹配表达式exp。
            //这里第一个子表达式匹配一个非空白字符
            //第二个子表达式断言这里会出现大写字母的，合起来就是：匹配一个大写字母的前一个字符
            //这里的零宽断言，都是非获取匹配，这里不会被replace取代
            $value = strtolower(preg_replace('/(.)(?=[A-Z])/', '$1' . $delimiter, $value));
        }

        return static::$snakeCache[$key] = $value;
    }
}
