<?php
/**
 * Created by PhpStorm.
 * User: rcb
 * Date: 2020/6/5
 * Time: 12:04
 */

namespace app;

require_once "app\api\User.php";

use app\api\User;

class Index
{
    public $redis;

    public function __construct()
    {
        //保存实例
        $this->redis = new \Redis();
        //链接redis
        $this->redis->connect('127.0.0.1', 6379);
    }

    //redis全局命令
    public function testCommon()
    {
        //查看所有键
        var_dump($this->redis->keys('*'));
        //获取键总数(如果存在大量键，线上禁止使用此指令)
        var_dump($this->redis->dbSize());
        //监察键是否存在
        var_dump($this->redis->exists('zSetSting'));
        //删除键
        var_dump($this->redis->del('zSetSting'));
        //设置键的过期时间
        var_dump($this->redis->expire('listString', 5000));
        //查看键剩余过期时间
        var_dump($this->redis->ttl('listString'));
        //键的数据结构类型
        var_dump($this->redis->type('listString'));


        //redis数据库管理
        //切换数据库
        var_dump($this->redis->select(0));
        //强制刷新当前db
        //var_dump($this->redis->flushDB());
        //强制刷新所有db
        //var_dump($this->redis->flushAll());
        //返回当前数据库的 key 的数量。
        var_dump($this->redis->dbSize());
    }

    //字符串（一个key最大能存储512MB）（string类型是redis最基本的数据类型）
    public function testString()
    {
        //保存字符串
        echo $this->redis->set('string', 'string');
        //判断是否存在
        echo $this->redis->exists('string');
        //获取字符串
        echo $this->redis->get('string');
        //key不存在时是保存
        echo $this->redis->setnx('stringNx', '111');
        //保存key-》value并设置过期时间(秒)
        echo $this->redis->set('stringTimeout', 'string', 5000);
        echo $this->redis->setex('stringTimeoutM', 5000, 'string');
        //毫秒
        echo $this->redis->psetex('stringTimeoutHM', 5000000, 'string');
        //批量设置
        echo $this->redis->mset(array(
            'string1' => '1',
            'string2' => '2',
            'string3' => '3',
            'string4' => '4',
        ));
        //批量获取value
        var_dump($this->redis->mget(array(
            'string1',
            'string2',
            'string3',
            'string4',
        )));
        //自增
        echo $this->redis->incr('string1');
        //自减
        echo $this->redis->decr('string2');
        //增加增量
        echo $this->redis->incrBy('string3', 500);
        //减去增量
        echo $this->redis->decrBy('string4', 2);
        //增加浮点型增量
        echo $this->redis->incrByFloat('string3', 5.66);
        //追加到字符串末尾
        echo $this->redis->append('string', 'test');
        //获取字符串长度
        echo $this->redis->strlen('string');
        //截取字符串
        echo $this->redis->getRange('string', 6, 10);
        //删除key单个
        echo $this->redis->del('string');
        //删除key多个
        echo $this->redis->del(array(
            'string1',
            'string2',
            'string3',
            'string4',
            'stringNx',
            'stringTimeout',
            'stringTimeoutM',
            'stringTimeoutHM',
        ));
    }

    //hash（是一个键值对集合，特别适合用于存储对象）
    public function testHash()
    {
        //保存hash键值对集合
        echo $this->redis->hSet('hashString', 'string', '222');
        echo $this->redis->hSet('hashString', 'string2', '333');
        echo $this->redis->hSet('hashString', 'string3', '333');
        //获取hashKey对应的值
        echo $this->redis->hGet('hashString', 'string2');
        //删除
        //echo $this->redis->hDel('hashString', 'string');
        //删除多个
        //echo $this->redis->hDel('hashString', 'string', 'string2', 'string3');
        //计算个数
        echo $this->redis->hLen('hashString');
        //批量设置
        echo $this->redis->hMSet('hashString', array(
            'string3' => '1',
            'string4' => '2',
            'string5' => '3',
            'string6' => '4',
        ));
        //批量获取
        var_dump($this->redis->hMGet('hashString', array(
            'string3',
            'string4',
            'string5',
            'string6',
        )));
        //判断是否存在
        echo $this->redis->hExists('hashString', 'string3');
        //获取所有field
        var_dump($this->redis->hKeys('hashString'));
        //获取所有value
        var_dump($this->redis->hVals('hashString'));
        //获取所有field和value
        var_dump($this->redis->hGetAll('hashString'));
        //增加增量
        echo $this->redis->hIncrBy('hashString', 'string3', 500);
        //增加浮点型增量
        echo $this->redis->hIncrByFloat('hashString', 'string3', 5000.22);
    }

    //集合（无序）（不允许有重复元素）（一个集合最多可存2的32次方减1个元素）
    //除了支持增删改查还支持集合交集，并集，差集
    //（用户标签，社交，查询有共同爱好的人，智能推荐）
    //使用方式：给用户添加标签或给标签添加用户，计算出共同感兴趣的人
    public function testSet()
    {
        //插入元素
        echo $this->redis->sAdd('setString', '567', '578');
        //插入多个元素
        echo $this->redis->sAdd('setString2', '126', '578');
        //获取集合成员数量
        echo $this->redis->sCard('setString');
        //获取所有集合成员
        var_dump($this->redis->sMembers('setString'));
        //检查是否存在
        echo $this->redis->exists('setString');
        //删除元素
        echo $this->redis->sRem('setString', '589');
        //交集
        var_dump($this->redis->sInter('setString', 'setString2'));
        //执行一个交集操作，并把结果存储到一个新的SET容器中。获取交集成员数量
        var_dump($this->redis->sInterStore('output1', 'setString', 'setString2'));
        //获取并集
        var_dump($this->redis->sUnion('setString', 'setString2'));
        //执行一个并集操作，并把结果存储到一个新的SET容器中。获取并集成员数量
        var_dump($this->redis->sUnionStore('output2', 'setString', 'setString2'));
        //获取差集
        var_dump($this->redis->sDiff('setString', 'setString2'));
        //执行一个差操作，并把结果存储到一个新的SET容器中。获取差集成员数量
        var_dump($this->redis->sDiffStore('output3', 'setString', 'setString2'));
        //判断该值是否属于该键
        var_dump($this->redis->sIsMember('setString', '567'));
        //移动集合中的值到另一个集合中
        //var_dump($this->redis->sMove('setString', 'setString2', '567'));
        //按照value排序，分页
        var_dump($this->redis->sort('setString2', array(
            'sort' => 'ASC',
            'limit' => array(0, 2),
        )));
    }

    //有序集合（不允许有重复元素）
    //可以用于排行榜（如游戏需要对充值数据做排行榜，或设计网站点赞数）
    //分值实现有序
    public function testZSet()
    {
        //添加到有序集合
        echo $this->redis->zAdd('zSetSting', '1', 'haha', '0', 'ssss');
        //获取集合成员数量
        echo $this->redis->zCard('zSetSting');
        //取得特定范围内的排序元素按照score从低到高排列
        var_dump($this->redis->zRange('zSetSting', 0, -1));
        //获取指定范围内的元素按照score从高到低排列
        var_dump($this->redis->zRevRange('zSetSting', 0, -1));
        //从集合中删除指定元素
        //echo $this->redis->zRem('zSetSting', '22');
        //获取指定范围内的元素的个数
        var_dump($this->redis->zCount('zSetSting', 0, 3));
        //移除
        //var_dump($this->redis->zRem('zSetSting', 'haha'));
        //移除指定范围内的元素
        //var_dump($this->redis->zRemRangeByRank('zSetSting', 1, 1));
        //获取元素对应的score值
        var_dump($this->redis->zScore('zSetSting', 'haha'));
        //对元素的score增加增量
        //var_dump($this->redis->zIncrBy('zSetSting', 50, 'haha'));
        //按照value排序，分页
        var_dump($this->redis->sort('setString2', array(
            'sort' => 'ASC',
            'limit' => array(0, 2),
        )));
    }

    //列表（链表）(用来存储多个有序的字符串，一个列表最多可存2的32次方减一个元素)（列表元素可以重复）（因为有序可以通过索引下标获取某个元素或者某个范围的元素列表）
    //时间轴，消息队列
    //索引下标实现有序
    public function testList()
    {
        //插入到表头
        //echo $this->redis->lPush('listString', '1');
        //插入多个数据到表头
        //echo $this->redis->lPush('listString', '5', '6', '7');
        //当key存在并且是一个列表  插入到表头
        echo $this->redis->lPushx('listString', '3');
        //插入到表尾
        echo $this->redis->rPush('listString', '2');
        //当key存在并且是一个列表  插入到表尾
        echo $this->redis->rPushx('listString', '4');
        //移除表头并返回头元素
        echo $this->redis->lPop('listString');
        //移除表位并返回尾元素
        echo $this->redis->rPop('listString');
        //获取列表指定区间内的元素0正数第一个，-1倒数第一个
        var_dump($this->redis->lRange('listString', 0, -1));
        //插入5的前面
        echo $this->redis->lInsert('listString', \Redis::BEFORE, '5', '10');
        //插入5的后面
        echo $this->redis->lInsert('listString', \Redis::AFTER, '5', '9');
        //根据index获取value
        echo $this->redis->lIndex('listString', -1);
        //获取列表的长度
        echo $this->redis->lLen('listString');
    }
}

$indexClass = new Index();
$indexClass->testSet();

//$apiUser = new User();
//$apiUser->getUser();