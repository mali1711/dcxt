
# 购物车接口
## 接口介绍
本接口是用来，获取用户购物车列表，添加购物车，删除购物车等功能

## 添加购物车数量

请求方式 get 

url：[http://canyin.bjdingzhicheng.com/rcart/plus?gid=18](http://canyin.bjdingzhicheng.com/rcart/plus?gid=18)

**参数简介**

| 参数名 | 参数类型 | 参数介绍 | 是否必填|
| ------ | ------ | ------ |----|
|gid	 | int    |商品id   | 是   |



**返回值说明**

| 参数名 | 参数类型 | 参数介绍 |
| ------ | ------ | ------ |
|status	 | int    |状态码   |
|msg	 | string    |返回值信息|

**返回值实例：**

	{
	  "status": 10001,
	  "msg": "购物车添加成功"
	}

## 减少购物车数量

请求方式： get

url：[http://canyin.bjdingzhicheng.com/rcart/reduce?id=373](http://canyin.bjdingzhicheng.com/rcart/reduce?id=373)

**参数简介**

| 参数名 | 参数类型 | 参数介绍 | 是否必填|
| ------ | ------ | ------ |----|
|id	 | int    |购物车id  | 是   |   



**返回值说明**

| 参数名 | 参数类型 | 参数介绍 |
| ------ | ------ | ------ |
|status	 | int    |状态码   |
|msg	 | string    |返回值信息|

**返回值实例：**
	{
	  "status": 10000,
	  "msg": "商品已经删除"
	}

## 获取购物车列表

请求方式 get

url： [http://canyin.bjdingzhicheng.com/rcart/list](http://canyin.bjdingzhicheng.com/rcart/list "获取购物车里的商品列表")

**返回实例**

    {
      "status": 1,
      "msg": "商品列表获取成功",
      "data": [
        {
          "id": 379,
          "uid": 3,
          "gid": 24,
          "num": 3,
          "price": null,
          "create_time": "2018-11-05 14:50:18",
          "selected": 1,
          "status": 1,
          "goodsInfo": [
            {
              "id": 24,
              "goods_name": "测试商品1",
              "goods_pic": "12.90"
            }
          ]
        },
        {
          "id": 374,
          "uid": 3,
          "gid": 25,
          "num": 298,
          "price": null,
          "create_time": "2018-11-05 14:34:51",
          "selected": 1,
          "status": 1,
          "goodsInfo": [
            {
              "id": 25,
              "goods_name": "测试商品2",
              "goods_pic": "12.90"
            }
          ]
        },
        {
          "id": 375,
          "uid": 3,
          "gid": 27,
          "num": 1,
          "price": null,
          "create_time": "2018-11-05 14:34:55",
          "selected": 1,
          "status": 1,
          "goodsInfo": [
            {
              "id": 27,
              "goods_name": "订餐",
              "goods_pic": "0.00"
            }
          ]
        },
        {
          "id": 376,
          "uid": 3,
          "gid": 26,
          "num": 200,
          "price": null,
          "create_time": "2018-11-05 14:34:58",
          "selected": 1,
          "status": 1,
          "goodsInfo": [
            {
              "id": 26,
              "goods_name": "商品二",
              "goods_pic": "30.00"
            }
          ]
        },
        {
          "id": 377,
          "uid": 3,
          "gid": 24,
          "num": 1,
          "price": null,
          "create_time": "2018-11-05 14:35:01",
          "selected": 1,
          "status": 1,
          "goodsInfo": [
            {
              "id": 24,
              "goods_name": "测试商品1",
              "goods_pic": "12.90"
            }
          ]
        }
      ]
    }

## 清空购物车

请求方式：GET

url：http://canyin.bjdingzhicheng.com/rcart/empty

# 订单

## 购物车方式提交订单

请求方式： GET

| 参数名 | 参数类型 | 参数介绍 | 是否必填|
| ------ | ------ | ------ |----|
|userId	 | int    |所属用户id  | 是   |
|userName	 | string    |收件人姓名  | 是   |
|userAddress	 | string    |收件人地址  | 是   |
|userMobile	 | string    |收件人手机号  | 是   |
|orderRemarks	 | string    |订单备注  | 是   |
