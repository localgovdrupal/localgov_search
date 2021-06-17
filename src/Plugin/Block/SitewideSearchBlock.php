<?php

namespace Drupal\localgov_search\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormState;
use Drupal\views\Views;

/**
 * Provides a 'SitewideSearchBlock' block.
 *
 * @Block(
 *  id = "localgov_sitewide_search_block",
 *  admin_label = @Translation("Sitewide search block"),
 * )
 */
class SitewideSearchBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = [];

    // Add sitewide search view filters to block.
    // Adapted from: https://blog.werk21.de/en/2017/03/08/programmatically-render-exposed-filter-form
    $view_id = 'localgov_sitewide_search';
    $display_id = 'sitewide_search_page';
    $view = Views::getView($view_id);

    if ($view) {
      $view->setDisplay($display_id);
      $view->initHandlers();
      $form_state = (new FormState())->setStorage([
        'view' => $view,
        'display' => &$view->display_handler->display,
        'rerender' => TRUE,
      ])
        ->setMethod('get')
        ->setAlwaysProcess()
        ->disableRedirect();
      $form_state->set('rerender', NULL);
      $form = \Drupal::formBuilder()->buildForm('\Drupal\views\Form\ViewsExposedForm', $form_state);
      $form['#id'] .= '-block';
      $form['s']['#attributes']['placeholder'] = 'Search';
    }

    return $form;
  }
}
