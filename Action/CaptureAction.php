<?php
namespace Paradigmfi\PayumPaytrail\Action;

use Paradigmfi\PayumPaytrail\Request\Api\CreatePayment;
use Payum\Core\Action\GatewayAwareAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Request\Capture;
use Payum\Core\Exception\RequestNotSupportedException;

class CaptureAction extends GatewayAwareAction
{
    /**
     * {@inheritDoc}
     *
     * @param Capture $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        if (false == $model['url']) {
            $this->gateway->execute(new CreatePayment($model));

            if ($model['url']) {
                throw new HttpRedirect($model['url']);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
