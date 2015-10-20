<?php


function responsive_bartik_preprocess_html(&$variables)
{
  // Add variables for path to theme.
  $variables['base_path'] = base_path();
  $variables['path_to_resbartik'] = drupal_get_path('theme', 'responsive_bartik');

  // Add local.css stylesheet
  if (file_exists(drupal_get_path('theme', 'responsive_bartik') . '/css/local.css')) {
    drupal_add_css(drupal_get_path('theme', 'responsive_bartik') . '/css/local.css',
      array('group' => CSS_THEME, 'every_page' => TRUE));
  }

  // Add body classes if certain regions have content.
  if (!empty($variables['page']['featured'])) {
    $variables['classes_array'][] = 'featured';
  }

  if (!empty($variables['page']['triptych_first'])
    || !empty($variables['page']['triptych_middle'])
    || !empty($variables['page']['triptych_last'])
  ) {
    $variables['classes_array'][] = 'triptych';
  }

  if (!empty($variables['page']['footer_firstcolumn'])
    || !empty($variables['page']['footer_secondcolumn'])
    || !empty($variables['page']['footer_thirdcolumn'])
    || !empty($variables['page']['footer_fourthcolumn'])
  ) {
    $variables['classes_array'][] = 'footer-columns';
  }

}

/**
 * Override or insert variables into the page template for HTML output.
 */
function responsive_bartik_process_html(&$variables)
{
  // Hook into color.module.
  if (module_exists('color')) {
    _color_html_alter($variables);
  }
}

/**
 * Override or insert variables into the page template.
 */
function responsive_bartik_process_page(&$variables)
{
/*  static $dla_menu;
  if (! isset($dla_menu) || $reset){
    // Retrieve DLA menu from cache...
#    if (! $reset && ($cache = cache_get('responsive_bartik_dla_menu')) && ! empty($cache->data) ){
#      $dla_menu = $cache->data;
#    }
    // ... or get DLA menu from main dla site and cache.
   
      global $base_url;
      preg_match('@^(http://[^/]+)@i',$base_url, $matches);
      $hostname = $matches[0];
      $menu_url =  $base_url . '/sites/all/themes/custom/responsive_bartik/js/dla_menu';

      $context = stream_context_create(array('http' => array('timeout' => 3)));
      $dla_menu = file_get_contents($menu_url, 0, $context);

dpm($variables['dla_menu']);

      if ($dla_menu){
        // Cache for 15 minutes.
        cache_set('responsive_bartik_dla_menu', $dla_menu, 'cache', time() + 10);
      }
  }

*/
  // Hook into color.module.
  if (module_exists('color')) {
    _color_page_alter($variables);
  }
  // Always print the site name and slogan, but if they are toggled off, we'll
  // just hide them visually.
  $variables['hide_site_name'] = theme_get_setting('toggle_name') ? FALSE : TRUE;
  $variables['hide_site_slogan'] = theme_get_setting('toggle_slogan') ? FALSE : TRUE;
  if ($variables['hide_site_name']) {
    // If toggle_name is FALSE, the site_name will be empty, so we rebuild it.
    $variables['site_name'] = filter_xss_admin(variable_get('site_name', 'Drupal'));
  }
  if ($variables['hide_site_slogan']) {
    // If toggle_site_slogan is FALSE, the site_slogan will be empty, so we rebuild it.
    $variables['site_slogan'] = filter_xss_admin(variable_get('site_slogan', ''));
  }
  // Since the title and the shortcut link are both block level elements,
  // positioning them next to each other is much simpler with a wrapper div.
  if (!empty($variables['title_suffix']['add_or_remove_shortcut']) && $variables['title']) {
    // Add a wrapper div using the title_prefix and title_suffix render elements.
    $variables['title_prefix']['shortcut_wrapper'] = array(
      '#markup' => '<div class="shortcut-wrapper clearfix">',
      '#weight' => 100,
    );
    $variables['title_suffix']['shortcut_wrapper'] = array(
      '#markup' => '</div>',
      '#weight' => -99,
    );
    // Make sure the shortcut link is the first item in title_suffix.
    $variables['title_suffix']['add_or_remove_shortcut']['#weight'] = -100;

  }
 
}


/*
 * Helper function to get DLA menu from main DLA site or local cache.

function _responsive_bartik_get_dla_menu($reset = FALSE){

  $variables['dla_menu'] = 'inside function';  

  return $dla_menu;
} */


/**
 * Implements hook_preprocess_maintenance_page().
 */
function responsive_bartik_preprocess_maintenance_page(&$variables)
{
  // By default, site_name is set to Drupal if no db connection is available
  // or during site installation. Setting site_name to an empty string makes
  // the site and update pages look cleaner.
  // @see template_preprocess_maintenance_page
  if (!$variables['db_is_active']) {
    $variables['site_name'] = '';
  }
  drupal_add_css(drupal_get_path('theme', 'responsive_bartik') . '/css/maintenance-page.css');
}

/**
 * Override or insert variables into the maintenance page template.
 */
function responsive_bartik_process_maintenance_page(&$variables)
{
  // Always print the site name and slogan, but if they are toggled off, we'll
  // just hide them visually.
  $variables['hide_site_name'] = theme_get_setting('toggle_name') ? FALSE : TRUE;
  $variables['hide_site_slogan'] = theme_get_setting('toggle_slogan') ? FALSE : TRUE;
  if ($variables['hide_site_name']) {
    // If toggle_name is FALSE, the site_name will be empty, so we rebuild it.
    $variables['site_name'] = filter_xss_admin(variable_get('site_name', 'Drupal'));
  }
  if ($variables['hide_site_slogan']) {
    // If toggle_site_slogan is FALSE, the site_slogan will be empty, so we rebuild it.
    $variables['site_slogan'] = filter_xss_admin(variable_get('site_slogan', ''));
  }
}

/**
 * Override or insert variables into the node template.
 */
function responsive_bartik_preprocess_node(&$variables)
{
  if ($variables['view_mode'] == 'full' && node_is_page($variables['node'])) {
    $variables['classes_array'][] = 'node-full';
  }
}

/**
 * Override or insert variables into the block template.
 */
function responsive_bartik_preprocess_block(&$variables)
{
  // In the header region visually hide block titles.
  if ($variables['block']->region == 'header') {
    $variables['title_attributes_array']['class'][] = 'element-invisible';
  }
}

/**
 * Implements theme_menu_tree().
 */
function responsive_bartik_menu_tree($variables)
{
  return '<ul class="menu clearfix">' . $variables['tree'] . '</ul>';
}

function responsive_bartik_links($variables) {
  if (array_key_exists('id', $variables['attributes']) && $variables['attributes']['id'] == 'main-menu-links') {
      $pid = variable_get('menu_main_links_source', 'main-menu');
    $tree = menu_tree($pid);
    return drupal_render($tree);
  }
  return theme_links($variables);
}

/**
 * Implementation of hook_theme_breadcrumb
 */
function responsive_bartik_breadcrumb($variables){
    $breadcrumb = $variables['breadcrumb'];
    // Remove 'home' breadcrumb (first array element) if it is present
    if (!empty($breadcrumb)) {
        array_shift($breadcrumb);
        array_shift($breadcrumb);
    }
    // Get sitename.
#    $sitename = variable_get('site_name', 'drupal');
    // Prepend MBLWHOI Library, DLA, and site name to breadcrumbs.
    array_unshift($breadcrumb, 
                  l('MBLWHOI Library', 'http://www.mblwhoilibrary.org'),
                  l('WHOI DLA', base_path(), array('external' => TRUE))
                 );
    // Append page title.
    $breadcrumb[] = drupal_get_title();
    return '<div class="breadcrumb">' . implode(' » ', $breadcrumb) . '</div>';

}


/**
 * Implements theme_field__field_type().
 */
function responsive_bartik_field__taxonomy_term_reference($variables)
{
  $output = '';

  // Render the label, if it's not hidden.
  if (!$variables['label_hidden']) {
    $output .= '<h3 class="field-label">' . $variables['label'] . ': </h3>';
  }

  // Render the items.
  $output .= ($variables['element']['#label_display'] == 'inline') ? '<ul class="links inline">' : '<ul class="links">';
  foreach ($variables['items'] as $delta => $item) {
    $output .= '<li class="taxonomy-term-reference-' . $delta . '"' . $variables['item_attributes'][$delta] . '>' . drupal_render($item) . '</li>';
  }
  $output .= '</ul>';

  // Render the top-level DIV.
  $output = '<div class="' . $variables['classes'] . (!in_array('clearfix', $variables['classes_array']) ? ' clearfix' : '') . '"' . $variables['attributes'] . '>' . $output . '</div>';

  return $output;
}
