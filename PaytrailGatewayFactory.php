<?php
namespace Paradigmfi\PayumPaytrail;

use Paradigmfi\PayumPaytrail\Action\AuthorizeAction;
use Paradigmfi\PayumPaytrail\Action\CancelAction;
use Paradigmfi\PayumPaytrail\Action\ConvertPaymentAction;
use Paradigmfi\PayumPaytrail\Action\CaptureAction;
use Paradigmfi\PayumPaytrail\Action\NotifyAction;
use Paradigmfi\PayumPaytrail\Action\RefundAction;
use Paradigmfi\PayumPaytrail\Action\StatusAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

class PaytrailGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritDoc}
     */
    protected function populateConfig(ArrayObject $config)
    {
        $config->defaults([
            'payum.factory_name' => 'paytrail',
            'payum.factory_title' => 'Paytrail',
            'payum.action.capture' => new CaptureAction(),
            'payum.action.authorize' => new AuthorizeAction(),
            'payum.action.refund' => new RefundAction(),
            'payum.action.cancel' => new CancelAction(),
            'payum.action.notify' => new NotifyAction(),
            'payum.action.status' => new StatusAction(),
            'payum.action.convert_payment' => new ConvertPaymentAction(),
        ]);

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = array(
                'merchantId' => null,
                'merchantSecret' => null,
            );
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = ['merchantId', 'merchantSecret'];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                return new Api((array) $config, $config['payum.http_client']);
            };
        }
    }
}
