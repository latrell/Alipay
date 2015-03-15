<?php
namespace Latrell\Alipay;

use Illuminate\Support\ServiceProvider;

class AlipayServiceProvider extends ServiceProvider
{

	/**
	 * boot process
	 */
	public function boot()
	{
		$this->publishes([
			__DIR__ . '/../../config/config.php' => config_path('latrell-alipay.php'),
			__DIR__ . '/../../config/mobile.php' => config_path('latrell-alipay-mobile.php'),
			__DIR__ . '/../../config/web.php' => config_path('latrell-alipay-web.php')
		]);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->mergeConfigFrom(__DIR__ . '/../../config/config.php', 'latrell-alipay');
		$this->mergeConfigFrom(__DIR__ . '/../../config/mobile.php', 'latrell-alipay-mobile');
		$this->mergeConfigFrom(__DIR__ . '/../../config/web.php', 'latrell-alipay-web');

		$this->app->bind('alipay.mobile', function ($app)
		{
			$alipay = new Mobile\SdkPayment();

			$alipay->setPartner($app->config->get('latrell-alipay.partner_id'))
				->setSellerId($app->config->get('latrell-alipay.seller_id'))
				->setSignType($app->config->get('latrell-alipay-mobile.sign_type'))
				->setPrivateKeyPath($app->config->get('latrell-alipay-mobile.private_key_path'))
				->setPublicKeyPath($app->config->get('latrell-alipay-mobile.public_key_path'))
				->setNotifyUrl($app->config->get('latrell-alipay-mobile.notify_url'));

			return $alipay;
		});

		$this->app->bind('alipay.web', function ($app)
		{
			$alipay = new Web\SdkPayment();

			$alipay->setPartner($app->config->get('latrell-alipay.partner_id'))
				->setSellerId($app->config->get('latrell-alipay.seller_id'))
				->setKey($app->config->get('latrell-alipay-web.key'))
				->setSignType($app->config->get('latrell-alipay-web.sign_type'))
				->setNotifyUrl($app->config->get('latrell-alipay-web.notify_url'))
				->setReturnUrl($app->config->get('latrell-alipay-web.return_url'))
				->setExterInvokeIp($app->request->getClientIp());

			return $alipay;
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return [
			'alipay.mobile',
			'alipay.web'
		];
	}
}
