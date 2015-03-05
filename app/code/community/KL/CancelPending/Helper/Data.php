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
 * Class KL_CancelPending_Helper_Data
 */
class KL_CancelPending_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Fetch config setting
     *
     * @param        $field
     * @param string $group
     *
     * @return mixed
     */
    protected function getConfigValue($field, $group = 'kl_cancelpending_general')
    {
        return Mage::getStoreConfig('kl_cancelpending/' . $group . '/' . $field);
    }

    /**
     * Check if the module is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        if ($this->getConfigValue('enabled') === '1') {
            return true;
        }

        return false;
    }

    /**
     * Return time limit in minutes
     *
     * @return int
     */
    public function getTimeLimit()
    {
        $timeLimit = $this->getConfigValue('waithours');

        return intval($timeLimit) * 60;
    }
}
