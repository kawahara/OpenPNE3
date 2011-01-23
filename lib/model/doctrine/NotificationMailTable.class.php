<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * NotificationMailTable
 *
 * @package    OpenPNE
 * @subpackage model
 * @author     Shogo Kawahara <kawahara@bucyou.net>
 */
class NotificationMailTable extends Doctrine_Table
{
  protected
    $configs = null;

  public function getDisabledNotificationNames()
  {
    $result = array();

    $list = $this->createQuery()
      ->select('name')
      ->where('is_enabled = ?', false)
      ->execute(array(), Doctrine::HYDRATE_NONE);

    foreach ($list as $v)
    {
      $result[] = $v[0];
    }

    return $result;
  }

  public function fetchTemplateFromConfigSample($templateName)
  {
    $configs = $this->getConfigs();

    $template = explode('_', $templateName, 2);
    $sampleSubject = '';
    if (2 === count($template) && isset($configs[$template[0]][$template[1]]))
    {
      $config = $configs[$template[0]][$template[1]];
      if (isset($config['sample']) && is_array($config['sample']) && $config['sample'])
      {
        if (isset($config['sample'][sfDoctrineRecord::getDefaultCulture()]))
        {
          $sample = $config['sample'][sfDoctrineRecord::getDefaultCulture()];
        }
        else
        {
          $sample = current($config['sample']);
        }

        if ($sample)
        {
          if (is_array($sample))
          {
            if (2 <= count($sample))
            {
              return $sample;
            }
            else
            {
              return array('', $sample[0]);
            }
          }
          else
          {
            return array('', $sample);
          }
        }
      }
    }

    return null;
  }

  public function fetchTemplate($templateName)
  {
    $object = $this->findOneByName($templateName);
    if (!($object && $object->getTemplate()))
    {
      if (($sample = $this->fetchTemplateFromConfigSample($templateName)) && $sample[1])
      {
        $object = new NotificationMail();
        $object->Translation[sfDoctrineRecord::getDefaultCulture()]->title    = $sample[0];
        $object->Translation[sfDoctrineRecord::getDefaultCulture()]->template = $sample[1];
      }
    }

    return $object;
  }

  public function getConfigs()
  {
    if (null === $this->configs)
    {
      $this->configs = include(sfContext::getInstance()->getConfigCache()->checkConfig('config/mail_template.yml'));
    }

    return $this->configs;
  }

  public function isEnabledMemberConfig($member = null)
  {
    if (null === $member)
    {
      $member = sfContext::getInstance()->getUser()->getMember();
    }

    $configs = $this->getConfigs();

    $apps = array();
    if (opConfig::get('enable_pc') && isset($configs['pc']) &&
      $member->getConfig('pc_address'))
    {
      $apps[] = 'pc';
    }
    if (opConfig::get('enable_mobile') && isset($configs['mobile']) &&
      $member->getConfig('mobile_address'))
    {
      $apps[] = 'mobile';
    }


    foreach ($apps as $app)
    {
      if (isset($configs[$app]['dailyNews']))
      {
        $dailyNewsNotification = $this->findOneByName($app.'_dailyNews');
        if (!$dailyNewsNotification || $dailyNewsNotification->getIsEnabled())
        {
          return true;
        }
      }

      foreach ($configs[$app] as $name => $value)
      {
        if (isset($value['member_configurable']) && $value['member_configurable'])
        {
          $notification = $this->findOneByName($app.'_'.$name);
          if (!$notification || $notification->getIsEnabled())
          {
            return true;
          }
        }
      }
    }

    return false;
  }
}
