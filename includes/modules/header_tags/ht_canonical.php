<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Sites\Shop\HeaderTags;

  class ht_canonical
  {
    public $code;
    public $group;
    public string $title;
    public string $description;
    public ?int $sort_order = 0;
    public bool $enabled = false;

    public function __construct()
    {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_header_tags_canonical_title');
      $this->description = CLICSHOPPING::getDef('module_header_tags_canonical_description');

      if (\defined('MODULE_HEADER_TAGS_CANONICAL_STATUS')) {
        $this->sort_order = MODULE_HEADER_TAGS_CANONICAL_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_CANONICAL_STATUS == 'True');
      }
    }

    public function execute()
    {
      $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');
      $CLICSHOPPING_Category = Registry::get('Category');

      $this->rewriteUrl = Registry::get('RewriteUrl');

      $cPath = $CLICSHOPPING_Category->getPath();

      $CLICSHOPPING_Template = Registry::get('Template');

      if (isset($_GET['Products']) && isset($_GET['ProductsNew'])) {
        $CLICSHOPPING_Template->addBlock('<link rel="canonical" href="' . CLICSHOPPING::link(null, 'Products&ProductsNew') . '" />' . "\n", $this->group);
      }

      if (isset($_GET['Products']) && isset($_GET['Specials'])) {
        $CLICSHOPPING_Template->addBlock('<link rel="canonical" href="' . CLICSHOPPING::link(null, 'Products&Specials') . '" />' . "\n", $this->group);
      }

      if (isset($_GET['Products']) && isset($_GET['Description'])) {
        $CLICSHOPPING_Template->addBlock('<link rel="canonical" href="' . $this->rewriteUrl->getProductNameUrl((int)$CLICSHOPPING_ProductsCommon->getID()) . '" />' . "\n", $this->group);
      }

      if (isset($_GET['Blog']) && isset($_GET['Categories'])) {
        if (isset($cPath) && !empty($cPath)) {
          $CLICSHOPPING_Template->addBlock('<link rel="canonical" href="' . $this->rewriteUrl->getBlogCategoriesUrl($cPath) . '" />' . "\n", $this->group);
        }
      }

      if (isset($_GET['Blog']) && isset($_GET['Content'])) {
        $CLICSHOPPING_Template->addBlock('<link rel="canonical" href="' . $this->rewriteUrl->getBlogContentUrl((int)$_GET['blogContentId']) . '" />' . "\n", $this->group);
      }

      if (isset($_GET['PageManager']) && isset($_GET['Infos'])) {
        $CLICSHOPPING_Template->addBlock('<link rel="canonical" href="' . $this->rewriteUrl->getPageManagerContentUrl((int)$_GET['pagesId']) . '" />' . "\n", $this->group);
      }

      if (isset($_GET['Search']) && isset($_GET['Q'])) {
        $CLICSHOPPING_Template->addBlock('<link rel="canonical" href="' . CLICSHOPPING::link('inde.php', 'Search&Q') . '" />' . "\n", $this->group);
      }

      if (isset($_GET['Index'])) {
        if (isset($cPath) && !empty($cPath)) {
          $CLICSHOPPING_Template->addBlock('<link rel="canonical" href="' . $this->rewriteUrl->getCategoryTreeUrl($cPath) . '" />' . "\n", $this->group);
        } elseif (isset($_GET['manufacturersId']) && !\is_null($_GET['manufacturersId'])) {
          $CLICSHOPPING_Template->addBlock('<link rel="canonical" href="' . $this->rewriteUrl->getManufacturerUrl((int)$_GET['manufacturersId']) . '" />' . "\n", $this->group);
        }
      }
    }

    public function isEnabled()
    {
      return $this->enabled;
    }

    public function check()
    {
      return \defined('MODULE_HEADER_TAGS_CANONICAL_STATUS');
    }

    public function install()
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want enable this module ?',
          'configuration_key' => 'MODULE_HEADER_TAGS_CANONICAL_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want enable this module ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort Order',
          'configuration_key' => 'MODULE_HEADER_TAGS_CANONICAL_SORT_ORDER',
          'configuration_value' => '50',
          'configuration_description' => 'Sort order. Lowest is displayed in first',
          'configuration_group_id' => '6',
          'sort_order' => '25',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );
    }

    public function remove()
    {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys()
    {
      return array('MODULE_HEADER_TAGS_CANONICAL_STATUS',
        'MODULE_HEADER_TAGS_CANONICAL_SORT_ORDER');
    }
  }
