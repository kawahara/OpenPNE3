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

  public function __construct(Member $member = null, $options = array(), $CSRFSecret = null)
  {
    parent::__construct($member, $options, $CSRFSecret);

    $count = count(opConfig::get('daily_news_day'));

    $i18n = sfContext::getInstance()->getI18N();
    $translated = $i18n->__('[1]Send once a week (%2%)|[2]Send twice a week (%2%)|(2,+Inf]Send %1% times a week (%2%)', array(
      '%1%' => $count,
      '%2%' => implode(',', $this->generateDayList()))
    );

    $choice = new sfChoiceFormat();
    $retval = $choice->format($translated, $count);

    $options = $this->widgetSchema['daily_news']->getOptions();
    $options['choices'][1] = $retval;
    $this->widgetSchema['daily_news']->setOptions($options);
  }

  public function configure()
  {
    $configs = Doctrine::getTable('NotificationMail')->getConfigs();
    $app = 'mobile_frontend' == sfConfig::get('sf_app') ? 'mobile' : 'pc';
    if (!isset($configs[$app]))
    {
      return;
    }

    $i18n = sfContext::getInstance()->getI18N();
    $choices = array(
      1 => $i18n->__('Receive'),
      0 => $i18n->__('Don\'t Receive')
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
