<?php
/**
 * 路由注册
 *
 * 以下代码为了尽量简单，没有使用路由分组
 * 实际上，使用路由分组可以简化定义
 * 并在一定程度上提高路由匹配的效率
 */

// 写完代码后对着路由表看，能否不看注释就知道这个接口的意义
use think\Route;
/**
 * 接口部分
 */
//获取登录小程序的用户存入数据库
Route::post('api/:version/token/user', 'api/:version.User/UserToken');
Route::get('api/:version/billList', 'api/:version.User/billList');

/**
 *帮助中心接口 @todo 无参数
 */
Route::get('api/:version/help', 'api/:version.HelpApi/getHelpList');




/**
 *平台消息接口 @todo 无参数
 */
Route::get('api/:version/getMessageList', 'api/:version.HelpApi/getMessageList');
// 获取消息数量
Route::get('api/:version/MessageNumber', 'api/:version.HelpApi/MessageNumber');
/**
 * 平台消息--状态设置
 */
Route::get('api/:version/setMessage','api/:version.HelpApi/setMessage');

/**
 * 平台消息--状态设置
 */
Route::get('api/:version/GetShangUserInfo','api/:version.User/UserInfo');

/**
 * 交流互动  @todo 无参数
 */
Route::get('api/:version/InterList', 'api/:version.Interactive/InterList');
/**
 * 交流互动详情	@todo id 
 */
Route::get('api/:version/InterInfo','api/:version.Interactive/InterInfo');
/**
 * 交流互动--点赞
 */
Route::get('api/:version/GiveLike','api/:version.Interactive/GiveLike');
/**
 * 交流互动--取消点赞
 */
Route::get('api/:version/UnGiveLike','api/:version.Interactive/UnGiveLike');
/**
 * 交流互动--添加评论
 */
Route::post('api/:version/AddComment','api/:version.Interactive/AddComment');


/**
 * 添加阅读量 @todo id 
 */
Route::get('api/:version/ReadingAdd', 'api/:version.Interactive/ReadingAdd');
/**
 * 添加点赞量 @todo uid 点赞用户的id wid 文章id 
 */
Route::post('api/:version/PraiseAdd', 'api/:version.Interactive/PraiseAdd');
/**
 * 热销商品接口  @todo 无参数 
 */
Route::get('api/:version/GoodsList','api/:version.Goods/GoodsList');

/**
 * 热搜  @todo 无参数 
 */
Route::get('api/:version/SearchList','api/:version.Search/getList');

/**
 * 热搜  @todo 无参数 
 */
Route::get('api/:version/SetCartStatus','api/:version.Cart/SetCartStatus');

/**
 * 热搜  @todo 无参数 
 */
Route::get('api/:version/SetCartStatusALl','api/:version.Cart/SetCartStatusALl');



/**
 * 商品列表接口  @todo 无参数 
 */
Route::get('api/:version/GoodsCategoryList','api/:version.Goods/GoodsCategoryList');

/**
 * 商家接口  @todo 无参数 
 */
Route::get('api/:version/UserInfoList','api/:version.Users/UserInfoList');

/**
 * 商家详情 @todo 无参数 
 */
Route::get('api/:version/ShangInfo','api/:version.Users/ShangInfo');

/**
 * 商品搜索 @todo keyword 
 */
Route::get('api/:version/GoodsSeach','api/:version.Goods/GoodsSeach');
/**
 * 商品详情
 */
Route::get('api/:version/GoodsInfo','api/:version.Goods/GoodsInfo');
/**
 * Banner--Api @todo 无参数
 */
Route::get('api/:version/BannerList','api/:version.Conf/BannerList');

/**
 * Banner--Api @todo 无参数
 */
Route::get('api/:version/Configur','api/:version.Conf/Config');

/**
 * 交流互动Banner--Api @todo 无参数
 */
Route::get('api/:version/ProList','api/:version.Conf/ProList');
/**
 *注册 -- API @todo 参数有 username(用户名) mobile(手机号) password(密码)
 */
Route::post('api/:version/Registered','api/:version.Login/registered');
/**
 *短信验证码 -- API @todo  mobile(手机号)
 */
Route::post('api/:version/SMS','api/:version.Login/SMS');
/**
 *检测缓存验证码是否正确 -- API @todo  mobile(手机号) recode(验证码)
 */
Route::get('api/:version/CheckRecode','api/:version.Login/CheckRecode');
/**
 *登录接口 API @todo  username(用户名)  password(密码)
 */
Route::post('api/:version/DoLogin','api/:version.Login/DoLogin');



/**
 *添加地址接口 -- API @todo  username(用户名)  password(密码) 
 */
Route::post('api/:version/AddAddress','api/:version.User/AddAddress');

/**
 *获取地址列表 -- API @todo  
 */
Route::get('api/:version/GetAddress','api/:version.User/GetAddress');

/**
 *添加银行卡接口 -- API @todo  
 */
Route::post('api/:version/AddCard','api/:version.Card/AddCard');

/**
 *设置默认银行卡 -- API @todo  
 */
Route::get('api/:version/SetCardDefault','api/:version.Card/SetCardDefault');

/**
 * 获取默认银行卡 默认 -- API @todo  uid id
 */
Route::get('api/:version/GetCardDefault','api/:version.Card/GetCardDefault');

/**
 * 获取默认银行卡 默认 -- API @todo  uid id
 */
Route::get('api/:version/CardInfo','api/:version.Card/CardInfo');

/**
 * 删除用户的银行卡 -- API addressid
 */
Route::get('api/:version/CardDelete','api/:version.Card/CardDelete');
/**
 * 读取用户修改的银行卡数据 -- API addressid userid
 */
Route::get('api/:version/CardOne','api/:version.Card/CardOne');
/**
 * 执行修改用户银行卡
 */
Route::post('api/:version/updateCard','api/:version.Card/updateCard');

// 银行卡详情
Route::get('api/:version/CardDetail','api/:version.Card/CardDetail');
/**
 *设置收货地址 默认 -- API @todo  id(用户名)  uid(密码)
 */
Route::get('api/:version/AddressDefault','api/:version.User/AddressDefault');

/**
 * 获取默认收货地址 默认 -- API @todo  uid
 */
Route::get('api/:version/getDefault','api/:version.User/getDefault');

/**
 *查找用户地址 -- API @todo  
 */
Route::get('api/:version/AddressInfo','api/:version.User/AddressInfo');

/**
 *查找用户地址 -- API @todo  
 */
Route::get('api/:version/AddressDetail','api/:version.User/AddressDetail');

/**
 * 支付
 */
Route::get('api/:version/DoPay','api/:version.Pay/DoPay');

Route::get('api/:version/yuePay','api/:version.Pay/yuePay');

/**
 *支付回调接口 API @todo  username(用户名)  password(密码)
 */
Route::post('api/:version/setOrderStatus','api/:version.Order/DoOrderPay');

/**
 * 删除用户的收货地址 -- API addressid
 */
Route::get('api/:version/AddressDelete','api/:version.WeChatUser/AddressDelete');
/**
 * 读取用户修改的数据 -- API addressid userid
 */
Route::get('api/:version/AddressOne','api/:version.WeChatUser/AddressOne');
/**
 * 执行修改用户地址
 */
Route::post('api/:version/updateAddress','api/:version.WeChatUser/updateAddress');

// 提现申请 -- API userId money
Route::post('api/:version/TiXian','api/:version.User/tixian');

// 验证 -- API userId money
Route::post('api/:version/yz','api/:version.User/yanzheng');

// 脚本 -- API userId money
Route::get('api/:version/SetUserLevel','api/:version.User/SetUserLevel');

// 冻结
Route::post('api/:version/cacheStatus','api/:version.User/cacheStatus');


// 添加购物车
Route::post('api/:version/AddCart','api/:version.Cart/AddCart');

// 购物车列表
Route::get('api/:version/CartList','api/:version.Cart/CartList');

// 购物车数量修改
Route::post('api/:version/SetCart','api/:version.Cart/SetCart');

//删除购物车
Route::get('api/:version/CartDelete','api/:version.Cart/CartDelete');

//删除购物车
Route::get('api/:version/bucketNum','api/:version.Cart/bucketNum');



/**
 * 礼盒分类 @todo 无参数
 */
Route::get('api/:version/Boxcla','api/:version.Box/Boxcla');
/**
 * 礼盒列表 @todo 无参数
 */
Route::get('api/:version/Boxinfo','api/:version.Box/Boxinfo');
/**
 * 礼盒推荐 @todo 无参数
 */
Route::get('api/:version/Boxrem','api/:version.Box/Boxrem');

/**
 * 用户添加购物车 @todo uid 用户id gid 商品id  num 数量 price 总价格
 */
Route::post('api/:version/AddShopCar','api/:version.Goods/AddShopCar');
/**
 * 增加购物车数量 每次加1; @todo id 购物车id picone 单价
 */
Route::get('api/:version/AddUpdateCart','api/:version.Goods/AddUpdateCart');
/**
 * 减少购物车数量 每次减1; @todo id 购物车id picone 单价
 */
Route::get('api/:version/ReduUpdateCart','api/:version.Goods/ReduUpdateCart');
/**
 * 删除购物车 @todo 参数  id 购物车id
 */
Route::get('api/:version/DeleteCart','api/:version.Goods/DeleteCart');

/**
 * 用户信息 @todo 参数  userid
 */
Route::get('api/:version/UserInfo','api/:version.WeChatUser/UserInfo');
/**
 * 用户信息修改 @todo 参数userid
 */
Route::post('api/:version/UpdatePassword','api/:version.WeChatUser/UpdatePassword');
/**
 * 用户修改绑定手机号
 */
Route::post('api/:version/UpdateMobile','api/:version.WeChatUser/UpdateMobile');

/**
 * 测试二维码接口
 */
Route::get('api/:version/ceshi','api/:version.Goods/ceshi');

/*
 * 生成订单接口
 */
Route::post('api/:version/addOrder','api/:version.Order/addOrder');

/*
 * 生成余额支付订单接口
 */
Route::post('api/:version/addyuEorder','api/:version.Order/yuEorder');

/*
 * 生成同租金订单接口
 */
Route::post('api/:version/addOrderTzj','api/:version.Order/addOrderTzj');

/*
 * 生成充值卡金订单接口
 */
Route::post('api/:version/addCardorder','api/:version.Order/addCardorder');

/*
 * 生成线下消费订单接口
 */
Route::post('api/:version/AddPaymentOrder','api/:version.Order/AddPaymentOrder');


/*
 * 获取订单列表接口
 */
Route::get('api/:version/orderList','api/:version.Order/getOrderList');


/*
 * 获取订单详情接口
 */
Route::get('api/:version/OrderDetail','api/:version.Order/OrderDetail');

/*
 * 完成订单操作
 */
Route::post('api/:version/completeOrder','api/:version.Order/completeOrder');

Route::post('api/:version/completeOrderZiQu','api/:version.Order/completeOrderZiQu');

/*
 * 取消订单 
 */
Route::post('api/:version/cancelOrder','api/:version.Order/cancelOrder');

/*
 * 申请售后
 */
Route::post('api/:version/ShouOrder','api/:version.Order/ShouOrder');



/*
 * 生成押桶订单接口
 */
Route::post('api/:version/addOrderT','api/:version.Order/addOrderT');

/*
 * 生成退桶订单接口
 */
Route::post('api/:version/addOrderTui','api/:version.Order/addOrderTui');

/*
 * 生成退卡金订单接口
 */
Route::post('api/:version/TuiCardorder','api/:version.Order/TuiCardorder');

/*
 * 生成退卡金订单接口
 */
Route::post('api/:version/TuiKajin','api/:version.Order/TuiKajin');

/**
 *桶租金接口 @todo 无参数
 */
Route::get('api/:version/bucketList', 'api/:version.Bucket/getList');

/**
 *桶租金接口 @todo 无参数
 */
Route::get('api/:version/bucketInfo', 'api/:version.Bucket/bucketInfo');








































Route::get('/Admin/Login','admin/Login/login');
/**
 * 执行登录
 */
Route::post('/Admin/DoLogin','admin/Login/DoLogin');
/**
 * 后台页面显示
 */        //模拟路由       控制器
Route::get('/Admin','admin/Index/index');
//栏目管理
Route::get('/Admin/Column','admin/Column/index');
//显示修改
Route::get('/Admin/Column/update/:id','admin/Column/update');
/**
 * 管理员
 */
Route::get('/Admin/Administrator','admin/Administrator/index');
//修改
Route::get('/Admin/Administrator/update/:id','admin/Administrator/update');
/**
 * 商品管理--->商品分类
 */
Route::get('/Admin/Category','admin/Category/index');
//修改
Route::get('/Admin/Category/update/:id','admin/Category/update');
/**
 * 商品管理--->商品列表
 */
Route::get('/Admin/Goods','admin/Goods/index');
//修改
Route::get('/Admin/Goods/update/:id','admin/Goods/update');
//查看
Route::get('/Admin/Goods/show/:id','admin/Goods/show');
/**
 * 礼盒专区--->礼盒分类
 */
Route::get('/Admin/Giftbox','admin/Giftbox/index');
//修改
Route::get('/Admin/Giftbox/update/:id','admin/Giftbox/update');
/**
 * 交流互动--->发布信息
 */
Route::get('/Admin/Interactive','admin/Interactive/index');
//修改
Route::get('/Admin/Interactive/update/:id','admin/Interactive/update');
//查看
Route::get('/Admin/Interactive/show/:id','admin/Interactive/show');
/**
 * 交流互动--->发布信息
 */
Route::get('/Admin/Help','admin/Help/index?type=1');
//修改
Route::get('/Admin/Help/update/:id','admin/Help/update');
//查看
Route::get('/Admin/Help/show/:id','admin/Help/show');

/**
 * 交流互动--->发布信息
 */
Route::get('/Admin/Ping','admin/Help/index?type=2');
//修改
Route::get('/Admin/Ping/update/:id','admin/Help/update');
//查看
Route::get('/Admin/Ping/show/:id','admin/Help/show');

/**
 * 交流互动--->发布信息
 */
Route::get('/Admin/Ding','admin/Help/index?type=3');
//修改
Route::get('/Admin/Ding/update/:id','admin/Help/update');
//查看
Route::get('/Admin/Ding/show/:id','admin/Help/show');
/**
 * 交流互动--->发布信息
 */
Route::get('/Admin/Banner','admin/Banner/index');
//修改
Route::get('/Admin/Banner/update/:id','admin/Banner/update');
/**
 * 交流互动--->发布信息
 */
Route::get('/Admin/Comments','admin/Comments/index');
//修改
Route::get('/Admin/Comments/update/:id','admin/Comments/update');
/**
 * 礼盒专区--->礼盒分类
 */
Route::get('/Admin/Boxclass','admin/Boxclass/index');
//修改
Route::get('/Admin/Boxclass/update/:id','admin/Boxclass/update');
/**
 * 礼盒专区--->礼盒列表
 */
Route::get('/Admin/Box','admin/Box/index');
//修改
Route::get('/Admin/Box/update/:id','admin/Box/update');
//查看
Route::get('/Admin/Box/show/:id','admin/Box/show');


/**
 * 订单专区--->配送订单列表
 */
Route::get('/Admin/Order','admin/Order/index?deliverType=1');

/**
 * 订单专区--->自取订单列表
 */
Route::get('/Admin/ZiquOrder','admin/Order/index?deliverType=2');

/**
 * 订单专区--->退款订单列表
 */
Route::get('/Admin/OrderTui','admin/Order/orderTui');

/**
 * 订单专区--->售后订单列表
 */
Route::get('/Admin/OrderShouHou','admin/Order/OrderShouHou');

/**
 * 订单专区--->押桶订单列表
 */
Route::get('/Admin/bucketList','admin/Order/bucketList');

/**
 * 订单专区--->押桶订单列表
 */
Route::get('/Admin/bucketsList','admin/Bucket/index');

/*
 * 提醒
 */
Route::get('/Admin/Tixing','admin/Bucket/Tixing');

/**
 * 订单专区--->退桶订单列表
 */
Route::get('/Admin/bucketListTui','admin/Order/bucketListTui');

// Route::get('/Admin/getBucketInfo','admin/Order/getBucketInfo');
Route::post('/Admin/orderTuiKuan','admin/Order/orderTuiKuan');
Route::post('/Admin/completeOrderZiQu','admin/Order/completeOrderZiQu');
Route::post('/Admin/doShouHou','admin/Order/doShouHou');
Route::post('/Admin/orderTuiTong','admin/Order/orderTuiTong');


/**
 * 用户--->会员列表
 */
Route::get('/Admin/User','admin/User/index?id=1');

//薪资记录
Route::get('/Admin/User/xinzi/:id','admin/User/xinzi');

//收益记录
Route::get('/Admin/User/profit/:id','admin/User/profit');

//查看
Route::get('/Admin/User/show/:id','admin/User/show');

//修改
Route::get('/Admin/User/update/:id','admin/User/update');

//重置支付密码
Route::get('/Admin/User/update_pay/:id','admin/User/update_pay');



/**
 * 用户--->商家列表
 */
Route::get('/Admin/Shang','admin/User/index?id=2');

//查看
Route::get('/Admin/User/Shang_show/:id','admin/User/Shang_show');

//重置登录密码 update_pwd
Route::get('/Admin/User/update_pwd/:id','admin/User/update_pwd');

//商家展示信息
Route::get('/Admin/User/info_show/:id','admin/User/info_show');


/**
 * 用户--->送水员列表
 */
Route::get('/Admin/Song','admin/User/index?id=3');

//查看
Route::get('/Admin/User/Song_show/:id','admin/User/Song_show');

//添加
Route::get('/Admin/User/Song_add/:id','admin/User/Song_add');

// 片区
Route::get('/Admin/User/pianqu/:id','admin/User/pianqu');

/**
 * 地址管理
 */
Route::get('/Admin/Address','admin/Address/index');

//添加
Route::get('/Admin/Address/add','admin/Address/add');

//修改
Route::get('/Admin/Address/update/:id','admin/Address/update');

/**
 * 热搜管理
 */
Route::get('/Admin/Search','admin/Search/index');

//添加
Route::get('/Admin/Search/add','admin/Search/add');

//修改
Route::get('/Admin/Search/update/:id','admin/Search/update');

/**
 * 提现管理
 */
Route::get('/Admin/Putforward','admin/Putforward/index');

/**
 * 提现管理
 */
Route::get('/Admin/shangTixain','admin/Putforward/shangTixain');

//修改
Route::get('/Admin/Putforward/action/:id','admin/Putforward/action');

// 区域管理

Route::get('/Admin/Region','admin/Region/index');

//添加
Route::get('/Admin/Region/add','admin/Region/add');

//修改
Route::get('/Admin/Region/update/:id','admin/Region/update');

// 配置管理

Route::get('/Admin/Conf','admin/Config/index');

//添加
Route::get('/Admin/Kefu','admin/Config/kefu');

//修改
Route::get('/Admin/Guanyu','admin/Config/guanyu');

//修改
// Route::get('/Admin/Conf/DoUpdate','admin/Config/DoUpdate');


// 送水员端


Route::get('/Admin/Login','admin/Login/login');
/**
 * 执行登录
 */
Route::get('/Song/index','index/Index/index');


/**
 * 获取订单接口
 */
// Route::get('/Song/','api/:version.Order/getOrderList');
/**
 * 后台页面显示
 */        //模拟路由       控制器
// Route::get('/S/index','admin/Index/index');






//-------------------------- todo二次基础上的 订餐系统路由  ------------------------


Route::controller('rcart','reservation/Shopcart');//首页
Route::controller('rcategory','reservation/Category');//分类
Route::controller('rgoods','reservation/Goodss');//分类
Route::controller('rpercen','reservation/PersonalCenter');//个人中心
Route::controller('rorder','reservation/Order');//订单


//-------------------------- todo二次基础上的 订餐系统路由end  ------------------------
















// 商户端




