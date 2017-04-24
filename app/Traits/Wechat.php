<?php
/**
 * Created by PhpStorm.
 * User: Felix
 * Date: 2017/4/9
 * Time: 下午5:34
 */

namespace App\Traits;

use App\Mail\WechatTips;
use App\Model\Admin\Goods;
use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Message\Entity\Message;
use Hanson\Vbot\Message\Entity\Image;
use Hanson\Vbot\Message\Entity\Text;
use Hanson\Vbot\Message\Entity\Emoticon;
use Hanson\Vbot\Message\Entity\Location;
use Hanson\Vbot\Message\Entity\Video;
use Hanson\Vbot\Message\Entity\Voice;
use Hanson\Vbot\Message\Entity\Recall;
use Hanson\Vbot\Message\Entity\RedPacket;
use Hanson\Vbot\Message\Entity\Transfer;
use Hanson\Vbot\Message\Entity\Recommend;
use Hanson\Vbot\Message\Entity\Share;
use Hanson\Vbot\Message\Entity\Official;
use Hanson\Vbot\Message\Entity\Touch;
use Hanson\Vbot\Message\Entity\Mina;
use Hanson\Vbot\Message\Entity\RequestFriend;
use Hanson\Vbot\Message\Entity\GroupChange;

trait Wechat
{
	protected function start()
	{
		$path = storage_path('app').'/wechat/';

		$robot = new Vbot([
			'user_path' => $path,
			'debug' => true,
			'session' => 'yuanyueshui'
		]);
		$robot->server->setMessageHandler(function ($message) use ($path) {
			/** @var $message Message */

			// 位置信息 返回位置文字
			if ($message instanceof Location) {
				/** @var $message Location */
				Text::send($message->from['UserName'], '地图链接：' . $message->url);
				return '位置：' . $message;
			}

			// 文字信息
			if ($message instanceof Text) {
				/** @var $message Text */
				// 联系人自动回复商品
				if($this->isSearch($message)) {
				//if(starts_with($message->content, '找')) {
					if ($message->fromType === 'Contact') {
						$info = $this->replyGoods($message->content, $image);

						// 群组@我回复
					} elseif ($message->fromType === 'Group') {
						//群聊at开关
						if(\Voyager::setting('wechat_group_at') == '0') {
							$info = $this->replyGoods($message->content, $image);
						} else {
							if($message->isAt) {
								$info = $this->replyGoods($message->content, $image);
							}
						}

					}
					Image::send($message->msg['FromUserName'], $image);
					return $info;
				} else {
					if ($message->fromType === 'Contact') {
						return $this->reply($message->content);
						// 群组@我回复
					} elseif ($message->fromType === 'Group') {

						if (str_contains($message->content, '设置群名称') && $message->from['Alias'] === '天猫淘宝优惠购') {
							group()->setGroupName($message->from['UserName'], str_replace('设置群名称', '', $message->content));
						}

						if ($message->isAt) {
							return $this->reply($message->content);
						}
					}
				}
			}

			// 图片信息 返回接收到的图片
			if ($message instanceof Image) {
//        return $message;
			}

			// 视频信息 返回接收到的视频
			if ($message instanceof Video) {
//        return $message;
			}

			// 表情信息 返回接收到的表情
			if ($message instanceof Emoticon) {
				Emoticon::sendRandom($message->from['UserName']);
			}

			// 语音消息
			if ($message instanceof Voice) {
				/** @var $message Voice */
//        return '收到一条语音并下载在' . $message::getPath($message::$folder) . "/{$message->msg['MsgId']}.mp3";
			}

			// 撤回信息
			if ($message instanceof Recall && $message->msg['FromUserName'] !== myself()->username) {
				/** @var $message Recall */
				if ($message->origin instanceof Image) {
					Text::send($message->msg['FromUserName'], "{$message->nickname} 撤回了一张照片");
					Image::sendByMsgId($message->msg['FromUserName'], $message->origin->msg['MsgId']);
				} elseif ($message->origin instanceof Emoticon) {
					Text::send($message->msg['FromUserName'], "{$message->nickname} 撤回了一个表情");
					Emoticon::sendByMsgId($message->msg['FromUserName'], $message->origin->msg['MsgId']);
				} elseif ($message->origin instanceof Video) {
					Text::send($message->msg['FromUserName'], "{$message->nickname} 撤回了一个视频");
					Video::sendByMsgId($message->msg['FromUserName'], $message->origin->msg['MsgId']);
				} elseif ($message->origin instanceof Voice) {
					Text::send($message->msg['FromUserName'], "{$message->nickname} 撤回了一条语音");
				} else {
					Text::send($message->msg['FromUserName'], "{$message->nickname} 撤回了一条信息 \"{$message->origin->msg['Content']}\"");
				}
			}

			// 红包信息
			if ($message instanceof RedPacket) {
				// do something to notify if you want ...
				return $message->content . ' 来自 ' . $message->from['NickName'];
			}

			// 转账信息
			if ($message instanceof Transfer) {
				/** @var $message Transfer */
				return $message->content . ' 收到金额 ' . $message->fee;
			}

			// 推荐名片信息
			if ($message instanceof Recommend) {
				/** @var $message Recommend */
				if ($message->isOfficial) {
					return $message->from['NickName'] . ' 向你推荐了公众号 ' . $message->province . $message->city .
						" {$message->info['NickName']} 公众号信息： {$message->description}";
				} else {
					return $message->from['NickName'] . ' 向你推荐了 ' . $message->province . $message->city .
						" {$message->info['NickName']} 头像链接： {$message->bigAvatar}";
				}
			}

			// 请求添加信息
			if ($message instanceof RequestFriend) {
				/** @var $message RequestFriend */

				//if ($message->info['Content'] === '上山打老虎') {
				//	$message->verifyUser($message::VIA);
				//}
			}

			// 分享信息
			if ($message instanceof Share) {
				/** @var $message Share */
				$reply = "收到分享\n标题：{$message->title}\n描述：{$message->description}\n链接：{$message->url}";
				if ($message->app) {
					$reply .= "\n来源APP：{$message->app}";
				}
				return $reply;
			}

			// 分享小程序信息
			if ($message instanceof Mina) {
				/** @var $message Mina */
				$reply = "收到小程序\n小程序名词：{$message->title}\n链接：{$message->url}";
				return $reply;
			}

			// 公众号推送信息
			if ($message instanceof Official) {
				/** @var $message Official */
				$reply = "收到公众号推送\n标题：{$message->title}\n描述：{$message->description}\n链接：{$message->url}\n来源公众号名称：{$message->app}";
				return $reply;
			}

			// 手机点击聊天事件
			if ($message instanceof Touch) {
//        Text::send($message->msg['ToUserName'], "我点击了此聊天");
			}

			// 新增好友
			if ($message instanceof \Hanson\Vbot\Message\Entity\NewFriend) {
				\Hanson\Vbot\Support\Console::log('新加好友：' . $message->from['NickName']);
			}

			// 群组变动
			if ($message instanceof GroupChange) {
				/** @var $message GroupChange */
				if ($message->action === 'ADD') {
					\Hanson\Vbot\Support\Console::log('新人进群');
					return '欢迎新人 ' . $message->nickname;
				} elseif ($message->action === 'REMOVE') {
					\Hanson\Vbot\Support\Console::log('群主踢人了');
					return $message->content;
				} elseif ($message->action === 'RENAME') {
//            \Hanson\Vbot\Support\Console::log($message->from['NickName'] . ' 改名为 ' . $message->rename);
					if ($message->rename !== '天猫淘宝优惠购'){
						group()->setGroupName($message->from['UserName'], '天猫淘宝优惠购');
						return '行不改名,坐不改姓！';
					}
				}
			}

			return false;

		});

		//todo test
		$robot->server->setExitHandler(function () {
			\Hanson\Vbot\Support\Console::log('其他设备登录');
			$php = config('php');
			$shell = base_path().'/artisan wechat:serve start >> ~/wechat.log 2>&1 &';
			exec("$php $shell");

			\Mail::to('18676756298@163.com')->send(new WechatTips());
		});

		//todo test
		$robot->server->setExceptionHandler(function () {
			\Hanson\Vbot\Support\Console::log('异常退出');
			$php = config('php');
			$shell = base_path().'/artisan wechat:serve start >> ~/wechat.log 2>&1 &';
			exec("$php $shell");
			\Mail::to('18676756298@163.com')->send(new WechatTips());
		});

		$robot->server->run();
	}

	/**
	 * 机器人自动回复
	 * @param $str
	 *
	 * @return mixed
	 */
	protected function reply($str)
	{
		return http()->post('http://www.tuling123.com/openapi/api', [
			'key'  => '1dce02aef026258eff69635a06b0ab7d',
			'info' => $str
		], true)['text'];

	}

	/**
	 * 回复商品数据
	 * @param $str
	 *
	 * @return string
	 */
	protected function replyGoods($str, &$image)
	{
		start_limit($str, 1, 100, '');

		$goodsInfo = Goods::getWechatGoodsInfos($str);
		//"收到分享\n标题：{$message->title}\n描述：{$message->description}\n链接：{$message->url}";
		//卓诗尼2017春季新款单鞋内增高女鞋子一脚蹬乐福鞋休闲套脚小白鞋【包邮】
		//【在售价】199.00元
		//【券后价】139.00元
		//【下单链接】http://c.b1wt.com/h.4rPcF6?cv=AqvRZHwJTkL
		//-----------------
		//复制这条信息，￥AqvRZHwJTkL￥ ，打开【手机淘宝】即可查看
		//阿李罗火火兔F6蓝牙故事机早教机益智能宝宝婴幼MP3下载儿童玩具【包邮】
		//【在售价】258.00元
		//【券后价】243.00元
		//【下单链接】http://c.b1wt.com/h.fW56G2?cv=wCP5ZtZaGz3
		//-----------------
		//复制这条信息，￥wC P5ZtZaGz3￥ ，打开【手机淘宝】即可查看
		try {
			$goodsStr = $goodsInfo->goods_name."\n"
				. '【在售价】 '. $goodsInfo-> price. "元\n";
			if($goodsInfo->coupon_short_url) {
				$goodsStr .= '【优惠券】'. $goodsInfo->coupon_denomination ."\n【下单链接】".$goodsInfo->coupon_short_url."\n";

			} else {
				$goodsStr .= '【下单链接】'. $goodsInfo->tao_short_url."\n";
			}

			$goodsStr .= '【月销量】'. $goodsInfo->mouth_sell_number."\n-----------------\n";
			if($goodsInfo->coupon_short_url) {
				$goodsStr .= '复制这条信息，' . $goodsInfo->coupon_password . ' ，打开【手机淘宝】即可查看';
			} else {
				$goodsStr .= '复制这条信息，' . $goodsInfo->tao_password . ' ，打开【手机淘宝】即可查看';
			}

			$image = $goodsInfo->local_image;
		} catch (\Exception $e) {
			$goodsStr = '客官，小二暂未帮您找到相关商品，要不您换个关键词试试 [奸笑]';
			$image = '';
		}

		return $goodsStr;
	}

	public function isSearch(Message &$message)
	{
		if ($message->fromType === 'Group') {
			$message->content = wechat_at($message->content, myself()->nickname);
		}

		if(starts_with(trim($message->content), '找')) return true;

		return false;
	}
}
