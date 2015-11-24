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
    //todo 继承的attributes属性是干什么的？这两个有什么关系？
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

        //todo this->to这样能直接访问到数组里的元素吗？？
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

        //TODO
    }

    /**
     * 获取默认的消息类型名称
     *
     * @return string
     */
    public function getDefaultMessageType() {

        //获取当前对象的类的名字(会带有//分隔符)，将其按照分隔符分开，取出最后一个字符串，也就是消息的类型
        //要求类的命名要跟微信回复要求中的MsgType一致

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
    protected function validate($attribute, $value) {

        $properties = array_merge($this->baseProperties, $this->properties);

        return in_array($attribute, $properties, true);
    }
}
