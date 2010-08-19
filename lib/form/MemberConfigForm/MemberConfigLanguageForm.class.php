<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * MemberConfigLanguageForm.
 *
 * @package    OpenPNE
 * @subpackage form
 * @author     Kousuke Ebihara <ebihara@tejimaya.com>
 */
class MemberConfigLanguageForm extends MemberConfigForm
{
  protected $category = 'language';

  public function save()
  {
    if ($language = $this->getValue('language'))
    {
      sfContext::getInstance()->getUser()->setCulture($language);
    }

    if ($timezone = $this->getValue('time_zone'))
    {
      sfContext::getInstance()->getUser()->setTimezone($timezone);
    }

    return parent::save();
  }
}
