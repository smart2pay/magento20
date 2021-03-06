<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Smart2Pay\GlobalPay\Gateway\Response;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;

class PaymentHandler implements HandlerInterface
{
    const TXN_ID = 'TXN_ID';

    /** @var \Smart2Pay\GlobalPay\Helper\S2pHelper $_s2pHelper */
    protected $_s2pHelper;

    /**
     * @param \Smart2Pay\GlobalPay\Helper\S2pHelper $s2pHelper
     */
    public function __construct(
        \Smart2Pay\GlobalPay\Helper\S2pHelper $s2pHelper
    ) {
        $this->_s2pHelper = $s2pHelper;
    }

    /**
     * Handles transaction id
     *
     * @param array $handlingSubject
     * @param array $response
     * @return void
     */
    public function handle(array $handlingSubject, array $response)
    {
        $s2p_helper = $this->_s2pHelper;

        /** @var \Magento\Payment\Gateway\Data\PaymentDataObject $paymentDataObject */
        $paymentDataObject = \Magento\Payment\Gateway\Helper\SubjectReader::readPayment($handlingSubject);
        /** @var $payment \Magento\Sales\Model\Order\Payment */
        $payment = $paymentDataObject->getPayment();
        $order = $payment->getOrder();

        if (!($smart2pay_config = $s2p_helper->getFullConfigArray())) {
            $smart2pay_config = [];
        }

        if (!empty($response['response']['id'])) {
            $payment->setTransactionId($response['response']['id']);
        }

        $payment->setIsTransactionClosed(false);
        $payment->setIsTransactionPending(true);

        if (!empty($response) && is_array($response)
        && !empty($response['response']) && is_array($response['response'])) {
            if (!empty($response['response']['status'])
            && !empty($response['response']['status']['id'])) {
                $order->setState(\Magento\Sales\Model\Order::STATE_NEW);

                if (($magento_status_id = $s2p_helper::convertGPStatusToMagentoStatus(
                    $response['response']['status']['id']
                ))
                && !empty($smart2pay_config['order_status_on_'.$magento_status_id])) {
                    $order->setStatus($smart2pay_config['order_status_on_'.$magento_status_id]);
                }

                $order->save();
            }
        } else {
            $order->setState(\Magento\Sales\Model\Order::STATE_CANCELED);
            if (!empty($smart2pay_config['order_status_on_'.$s2p_helper::S2P_STATUS_FAILED])) {
                $order->setStatus($smart2pay_config['order_status_on_'.$s2p_helper::S2P_STATUS_FAILED]);
            }

            $order->save();
        }

        $payment->save();
    }
}
