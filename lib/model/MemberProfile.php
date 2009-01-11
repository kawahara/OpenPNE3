<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

class MemberProfile extends BaseMemberProfileNestedSet
{
  private $name;
  private $caption;

  public function __toString()
  {
    if ($this->getProfile()->getFormType() === 'date')
    {
      return (string)$this->getValue();
    }

    if ($this->hasChildren())
    {
      $pieces = array();
      $children = $this->getChildren();

      foreach ($children as $child)
      {
        if ($child->getProfileOptionId())
        {
          $option = ProfileOptionPeer::retrieveByPk($child->getProfileOptionId());
          $pieces[] = $option->getValue();
        }
      }

      return implode(', ', $pieces);
    }

    if ($this->getProfileOptionId())
    {
      $option = ProfileOptionPeer::retrieveByPk($this->getProfileOptionId());
      return (string)$option->getValue();
    }

    return (string)$this->getValue();
  }

  public function getValue()
  {
    if ($this->getProfile()->getFormType() === 'date' && !$this->isRoot())
    {
      return parent::getValue();
    }

    if ($this->hasChildren())
    {
      $children = $this->getChildren();
      $value = array();
      foreach ($children as $child)
      {
        if ($child->getProfile()->getFormType() === 'date')
        {
          $value[] = $child->getValue();
        }
        elseif ($child->getProfileOptionId())
        {
          $option = ProfileOptionPeer::retrieveByPk($child->getProfileOptionId());
          $value[] = $option->getValue();
        }
      }

      if ($this->getProfile()->getFormType() === 'date' && $this->isRoot())
      {
        $obj = new DateTime();
        $obj->setDate($value[0], $value[1], $value[2]);
        return $obj->format('Y-m-d');
      }

      return $value;
    }
    if ($this->getProfileOptionId())
    {
      return $this->getProfileOptionId();
    }

    return parent::getValue();
  }

  public function hydrateProfiles($row)
  {
    try {
      $col = parent::hydrate($row);
      $this->name = $row[$col+0];
      $this->caption = $row[$col+1];
    } catch (Exception $e) {
      throw new PropelException("Error populating MemberProfile object", $e);
    }
  }

  public function getName()
  {
    return $this->name;
  }

  public function getCaption()
  {
    if (is_null($this->caption))
    {
      $this->caption = $this->getProfile()->getCaption();
    }
    return $this->caption;
  }
}
