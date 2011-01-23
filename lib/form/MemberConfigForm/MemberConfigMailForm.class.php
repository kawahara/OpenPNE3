<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * MemberConfigMail form.
 *
 * @package    OpenPNE
 * @subpackage form
 * @author     Kousuke Ebihara <ebihara@tejimaya.com>
 */
class MemberConfigMailForm extends MemberConfigForm
{
  protected $category = 'mail';

  public function configure()
  {
    $configs = Doctrine::getTable('NotificationMail')->getConfigs();

    $apps = array();
    if (opConfig::get('enable_pc') && isset($configs['pc']))
    {
      $apps[] = 'pc';
    }
    if (opConfig::get('enable_mobile') && isset($configs['mobile']))
    {
      $apps[] = 'mobile';
    }

    foreach ($apps as $app)
    {
      $this->setWidget($app.'_separator', new opWidgetFormSeparator());
      $this->setValidator($app.'_separator', new sfValidatorPass());
      $this->widgetSchema->setLabel($app.'_separator', $app);

      if (isset($configs[$app]['dailyNews']))
      {
        $name = $app.'_dailyNews';
        $notification = Doctrine::getTable('NotificationMail')->findOneByName($app.'_daily_news');

        if (!$notification || $notification->getIsEnabled())
        {
          $count = count(opConfig::get('daily_news_day'));

          $i18n = sfContext::getInstance()->getI18N();
          $translated = $i18n->__('[1]Send once a week (%2%)|[2]Send twice a week (%2%)|(2,+Inf]Send %1% times a week (%2%)', array(
            '%1%' => $count,
            '%2%' => implode(',', $this->generateDayList()))
          );

          $choice = new sfChoiceFormat();
          $retval = $choice->format($translated, $count);

          $choices = array(
            2 => 'Send Everyday',
            1 => $retval,
            0 => "Don't Send",
          );

          $this->setWidget($name, new sfWidgetFormChoice(array('choices' => $choices, 'expanded' => true)));
          $this->setValidator($name, new sfValidatorChoice(array('choices' => array_keys($choices))));
          $this->widgetSchema->setLabel($name, 'Daily News');

          $this->setDefault($name, $this->member->getConfig($name, 2));
        }
      }

      $choices = array(
        1 => '受信する',
        0 => '受信しない'
      );

      foreach ($configs[$app] as $key => $value)
      {
        if (isset($value['member_configurable']) && $value['member_configurable'])
        {
          $notification = Doctrine::getTable('NotificationMail')->findOneByName($app.'_'.$key);
          if (!$notification || $notification->getIsEnabled())
          {
            $name  = 'is_send_'.$app.'_'.$key.'_mail';

            $this->setWidget($name, new sfWidgetFormChoice(array('choices' => $choices, 'expanded' => true)));
            $this->setValidator($name, new sfValidatorChoice(array('choices' => array_keys($choices), 'required' => true)));
            $this->widgetSchema->setLabel($name, $value['caption']);

            $this->setDefault($name, $this->member->getConfig($name, 1));
          }
        }
      }
    }
  }

  protected function generateDayList()
  {
    $result = array();

    $dayNames = sfDateTimeFormatInfo::getInstance(sfContext::getInstance()->getUser()->getCulture())->getAbbreviatedDayNames();
    $sun = array_shift($dayNames);
    $dayNames[] = $sun;

    $day = opConfig::get('daily_news_day');
    $config = sfConfig::get('openpne_sns_config');
    $i18n = sfContext::getInstance()->getI18N();

    foreach ($day as $v)
    {
      $result[] = $i18n->__($config['daily_news_day']['Choices'][$v]);
    }

    return $result;
  }
}
