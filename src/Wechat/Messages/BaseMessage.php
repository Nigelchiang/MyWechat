<?php
/**
 * BaseMessage.php
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
//这个SDK面向对象的观念很强，继承用得很多，所有的参数都是用数组传递的，理解上有难度，很多地方的写法要根据他的子类来理解
//命名空间的用法也很溜，的确值得我学习

namespace Overtrue\Wechat\Messages;

use Overtrue\Wechat\Utils\MagicAttributes;
use Overtrue\Wechat\Utils\XML;

/**
 * 消息基类
 *
 * @property string $from
 * @property string $to
 * @property string $staff
 *
 * @method BaseMessage to($to)
 * @method BaseMessage from($from)
 * @method BaseMessage staff($staff)
 * @method array       toStaff()
 * @method array       toReply()
 * @method array       toBroadcast()
 */
abstract class BaseMessage extends MagicAttributes {

    /**
     * 允许的属性
     *
     * @var array
     */
    // 继承的attributes属性是干什么的？这两个有什么关系？
    //properties是给子类添加属性用的，
    protected $properties = array();

    /**
     * 基础属性
     *
     * @var array
     */
    protected $baseProperties
        = array(
            'from',
            'to',
            'to_group',
            'to_all',
            'staff',
        );

    /**
     * 生成用于主动推送的数据
     *
     * @return array
     */
    public function buildForStaff() {

        //由特定功能的子类实现toStaff方法，这里只是提供一个生成客服回复数据的方法

        if (!method_exists($this, 'toStaff')) {
            throw new \Exception(__CLASS__ . '未实现此方法：toStaff()');
        }

        // this->to这样能直接访问到数组里的元素吗？？ =>解答：这里就是那个MagicAttributes类的作用了，通过魔术get和set做到的效果
        //但是魔术的访问是从attributes里面获取的呀，这里是怎么从baseProperties获取的呢？
        //这并不是从baseAttributes里面获取的呀，这是抽象类的什么特性吗？
        $base = array(
            'touser'  => $this->to,
            'msgtype' => $this->getDefaultMessageType(),
        );
        if (!empty($this->staff)) {
            $base['customservice'] = array('kf_account' => $this->staff);
        }

        return array_merge($base, $this->toStaff());
    }

    /**
     * 生成用于回复的数据
     *
     * @return array
     */
    //还是有点诡异，数据是怎么填充的呢？你这也没有构造方法呀
    //这个是BaseMessage，作为消息的父类，只填充了基本的必须属性，具体的toReply里面有什么其他的属性，由其他的各个消息子类来实现、填充
    //比如说，在text子类里面，toreply就会返回content，其他的也是一样
    public function buildForReply() {

        if (!method_exists($this, 'toReply')) {
            throw new \Exception(__CLASS__ . '未实现此方法：toReply()');
        }

        $base = array(
            'ToUserName'   => $this->to,
            'FromUserName' => $this->from,
            'CreateTime'   => time(),
            'MsgType'      => $this->getDefaultMessageType(),
        );

        //生成用于回复的xml数据，需要的基本属性已经传过去了
        return XML::build(array_merge($base, $this->toReply()));
    }

    /**
     * 生成群发的数据
     *
     * @return array
     */
    public function buildForBroadcast() {

        if (!method_exists($this, 'toBroadcast')) {
            throw new \Exception(__CLASS__ . '未实现此方法：toBroadcast()');
        }

        //
    }

    /**
     * 获取默认的消息类型名称
     *
     * @return string
     */
    public function getDefaultMessageType() {

        //获取当前对象的类的名字(会带有//分隔符)，将其按照分隔符分开，取出最后一个字符串，也就是消息的类型
        //要求类的命名要跟微信回复要求中的MsgType一致
        //php里面的命名空间为什么要用反斜线呢…真是反人类…

        $class = explode('\\', get_class($this));

        return strtolower(array_pop($class));
    }

    /**
     * 验证
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    //果然是这样！！attributes是用户通过魔术方法call添加的属性，BaseProperties是父类的基本属性，
    //properties是子类添加的个性属性，这个validate将baseProperties和properties和起来，就是这个具体的子类的所有属性，
    //检查用户添加的属性是否在这个数组里，如果在就是合法的，可以设置属性
    //卧槽，真的真的真的真的很烧脑子呀！
    protected function validate($attribute, $value) {

        $properties = array_merge($this->baseProperties, $this->properties);

        return in_array($attribute, $properties, true);
    }
}
