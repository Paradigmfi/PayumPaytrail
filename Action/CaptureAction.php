<?php
namespace Paradigm\PayumPaytrail\Action;

use League\Url\Url;
use Paradigm\PayumPaytrail\Request\Api\ConfirmPayment;
use Paradigm\PayumPaytrail\Request\Api\CreatePayment;
use Payum\Core\Action\GatewayAwareAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Request\Capture;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Security\GenericTokenFactoryAwareInterface;
use Payum\Core\Security\GenericTokenFactoryInterface;

class CaptureAction extends GatewayAwareAction implements GenericTokenFactoryAwareInterface
{
    /**
     * @var GenericTokenFactoryInterface
     */
    protected $tokenFactory;

    /**
     * @param GenericTokenFactoryInterface $genericTokenFactory
     *
     * @return void
     */
    public function setGenericTokenFactory(GenericTokenFactoryInterface $genericTokenFactory = null)
    {
        $this->tokenFactory = $genericTokenFactory;
    }

    /**
     * {@inheritDoc}
     *
     * @param Capture $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        $this->gateway->execute($httpRequest = new GetHttpRequest());

        if (false == $model['url']) {
            $urlSet = ArrayObject::ensureArrayObject($model->get('urlSet', []));

            if (false == $urlSet['success'] && $request->getToken()) {
                $urlSet['success'] = $request->getToken()->getTargetUrl();
            }
            if (false == $urlSet['failure'] && $request->getToken()) {
                $urlSet['failure'] = $request->getToken()->getTargetUrl();
            }

            if (empty($urlSet['notification']) && $request->getToken() && $this->tokenFactory) {
                $notifyToken = $this->tokenFactory->createNotifyToken(
                    $request->getToken()->getGatewayName(),
                    $request->getToken()->getDetails()
                );

                $urlSet['notification'] = $notifyToken->getTargetUrl();
            }

            $failureUrl = Url::createFromUrl($urlSet['failure']);
            $query = $failureUrl->getQuery();
            $query->modify(['cancelled' => 1]);
            $failureUrl->setQuery($query);
            $urlSet['failure'] = (string) $failureUrl;

            $model['urlSet'] = (array) $urlSet;

            $this->gateway->execute(new CreatePayment($model));

            if ($model['url']) {
                throw new HttpRedirect($model['url']);
            }
        } elseif (isset($httpRequest->query['cancelled'])) {
              $model['cancelled'] = true;
        } else {
            $this->gateway->execute(new ConfirmPayment($model));
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
