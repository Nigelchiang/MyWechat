<?php
/**
 * Text.php
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

namespace Overtrue\Wechat\Messages;

/**
 * 文本消息
 *
 * @property string $content
 */
class Text extends BaseMessage {

    /**
     * 属性
     *
     * @var array
     */
    //这里覆盖了父类的的属性，添加了content属性，那么祖父的attributes有什么用呢？
    protected $properties = array('content');

    /**
     * 生成主动消息数组
     *
     * @return array
     */
    public function toStaff() {

        return array(
            'text' => array(
                'content' => $this->content,
            ),
        );
    }

    /**
     * 生成回复消息数组
     *
     * @return array
     */
    //为了与其他回复xml中的属性使用merge方法，所以即使这里text只有一个content属性，也要把他包在数组里，其他的回复类型可能有其他特殊的属性
    public function toReply() {

        return array(
            'Content' => $this->content,
        );
    }
}
