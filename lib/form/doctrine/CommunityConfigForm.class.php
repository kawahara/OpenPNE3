<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * CommunityConfig form.
 *
 * @package    OpenPNE
 * @subpackage form
 * @author     Kousuke Ebihara <ebihara@tejimaya.com>
 */
class CommunityConfigForm extends BaseForm
{
  protected
    $configSettings = array(),
    $category = '',
    $community,
    $isNew = false,
    $isAutoGenerate = true;

  public function __construct($defaults = array(), $options = array(), $CSRFSecret = null)
  {
    return parent::__construct($defaults, $options, false);
  }

  public function configure()
  {
    $this->setCommunity($this->getOption('community'));

    $this->setConfigSettings();

    if ($this->isAutoGenerate)
    {
      $this->generateConfigWidgets();
    }

    $this->widgetSchema->setNameFormat('community_config[%s]');
    $this->widgetSchema->getFormFormatter()->setTranslationCatalogue('form_community');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
  }

  public function setCommunity($community)
  {
    if (!($community instanceof Community))
    {
      $community = new Community();
    }
    $this->community = $community;
  }

  public function generateConfigWidgets()
  {
    foreach ($this->configSettings as $key => $value)
    {
      $this->setConfigWidget($key);
    }

    $apps = array();
    if (opConfig::get('enable_pc'))
    {
      $apps[] = 'pc';
    }
    if (opConfig::get('enable_mobile'))
    {
      $apps[] = 'mobile';
    }

    $template = 'joinCommunity';
    foreach ($apps as $app)
    {
      $notificationMail = Doctrine::getTable('NotificationMail')->findOneByName($app.'_'.$template);

      if (!$notificationMail || $notificationMail->getIsEnabled())
      {
        $choices = array(
          1 => '受信する',
          0 => '受信しない'
        );
        $name = 'is_send_'.$app.'_'.$template.'_mail';
        $this->setWidget($name, new sfWidgetFormChoice(array('choices' => $choices, 'expanded' => true)));
        $this->setValidator($name, new sfValidatorChoice(array('choices' => array_keys($choices))));
        $this->widgetSchema->setLabel($name, '参加お知らせメール受信設定を'.$app.'メールで');
        $this->widgetSchema->setHelp($name, 'コミュニティに新しく参加者が加わったときに、管理者(あなた)にメールを送ります。');

        $default = $this->community->getConfig($name);
        $default = null === $default ? 1 : $default;
        $this->setDefault($name, $default);
      }
    }
  }

  public function setConfigWidget($name)
  {
    $config = $this->configSettings[$name];
    $this->widgetSchema[$name] = opFormItemGenerator::generateWidget($config);
    $this->widgetSchema->setLabel($name, $config['Caption']);
    $communityConfig = Doctrine::getTable('CommunityConfig')->retrieveByNameAndCommunityId($name, $this->community->getId());
    if ($communityConfig)
    {
      $this->setDefault($name, $communityConfig->getValue());
    }
    $this->validatorSchema[$name] = opFormItemGenerator::generateValidator($config);
  }

  public function setConfigSettings($category = '')
  {
    $categories = sfConfig::get('openpne_community_category');
    $configs = sfConfig::get('openpne_community_config');

    if (!$category)
    {
      $this->configSettings = $configs;
      return true;
    }

    foreach ($categories[$category] as $value)
    {
      $this->configSettings[$value] = $configs[$value];
    }
  }

  public function save()
  {
    foreach ($this->getValues() as $key => $value)
    {
      $config = Doctrine::getTable('CommunityConfig')->retrieveByNameAndCommunityId($key, $this->community->getId());
      if (!$config)
      {
        $config = new CommunityConfig();
        $config->setCommunity($this->community);
        $config->setName($key);
      }
      $config->setValue($value);
      $config->save();
    }
  }
}
