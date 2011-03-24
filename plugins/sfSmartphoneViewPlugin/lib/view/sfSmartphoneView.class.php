<?php

/**
 * This file is part of the sfSmartphoneViewPlugin.
 * (c) 2010 Shogo Kawahara <kawahara@bucyou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A view that uses to switch the template of smartphone.
 *
 * @package    sfSmartphoneViewPlugin
 * @subpackage view
 * @author     Shogo Kawahara <kawahara@bucyou.net>
 */
class sfSmartphoneView extends sfPHPView
{
  protected
    $nameSuffix = '',
    $enableNameSuffix = '';

  public function initialize($context, $moduleName, $actionName, $viewName)
  {
    $this->nameSuffix = sfSmartphoneViewToolKit::getViewNameSuffixFromUA($this, $context, $moduleName, $actionName, $viewName, false);
    if ($this->nameSuffix)
    {
      $this->enableNameSuffix = sfSmartphoneViewToolKit::getViewNameSuffixFromUA($this, $context, $moduleName, $actionName, $viewName);
    }

    $event = new sfEvent($this, 'sf_smartphone_view.post_configure_smartphone', array(
      'is_smartphone' => (boolean)$this->nameSuffix,
      'suffix' => $this->nameSuffix,
      'enableNameSuffix' => $this->enableNameSuffix
    ));
    $context->getEventDispatcher()->notify($event);

    parent::initialize($context, $moduleName, $actionName, $viewName.sfInflector::camelize($this->enableNameSuffix));
  }

  public function getNameSuffix()
  {
    return $this->nameSuffix;
  }
}
