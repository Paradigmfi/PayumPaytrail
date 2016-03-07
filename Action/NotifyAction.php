<?php
namespace Paradigm\PayumPaytrail\Action;

use Payum\Core\Action\GatewayAwareAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\Notify;

class NotifyAction extends GatewayAwareAction
{
    /**
     * {@inheritDoc}
     *
     * @param Notify $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $this->gateway->execute($httpRequest = new GetHttpRequest());

        $model = ArrayObject::ensureArrayObject($request->getModel());

        $model['nq'] = $httpRequest->query;
        $model['nr'] = $httpRequest->request;
        $model['nua'] = $httpRequest->userAgent;
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Notify &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
