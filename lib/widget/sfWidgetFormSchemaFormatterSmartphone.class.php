<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * sfWidgetFormSchemaFormatterSmartphone
 *
 * @package    OpenPNE
 * @subpackage widget
 * @author     Shogo Kawahara <kawahara@bucyou.net>
 */
class sfWidgetFormSchemaFormatterSmartphone extends opWidgetFormSchemaFormatter
{
  protected
    $rowFormat = "<li>\n <div class=\"label\">%label%</div>\n <div class=\"field\">%error%%field%%help%%hidden_fields%</div>\n </li>",
    $helpFormat = '<div class="help">%help%</div>',
    $errorListFormatInARow = "  <div class=\"error\"><ul class=\"error_list\">\n%errors%  </ul></div>\n",
    $decoratorFormat = "<ul>\n %content</ul>";
}
