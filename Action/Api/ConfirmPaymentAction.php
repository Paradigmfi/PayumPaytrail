<?php
namespace Paradigm\PayumPaytrail\Action\Api;

use Paradigm\PayumPaytrail\Request\Api\ConfirmPayment;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\LogicException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayInterface;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\GetHttpRequest;

class ConfirmPaymentAction extends BaseApiAwareAction implements GatewayAwareInterface
{
    /**
     * @var GatewayInterface
     */
    protected $gateway;

    /**
     * {@inheritDoc}
     */
    public function setGateway(GatewayInterface $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * {@inheritDoc}
     *
     * @param ConfirmPayment $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        if (false == $model['url']) {
            throw new LogicException('The payment has not been created');
        }

        $this->gateway->execute($httpRequest = new GetHttpRequest());

        $query = ArrayObject::ensureArrayObject($httpRequest->query);

        if ($this->api->confirmPayment($query['RETURN_AUTHCODE'], $query['ORDER_NUMBER'], $query['TIMESTAMP'], $query['PAID'], $query['METHOD'])) {
            $model['order_number'] = $query['ORDER_NUMBER'];
            $model['confirmed_timestemp'] = $query['TIMESTAMP'];
            $model['paid'] = $query['PAID'];
            $model['method'] = $query['METHOD'];
        } else {
            throw new HttpResponse('', 400);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof ConfirmPayment &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
