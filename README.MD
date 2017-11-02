总裁意见箱

## 简介
该意见箱基于 Discuz!X3.4 、以独立插件的形式开发的一个简易意见箱（总裁意见箱），借助微信公众号通过微信登陆。除了对意见进行回复、分级操作意外，还可以对好意见进行发送微信红包奖励（涉及微信支付平台）。最初是用于公司内部来进行企业意见管理以及为意见发放奖励。

## 安装
* 搭好 Discuz!X3.4 环境，将项目放在 *discuz/source/plugin* 下，将 apache 或是其他部署工具部署到 *discuz/source/plugin/zcbox* 位置；
* 进入 *zcbox* 目录，执行 `cp .env.example .env` 操作，复制一份 .env 文件，并按文件内注释做好相关配置。
* 正式服需要通过微信进入页面来使用，测试服（.env 里面可配置）可以通过手动传 **uid** 参数进行访问；
* 在 *task/setAdmin.php* 文件中输入要设置成管理员的用户名，然后执行 `php task/setAdmin.php` 进行设置，unsetAdmin.php 则是取消相应管理员（超级管理员拥有发奖励的权限、普通管理员只能对意见进行删除、回复、分级等操作）；

## 使用
* 进入首页需先设置用户名和公司名后，才能使用；

![使用声明](https://coding.net/u/AbraZ/p/zcbox/git/raw/master/img/use_statement.png)
![注册页面](https://coding.net/u/AbraZ/p/zcbox/git/raw/master/img/register.png)
![用户名已存在](https://coding.net/u/AbraZ/p/zcbox/git/raw/master/img/name_exist.png)

* 首页上方为意见栏，用于用户提意见；

![用户首页](https://coding.net/u/AbraZ/p/zcbox/git/raw/master/img/user_index.png)

意见右上角为意见状态，分为未处理以及有效和无效意见；
* 管理员和超级管理可以查看所有人意见，普通用户则只能看到自己的意见；其中超级管理员首页默认显示由管理员（助理）提前筛选的有效意见

![超级管理员默认首页](https://coding.net/u/AbraZ/p/zcbox/git/raw/master/img/superadmin_index.png)

* 管理员可以对意见进行处理，右上角可将意见分级，分为有效意见和无效意见，已经修改则不能改回成“未处理”状态，并且不能删除。（意见仅可在未处理时可以删除）；
可以对意见进行回复，回复后用户可在首页查看，回复可以覆盖或者被管理严删除

![意见回复](https://coding.net/u/AbraZ/p/zcbox/git/raw/master/img/tip_reply.png)
![回复页面](https://coding.net/u/AbraZ/p/zcbox/git/raw/master/img/reply.png)
![回复后](https://coding.net/u/AbraZ/p/zcbox/git/raw/master/img/after_reply.png)

* 仅超级管理员可以发放微信红包奖励，且只能发放一次，已发送奖励的意见会有 ‘√’ 标识；

![发送奖励](https://coding.net/u/AbraZ/p/zcbox/git/raw/master/img/has_rewarded.png)

## 相关说明
本意见箱，通过微信授权登陆，所以需要使用者拥有微信公众号；
涉及到红包奖励的功能，需要使用微信支付平台，并且在支付平台开通**现金红包**功能；
