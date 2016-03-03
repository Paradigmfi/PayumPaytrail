<?php
namespace Paradigm\PayumPaytrail\Action\Api;

use Paradigm\PayumPaytrail\Request\Api\CreatePayment;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\LogicException;
use Payum\Core\Exception\RequestNotSupportedException;

class CreatePaymentAction extends BaseApiAwareAction
{
    /**
     * {@inheritDoc}
     *
     * @param CreatePayment $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        if ($model['url']) {
            throw new LogicException('The payment has  already been created');
        }

        $model->replace(
            $this->api->createPayment((array) $model)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof CreatePayment &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
