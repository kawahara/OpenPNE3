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
class sfSmartphoneViewToolKit
{
  static protected
    $generalSmartphoneSuffix = 'smartphone',
    $smartphoneSuffixes = array(
      '/iPhone|iPod/' => 'i_phone',
      '/Android/'     => 'android',
    ),
    $isSm = null,
    $isUseSmTemplate = null,
    $suffix = null;

  static protected function isExistsTemplateFile($view, $context, $moduleName, $actionName, $viewName, $suffix)
  {
    $config = $context->getConfiguration();

    $templateFile = $actionName.$viewName.sfInflector::camelize($suffix).$view->getExtension();

    if ('global' == $moduleName && $config->getDecoratorDir($templateFile))
    {
      return true;
    }
    elseif ($config->getTemplateDir($moduleName, $templateFile))
    {
      return true;
    }

    return false;
  }

  static public function getGeneralSmartphoneSuffix()
  {
    return self::$generalSmartphoneSuffix;
  }

  static public function getSmartphoneSuffixes()
  {
    return self::$smartphoneSuffixes;
  }

  static public function checkUA($context, $pattern)
  {
    $pathArray = $context->getRequest()->getPathInfoArray();

    return preg_match($pattern, $pathArray['HTTP_USER_AGENT']);
  }

  static public function isSmartphone($context = null)
  {
    if (null !== self::$isSm) return self::$isSm;

    if (null === $context)
    {
      $context = sfContext::getInstance();
    }

    foreach (self::$smartphoneSuffixes as $key => $name)
    {
      if (self::checkUA($context, $key))
      {
        self::$suffix = $name;
        self::$isSm = true;
        break;
      }
    }

    if (!self::$isSm) self::$isSm = false;

    $event = new sfEvent(null, 'sf_smartphone_view.post_check_smartphone', array(
      'is_smartphone' => self::$isSm,
      'suffix' => self::$suffix
    ));
    $context->getEventDispatcher()->notify($event);

    return self::$isSm;
  }

  static public function getViewNameSuffixFromUA($view, $context, $moduleName, $actionName, $viewName, $checkFile = true)
  {
    if (!self::isSmartphone())
    {
      $suffix = '';
    }
    elseif (!$checkFile
      || self::isExistsTemplateFile($view, $context, $moduleName, $actionName, $viewName, self::$suffix)
    )
    {
      $suffix = self::$suffix;
    }
    elseif (!$checkFile
        || self::isExistsTemplateFile($view, $context, $moduleName, $actionName, $viewName, self::$generalSmartphoneSuffix)
    )
    {
      $suffix = self::$generalSmartphoneSuffix;
    }
    else
    {
      $suffix = '';
    }

    if ($checkFile && null === self::$isUseSmTemplate)
    {
      self::$isUseSmTemplate = !empty($suffix);

      $event = new sfEvent(null, 'sf_smartphone_view.post_decide_smartphone', array(
        'is_use_smartphone_template' => self::$isUseSmTemplate,
        'suffix' => self::$suffix
      ));
      $context->getEventDispatcher()->notify($event);
    }

    return $suffix;
  }

  static public function getViewNameFromUA($view, $context, $moduleName, $actionName, $viewName)
  {
    if (null === self::$isUseSmTemplate || self::$isUseSmTemplate)
      return $viewName.sfInflector::camelize(self::getViewNameSuffixFromUA($view, $context, $moduleName, $actionName, $viewName));

    return $viewName;
  }
}
