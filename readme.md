Alipay
======

支付宝SDK在Laravel5/Lumen封装包。

该拓展包想要达到在Laravel5/Lumen框架下，便捷使用支付宝的目的。

## 安装

```
composer require latrell/alipay dev-master
```

更新你的依赖包 ```composer update``` 或者全新安装 ```composer install```。


## 使用

要使用支付宝SDK服务提供者，你必须自己注册服务提供者到Laravel/Lumen服务提供者列表中。
基本上有两种方法可以做到这一点。

### Laravel
找到 `config/app.php` 配置文件中，key为 `providers` 的数组，在数组中添加服务提供者。

```php
    'providers' => [
        // ...
        'Latrell\Alipay\AlipayServiceProvider',
    ]
```

运行 `php artisan vendor:publish` 命令，发布配置文件到你的项目中。

### Lumen
在`bootstrap/app.php`里注册服务。

```php
//Register Service Providers
$app->register(Latrell\Alipay\AlipayServiceProvider::class);
```

由于Lumen的`artisan`命令不支持`vendor:publish`,需要自己手动将`src/config`下的配置文件拷贝到项目的`config`目录下,
并将`config.php`改名成`latrell-alipay.php`,
`mobile.php`改名成`latrell-alipay-mobile.php`,
`web.php`改名成`latrell-alipay-web.php`.

### 说明
配置文件 `config/latrell-alipay.php` 为公共配置信息文件， `config/latrell-alipay-web.php` 为Web版支付宝SDK配置， `config/latrell-alipay-mobile.php` 为手机端支付宝SDK配置。

## 例子

### 支付申请

#### 网页

```php
	// 创建支付单。
	$alipay = app('alipay.web');
	$alipay->setOutTradeNo('order_id');
	$alipay->setTotalFee('order_price');
	$alipay->setSubject('goods_name');
	$alipay->setBody('goods_description');
	
	$alipay->setQrPayMode('4'); //该设置为可选，添加该参数设置，支持二维码支付。

	// 跳转到支付页面。
	return redirect()->to($alipay->getPayLink());
```

#### 手机端

```php
	// 创建支付单。
	$alipay = app('alipay.mobile');
	$alipay->setOutTradeNo('order_id');
	$alipay->setTotalFee('order_price');
	$alipay->setSubject('goods_name');
	$alipay->setBody('goods_description');

	// 返回签名后的支付参数给支付宝移动端的SDK。
	return $alipay->getPayPara();
```

### 结果通知

#### 网页

```php
	/**
	 * 异步通知
	 */
	public function webNotify()
	{
		// 验证请求。
		if (! app('alipay.web')->verify()) {
			Log::notice('Alipay notify post data verification fail.', [
				'data' => Request::instance()->getContent()
			]);
			return 'fail';
		}

		// 判断通知类型。
		switch (Input::get('trade_status')) {
			case 'TRADE_SUCCESS':
			case 'TRADE_FINISHED':
				// TODO: 支付成功，取得订单号进行其它相关操作。
				Log::debug('Alipay notify post data verification success.', [
					'out_trade_no' => Input::get('out_trade_no'),
					'trade_no' => Input::get('trade_no')
				]);
				break;
		}
	
		return 'success';
	}

	/**
	 * 同步通知
	 */
	public function webReturn()
	{
		// 验证请求。
		if (! app('alipay.web')->verify()) {
			Log::notice('Alipay return query data verification fail.', [
				'data' => Request::getQueryString()
			]);
			return view('alipay.fail');
		}

		// 判断通知类型。
		switch (Input::get('trade_status')) {
			case 'TRADE_SUCCESS':
			case 'TRADE_FINISHED':
				// TODO: 支付成功，取得订单号进行其它相关操作。
				Log::debug('Alipay notify get data verification success.', [
					'out_trade_no' => Input::get('out_trade_no'),
					'trade_no' => Input::get('trade_no')
				]);
				break;
		}

		return view('alipay.success');
	}
```

#### 手机端

```php
	/**
	 * 支付宝异步通知
	 */
	public function alipayNotify()
	{
		// 验证请求。
		if (! app('alipay.mobile')->verify()) {
			Log::notice('Alipay notify post data verification fail.', [
				'data' => Request::instance()->getContent()
			]);
			return 'fail';
		}

		// 判断通知类型。
		switch (Input::get('trade_status')) {
			case 'TRADE_SUCCESS':
			case 'TRADE_FINISHED':
				// TODO: 支付成功，取得订单号进行其它相关操作。
				Log::debug('Alipay notify get data verification success.', [
					'out_trade_no' => Input::get('out_trade_no'),
					'trade_no' => Input::get('trade_no')
				]);
				break;
		}

		return 'success';
	}
```
