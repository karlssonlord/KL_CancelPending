<?php
/**
 * KL_CancelPending
 *
 * LICENSE
 *
 * This source file is subject to the MIT license that is available
 * through the world-wide-web at the following URI:
 * http://opensource.org/licenses/MIT
 *
 * @author     Robert Lord <robert@karlssonlord.com>
 * @copyright  2015 Karlsson & Lord AB
 * @license    http://opensource.org/licenses/MIT MIT License
 * @since      File available since release 0.0.1
 */

/**
 * Class KL_CancelPending_Model_Cron
 */
class KL_CancelPending_Model_Cron extends Mage_Core_Model_Abstract
{
    /**
     * Search and cancel pending orders
     *
     * @return $this
     */
    public function run()
    {
        /**
         * Make sure the module is enabled
         */
        if (!Mage::helper('cancelpending')->isEnabled()) {
            return $this;
        }

        $timeLimit = Mage::helper('cancelpending')->getTimeLimit();

        /**
         * Setup resource collection
         */
        $orderCollection = Mage::getResourceModel('sales/order_collection');

        /**
         * Fetch pending orders
         */
        $orderCollection
            ->addFieldToFilter(
                array('status', 'state'),
                array('pending', Mage_Sales_Model_Order::STATE_PENDING_PAYMENT)
            )
            ->addFieldToFilter(
                'created_at',
                array(
                    'lt' => new Zend_Db_Expr("DATE_ADD('" . now() . "', INTERVAL -'" . $timeLimit . ":00' HOUR_MINUTE)")
                )
            )
            ->getSelect()
            ->order('entity_id')
            ->limit(10);

        /**
         * Loop each order
         */
        foreach ($orderCollection as $order) {

            /**
             * Load the order
             */
            $order = $order->load($order->getId());

            /**
             * Make sure the order can be cancelled
             */
            if (!$order->canCancel()) {
                continue;
            }

            /**
             * Update order status
             */
            $order
                ->cancel();

            /**
             * Add comment to order
             */
            $order
                ->addStatusToHistory($order->getStatus(), 'Order cancelled by KL_CancelPending', false)
                ->save();

            /**
             * Add notice to the logfile
             */
            Mage::helper('cancelpending')->logMessage(
                'Cancelled order ID #' . $order->getId() . ' since time limit of ' . $timeLimit . ' minutes was reached'
            );
        }

    }

}
